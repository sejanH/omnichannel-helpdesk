@extends('layouts.app')

@section('title', 'Multi-Widget Builder Studio — OmniDesk')

@section('content')
@php
    $config = array_merge([
        'widget_color' => '#4f46e5',
        'position' => 'bottom-right',
        'title' => 'Customer Support',
        'subtitle' => 'We typically reply in under 5 minutes',
        'welcome_message' => 'Hello! How can we assist you today?',
        'logo_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk',
        'theme' => 'light',
        'launcher_icon' => 'message-dots',
        'require_prechat' => false,
    ], $activeChannel->configuration ?? []);
@endphp

<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <x-icon name="adjustments-horizontal" class="text-indigo-600" />
                <span>Multi-Widget Builder Studio</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Configure and manage live support widgets across multiple websites, apps, and brands.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Active Widget Selector -->
            <form action="{{ route('widget-builder.index') }}" method="GET" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-600">Selected Widget:</label>
                <select name="channel_id" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-semibold shadow-xs">
                    @foreach($channels as $chan)
                        <option value="{{ $chan->id }}" {{ $activeChannel->id === $chan->id ? 'selected' : '' }}>
                            {{ $chan->name }} (ID: #{{ $chan->id }})
                        </option>
                    @endforeach
                </select>
            </form>

            <button onclick="openCreateWidgetModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <x-icon name="plus" class="text-sm" />
                <span>Add New Widget</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <x-icon name="circle-check" class="text-base text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><x-icon name="x" class="text-sm" /></button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <x-icon name="alert-triangle" class="text-base text-rose-600" />
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Studio Grid: Controls & Live Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left Column: Customization Controls (7 Columns) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                            <x-icon name="palette" class="text-indigo-600" />
                            <span>Widget Customization — {{ $activeChannel->name }}</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Customize styling, text strings, and visitor pre-chat forms.</p>
                    </div>
                    
                    @if($channels->count() > 1)
                        <form action="{{ route('widget-builder.destroy', $activeChannel) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the widget \'{{ $activeChannel->name }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 hover:text-rose-700 border border-rose-200 transition cursor-pointer text-xs font-semibold flex items-center gap-1.5" title="Delete Widget">
                                <x-icon name="trash" class="text-sm" />
                                <span>Delete</span>
                            </button>
                        </form>
                    @endif
                </div>

                <form action="{{ route('widget-builder.update', $activeChannel) }}" method="POST" class="space-y-5" id="widget-form">
                    @csrf
                    @method('PUT')

                    <!-- Widget Name & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Widget Name / Label *</label>
                            <input type="text" name="name" value="{{ old('name', $activeChannel->name) }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Widget Status</label>
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $activeChannel->is_active ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold {{ $activeChannel->is_active ? 'text-emerald-700' : 'text-slate-500' }}">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Brand Color & Preset Palette -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Widget Brand Primary Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="cfg-color" name="widget_color" value="{{ old('widget_color', $config['widget_color']) }}" class="w-10 h-10 rounded-xl cursor-pointer bg-white border border-slate-300 p-1 shadow-2xs">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="setPresetColor('#4f46e5')" class="w-7 h-7 rounded-full bg-[#4f46e5] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Indigo"></button>
                                <button type="button" onclick="setPresetColor('#10b981')" class="w-7 h-7 rounded-full bg-[#10b981] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Emerald"></button>
                                <button type="button" onclick="setPresetColor('#ef4444')" class="w-7 h-7 rounded-full bg-[#ef4444] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Rose"></button>
                                <button type="button" onclick="setPresetColor('#f59e0b')" class="w-7 h-7 rounded-full bg-[#f59e0b] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Amber"></button>
                                <button type="button" onclick="setPresetColor('#0891b2')" class="w-7 h-7 rounded-full bg-[#0891b2] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Cyan"></button>
                                <button type="button" onclick="setPresetColor('#8b5cf6')" class="w-7 h-7 rounded-full bg-[#8b5cf6] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Purple"></button>
                                <button type="button" onclick="setPresetColor('#475569')" class="w-7 h-7 rounded-full bg-[#475569] ring-2 ring-slate-200 hover:scale-110 transition cursor-pointer" title="Slate"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Launcher Icon & Theme -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Launcher Icon</label>
                            <select id="cfg-launcher-icon" name="launcher_icon" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 shadow-2xs">
                                <option value="message-dots" {{ $config['launcher_icon'] === 'message-dots' ? 'selected' : '' }}>Message Dots (Default)</option>
                                <option value="chat" {{ $config['launcher_icon'] === 'chat' ? 'selected' : '' }}>Chat Bubble</option>
                                <option value="sparkles" {{ $config['launcher_icon'] === 'sparkles' ? 'selected' : '' }}>AI Sparkles</option>
                                <option value="help" {{ $config['launcher_icon'] === 'help' ? 'selected' : '' }}>Help Badge</option>
                                <option value="message" {{ $config['launcher_icon'] === 'message' ? 'selected' : '' }}>Message Envelope</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Widget Theme</label>
                            <select id="cfg-theme" name="theme" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 shadow-2xs">
                                <option value="light" {{ $config['theme'] === 'light' ? 'selected' : '' }}>Light Theme (Clean White)</option>
                                <option value="dark" {{ $config['theme'] === 'dark' ? 'selected' : '' }}>Dark Theme (Sleek Modern)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Position Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Website Floating Position</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="bottom-right" {{ $config['position'] === 'bottom-right' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-900">Bottom Right</div>
                                    <div class="text-[10px] text-slate-500">Standard bottom right</div>
                                </div>
                            </label>
                            <label class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="bottom-left" {{ $config['position'] === 'bottom-left' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-900">Bottom Left</div>
                                    <div class="text-[10px] text-slate-500">Bottom left edge</div>
                                </div>
                            </label>
                            <label class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="top-right" {{ $config['position'] === 'top-right' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-900">Top Right</div>
                                    <div class="text-[10px] text-slate-500">Header top right</div>
                                </div>
                            </label>
                            <label class="pos-option flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-500 transition">
                                <input type="radio" name="position" value="top-left" {{ $config['position'] === 'top-left' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="text-xs font-semibold text-slate-900">Top Left</div>
                                    <div class="text-[10px] text-slate-500">Header top left</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Header Title & Subtitle -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Header Title</label>
                            <input type="text" id="cfg-title" name="title" value="{{ old('title', $config['title']) }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Header Subtitle</label>
                            <input type="text" id="cfg-subtitle" name="subtitle" value="{{ old('subtitle', $config['subtitle']) }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Welcome Greeting Message -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Welcome Greeting Message</label>
                        <input type="text" id="cfg-greeting" name="welcome_message" value="{{ old('welcome_message', $config['welcome_message']) }}" placeholder="e.g. Hi there! How can our support team help you today?" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition shadow-2xs">
                    </div>

                    <!-- Pre-chat Requirement Toggle -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-900">Require Pre-Chat Form</div>
                            <div class="text-[10px] text-slate-500">Ask visitor for name & email address before starting chat</div>
                        </div>
                        <input type="checkbox" name="require_prechat" value="1" {{ !empty($config['require_prechat']) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                            <x-icon name="device-floppy" class="text-sm" />
                            <span>Save Widget Settings</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Interactive Live Preview & Embed Code Snippet (5 Columns) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Live Preview Simulator -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <x-icon name="eye" class="text-cyan-600" />
                        <span>Real-Time Widget Preview</span>
                    </h3>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">LIVE SIMULATOR</span>
                </div>

                <!-- Preview Web Canvas Container -->
                <div id="preview-canvas" class="relative w-full h-[420px] bg-slate-100/70 border border-slate-200 rounded-2xl overflow-hidden p-4 flex flex-col justify-between">
                    <div class="text-center text-slate-400 text-xs mt-10">
                        <x-icon name="browser" class="text-4xl text-slate-300 mx-auto mb-2" />
                        <div class="font-semibold text-slate-600">Your Website Content Canvas</div>
                        <div class="text-[10px] text-slate-400">Preview changes dynamically as you edit the configurator</div>
                    </div>

                    <!-- Simulated Floating Chat Window -->
                    <div id="pv-widget-box" class="absolute bottom-16 right-4 w-72 h-[320px] bg-white border border-slate-200 rounded-2xl shadow-xl flex flex-col overflow-hidden transition-all duration-300">
                        <!-- Header -->
                        <div id="pv-header" class="p-3 text-white flex items-center justify-between" style="background-color: {{ $config['widget_color'] }};">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <div>
                                    <h4 id="pv-title" class="font-bold text-xs leading-tight">{{ $config['title'] }}</h4>
                                    <p id="pv-subtitle" class="text-[10px] opacity-80 leading-tight">{{ $config['subtitle'] }}</p>
                                </div>
                            </div>
                            <x-icon name="x" class="text-xs opacity-70" />
                        </div>

                        <!-- Chat Stream -->
                        <div class="flex-1 p-3 space-y-2 overflow-y-auto text-[11px] bg-slate-50">
                            <div class="bg-white p-2.5 rounded-xl text-slate-800 max-w-[85%] border border-slate-200 shadow-2xs">
                                <span id="pv-greeting">{{ $config['welcome_message'] ?: 'Hello! How can we assist you today?' }}</span>
                            </div>
                            <div class="bg-indigo-600 text-white p-2.5 rounded-xl ml-auto max-w-[85%] text-right shadow-xs">
                                Hi! I have a question about my support ticket.
                            </div>
                        </div>

                        <!-- Input Footer -->
                        <div class="p-2 border-t border-slate-200 bg-white flex items-center gap-1.5">
                            <input type="text" disabled placeholder="Type a message..." class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-[11px] text-slate-500">
                            <button disabled class="p-1.5 text-white rounded-lg text-xs" style="background-color: {{ $config['widget_color'] }};">
                                <x-icon name="send" class="text-xs" />
                            </button>
                        </div>
                    </div>

                    <!-- Simulated Trigger Button -->
                    <div id="pv-trigger" class="absolute bottom-4 right-4 w-11 h-11 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-105" style="background-color: {{ $config['widget_color'] }};">
                        <x-icon id="pv-trigger-icon" name="{{ $config['launcher_icon'] }}" class="text-xl" />
                    </div>
                </div>
            </div>

            <!-- Embed Snippet Code Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <x-icon name="code" class="text-indigo-600" />
                        <span>Embed Code Snippet</span>
                    </h3>
                    <button type="button" onclick="copyEmbedCode()" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow transition flex items-center gap-1.5 cursor-pointer">
                        <x-icon name="copy" class="text-sm" />
                        <span id="copy-btn-text">Copy Script</span>
                    </button>
                </div>

                <p class="text-xs text-slate-500 mb-3">Copy and paste this script tag into the <code>&lt;head&gt;</code> or before the closing <code>&lt;/body&gt;</code> tag of your website.</p>

                <div class="relative bg-slate-900 p-4 rounded-2xl border border-slate-800 font-mono text-[11px] text-cyan-300 overflow-x-auto select-all">
                    <code id="embed-code-text">&lt;!-- OmniDesk Live Chat Widget Embed --&gt;
&lt;script src="{{ url('/api/v1/widget/config') }}?channel_id={{ $activeChannel->id }}" async&gt;&lt;/script&gt;</code>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1">
                    <div class="flex items-center gap-2">
                        <x-icon name="circle-check" class="text-emerald-600 text-xs" />
                        <span>Unique Channel Token ID: <strong class="text-slate-800">#{{ $activeChannel->id }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="circle-check" class="text-emerald-600 text-xs" />
                        <span>Supports HTML, WordPress, Shopify, Next.js, and React websites.</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Create New Widget -->
<div id="create-widget-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <x-icon name="plus" class="text-indigo-600" />
                <span>Create New Web Chat Widget</span>
            </h3>
            <button onclick="closeCreateWidgetModal()" class="text-slate-400 hover:text-slate-600"><x-icon name="x" class="text-lg" /></button>
        </div>

        <form action="{{ route('widget-builder.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Widget / Website Name *</label>
                <input type="text" name="name" required placeholder="e.g. EU Store Widget or Mobile Web Chat" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Primary Color *</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="widget_color" value="#4f46e5" class="w-10 h-10 rounded-xl cursor-pointer bg-white border border-slate-300 p-1">
                    <span class="text-xs text-slate-500">Choose custom theme color</span>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeCreateWidgetModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Create Widget</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateWidgetModal() {
        document.getElementById('create-widget-modal').classList.remove('hidden');
    }
    function closeCreateWidgetModal() {
        document.getElementById('create-widget-modal').classList.add('hidden');
    }

    function setPresetColor(hex) {
        document.getElementById('cfg-color').value = hex;
        updateLivePreview();
    }

    function updateLivePreview() {
        const color = document.getElementById('cfg-color').value;
        const title = document.getElementById('cfg-title').value;
        const subtitle = document.getElementById('cfg-subtitle').value;
        const greeting = document.getElementById('cfg-greeting').value;
        const icon = document.getElementById('cfg-launcher-icon').value;

        // Apply color
        document.getElementById('pv-header').style.backgroundColor = color;
        document.getElementById('pv-trigger').style.backgroundColor = color;
        
        // Apply text
        document.getElementById('pv-title').textContent = title || 'Customer Support';
        document.getElementById('pv-subtitle').textContent = subtitle || 'We typically reply in under 5 minutes';
        document.getElementById('pv-greeting').textContent = greeting || 'Hello! How can we assist you today?';
        
        // Apply icon
        document.getElementById('pv-trigger-icon').className = 'ti ti-' + icon + ' text-xl';
    }

    // Attach real-time input listeners
    document.addEventListener('DOMContentLoaded', function() {
        ['cfg-color', 'cfg-title', 'cfg-subtitle', 'cfg-greeting', 'cfg-launcher-icon'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', updateLivePreview);
                el.addEventListener('change', updateLivePreview);
            }
        });
    });

    function copyEmbedCode() {
        const code = document.getElementById('embed-code-text').textContent;
        navigator.clipboard.writeText(code).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            btnText.textContent = 'Copied!';
            setTimeout(() => { btnText.textContent = 'Copy Script'; }, 2000);
        });
    }
</script>
@endsection
