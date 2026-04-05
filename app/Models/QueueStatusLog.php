<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueStatusLog extends Model
{
    use HasFactory;

    protected $table = 'queue_status_logs';

    protected $fillable = [
        'visit_queue_id',
        'status_lama',
        'status_baru',
        'keterangan',
        'changed_by',
        'waktu_perubahan',
    ];

    protected $casts = [
        'waktu_perubahan' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function queue()
    {
        return $this->belongsTo(VisitQueue::class, 'visit_queue_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }
}
