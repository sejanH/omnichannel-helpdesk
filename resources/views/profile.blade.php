@extends('layouts.app')

@section('title', 'My Profile & Account Settings — OmniDesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-950 p-6 md:p-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
                <x-icon name="user-cog" class="text-indigo-400" />
                <span>My Profile & Preferences</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage your active agent status, account details, and security credentials.</p>
        </div>

        <a href="{{ route('agents.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition">
            <x-icon name="users-group" class="text-sm" />
            <span>View Agent Roster</span>
        </a>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <x-icon name="circle-check" class="text-base" />
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><x-icon name="x" class="text-sm" /></button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-semibold space-y-1 shadow-lg">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <x-icon name="alert-triangle" class="text-base" />
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: User Card & Status Setter -->
        <div class="space-y-6">

            <!-- Profile Summary Card -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl text-center relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl"></div>
                
                <div class="relative inline-block mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($user->name) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-4 border-slate-800 shadow-2xl mx-auto">
                    <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-2 border-slate-900 shadow-md {{ $user->status === 'online' ? 'bg-emerald-500 animate-pulse' : ($user->status === 'away' ? 'bg-amber-500' : ($user->status === 'busy' ? 'bg-rose-500' : 'bg-slate-500')) }}"></span>
                </div>

                <h2 class="text-lg font-bold text-white">{{ $user->name }}</h2>
                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>

                <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $user->role === 'admin' ? 'bg-purple-500/15 text-purple-300 border-purple-500/30' : ($user->role === 'supervisor' ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30' : 'bg-slate-800 text-slate-300 border-slate-700') }}">
                    <x-icon name="shield-check" class="text-sm" />
                    <span>{{ ucfirst($user->role) }}</span>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 grid grid-cols-2 gap-4 text-left text-xs">
                    <div>
                        <div class="text-slate-400 text-[11px]">Member Since</div>
                        <div class="font-semibold text-slate-200 mt-0.5">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Current Status</div>
                        <div class="font-bold capitalize mt-0.5 {{ $user->status === 'online' ? 'text-emerald-400' : ($user->status === 'away' ? 'text-amber-400' : ($user->status === 'busy' ? 'text-rose-400' : 'text-slate-400')) }}">
                            {{ $user->status ?? 'offline' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Set Availability Status Card -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-sm text-slate-100 flex items-center gap-2">
                        <x-icon name="circle-dot" class="text-emerald-400" />
                        <span>Set Availability Status</span>
                    </h3>
                </div>

                <p class="text-xs text-slate-400 mb-4">Your status determines ticket routing and team visibility on the support roster.</p>

                <form action="{{ route('profile.status') }}" method="POST" class="space-y-2.5">
                    @csrf

                    <!-- Status Option: Online -->
                    <button type="submit" name="status" value="online" class="w-full flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer text-left {{ $user->status === 'online' ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold">Online & Ready</div>
                                <div class="text-[10px] text-slate-400">Available to accept incoming support tickets</div>
                            </div>
                        </div>
                        @if($user->status === 'online')
                            <x-icon name="check" class="text-emerald-400 text-base shrink-0" />
                        @endif
                    </button>

                    <!-- Status Option: Away -->
                    <button type="submit" name="status" value="away" class="w-full flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer text-left {{ $user->status === 'away' ? 'bg-amber-500/10 border-amber-500/40 text-amber-300 ring-1 ring-amber-500/30' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold">Away</div>
                                <div class="text-[10px] text-slate-400">Temporarily away from desk / on break</div>
                            </div>
                        </div>
                        @if($user->status === 'away')
                            <x-icon name="check" class="text-amber-400 text-base shrink-0" />
                        @endif
                    </button>

                    <!-- Status Option: Busy -->
                    <button type="submit" name="status" value="busy" class="w-full flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer text-left {{ $user->status === 'busy' ? 'bg-rose-500/10 border-rose-500/40 text-rose-300 ring-1 ring-rose-500/30' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold">Busy / Do Not Disturb</div>
                                <div class="text-[10px] text-slate-400">In a meeting or handling high-priority tasks</div>
                            </div>
                        </div>
                        @if($user->status === 'busy')
                            <x-icon name="check" class="text-rose-400 text-base shrink-0" />
                        @endif
                    </button>

                    <!-- Status Option: Offline -->
                    <button type="submit" name="status" value="offline" class="w-full flex items-center justify-between p-3 rounded-2xl border transition cursor-pointer text-left {{ $user->status === 'offline' ? 'bg-slate-800/80 border-slate-700 text-slate-200 ring-1 ring-slate-700' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-slate-500 shrink-0"></span>
                            <div>
                                <div class="text-xs font-bold">Offline</div>
                                <div class="text-[10px] text-slate-400">Shift ended or off duty</div>
                            </div>
                        </div>
                        @if($user->status === 'offline')
                            <x-icon name="check" class="text-slate-300 text-base shrink-0" />
                        @endif
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Personal Info & Password Settings -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Edit Profile Information -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-sm text-slate-100 flex items-center gap-2">
                        <x-icon name="id" class="text-indigo-400" />
                        <span>Personal Profile Information</span>
                    </h3>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Display Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Account Role</label>
                            <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-400 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Avatar Seed</label>
                            <input type="text" value="Auto-generated (Dicebear)" disabled class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-400 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                            <x-icon name="device-floppy" class="text-sm" />
                            <span>Save Profile Changes</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card: Security & Change Password -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 bg-slate-900/60 shadow-xl">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-800">
                    <h3 class="font-bold text-sm text-slate-100 flex items-center gap-2">
                        <x-icon name="lock" class="text-indigo-400" />
                        <span>Security & Change Password</span>
                    </h3>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Current Password *</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-indigo-500 transition">
                            <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200">
                                <x-icon name="eye" class="text-sm" />
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">New Password *</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required placeholder="Minimum 6 characters" class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-indigo-500 transition">
                                <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200">
                                    <x-icon name="eye" class="text-sm" />
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Confirm New Password *</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat new password" class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-indigo-500 transition">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200">
                                    <x-icon name="eye" class="text-sm" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800/80 text-[11px] text-slate-400 flex items-start gap-2.5">
                        <x-icon name="shield-lock" class="text-indigo-400 text-base shrink-0 mt-0.5" />
                        <div>
                            <span class="font-semibold text-slate-300">Password Requirements:</span>
                            <ul class="list-disc list-inside mt-1 space-y-0.5 text-[10px]">
                                <li>Must be at least 6 characters long</li>
                                <li>Avoid reusing recent passwords</li>
                                <li>Include numbers or symbols for enhanced account safety</li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                            <x-icon name="key" class="text-sm" />
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            btn.querySelector('i').className = 'ti ti-eye-off text-sm';
        } else {
            input.type = 'password';
            btn.querySelector('i').className = 'ti ti-eye text-sm';
        }
    }
</script>
@endsection
