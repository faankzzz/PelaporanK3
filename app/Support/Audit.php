<?php

namespace App\Support;

use App\Models\AuditLog;

class Audit
{
    public static function log(string $aksi, ?string $deskripsi = null): void
    {
        AuditLog::create([
            'user_id'   => auth()->id(),
            'aksi'      => $aksi,
            'deskripsi' => $deskripsi,
            'ip'        => request()->ip(),
        ]);
    }
}
