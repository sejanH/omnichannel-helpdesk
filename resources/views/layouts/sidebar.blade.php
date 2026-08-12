<!-- Admin Panel Unified Sidebar Component -->
<aside id="main-sidebar" class="fixed md:relative z-50 -translate-x-full md:translate-x-0 w-64 bg-white border-r border-slate-200 flex flex-col justify-between p-4 shrink-0 h-screen transition-transform duration-300 shadow-xl md:shadow-xs">
    <script>
        if (localStorage.getItem('omnidesk_sidebar_collapsed') === 'true') {
            document.getElementById('main-sidebar').classList.add('sidebar-collapsed');
        }
    </script>

    <div>
        <!-- Brand Header & Toggle Button -->
        <div class="brand-container flex items-center justify-between pb-4 mb-6 border-b border-slate-100 transition-all duration-300">
            <a href="{{ route('home') }}" title="OmniHelp Home" class="flex items-center gap-3 overflow-hidden group">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <x-icon name="messages" class="text-2xl text-white shrink-0" />
                </div>
                <div class="sidebar-text overflow-hidden transition-all duration-300">
                    <h1 class="font-bold text-lg leading-tight tracking-tight text-slate-900 whitespace-nowrap">OmniHelp</h1>
                    <span class="text-xs text-indigo-600 font-semibold whitespace-nowrap">Omnichannel Platform</span>
                </div>
            </a>

            <!-- Collapse / Expand Toggle Button -->
            <button type="button" id="sidebar-toggle-btn"
                title="Collapse Sidebar"
                aria-label="Toggle Sidebar"
                class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all duration-200 focus:outline-none shrink-0 cursor-pointer">
                <x-icon id="sidebar-toggle-icon" name="chevrons-left" class="text-xl transition-transform duration-300" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" title="Dashboard"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left {{ request()->routeIs('dashboard') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="layout-dashboard" class="text-lg shrink-0" />
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('tickets') }}" id="nav-workspace" title="All Tickets"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('tickets') && request()->query('filter') !== 'mine' ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="ticket" class="text-lg shrink-0" />
                <span class="sidebar-text">All Tickets</span>
                <span id="open-ticket-count"
                    class="sidebar-text ml-auto bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-bold border border-indigo-200">{{ \App\Models\Ticket::where('status', 'open')->count() }}</span>
            </a>

            <a href="{{ route('tickets') }}?filter=mine" id="nav-my-tickets" title="My Assigned Tickets"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->query('filter') === 'mine' ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="user-check" class="text-lg shrink-0 text-cyan-600" />
                <span class="sidebar-text">My Tickets</span>
                <span id="my-ticket-count"
                    class="sidebar-text ml-auto bg-cyan-50 text-cyan-700 text-xs px-2 py-0.5 rounded-full font-bold border border-cyan-200">{{ \App\Models\Ticket::where('assigned_agent_id', auth()->id())->whereIn('status', ['open', 'in_progress', 'pending'])->count() }}</span>
            </a>

            <a href="{{ route('agents.index') }}" title="Agent Roster"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('agents.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="users-group" class="text-lg shrink-0" />
                <span class="sidebar-text">Agent Roster</span>
            </a>

            @if(auth()->check() && auth()->user()->hasPermissionTo('view-reports'))
            <a href="{{ route('reports.index') }}" title="Reports & Analytics"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('reports.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="chart-bar" class="text-lg shrink-0 text-amber-600" />
                <span class="sidebar-text">Analytics & Reports</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermissionTo('manage-canned-responses'))
            <a href="{{ route('canned-responses.index') }}" title="Auto-Responder Templates"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('canned-responses.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="messages" class="text-lg shrink-0 text-purple-600" />
                <span class="sidebar-text">Canned Responses</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermissionTo('manage-roles'))
            <a href="{{ route('roles.index') }}" title="Roles & Access"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('roles.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="shield-check" class="text-lg shrink-0 text-rose-600" />
                <span class="sidebar-text">Roles & Access</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('channels.index') }}" title="Omnichannel Integrations & Channels"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('channels.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="brand-whatsapp" class="text-lg shrink-0 text-emerald-600" />
                <span class="sidebar-text">Channel Integrations</span>
            </a>

            <a href="{{ route('widget-builder.index') }}" title="Widget Builder Studio"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('widget-builder.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="adjustments-horizontal" class="text-lg shrink-0" />
                <span class="sidebar-text">Widget Builder</span>
                <span
                    class="sidebar-text ml-auto text-[10px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded border border-emerald-200">NEW</span>
            </a>

            <a href="{{ route('billing.index') }}" title="Subscription & Billing"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('billing.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="credit-card" class="text-lg shrink-0 text-emerald-600" />
                <span class="sidebar-text">Billing & Plan</span>
                @if(auth()->user()->onTrial())
                    <span class="sidebar-text ml-auto text-[10px] bg-amber-100 text-amber-700 font-bold px-1.5 py-0.5 rounded border border-amber-200">TRIAL</span>
                @endif
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermissionTo('delete-tickets'))
            <a href="{{ route('tickets') }}?filter=trash" title="Ticket Trash Bin"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->query('filter') === 'trash' ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="trash" class="text-lg shrink-0 text-rose-600" />
                <span class="sidebar-text">Trash Bin</span>
                @php $trashedCount = \App\Models\Ticket::onlyTrashed()->count(); @endphp
                @if($trashedCount > 0)
                    <span class="sidebar-text ml-auto bg-rose-100 text-rose-700 text-xs px-2 py-0.5 rounded-full font-bold border border-rose-200">{{ $trashedCount }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('kb.index') }}" title="Knowledge Base & FAQ"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs transition text-left cursor-pointer {{ request()->routeIs('kb.*') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <x-icon name="help" class="text-lg shrink-0 text-cyan-600" />
                <span class="sidebar-text">Knowledge Base</span>
            </a>

            <a href="{{ route('demo') }}" target="_blank" title="Live Client Demo"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition text-left">
                <x-icon name="eye" class="text-lg text-cyan-600 shrink-0" />
                <span class="sidebar-text">Live Client Demo</span>
            </a>

            <a href="{{ route('docs.show') }}" title="Documentation"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition text-left">
                <x-icon name="book" class="text-lg shrink-0" />
                <span class="sidebar-text">Documentation</span>
            </a>
        </nav>
    </div>

    <!-- Active Agent Card & Logout -->
    @auth
    @php
        $currentStatus = auth()->user()->status ?? 'online';
        $statusDotClass = match($currentStatus) {
            'online' => 'bg-emerald-500',
            'away' => 'bg-amber-500',
            'busy' => 'bg-rose-500',
            default => 'bg-slate-400',
        };
        $statusTextClass = match($currentStatus) {
            'online' => 'text-emerald-700',
            'away' => 'text-amber-700',
            'busy' => 'text-rose-700',
            default => 'text-slate-500',
        };
    @endphp
    <div class="user-card-container bg-slate-50 border border-slate-200/80 p-3 rounded-2xl space-y-2.5 transition-all duration-300 relative group/usercard shadow-xs mt-auto">
        <!-- Row 1: Agent Avatar, Full Name & Role -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 group/userinfo" title="Manage Profile & Status">
            <div class="relative shrink-0">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode(auth()->user()->name) }}" alt="Agent Avatar"
                    class="w-9 h-9 rounded-full border border-slate-300 group-hover/userinfo:border-indigo-500 transition">
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 {{ $statusDotClass }} rounded-full border-2 border-white shadow-xs"></span>
            </div>
            <div class="sidebar-text overflow-hidden min-w-0 flex-1">
                <div class="font-extrabold text-xs text-slate-900 truncate group-hover/userinfo:text-indigo-600 transition">{{ auth()->user()->name }}</div>
                <div class="text-[10px] font-bold flex items-center gap-1 {{ $statusTextClass }} mt-0.5">
                    <span class="capitalize shrink-0">{{ $currentStatus }}</span>
                    <span class="text-slate-300 shrink-0">•</span>
                    <span class="text-slate-500 uppercase font-extrabold truncate">{{ auth()->user()->role }}</span>
                </div>
            </div>
        </a>

        <!-- Row 2: Notification Sound Toggle, Profile Settings & Logout -->
        <div class="sidebar-text flex items-center justify-between pt-2 border-t border-slate-200/60">
            <button type="button" id="btn-toggle-sound" title="Toggle Notification Sounds" class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-white border border-slate-200/80 text-slate-600 hover:bg-slate-100 transition cursor-pointer text-xs font-semibold shadow-2xs">
                <x-icon name="volume" id="sound-icon-on" class="text-sm text-emerald-600 shrink-0" />
                <x-icon name="volume-off" id="sound-icon-off" class="text-sm text-rose-600 hidden shrink-0" />
                <span id="sound-status-text" class="sound-status-text text-[11px] font-extrabold text-emerald-600">ON</span>
            </button>

            <div class="flex items-center gap-1">
                <a href="{{ route('profile.edit') }}" title="Profile & Password Settings" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-white transition">
                    <x-icon name="settings" class="text-base" />
                </a>
                <form action="{{ route('logout') }}" method="POST" class="shrink-0 inline">
                    @csrf
                    <button type="submit" title="Log Out" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-white transition cursor-pointer">
                        <x-icon name="logout" class="text-base" />
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth
</aside>

