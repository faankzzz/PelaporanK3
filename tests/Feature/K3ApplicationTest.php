<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class K3ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_with_demo_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@k3.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_pelapor_can_access_dashboard_and_incidents(): void
    {
        $pelapor = User::where('role', 'pelapor')->first();

        $response = $this->actingAs($pelapor)->get('/');
        $response->assertStatus(200);

        $response = $this->actingAs($pelapor)->get('/insiden');
        $response->assertStatus(200);
    }

    public function test_pelapor_cannot_access_admin_area(): void
    {
        $pelapor = User::where('role', 'pelapor')->first();

        $response = $this->actingAs($pelapor)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_area_and_audit(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/audit');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_user_with_nip(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Pegawai Baru',
            'email' => 'pegawai@k3.test',
            'nip' => '198501012010121001',
            'role' => 'pelapor',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'pegawai@k3.test',
            'nip' => '198501012010121001',
        ]);
    }

    public function test_pelapor_can_create_incident_with_automatic_datetime(): void
    {
        $pelapor = User::where('role', 'pelapor')->first();
        $unit = \App\Models\Unit::first();
        $type = \App\Models\IncidentType::first();

        $response = $this->actingAs($pelapor)->post('/insiden', [
            'unit_id' => $unit->id,
            'incident_type_id' => $type->id,
            'lokasi_detail' => 'Lantai 2 Pabrik Pusri',
            'kronologi' => 'Terjadi kebocoran minor pada pipa steam saat inspeksi rutin.',
            // tanggal_kejadian tidak dikirim manual, harus otomatis diset backend
        ]);

        $this->assertDatabaseHas('incidents', [
            'reporter_id' => $pelapor->id,
            'unit_id' => $unit->id,
            'lokasi_detail' => 'Lantai 2 Pabrik Pusri',
            'status' => 'dilaporkan',
        ]);

        $incident = \App\Models\Incident::where('reporter_id', $pelapor->id)->latest('id')->first();
        $this->assertNotNull($incident->tanggal_kejadian);
        $response->assertRedirect(route('incidents.show', $incident));
    }
}

