@extends('layouts.app')

@section('content')
    {{-- Reference: DESIGN.md & ARCHITECTURE.md --}}
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Manajemen User</h1>
            <button onclick="openModal('modalAddUser')"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Tambah User
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-700/50 text-slate-300">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Username</th>
                        <th class="px-6 py-4 font-semibold">Nama Pegawai</th>
                        <th class="px-6 py-4 font-semibold">Role</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-700/30">
                            <td class="px-6 py-4 font-medium">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $user->nama_pegawai }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-2 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-400/10 text-purple-400' : 'bg-blue-400/10 text-blue-400' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <button onclick="editUser({{ $user->id }}, '{{ $user->username }}', '{{ $user->nama_pegawai }}', '{{ $user->role }}')"
                                        class="text-slate-400 hover:text-white">Edit</button>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Add User --}}
    <div id="modalAddUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-800 p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-bold">Tambah User</h3>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Username</label>
                        <input type="text" name="username" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Nama Pegawai</label>
                        <input type="text" name="nama_pegawai" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Password</label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Role</label>
                        <select name="role" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="cashier">Cashier</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('modalAddUser')"
                        class="rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit User --}}
    <div id="modalEditUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-800 p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-bold">Edit User</h3>
            <form id="formEditUser" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Username</label>
                        <input type="text" name="username" id="edit_username" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Nama Pegawai</label>
                        <input type="text" name="nama_pegawai" id="edit_nama_pegawai" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password"
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">Role</label>
                        <select name="role" id="edit_role" required
                            class="w-full rounded-lg border border-slate-600 bg-slate-700 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <option value="cashier">Cashier</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('modalEditUser')"
                        class="rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function editUser(id, username, nama_pegawai, role) {
        const form = document.getElementById('formEditUser');
        form.action = `/users/${id}`;
        
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_nama_pegawai').value = nama_pegawai;
        document.getElementById('edit_role').value = role;
        
        openModal('modalEditUser');
    }
</script>
@endpush
