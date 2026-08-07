<?php

namespace Database\Seeders;

use App\Models\CannedResponse;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\KnowledgeBaseArticle;
use App\Models\Message;
use App\Models\Role;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OmnichannelSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Roles
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        Role::firstOrCreate(['slug' => 'agent'], ['name' => 'Agent']);

        // 1. Create Agents
        $admin = User::firstOrCreate(
            ['email' => 'admin@helpdesk.com'],
            [
                'name' => 'Alex Rivera (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'online',
            ]
        );

        $agentSarah = User::firstOrCreate(
            ['email' => 'sarah@helpdesk.com'],
            [
                'name' => 'Sarah Connor',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'status' => 'online',
            ]
        );

        $agentJohn = User::firstOrCreate(
            ['email' => 'john@helpdesk.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'status' => 'away',
            ]
        );

        // 2. Channels
        $webChannel = Channel::firstOrCreate(
            ['slug' => 'web-live-chat'],
            [
                'name' => 'Web Live Chat',
                'type' => 'web_chat',
                'icon' => 'globe',
                'is_active' => true,
                'configuration' => [
                    'title' => 'Customer Support',
                    'welcome_message' => 'Hello! How can we assist you today?',
                    'widget_color' => '#4f46e5',
                    'theme' => 'light',
                    'launcher_position' => 'right',
                    'require_email' => true,
                ],
            ]
        );

        $whatsappChannel = Channel::firstOrCreate(
            ['slug' => 'whatsapp-business'],
            [
                'name' => 'WhatsApp Business',
                'type' => 'whatsapp',
                'icon' => 'phone-call',
                'is_active' => true,
                'configuration' => [
                    'phone_number' => '+18005550199',
                    'phone_number_id' => '109827364519283',
                    'token' => 'EAAG_SAMPLE_WHATSAPP_TOKEN',
                    'verify_token' => 'omnihelp_secret',
                ],
            ]
        );

        $emailChannel = Channel::firstOrCreate(
            ['slug' => 'email-desk'],
            [
                'name' => 'Email Support',
                'type' => 'email',
                'icon' => 'mail',
                'is_active' => true,
                'configuration' => ['support_email' => 'support@helpdesk.com'],
            ]
        );

        $fbChannel = Channel::firstOrCreate(
            ['slug' => 'facebook-messenger'],
            [
                'name' => 'Facebook Messenger',
                'type' => 'facebook',
                'icon' => 'message-square',
                'is_active' => true,
                'configuration' => [
                    'page_id' => '10928374615',
                    'page_access_token' => 'EAAH_SAMPLE_FACEBOOK_TOKEN',
                    'verify_token' => 'omnihelp_secret',
                ],
            ]
        );

        $telegramChannel = Channel::firstOrCreate(
            ['slug' => 'telegram-bot'],
            [
                'name' => 'Telegram Bot',
                'type' => 'telegram',
                'icon' => 'send',
                'is_active' => true,
                'configuration' => [
                    'bot_username' => '@OmniSupportBot',
                    'bot_token' => '123456789:ABC_SAMPLE_TELEGRAM_TOKEN',
                ],
            ]
        );

        // 3. SLA Policies
        SlaPolicy::firstOrCreate(['priority' => 'urgent'], [
            'name' => 'Urgent Tier 1 SLA',
            'first_response_target_minutes' => 15,
            'resolution_target_minutes' => 120,
        ]);
        SlaPolicy::firstOrCreate(['priority' => 'high'], [
            'name' => 'High Priority SLA',
            'first_response_target_minutes' => 30,
            'resolution_target_minutes' => 240,
        ]);
        SlaPolicy::firstOrCreate(['priority' => 'medium'], [
            'name' => 'Standard SLA',
            'first_response_target_minutes' => 60,
            'resolution_target_minutes' => 720,
        ]);
        SlaPolicy::firstOrCreate(['priority' => 'low'], [
            'name' => 'Low Priority SLA',
            'first_response_target_minutes' => 120,
            'resolution_target_minutes' => 1440,
        ]);

        // 4. Canned Responses
        CannedResponse::firstOrCreate(['shortcut' => '/greeting'], [
            'title' => 'Warm Greeting',
            'content' => 'Hello! Thank you for reaching out to Omnichannel Support. How can I assist you today?',
        ]);
        CannedResponse::firstOrCreate(['shortcut' => '/pricing'], [
            'title' => 'Pricing Information',
            'content' => 'Our Starter plan is $49/mo, Business plan is $149/mo, and Enterprise plan is $299/mo. Let me know if you would like a demo!',
        ]);
        CannedResponse::firstOrCreate(['shortcut' => '/refund'], [
            'title' => 'Refund Policy',
            'content' => 'We offer a 30-day money-back guarantee. I will initiate the refund request for your account immediately.',
        ]);

        // 5. Knowledge Base FAQ Articles
        KnowledgeBaseArticle::firstOrCreate(['slug' => 'how-to-connect-whatsapp-business'], [
            'title' => 'How to Connect WhatsApp Business API',
            'category' => 'Integrations',
            'content' => 'Follow these steps to connect Meta WhatsApp Business Cloud API to OmniHelp...',
            'views_count' => 142,
            'is_published' => true,
            'author_id' => $admin->id,
        ]);
        KnowledgeBaseArticle::firstOrCreate(['slug' => 'embed-live-chat-widget-on-website'], [
            'title' => 'Embedding Live Chat Widget on Your Website',
            'category' => 'Widget Setup',
            'content' => 'Copy the widget script snippet from Multi-Widget Builder Studio and paste before </body> tag...',
            'views_count' => 289,
            'is_published' => true,
            'author_id' => $admin->id,
        ]);
        KnowledgeBaseArticle::firstOrCreate(['slug' => 'understanding-sla-policies'], [
            'title' => 'Understanding SLA Response & Resolution Target Timers',
            'category' => 'Helpdesk Policies',
            'content' => 'SLA targets ensure your agents respond to urgent tickets within 15 minutes and resolved on time...',
            'views_count' => 95,
            'is_published' => true,
            'author_id' => $admin->id,
        ]);

        // 6. Contacts
        $contact1 = Contact::firstOrCreate(['email' => 'michael.scott@dundermifflin.com'], [
            'name' => 'Michael Scott',
            'phone' => '+15550192831',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Michael',
            'external_ids' => ['whatsapp' => '+15550192831'],
            'notes' => 'VIP Customer. Prefers quick resolution.',
        ]);

        $contact2 = Contact::firstOrCreate(['email' => 'pam.beesly@dundermifflin.com'], [
            'name' => 'Pam Beesly',
            'phone' => '+15550192832',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Pam',
            'external_ids' => ['telegram' => 'pam_b_99'],
            'notes' => 'Inquiring about invoice #99281.',
        ]);

        $contact3 = Contact::firstOrCreate(['email' => 'jim.halpert@dundermifflin.com'], [
            'name' => 'Jim Halpert',
            'phone' => '+15550192833',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jim',
            'external_ids' => ['facebook' => 'fb_jim_halpert'],
            'notes' => 'Testing live chat widget integrations.',
        ]);

        // 7. Tickets & Messages
        // Ticket 1: Web Chat (Urgent)
        $t1 = Ticket::firstOrCreate(['ticket_number' => 'TCK-1001'], [
            'subject' => 'Payment Failed on Checkout Page',
            'status' => 'open',
            'priority' => 'urgent',
            'tags' => ['Payment', 'VIP'],
            'category' => 'Billing',
            'due_at' => now()->addHours(2),
            'channel_id' => $webChannel->id,
            'contact_id' => $contact1->id,
            'assigned_agent_id' => $agentSarah->id,
            'last_activity_at' => now(),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'sender_type' => 'customer',
            'sender_id' => $contact1->id,
            'sender_name' => $contact1->name,
            'content' => 'Hi team! My credit card payment keeps getting rejected at step 3. Can someone help?',
            'created_at' => now()->subMinutes(25),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'sender_type' => 'agent',
            'sender_id' => $agentSarah->id,
            'sender_name' => $agentSarah->name,
            'content' => 'Hi Michael! I am checking our Stripe transaction logs right now. Hang tight for a moment!',
            'created_at' => now()->subMinutes(15),
        ]);

        Message::create([
            'ticket_id' => $t1->id,
            'sender_type' => 'agent',
            'sender_id' => $agentSarah->id,
            'sender_name' => $agentSarah->name,
            'content' => 'Internal Note: Gateway logs show 3DS authentication timeout.',
            'is_internal_note' => true,
            'created_at' => now()->subMinutes(10),
        ]);

        // Ticket 2: WhatsApp (High - Overdue SLA for demo)
        $t2 = Ticket::firstOrCreate(['ticket_number' => 'TCK-1002'], [
            'subject' => 'WhatsApp Order Status Inquiry',
            'status' => 'in_progress',
            'priority' => 'high',
            'tags' => ['Shipping', 'WhatsApp'],
            'category' => 'Orders',
            'due_at' => now()->subMinutes(30),
            'channel_id' => $whatsappChannel->id,
            'contact_id' => $contact2->id,
            'assigned_agent_id' => $admin->id,
            'last_activity_at' => now()->subHours(1),
        ]);

        Message::create([
            'ticket_id' => $t2->id,
            'sender_type' => 'customer',
            'sender_id' => $contact2->id,
            'sender_name' => $contact2->name,
            'content' => 'Hello, can I check when order #ORD-4491 will be shipped?',
            'created_at' => now()->subHours(2),
        ]);

        Message::create([
            'ticket_id' => $t2->id,
            'sender_type' => 'agent',
            'sender_id' => $admin->id,
            'sender_name' => $admin->name,
            'content' => 'Hi Pam, your order has been dispatched today via DHL tracking #99281726!',
            'created_at' => now()->subHours(1),
        ]);

        // Ticket 3: Email (Medium)
        $t3 = Ticket::firstOrCreate(['ticket_number' => 'TCK-1003'], [
            'subject' => 'Feature Request: Dark mode for mobile SDK',
            'status' => 'open',
            'priority' => 'medium',
            'tags' => ['Feature Request', 'Mobile'],
            'category' => 'Technical',
            'due_at' => now()->addHours(12),
            'channel_id' => $emailChannel->id,
            'contact_id' => $contact3->id,
            'assigned_agent_id' => null,
            'last_activity_at' => now()->subHours(4),
        ]);

        Message::create([
            'ticket_id' => $t3->id,
            'sender_type' => 'customer',
            'sender_id' => $contact3->id,
            'sender_name' => $contact3->name,
            'content' => 'Would love to see dark mode support added to the web live chat widget in the next release.',
            'created_at' => now()->subHours(4),
        ]);
    }
}
