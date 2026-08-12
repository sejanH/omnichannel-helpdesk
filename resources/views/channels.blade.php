@extends('layouts.app')

@section('title', 'Omnichannel Channel Configurations — OmniDesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <i class="ti ti-brand-whatsapp text-emerald-600"></i>
                <span>Omnichannel Integrations & Channels</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Configure Meta WhatsApp Business, Facebook Messenger, Telegram Bot, Email, and Web Live Chat credentials & webhooks.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('widget-builder.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-50 shadow-xs transition">
                <i class="ti ti-adjustments-horizontal text-indigo-600 text-base"></i>
                <span>Widget Studio</span>
            </a>
            <button onclick="openCreateChannelModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <i class="ti ti-plus text-base"></i>
                <span>Add Channel</span>
            </button>
        </div>
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

    <!-- Channel Type Status Metrics Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- WhatsApp -->
        @php $waChannel = $channels->firstWhere('type', 'whatsapp'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-brand-whatsapp"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">WhatsApp</div>
                    <div class="text-[10px] font-semibold {{ ($waChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($waChannel->is_active ?? false) ? '● Connected' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Facebook Messenger -->
        @php $fbChannel = $channels->firstWhere('type', 'facebook'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-brand-facebook"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Messenger</div>
                    <div class="text-[10px] font-semibold {{ ($fbChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($fbChannel->is_active ?? false) ? '● Connected' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Instagram Direct -->
        @php $igChannel = $channels->firstWhere('type', 'instagram'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-200 text-pink-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-brand-instagram"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Instagram</div>
                    <div class="text-[10px] font-semibold {{ ($igChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($igChannel->is_active ?? false) ? '● Connected' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Telegram Bot -->
        @php $tgChannel = $channels->firstWhere('type', 'telegram'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-brand-telegram"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Telegram</div>
                    <div class="text-[10px] font-semibold {{ ($tgChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($tgChannel->is_active ?? false) ? '● Connected' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Support -->
        @php $emailChannel = $channels->firstWhere('type', 'email'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-mail"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Email Desk</div>
                    <div class="text-[10px] font-semibold {{ ($emailChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($emailChannel->is_active ?? false) ? '● Connected' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Web Chat -->
        @php $webChannel = $channels->firstWhere('type', 'web_chat'); @endphp
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                    <i class="ti ti-messages"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Web Chat</div>
                    <div class="text-[10px] font-semibold {{ ($webChannel->is_active ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ ($webChannel->is_active ?? false) ? '● Active' : '○ Offline' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Channels List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($channels as $channel)
        @php
            $config = $channel->configuration ?? [];
            $iconClass = match($channel->type) {
                'whatsapp' => 'ti ti-brand-whatsapp text-emerald-600 bg-emerald-50 border-emerald-200',
                'facebook' => 'ti ti-brand-facebook text-blue-600 bg-blue-50 border-blue-200',
                'instagram' => 'ti ti-brand-instagram text-pink-600 bg-pink-50 border-pink-200',
                'telegram' => 'ti ti-brand-telegram text-sky-600 bg-sky-50 border-sky-200',
                'email' => 'ti ti-mail text-purple-600 bg-purple-50 border-purple-200',
                default => 'ti ti-messages text-indigo-600 bg-indigo-50 border-indigo-200',
            };
            $webhookUrl = match($channel->type) {
                'whatsapp' => url('/api/v1/webhooks/whatsapp'),
                'facebook' => url('/api/v1/webhooks/facebook'),
                'instagram' => url('/api/v1/webhooks/instagram'),
                'telegram' => url('/api/v1/webhooks/telegram'),
                default => null,
            };
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between relative group hover:border-indigo-300 transition">
            <div>
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center text-2xl shrink-0 shadow-2xs {{ $iconClass }}"></div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm leading-snug">{{ $channel->name }}</h3>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 mt-0.5">
                                {{ strtoupper($channel->type) }}
                            </span>
                        </div>
                    </div>

                    <!-- Active Toggle Form -->
                    <form action="{{ route('channels.toggle', $channel->id) }}" method="POST">
                        @csrf
                        <button type="submit" title="{{ $channel->is_active ? 'Disable Channel' : 'Enable Channel' }}" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $channel->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $channel->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </form>
                </div>

                <!-- Key Parameters Summary -->
                <div class="space-y-2 text-xs bg-slate-50 border border-slate-200/70 p-3.5 rounded-2xl mb-4 font-mono">
                    @if($channel->type === 'whatsapp')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Phone:</span>
                            <span class="font-bold text-slate-800">{{ $config['phone_number'] ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Phone ID:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[140px]">{{ $config['phone_number_id'] ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Verify Token:</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $config['verify_token'] ?? 'omnihelp_secret' }}</span>
                        </div>
                    @elseif($channel->type === 'facebook')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Page ID:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[140px]">{{ $config['page_id'] ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Verify Token:</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $config['verify_token'] ?? 'omnihelp_secret' }}</span>
                        </div>
                    @elseif($channel->type === 'instagram')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">IG Account ID:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[140px]">{{ $config['instagram_account_id'] ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Verify Token:</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $config['verify_token'] ?? 'omnihelp_secret' }}</span>
                        </div>
                    @elseif($channel->type === 'telegram')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Bot Handle:</span>
                            <span class="font-bold text-sky-700">{{ $config['bot_username'] ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Bot Token:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[140px]">{{ !empty($config['bot_token']) ? '••••' . substr($config['bot_token'], -6) : 'Not set' }}</span>
                        </div>
                    @elseif($channel->type === 'email')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Support Email:</span>
                            <span class="font-bold text-purple-700">{{ $config['support_email'] ?? 'support@helpdesk.com' }}</span>
                        </div>
                    @elseif($channel->type === 'web_chat')
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Widget Title:</span>
                            <span class="font-bold text-indigo-700">{{ $config['title'] ?? 'Customer Support' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Theme:</span>
                            <span class="font-bold text-slate-800 capitalize">{{ $config['theme'] ?? 'light' }}</span>
                        </div>
                    @endif
                </div>

                <!-- Webhook URL Quick Copy Box -->
                @if($webhookUrl)
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Webhook Callback URL</label>
                    <div class="flex items-center gap-1.5 bg-slate-100 border border-slate-200 rounded-xl p-1.5">
                        <input type="text" readonly value="{{ $webhookUrl }}" id="webhook-input-{{ $channel->id }}" class="w-full bg-transparent text-[11px] font-mono text-slate-600 px-2 focus:outline-none select-all">
                        <button type="button" onclick="copyWebhookUrl('webhook-input-{{ $channel->id }}', this)" class="px-2.5 py-1 bg-white hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold border border-slate-200 transition cursor-pointer shrink-0">
                            Copy
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Card Actions Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <div class="text-[11px] text-slate-400 font-medium">
                    Updated {{ $channel->updated_at->diffForHumans() }}
                </div>
                <button type="button" onclick="openEditChannelModal({{ json_encode($channel) }})" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-bold rounded-xl border border-indigo-200 hover:border-indigo-600 transition cursor-pointer flex items-center gap-1">
                    <i class="ti ti-settings text-sm"></i> Configure
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal: Configure Channel Settings -->
<div id="edit-channel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="w-full max-w-xl bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div id="modal-channel-icon" class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 text-xl font-bold">
                    <i class="ti ti-settings"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900" id="modal-channel-title">Configure Integration Channel</h3>
                    <p class="text-xs text-slate-500">API keys, tokens, webhooks, and routing parameters.</p>
                </div>
            </div>
            <button onclick="closeEditChannelModal()" class="text-slate-400 hover:text-slate-600"><i class="ti ti-x text-xl"></i></button>
        </div>

        <form id="edit-channel-form" action="" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- General Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Channel Name *</label>
                    <input type="text" id="edit-channel-name" name="name" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Channel Status</label>
                    <select id="edit-channel-active" name="is_active" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        <option value="1">Active / Connected</option>
                        <option value="0">Disabled / Offline</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic Configuration Fields Container -->
            <div id="dynamic-config-fields" class="space-y-4 pt-2 border-t border-slate-100">
                <!-- Injected via JavaScript based on channel type -->
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditChannelModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Save Channel Settings</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create New Channel -->
<div id="create-channel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i class="ti ti-plus text-indigo-600"></i>
                <span>Add Custom Integration Channel</span>
            </h3>
            <button onclick="closeCreateChannelModal()" class="text-slate-400 hover:text-slate-600"><i class="ti ti-x text-lg"></i></button>
        </div>

        <form action="{{ route('channels.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Channel Display Name *</label>
                <input type="text" name="name" required placeholder="e.g. WhatsApp Support Line 2" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Channel Type *</label>
                <select name="type" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                    <option value="whatsapp">WhatsApp Business Cloud API</option>
                    <option value="facebook">Facebook Messenger API</option>
                    <option value="instagram">Instagram Direct Messaging</option>
                    <option value="telegram">Telegram Bot API</option>
                    <option value="email">Email Support Desk</option>
                    <option value="web_chat">Web Live Chat Widget</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeCreateChannelModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20">Create Channel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateChannelModal() {
        document.getElementById('create-channel-modal').classList.remove('hidden');
    }
    function closeCreateChannelModal() {
        document.getElementById('create-channel-modal').classList.add('hidden');
    }

    function openEditChannelModal(channel) {
        const form = document.getElementById('edit-channel-form');
        form.action = '/channels/' + channel.id;

        document.getElementById('modal-channel-title').innerText = 'Configure ' + channel.name;
        document.getElementById('edit-channel-name').value = channel.name;
        document.getElementById('edit-channel-active').value = channel.is_active ? '1' : '0';

        const config = channel.configuration || {};
        const container = document.getElementById('dynamic-config-fields');
        container.innerHTML = '';

        if (channel.type === 'whatsapp') {
            container.innerHTML = `
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-brand-whatsapp text-emerald-600"></i> Meta WhatsApp Cloud API Setup
                    </div>
                    <p class="text-[11px] mt-1">Provide your WhatsApp Business Phone Number ID and Access Token from Meta Developer Console.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp Phone Number *</label>
                    <input type="text" name="configuration[phone_number]" value="${config.phone_number || ''}" placeholder="+18005550199" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number ID (Meta Graph API) *</label>
                    <input type="text" name="configuration[phone_number_id]" value="${config.phone_number_id || ''}" placeholder="109827364519283" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Permanent Meta Access Token *</label>
                    <div class="relative">
                        <input type="password" id="wa-token" name="configuration[token]" value="${config.token || ''}" placeholder="EAAG..." required class="w-full bg-white border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                        <button type="button" onclick="togglePassVisibility('wa-token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="ti ti-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Webhook Verify Token *</label>
                    <input type="text" name="configuration[verify_token]" value="${config.verify_token || 'omnihelp_secret'}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-[11px]">
                    <div class="font-bold text-slate-700">Meta Webhook Callback URL:</div>
                    <div class="font-mono text-indigo-600 font-bold select-all mt-0.5">${window.location.origin}/api/v1/webhooks/whatsapp</div>
                </div>
            `;
        } else if (channel.type === 'facebook') {
            container.innerHTML = `
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-2xl text-xs text-blue-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-brand-facebook text-blue-600"></i> Meta Facebook Messenger API Setup
                    </div>
                    <p class="text-[11px] mt-1">Connect your Facebook Page ID and Page Access Token to route customer inbox messages.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page ID *</label>
                    <input type="text" name="configuration[page_id]" value="${config.page_id || ''}" placeholder="10928374615" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Page Access Token *</label>
                    <div class="relative">
                        <input type="password" id="fb-token" name="configuration[page_access_token]" value="${config.page_access_token || ''}" placeholder="EAAH..." required class="w-full bg-white border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                        <button type="button" onclick="togglePassVisibility('fb-token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="ti ti-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Webhook Verify Token *</label>
                    <input type="text" name="configuration[verify_token]" value="${config.verify_token || 'omnihelp_secret'}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-[11px]">
                    <div class="font-bold text-slate-700">Messenger Webhook Callback URL:</div>
                    <div class="font-mono text-indigo-600 font-bold select-all mt-0.5">${window.location.origin}/api/v1/webhooks/facebook</div>
                </div>
            `;
        } else if (channel.type === 'instagram') {
            container.innerHTML = `
                <div class="p-3 bg-pink-50 border border-pink-200 rounded-2xl text-xs text-pink-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-brand-instagram text-pink-600"></i> Meta Instagram Direct Messaging API Setup
                    </div>
                    <p class="text-[11px] mt-1">Connect your Instagram Business Account ID and Meta Access Token to receive and respond to DMs.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Instagram Business Account ID *</label>
                    <input type="text" name="configuration[instagram_account_id]" value="${config.instagram_account_id || ''}" placeholder="178414092817263" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Meta / Page Access Token *</label>
                    <div class="relative">
                        <input type="password" id="ig-token" name="configuration[page_access_token]" value="${config.page_access_token || ''}" placeholder="EAAI..." required class="w-full bg-white border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                        <button type="button" onclick="togglePassVisibility('ig-token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="ti ti-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Webhook Verify Token *</label>
                    <input type="text" name="configuration[verify_token]" value="${config.verify_token || 'omnihelp_secret'}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-[11px]">
                    <div class="font-bold text-slate-700">Instagram Webhook Callback URL:</div>
                    <div class="font-mono text-indigo-600 font-bold select-all mt-0.5">${window.location.origin}/api/v1/webhooks/instagram</div>
                </div>
            `;
        } else if (channel.type === 'telegram') {
            container.innerHTML = `
                <div class="p-3 bg-sky-50 border border-sky-200 rounded-2xl text-xs text-sky-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-brand-telegram text-sky-600"></i> Telegram Bot API Setup
                    </div>
                    <p class="text-[11px] mt-1">Create a Telegram Bot via @BotFather and paste your HTTP API Bot Token below.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bot Username *</label>
                    <input type="text" name="configuration[bot_username]" value="${config.bot_username || ''}" placeholder="@OmniSupportBot" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telegram Bot Token *</label>
                    <div class="relative">
                        <input type="password" id="tg-token" name="configuration[bot_token]" value="${config.bot_token || ''}" placeholder="123456789:ABC..." required class="w-full bg-white border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                        <button type="button" onclick="togglePassVisibility('tg-token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="ti ti-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-[11px]">
                    <div class="font-bold text-slate-700">Telegram Webhook Endpoint:</div>
                    <div class="font-mono text-indigo-600 font-bold select-all mt-0.5">${window.location.origin}/api/v1/webhooks/telegram</div>
                </div>
            `;
        } else if (channel.type === 'email') {
            container.innerHTML = `
                <div class="p-3 bg-purple-50 border border-purple-200 rounded-2xl text-xs text-purple-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-mail text-purple-600"></i> Email Support Desk Setup
                    </div>
                    <p class="text-[11px] mt-1">Configure inbound/outbound support email address for automated ticket conversion.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Support Email Address *</label>
                    <input type="email" name="configuration[support_email]" value="${config.support_email || ''}" placeholder="support@helpdesk.com" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">SMTP Host</label>
                        <input type="text" name="configuration[smtp_host]" value="${config.smtp_host || 'smtp.mailtrap.io'}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">SMTP Port</label>
                        <input type="text" name="configuration[smtp_port]" value="${config.smtp_port || '587'}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-2xl text-xs text-indigo-800 mb-3">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="ti ti-messages text-indigo-600"></i> Web Live Chat Widget Setup
                    </div>
                    <p class="text-[11px] mt-1">Configure title, theme, and welcome text for web chat widgets.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Widget Header Title *</label>
                    <input type="text" name="configuration[title]" value="${config.title || 'Customer Support'}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Welcome Message *</label>
                    <input type="text" name="configuration[welcome_message]" value="${config.welcome_message || 'Hello! How can we assist you today?'}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>
            `;
        }

        document.getElementById('edit-channel-modal').classList.remove('hidden');
    }

    function closeEditChannelModal() {
        document.getElementById('edit-channel-modal').classList.add('hidden');
    }

    function togglePassVisibility(id) {
        const input = document.getElementById(id);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    }

    function copyWebhookUrl(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.select();
        navigator.clipboard.writeText(input.value);
        
        const origText = btn.innerText;
        btn.innerText = 'Copied!';
        btn.classList.add('bg-emerald-600', 'text-white');
        setTimeout(() => {
            btn.innerText = origText;
            btn.classList.remove('bg-emerald-600', 'text-white');
        }, 2000);
    }
</script>
@endsection
