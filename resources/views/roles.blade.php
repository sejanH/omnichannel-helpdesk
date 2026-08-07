@extends('layouts.app')

@section('title', 'Roles & Access Management — OmniDesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i class="ti ti-shield-check text-indigo-600"></i>
                <span>Roles & Access</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Manage custom roles for your agents and team members.</p>
        </div>

        @if(auth()->user()->role === 'admin')
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <i class="ti ti-plus text-base"></i>
                <span>Create New Role</span>
            </button>
        @endif
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-base text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="ti ti-x"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="ti ti-alert-triangle text-base text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Roles Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
            <div class="font-bold text-sm text-slate-900">Configured Roles ({{ $roles->count() }})</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 w-1/3">Role Name</th>
                        <th class="py-3.5 px-4 w-1/3">System Slug</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $role->name }}
                                @if(in_array($role->slug, ['admin', 'supervisor', 'agent']))
                                    <span class="ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200 uppercase">Core</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-600 font-mono text-[10px]">
                                    {{ $role->slug }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openEditModal({{ $role->id }}, '{{ addslashes($role->name) }}', '{{ $role->slug }}', {{ $role->permissions->pluck('id')->toJson() }})" title="Edit Role" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer">
                                        <i class="ti ti-edit text-lg"></i>
                                    </button>
                                    @if(!in_array($role->slug, ['admin', 'supervisor', 'agent']))
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this role? Agents with this role might lose access.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete Role" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                            <i class="ti ti-trash text-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500 font-medium">
                                No roles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create Role -->
<div id="create-modal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
    <div class="relative bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 mx-4">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-900">Create Custom Role</h3>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role Name</label>
                    <input type="text" name="name" required placeholder="e.g. Content Manager" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    <p class="text-[10px] text-slate-400 mt-1">A system slug will be generated automatically.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Permissions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span>{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">Create Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Role -->
<div id="edit-modal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 mx-4">
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-900">Edit Role</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role Name</label>
                    <input type="text" name="name" id="edit-name" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
                <div id="edit-warning" class="hidden text-[10px] text-amber-600 bg-amber-50 p-2 rounded">
                    Warning: Changing a core system role's name is not recommended and might cause confusion.
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Permissions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="edit-permission-checkbox rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span>{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
        document.getElementById('create-modal').classList.add('flex');
    }

    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
        document.getElementById('create-modal').classList.remove('flex');
    }

    function openEditModal(id, name, slug, permissionsArray = []) {
        const form = document.getElementById('edit-form');
        form.action = `/roles/${id}`;
        
        document.getElementById('edit-name').value = name;
        
        if (['admin', 'supervisor', 'agent'].includes(slug)) {
            document.getElementById('edit-warning').classList.remove('hidden');
        } else {
            document.getElementById('edit-warning').classList.add('hidden');
        }

        document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
            cb.checked = permissionsArray.includes(parseInt(cb.value));
        });

        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-modal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.getElementById('edit-modal').classList.remove('flex');
    }
</script>
@endsection
