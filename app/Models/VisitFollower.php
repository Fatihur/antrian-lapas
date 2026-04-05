<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitFollower extends Model
{
    use HasFactory;

    protected $table = 'visit_followers';

    protected $fillable = [
        'visit_queue_id',
        'nama_pengikut',
        'nomor_identitas_pengikut',
        'jenis_kelamin_pengikut',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function queue()
    {
        return $this->belongsTo(VisitQueue::class, 'visit_queue_id');
    }
}
