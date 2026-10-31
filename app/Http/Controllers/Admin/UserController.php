<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::with('unit')->orderBy('name')->paginate(15)]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(), 'units' => Unit::orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nip'      => 'nullable|string|max:50',
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
            'unit_id'  => 'nullable|exists:units,id',
            'password' => 'required|string|min:8',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        Audit::log('tambah_user', "Menambah akun {$user->email} ({$user->role})");

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['user' => $user, 'units' => Unit::orderBy('nama')->get()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nip'      => 'nullable|string|max:50',
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
            'unit_id'  => 'nullable|exists:units,id',
            'password' => 'nullable|string|min:8',
        ]);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan peran akun Anda sendiri.')->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);

        Audit::log('ubah_user', "Mengubah akun {$user->email}");

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        if (\App\Models\Incident::where('reporter_id', $user->id)->orWhere('verified_by', $user->id)->exists()) {
            return back()->with('error', 'Akun tidak dapat dihapus karena sudah terkait laporan insiden.');
        }

        Audit::log('hapus_user', "Menghapus akun {$user->email}");
        $user->delete();

        return back()->with('success', 'Akun dihapus.');
    }
}
