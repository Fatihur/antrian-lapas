<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioFragmentService
{
    private $client;
    private $fragmentsPath = 'audio/fragments/';
    private $useLocalFiles = true;
    
    private $requiredFragments = [
        'words' => [
            'perhatian' => 'Perhatian',
            'nomor-antrian' => 'Nomor antrian',
            'silakan-menuju' => 'Silakan menuju',
            'loket' => 'Loket',
            'panggilan-ulang' => 'Panggilan ulang',
            'ke' => 'ke',
        ],
        'numbers' => ['nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'],
        'letters' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
                      'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
        'counters' => ['1', '2', '3', '4'],
    ];
    
    public function __construct()
    {
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        
        if ($credentialsPath && file_exists($credentialsPath)) {
            $this->client = new TextToSpeechClient([
                'credentials' => $credentialsPath,
            ]);
        }
        
        $this->useLocalFiles = $this->checkLocalFilesAvailable();
    }
    
    private function checkLocalFilesAvailable(): bool
    {
        $testFiles = ['perhatian.mp3', 'num-1.mp3', 'letter-A.mp3'];
        
        foreach ($testFiles as $file) {
            if (!Storage::disk('public')->exists($this->fragmentsPath . $file)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function isReady(): bool
    {
        return $this->useLocalFiles || $this->client !== null;
    }
    
    public function getReadinessStatus(): array
    {
        return [
            'local_files_available' => $this->useLocalFiles,
            'google_cloud_available' => $this->client !== null,
            'ready' => $this->isReady(),
            'message' => $this->useLocalFiles 
                ? '✅ Using local audio files (FREE)'
                : ($this->client !== null 
                    ? '✅ Using Google Cloud TTS'
                    : '❌ No audio source. Run: php artisan audio:download'),
        ];
    }
    
    /**
     * Generate via Google Cloud (fallback)
     */
    public function generateAllFragments(): array
    {
        $results = [];
        
        if (!$this->client) {
            return ['error' => 'Google Cloud credentials tidak tersedia. Gunakan: php artisan audio:download'];
        }
        
        foreach ($this->requiredFragments['words'] as $filename => $text) {
            $results[] = $this->generateFragment($filename, $text);
        }
        
        foreach ($this->requiredFragments['numbers'] as $index => $text) {
            $results[] = $this->generateFragment("num-{$index}", $text);
        }
        
        foreach ($this->requiredFragments['letters'] as $letter) {
            $results[] = $this->generateFragment("letter-{$letter}", $letter);
        }
        
        foreach ($this->requiredFragments['counters'] as $counter) {
            $results[] = $this->generateFragment("counter-{$counter}", $counter);
        }
        
        return $results;
    }
    
    private function generateFragment(string $filename, string $text): array
    {
        try {
            $filepath = $this->fragmentsPath . $filename . '.mp3';
            
            if (Storage::disk('public')->exists($filepath)) {
                return ['filename' => $filename, 'status' => 'exists'];
            }
            
            $synthesisInput = new SynthesisInput();
            $synthesisInput->setText($text);
            
            $voice = new VoiceSelectionParams();
            $voice->setLanguageCode('id-ID');
            $voice->setName('id-ID-Standard-A');
            
            $audioConfig = new AudioConfig();
            $audioConfig->setAudioEncoding(AudioEncoding::MP3);
            $audioConfig->setSpeakingRate(0.9);
            $audioConfig->setVolumeGainDb(6.0);
            
            $response = $this->client->synthesizeSpeech($synthesisInput, $voice, $audioConfig);
            
            Storage::disk('public')->put($filepath, $response->getAudioContent());
            
            return ['filename' => $filename, 'status' => 'created'];
            
        } catch (\Exception $e) {
            return ['filename' => $filename, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    public function createCallPlaylist(string $queueNumber, string $counter, bool $isRecall = false, int $recallCount = 0): array
    {
        $playlist = [];
        $chars = str_split($queueNumber);
        
        if ($isRecall && $recallCount > 0) {
            $playlist[] = $this->getFragmentUrl('perhatian');
            $playlist[] = $this->getFragmentUrl('perhatian');
            $playlist[] = $this->getFragmentUrl('panggilan-ulang');
            $playlist[] = $this->getFragmentUrl('ke');
            $playlist[] = $this->getFragmentUrl('num-' . ($recallCount > 9 ? 9 : $recallCount));
            $playlist[] = $this->getFragmentUrl('nomor-antrian');
        } else {
            $playlist[] = $this->getFragmentUrl('perhatian');
            $playlist[] = $this->getFragmentUrl('nomor-antrian');
        }
        
        foreach ($chars as $char) {
            if (preg_match('/[A-Za-z]/', $char)) {
                $playlist[] = $this->getFragmentUrl('letter-' . strtoupper($char));
            } elseif (preg_match('/[0-9]/', $char)) {
                $playlist[] = $this->getFragmentUrl('num-' . $char);
            }
        }
        
        $playlist[] = $this->getFragmentUrl('silakan-menuju');
        $playlist[] = $this->getFragmentUrl('loket');
        
        $counterNum = preg_replace('/[^0-9]/', '', $counter);
        if ($counterNum) {
            $playlist[] = $this->getFragmentUrl('counter-' . $counterNum);
        }
        
        return array_filter($playlist);
    }
    
    private function getFragmentUrl(string $filename): ?string
    {
        $filepath = $this->fragmentsPath . $filename . '.mp3';
        
        if (Storage::disk('public')->exists($filepath)) {
            // Get the URL and ensure it's absolute
            $url = Storage::disk('public')->url($filepath);
            
            // If URL is relative (starts with /), prepend the app URL
            if (str_starts_with($url, '/')) {
                $baseUrl = rtrim(config('app.url'), '/');
                $url = $baseUrl . $url;
            }
            
            return $url;
        }
        
        return null;
    }
    
    public function checkFragments(): array
    {
        $missing = [];
        $existing = [];
        
        foreach ($this->requiredFragments['words'] as $filename => $text) {
            if (Storage::disk('public')->exists($this->fragmentsPath . $filename . '.mp3')) {
                $existing[] = $filename;
            } else {
                $missing[] = $filename;
            }
        }
        
        for ($i = 0; $i <= 9; $i++) {
            if (Storage::disk('public')->exists($this->fragmentsPath . "num-{$i}.mp3")) {
                $existing[] = "num-{$i}";
            } else {
                $missing[] = "num-{$i}";
            }
        }
        
        foreach ($this->requiredFragments['letters'] as $letter) {
            if (Storage::disk('public')->exists($this->fragmentsPath . "letter-{$letter}.mp3")) {
                $existing[] = "letter-{$letter}";
            } else {
                $missing[] = "letter-{$letter}";
            }
        }
        
        foreach ($this->requiredFragments['counters'] as $counter) {
            if (Storage::disk('public')->exists($this->fragmentsPath . "counter-{$counter}.mp3")) {
                $existing[] = "counter-{$counter}";
            } else {
                $missing[] = "counter-{$counter}";
            }
        }
        
        return [
            'total_required' => count($existing) + count($missing),
            'existing' => count($existing),
            'missing' => count($missing),
            'missing_files' => $missing,
            'complete' => count($missing) === 0,
        ];
    }
    
    public function getAllFragmentUrls(): array
    {
        $urls = [];
        
        $allFragments = array_merge(
            array_keys($this->requiredFragments['words']),
            array_map(fn($i) => "num-{$i}", range(0, 9)),
            array_map(fn($l) => "letter-{$l}", $this->requiredFragments['letters']),
            array_map(fn($c) => "counter-{$c}", $this->requiredFragments['counters'])
        );
        
        foreach ($allFragments as $filename) {
            $url = $this->getFragmentUrl($filename);
            if ($url) {
                $urls[$filename] = $url;
            }
        }
        
        return $urls;
    }
}
