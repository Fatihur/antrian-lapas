<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TextToSpeechService
{
    private $client;
    
    public function __construct()
    {
        // Initialize with credentials from .env
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        
        if ($credentialsPath && file_exists($credentialsPath)) {
            $this->client = new TextToSpeechClient([
                'credentials' => $credentialsPath,
            ]);
        } else {
            $this->client = null;
        }
    }
    
    /**
     * Generate audio file for queue call
     */
    public function generateQueueAudio(string $queueNumber, string $counter, bool $isRecall = false, int $recallCount = 0): ?string
    {
        if (!$this->client) {
            return $this->generateFallbackAudio($queueNumber, $counter, $isRecall, $recallCount);
        }
        
        try {
            // Prepare text in Indonesian
            $text = $this->prepareIndonesianText($queueNumber, $counter, $isRecall, $recallCount);
            
            // Set up synthesis input
            $synthesisInput = new SynthesisInput();
            $synthesisInput->setText($text);
            
            // Voice selection - Indonesian
            $voice = new VoiceSelectionParams();
            $voice->setLanguageCode('id-ID');
            $voice->setName('id-ID-Standard-A'); // Female voice, clear
            
            // Audio config
            $audioConfig = new AudioConfig();
            $audioConfig->setAudioEncoding(AudioEncoding::MP3);
            $audioConfig->setSpeakingRate(0.9); // Slower for clarity
            $audioConfig->setPitch(0); // Natural pitch
            $audioConfig->setVolumeGainDb(6.0); // Louder
            
            // Synthesize
            $response = $this->client->synthesizeSpeech($synthesisInput, $voice, $audioConfig);
            $audioContent = $response->getAudioContent();
            
            // Save to storage
            $filename = $this->saveAudio($audioContent, $queueNumber, $counter, $isRecall);
            
            return $filename;
            
        } catch (\Exception $e) {
            \Log::error('TTS Generation Failed: ' . $e->getMessage());
            return $this->generateFallbackAudio($queueNumber, $counter, $isRecall, $recallCount);
        }
    }
    
    /**
     * Prepare Indonesian text for TTS
     */
    private function prepareIndonesianText(string $queueNumber, string $counter, bool $isRecall, int $recallCount): string
    {
        // Extract queue part (e.g., "A001" from "A001-PAGI-06042026")
        $parts = explode('-', $queueNumber);
        $queuePart = $parts[0];
        
        // Convert to spoken form
        $spokenQueue = $this->spellOutQueueNumber($queuePart);
        
        if ($isRecall && $recallCount > 0) {
            $recallText = $this->numberToIndonesian($recallCount);
            return "Perhatian. Perhatian. Panggilan ulang ke {$recallText}. Nomor antrian {$spokenQueue}. Silakan segera menuju ke {$counter}.";
        }
        
        return "Perhatian. Nomor antrian {$spokenQueue}. Silakan menuju ke {$counter}.";
    }
    
    /**
     * Spell out queue number
     */
    private function spellOutQueueNumber(string $queuePart): string
    {
        $result = [];
        
        foreach (str_split($queuePart) as $char) {
            if (preg_match('/[A-Za-z]/', $char)) {
                $result[] = strtoupper($char);
            } elseif (preg_match('/[0-9]/', $char)) {
                $result[] = $this->numberToIndonesian((int)$char);
            }
        }
        
        return implode('. ', $result);
    }
    
    /**
     * Convert number to Indonesian words
     */
    private function numberToIndonesian(int $num): string
    {
        $words = [
            'nol', 'satu', 'dua', 'tiga', 'empat', 
            'lima', 'enam', 'tujuh', 'delapan', 'sembilan'
        ];
        
        return $words[$num] ?? (string)$num;
    }
    
    /**
     * Save audio file
     */
    private function saveAudio(string $audioContent, string $queueNumber, string $counter, bool $isRecall): string
    {
        $filename = 'audio/queue_' . Str::slug($queueNumber) . '_' . Str::slug($counter);
        if ($isRecall) {
            $filename .= '_recall';
        }
        $filename .= '_' . time() . '.mp3';
        
        Storage::disk('public')->put($filename, $audioContent);
        
        return $filename;
    }
    
    /**
     * Fallback: Use pre-recorded audio or Web Speech API
     */
    private function generateFallbackAudio(string $queueNumber, string $counter, bool $isRecall, int $recallCount): ?string
    {
        // Return null to trigger Web Speech API fallback in frontend
        return null;
    }
    
    /**
     * Get audio URL
     */
    public function getAudioUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }
        
        return Storage::disk('public')->url($filename);
    }
    
    /**
     * Cleanup old audio files
     */
    public function cleanupOldAudio(int $days = 7): void
    {
        $files = Storage::disk('public')->files('audio');
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if ($lastModified < $cutoffTime) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
