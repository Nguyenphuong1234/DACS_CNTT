<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()->orderBy('name')->get();

        $users = User::query()
            ->with('role')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where('name', 'like', $search)->orWhere('email', 'like', $search);
            })
            ->when($request->filled('role_id'), fn ($query) => $query->where('role_id', $request->integer('role_id')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_locked' => ['nullable', 'boolean'],
        ]);

        $validated['is_locked'] = $request->boolean('is_locked');

        if ($user->id === $request->user()->id && $validated['is_locked']) {
            return back()->with('error', 'Không thể tự khóa tài khoản đang đăng nhập.');
        }

        $user->update($validated);

        return back()->with('success', 'Đã cập nhật người dùng.');
    }
}
