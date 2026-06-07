<x-layouts::app title="Quản lý người dùng">
    @include('partials.flash')

    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-semibold">Người dùng</h1>
            <p class="mt-1 text-sm text-zinc-500">Quản lý thông tin, vai trò và trạng thái tài khoản.</p>
        </div>

        <form class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-3" method="GET">
            <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email">
            <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="role_id">
                <option value="">Tất cả vai trò</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" type="submit">Lọc</button>
        </form>

        <div class="space-y-4">
            @forelse ($users as $user)
                <form class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 lg:grid-cols-[1fr_1fr_180px_160px_120px]" method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-zinc-500">Tên</label>
                        <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-zinc-500">Email</label>
                        <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-zinc-500">Điện thoại</label>
                        <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="phone" value="{{ $user->phone }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-zinc-500">Vai trò</label>
                        <select class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="role_id">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <label class="mb-2 flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_locked" value="1" @checked($user->is_locked)>
                            Khóa
                        </label>
                        <button class="rounded-md bg-zinc-900 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lưu</button>
                    </div>
                </form>
            @empty
                <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center text-zinc-500">Chưa có người dùng.</div>
            @endforelse
        </div>

        {{ $users->links() }}
    </div>
</x-layouts::app>
