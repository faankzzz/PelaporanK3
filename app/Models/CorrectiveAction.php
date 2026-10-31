<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    protected $fillable = ['incident_id', 'deskripsi', 'penanggung_jawab', 'tenggat', 'status'];

    protected $casts = ['tenggat' => 'date'];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
