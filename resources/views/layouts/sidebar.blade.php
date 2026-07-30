<!-- Admin Panel Unified Sidebar Component -->
<aside id="main-sidebar" class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between p-4 shrink-0 h-screen transition-all duration-300 relative group/sidebar">
    <script>
        if (localStorage.getItem('omnidesk_sidebar_collapsed') === 'true') {
            document.getElementById('main-sidebar').classList.add('sidebar-collapsed');
        }
    </script>

    <div>
        <!-- Brand Header & Toggle Button -->
        <div class="brand-container flex items-center justify-between pb-4 mb-6 border-b border-slate-800 transition-all duration-300">
            <a href="{{ route('home') }}" title="OmniDesk Home" class="flex items-center gap-3 overflow-hidden group">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <x-icon name="messages" class="text-2xl text-white shrink-0" />
                </div>
                <div class="sidebar-text overflow-hidden transition-all duration-300">
                    <h1 class="font-bold text-lg leading-tight tracking-tight text-white whitespace-nowrap">OmniDesk</h1>
                    <span class="text-xs text-indigo-400 font-medium whitespace-nowrap">Omnichannel Platform</span>
                </div>
            </a>

            <!-- Collapse / Expand Toggle Button -->
            <button type="button" id="sidebar-toggle-btn"
                title="Collapse Sidebar"
                aria-label="Toggle Sidebar"
                class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-200 focus:outline-none shrink-0 cursor-pointer">
                <x-icon id="sidebar-toggle-icon" name="chevrons-left" class="text-xl transition-transform duration-300" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1.5">
            <a href="{{ route('dashboard') }}" title="Dashboard"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition text-left {{ request()->routeIs('dashboard') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <x-icon name="layout-dashboard" class="text-xl shrink-0" />
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('tickets') }}" id="nav-workspace" title="All Tickets"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition text-left cursor-pointer {{ request()->routeIs('tickets') ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <x-icon name="ticket" class="text-xl shrink-0" />
                <span class="sidebar-text">All Tickets</span>
                <span id="open-ticket-count"
                    class="sidebar-text ml-auto bg-indigo-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ \App\Models\Ticket::where('status', 'open')->count() }}</span>
            </a>

            <button type="button" id="nav-widget-config" title="Widget Builder"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition text-left cursor-pointer">
                <x-icon name="adjustments-horizontal" class="text-xl shrink-0" />
                <span class="sidebar-text">Widget Builder</span>
                <span
                    class="sidebar-text ml-auto text-[10px] bg-emerald-500/20 text-emerald-400 font-bold px-1.5 py-0.5 rounded border border-emerald-500/30">NEW</span>
            </button>

            <a href="{{ route('demo') }}" target="_blank" title="Live Client Demo"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition text-left">
                <x-icon name="eye" class="text-xl text-cyan-400 shrink-0" />
                <span class="sidebar-text">Live Client Demo</span>
            </a>

            <a href="{{ url('/docs/README.md') }}" target="_blank" title="Documentation"
                class="nav-link-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition text-left">
                <x-icon name="book" class="text-xl shrink-0" />
                <span class="sidebar-text">Documentation</span>
            </a>
        </nav>
    </div>

    <!-- Active Agent Card & Logout -->
    <div class="user-card-container glass-panel p-3 rounded-xl flex items-center justify-between gap-3 transition-all duration-300">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="relative shrink-0" title="{{ auth()->user()->name ?? 'Sarah Connor' }}">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode(auth()->user()->name ?? 'Sarah') }}" alt="Agent Avatar"
                    class="w-10 h-10 rounded-full border border-indigo-500/50">
                <span
                    class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-slate-900"></span>
            </div>
            <div class="sidebar-text overflow-hidden">
                <div class="font-semibold text-sm truncate text-slate-200">{{ auth()->user()->name ?? 'Sarah Connor' }}</div>
                <div class="text-xs text-emerald-400 font-medium">Online ({{ ucfirst(auth()->user()->role ?? 'Agent') }})</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="shrink-0">
            @csrf
            <button type="submit" title="Log Out" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition cursor-pointer">
                <x-icon name="logout" class="text-xl" />
            </button>
        </form>
    </div>
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
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            initSidebar();
        }
    })();
</script>
