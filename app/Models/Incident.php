<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'verified_at'      => 'datetime',
        'closed_at'        => 'datetime',
    ];

    public const STATUS = [
        'dilaporkan'    => ['Dilaporkan', 'secondary'],
        'diverifikasi'  => ['Diverifikasi', 'info'],
        'investigasi'   => ['Investigasi', 'warning'],
        'tindak_lanjut' => ['Tindak Lanjut', 'primary'],
        'selesai'       => ['Selesai', 'success'],
    ];

    public const SEVERITY = [
        'ringan' => ['Ringan', 'success'],
        'sedang' => ['Sedang', 'warning'],
        'berat'  => ['Berat', 'danger'],
        'fatal'  => ['Fatal', 'dark'],
    ];

    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function type() { return $this->belongsTo(IncidentType::class, 'incident_type_id'); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function actions() { return $this->hasMany(CorrectiveAction::class); }

    public function statusLabel(): string { return self::STATUS[$this->status][0] ?? $this->status; }
    public function statusColor(): string { return self::STATUS[$this->status][1] ?? 'secondary'; }
    public function severityLabel(): string { return $this->severity ? (self::SEVERITY[$this->severity][0] ?? $this->severity) : '-'; }
    public function severityColor(): string { return $this->severity ? (self::SEVERITY[$this->severity][1] ?? 'secondary') : 'light'; }
}
