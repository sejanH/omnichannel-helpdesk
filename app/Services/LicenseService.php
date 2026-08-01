<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    /**
     * Generate a signed cryptographic license key
     */
    public static function generateLicenseKey(string $domain, string $plan, int $seats, string $expiresAt): string
    {
        $payload = [
            'domain' => strtolower(trim($domain)),
            'plan' => strtolower($plan),
            'seats' => $seats,
            'expires_at' => $expiresAt,
            'nonce' => \Illuminate\Support\Str::random(8),
        ];

        $jsonPayload = json_encode($payload);
        $encoded = base64_encode($jsonPayload);
        $signature = hash_hmac('sha256', $encoded, config('app.key'));

        return 'OMNI-' . strtoupper(substr($signature, 0, 4)) . '-' . strtoupper(substr($signature, 4, 4)) . '-' . $encoded;
    }

    /**
     * Verify and apply a License Key
     */
    public static function verifyAndApplyLicense(string $licenseKey, ?string $currentDomain = null): array
    {
        $licenseKey = trim($licenseKey);

        if (!str_starts_with($licenseKey, 'OMNI-')) {
            return ['success' => false, 'message' => 'Invalid License Key format. Key must begin with OMNI-'];
        }

        $parts = explode('-', $licenseKey);
        if (count($parts) < 4) {
            return ['success' => false, 'message' => 'Malformed License Key.'];
        }

        $encodedPayload = array_pop($parts);
        $jsonPayload = base64_decode($encodedPayload, true);
        if (!$jsonPayload) {
            return ['success' => false, 'message' => 'Invalid license key signature decoding.'];
        }

        $data = json_decode($jsonPayload, true);
        if (!$data || !isset($data['domain'], $data['plan'], $data['seats'])) {
            return ['success' => false, 'message' => 'Corrupted license data payload.'];
        }

        // Verify Domain Match if provided
        $targetDomain = strtolower($currentDomain ?? request()->getHost());
        $licensedDomain = strtolower($data['domain']);

        if ($licensedDomain !== '*' && $licensedDomain !== $targetDomain && !str_contains($targetDomain, $licensedDomain)) {
            return [
                'success' => false,
                'message' => "License domain mismatch. Granted for [{$licensedDomain}], but running on [{$targetDomain}]."
            ];
        }

        // Verify Expiration
        if (isset($data['expires_at']) && strtotime($data['expires_at']) < time()) {
            return ['success' => false, 'message' => 'License Key expired on ' . $data['expires_at']];
        }

        // Grant & Update Subscription in Database
        $subscription = Subscription::firstOrCreate(
            ['id' => 1],
            ['subscription_status' => 'active', 'subscription_plan' => 'pro']
        );

        $subscription->update([
            'subscription_status' => 'active',
            'subscription_plan' => strtolower($data['plan']),
            'max_agent_seats' => (int) $data['seats'],
            'max_channels' => (int) ($data['seats'] * 2),
            'subscription_ends_at' => isset($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : now()->addYear(),
            'pm_type' => 'License Grant',
            'pm_last_four' => 'LIC',
        ]);

        return [
            'success' => true,
            'message' => 'License successfully verified and granted for ' . $data['domain'] . '!',
            'data' => $data
        ];
    }
}
