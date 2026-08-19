<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeRequest;
use App\Http\Requests\Admin\UpdateEmployeeRequest;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $employees = User::with('roles', 'outlet')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'customer'))
            ->orderBy('name')
            ->paginate(15);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::whereIn('name', ['admin', 'kasir', 'barista'])->orderBy('name')->get();
        $outlets = Outlet::orderBy('nama')->get();

        return view('admin.employees.create', compact('roles', 'outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'outlet_id' => $request->outlet_id,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee): View
    {
        $roles = Role::whereIn('name', ['super-admin', 'admin', 'kasir', 'barista', 'customer'])->orderBy('name')->get();
        $outlets = Outlet::orderBy('nama')->get();

        return view('admin.employees.edit', compact('employee', 'roles', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, User $employee): RedirectResponse
    {
        $data = $request->safe()->except(['password', 'password_confirmation', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);
        $employee->syncRoles([$request->role]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee): RedirectResponse
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
