@extends('layouts.app')

@section('title', 'Agent & Team Management — OmniDesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i class="ti ti-users-group text-indigo-600"></i>
                <span>Agent & CRM Team Management</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Manage support agents, roles, and instantly deactivate departing staff accounts.</p>
        </div>

        @if(auth()->user()->role === 'admin')
            <button onclick="openCreateAgentModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <i class="ti ti-user-plus text-base"></i>
                <span>Add New Agent</span>
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

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 mb-1">Total Team Members</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $agents->count() }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 mb-1">Active Online</div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ $agents->where('status', 'online')->count() }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 mb-1">Supervisors / Admins</div>
            <div class="text-2xl font-extrabold text-indigo-600">{{ $agents->whereIn('role', ['admin', 'supervisor'])->count() }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 mb-1">Deactivated Accounts</div>
            <div class="text-2xl font-extrabold text-rose-600">{{ $agents->whereIn('status', ['disabled', 'inactive'])->count() }}</div>
        </div>
    </div>

    <!-- Agent Roster Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50">
            <div class="font-bold text-sm text-slate-900">Support Roster ({{ $agents->count() }})</div>
            
            <form action="{{ route('agents.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-indigo-500">
                <select name="status" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="away" {{ request('status') === 'away' ? 'selected' : '' }}>Away</option>
                    <option value="busy" {{ request('status') === 'busy' ? 'selected' : '' }}>Busy / DND</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Deactivated / Disabled</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Agent Name</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Assigned Tickets</th>
                        <th class="py-3.5 px-4">Joined Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-slate-50/80 transition {{ auth()->id() === $agent->id ? 'bg-indigo-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($agent->name) }}" alt="Avatar" class="w-9 h-9 rounded-full border border-slate-200 shadow-xs">
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-2">
                                            <span>{{ $agent->name }}</span>
                                            @if(auth()->id() === $agent->id)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">You</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">{{ $agent->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide border {{ $agent->role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : ($agent->role === 'supervisor' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
                                    {{ $roles->where('slug', $agent->role)->first()->name ?? $agent->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($agent->status === 'disabled' || $agent->status === 'inactive')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>Deactivated (Sessions Cleared)</span>
                                    </span>
                                @elseif($agent->status === 'online')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Online Active</span>
                                    </span>
                                @elseif($agent->status === 'away')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span>Away</span>
                                    </span>
                                @elseif($agent->status === 'busy')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>Busy / DND</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        <span>Offline</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-800">
                                {{ $agent->assignedTickets->count() }} tickets
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $agent->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(auth()->id() === $agent->id)
                                        <a href="{{ route('profile.edit') }}" title="Edit My Profile & Status" class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 transition flex items-center gap-1">
                                            <i class="ti ti-user-cog"></i> Profile & Status
                                        </a>
                                    @elseif(auth()->user()->role === 'admin')
                                        <!-- Edit Agent Profile & Role (Admin Only) -->
                                        <button type="button" onclick="openEditAgentModal({{ json_encode(['id' => $agent->id, 'name' => $agent->name, 'email' => $agent->email, 'role' => $agent->role]) }})" title="Edit Agent Profile" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white border border-slate-200 hover:border-indigo-500 transition flex items-center gap-1 cursor-pointer">
                                            <i class="ti ti-edit text-xs"></i> Edit
                                        </button>

                                        <!-- Toggle Account Status / Instant Session Invalidation -->
                                        <form action="{{ route('agents.toggle', $agent) }}" method="POST">
                                            @csrf
                                            @if($agent->status === 'disabled' || $agent->status === 'inactive')
                                                <button type="submit" title="Reactivate Account" class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 transition cursor-pointer">
                                                    <i class="ti ti-user-check mr-1"></i> Reactivate
                                                </button>
                                            @else
                                                <button type="submit" onclick="return confirm('Are you sure you want to deactivate {{ $agent->name }}? All active sessions and remember-me tokens will be invalidated instantly!')" title="Deactivate & Invalidate Session" class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 transition cursor-pointer">
                                                    <i class="ti ti-user-off mr-1"></i> Deactivate
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Delete Agent Account -->
                                        <form action="{{ route('agents.destroy', $agent) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $agent->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition cursor-pointer">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Read-only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-xs">No agents found matching your query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<!-- Modal: Create New Agent (Admin Only) -->
<div id="create-agent-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i class="ti ti-user-plus text-indigo-600"></i>
                <span>Add New Agent / CRM Member</span>
            </h3>
            <button onclick="closeCreateAgentModal()" class="text-slate-400 hover:text-slate-600"><i class="ti ti-x text-lg"></i></button>
        </div>

        <form action="{{ route('agents.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Sarah Jenkins" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Work Email Address *</label>
                <input type="email" name="email" required placeholder="sarah@company.com" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role *</label>
                    <select name="role" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->slug }}">{{ $roleOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeCreateAgentModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Create Agent</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Agent Profile & Role (Admin Only) -->
<div id="edit-agent-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i class="ti ti-user-edit text-indigo-600"></i>
                <span>Edit Agent Profile & Role</span>
            </h3>
            <button onclick="closeEditAgentModal()" class="text-slate-400 hover:text-slate-600"><i class="ti ti-x text-lg"></i></button>
        </div>

        <form id="edit-agent-form" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                <input type="text" id="edit-agent-name" name="name" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Work Email Address *</label>
                <input type="email" id="edit-agent-email" name="email" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role *</label>
                    <select id="edit-agent-role" name="role" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->slug }}">{{ $roleOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditAgentModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openCreateAgentModal() {
        document.getElementById('create-agent-modal').classList.remove('hidden');
    }
    function closeCreateAgentModal() {
        document.getElementById('create-agent-modal').classList.add('hidden');
    }

    function openEditAgentModal(agent) {
        document.getElementById('edit-agent-form').action = '/agents/' + agent.id;
        document.getElementById('edit-agent-name').value = agent.name;
        document.getElementById('edit-agent-email').value = agent.email;
        document.getElementById('edit-agent-role').value = agent.role;
        document.getElementById('edit-agent-modal').classList.remove('hidden');
    }
    function closeEditAgentModal() {
        document.getElementById('edit-agent-modal').classList.add('hidden');
    }
</script>
@endsection
