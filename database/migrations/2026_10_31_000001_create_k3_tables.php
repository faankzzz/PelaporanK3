<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('incident_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable()->unique();
            $table->foreignId('reporter_id')->constrained('users');
            $table->foreignId('incident_type_id')->constrained('incident_types');
            $table->foreignId('unit_id')->constrained('units');
            $table->dateTime('tanggal_kejadian');
            $table->string('lokasi_detail');
            $table->text('kronologi');
            $table->string('korban')->nullable();
            $table->string('saksi')->nullable();
            $table->string('foto')->nullable();
            // dilaporkan | diverifikasi | investigasi | tindak_lanjut | selesai
            $table->string('status')->default('dilaporkan');
            // ringan | sedang | berat | fatal
            $table->string('severity')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->dateTime('verified_at')->nullable();
            $table->text('penyebab_langsung')->nullable();
            $table->text('penyebab_dasar')->nullable();
            $table->text('hasil_investigasi')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->string('penanggung_jawab');
            $table->date('tenggat');
            $table->string('status')->default('belum'); // belum | proses | selesai
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('aksi');
            $table->text('deskripsi')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('corrective_actions');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('incident_types');
        Schema::dropIfExists('units');
    }
};
