<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->withCount('transactions')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create($data);

        NotificationService::send(
            $request->user(),
            'Akun pengguna ditambahkan',
            'Akun "'.$user->name.'" dengan role '.$user->role_label.' berhasil dibuat.',
            'user-plus',
            'warning',
            route('users.index'),
            NotificationService::superAdmins(),
        );

        return back()->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($user->role === 'super_admin'
            && $data['role'] !== 'super_admin'
            && User::query()->where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Super Admin yang aktif.');
        }

        if ($user->role === 'admin'
            && ! in_array($data['role'], ['admin', 'super_admin'], true)
            && User::query()->whereIn('role', ['admin', 'super_admin'])->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun admin yang aktif.');
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $selfRoleChanged = $user->id === $request->user()->id && $data['role'] !== $user->role;

        $user->update($data);

        NotificationService::send(
            $request->user(),
            'Akun pengguna diperbarui',
            'Akun "'.$user->name.'" berhasil diperbarui.',
            'users',
            'warning',
            $selfRoleChanged ? route('dashboard') : route('users.index'),
            NotificationService::superAdmins(),
        );

        if ($selfRoleChanged && ! $request->user()->fresh()->isSuperAdmin()) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Role akun Anda berhasil diperbarui.');
        }

        return back()->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->role === 'super_admin' && User::query()->where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Super Admin yang aktif.');
        }

        if ($user->role === 'admin'
            && User::query()->whereIn('role', ['admin', 'super_admin'])->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun admin yang aktif.');
        }

        $name = $user->name;
        $user->delete();

        NotificationService::send(
            $request->user(),
            'Akun pengguna dihapus',
            'Akun "'.$name.'" telah dihapus.',
            'trash',
            'expense',
            route('users.index'),
            NotificationService::superAdmins(),
        );

        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
