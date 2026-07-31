<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WidgetBuilderController extends Controller
{
    /**
     * Display the Widget Builder studio & listing of widgets.
     */
    public function index(Request $request)
    {
        $channels = Channel::where('type', 'web_chat')->get();

        // If no web chat channel exists, create a default one
        if ($channels->isEmpty()) {
            $defaultChannel = Channel::create([
                'name' => 'Main Website Chat Widget',
                'slug' => 'main-website-chat',
                'type' => 'web_chat',
                'icon' => 'globe',
                'is_active' => true,
                'configuration' => [
                    'widget_color' => '#6366f1',
                    'position' => 'bottom-right',
                    'title' => 'Customer Support',
                    'subtitle' => 'We typically reply in under 5 minutes',
                    'welcome_message' => 'Hello! How can we assist you today?',
                    'logo_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk',
                    'theme' => 'dark',
                    'launcher_icon' => 'message-dots',
                    'require_prechat' => false,
                ],
            ]);
            $channels = collect([$defaultChannel]);
        }

        $selectedChannelId = $request->query('channel_id');
        $activeChannel = $selectedChannelId ? $channels->firstWhere('id', $selectedChannelId) : $channels->first();

        if (!$activeChannel) {
            $activeChannel = $channels->first();
        }

        return view('widget-builder', compact('channels', 'activeChannel'));
    }

    /**
     * Create a new Web Chat Widget channel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'widget_color' => 'required|string|max:30',
        ]);

        $channel = Channel::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'type' => 'web_chat',
            'icon' => 'message-dots',
            'is_active' => true,
            'configuration' => [
                'widget_color' => $validated['widget_color'],
                'position' => 'bottom-right',
                'title' => $validated['name'],
                'subtitle' => 'We typically reply in a few minutes',
                'welcome_message' => 'Hello! How can we help you?',
                'logo_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($validated['name']),
                'theme' => 'dark',
                'launcher_icon' => 'message-dots',
                'require_prechat' => false,
            ],
        ]);

        return redirect()->route('widget-builder.index', ['channel_id' => $channel->id])
            ->with('success', "New widget '{$channel->name}' created successfully!");
    }

    /**
     * Update active widget configuration.
     */
    public function update(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'widget_color' => 'required|string|max:30',
            'position' => 'required|string|in:bottom-right,bottom-left,top-right,top-left',
            'title' => 'required|string|max:100',
            'subtitle' => 'nullable|string|max:150',
            'welcome_message' => 'nullable|string|max:500',
            'logo_url' => 'nullable|string|max:500',
            'theme' => 'required|string|in:dark,light,auto',
            'launcher_icon' => 'required|string|in:message-dots,chat,sparkles,help,message',
            'require_prechat' => 'nullable|boolean',
        ]);

        $config = [
            'widget_color' => $validated['widget_color'],
            'position' => $validated['position'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? '',
            'welcome_message' => $validated['welcome_message'] ?? '',
            'logo_url' => $validated['logo_url'] ?? '',
            'theme' => $validated['theme'],
            'launcher_icon' => $validated['launcher_icon'],
            'require_prechat' => (bool)($validated['require_prechat'] ?? false),
        ];

        $channel->update([
            'name' => $validated['name'],
            'is_active' => (bool)($validated['is_active'] ?? true),
            'configuration' => $config,
        ]);

        Channel::flushChannelCache($channel->type);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Widget configuration saved successfully!',
                'channel' => $channel,
            ]);
        }

        return redirect()->route('widget-builder.index', ['channel_id' => $channel->id])
            ->with('success', "Widget '{$channel->name}' configuration saved!");
    }

    /**
     * Delete a web chat widget.
     */
    public function destroy(Channel $channel)
    {
        if ($channel->type !== 'web_chat') {
            return back()->withErrors(['error' => 'Only web chat channels can be deleted here.']);
        }

        $count = Channel::where('type', 'web_chat')->count();
        if ($count <= 1) {
            return back()->withErrors(['error' => 'You must keep at least one active web chat widget.']);
        }

        $name = $channel->name;
        $channel->delete();

        return redirect()->route('widget-builder.index')
            ->with('success', "Widget '{$name}' has been deleted.");
    }
}
