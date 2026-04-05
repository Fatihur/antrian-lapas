<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueCall extends Model
{
    use HasFactory;

    protected $table = 'queue_calls';

    protected $fillable = [
        'visit_queue_id',
        'called_by',
        'loket',
        'waktu_panggilan',
        'waktu_selesai',
        'status_panggilan',
        'recall_count',
    ];

    protected $casts = [
        'waktu_panggilan' => 'datetime',
        'waktu_selesai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recall_count' => 'integer',
    ];

    public function queue()
    {
        return $this->belongsTo(VisitQueue::class, 'visit_queue_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'called_by');
    }

    public function markAsCompleted()
    {
        $this->update([
            'waktu_selesai' => now(),
            'status_panggilan' => 'Selesai',
        ]);
    }

    public function markAsSkipped()
    {
        $this->update([
            'waktu_selesai' => now(),
            'status_panggilan' => 'Dilewati',
        ]);
    }
}