<script>
    (function () {
        const STORAGE_KEY = 'omnidesk_sidebar_collapsed';

        function initSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            if (!sidebar || !toggleBtn) return;

            // Apply saved state
            const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
                toggleBtn.setAttribute('title', 'Expand Sidebar');
                toggleBtn.setAttribute('aria-expanded', 'false');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                toggleBtn.setAttribute('title', 'Collapse Sidebar');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }

            // Click listener
            toggleBtn.onclick = function () {
                const currentlyCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem(STORAGE_KEY, currentlyCollapsed ? 'true' : 'false');

                if (currentlyCollapsed) {
                    toggleBtn.setAttribute('title', 'Expand Sidebar');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                } else {
                    toggleBtn.setAttribute('title', 'Collapse Sidebar');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
            };

            // Mobile Menu Listeners
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const backdrop = document.getElementById('sidebar-backdrop');

            function openMobileSidebar() {
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.remove('hidden');
            }

            function closeMobileSidebar() {
                sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }

            if (mobileBtn) mobileBtn.addEventListener('click', openMobileSidebar);
            if (backdrop) backdrop.addEventListener('click', closeMobileSidebar);

            // Close mobile sidebar when clicking any navigation link
            document.querySelectorAll('#main-sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeMobileSidebar();
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            initSidebar();
        }
    })();
</script>
