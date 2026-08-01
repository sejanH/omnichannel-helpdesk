<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class GrantLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omnihelp:license-grant 
                            {key? : Existing license key string to activate}
                            {--domain=support.sejan.dev : Target domain name}
                            {--plan=pro : Plan tier (starter, pro, enterprise)}
                            {--seats=15 : Maximum support agent seats}
                            {--expires=2027-12-31 : Expiration date YYYY-MM-DD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or activate an official OmniHelp license key for a domain host instance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $domain = $this->option('domain');
        $plan = $this->option('plan');
        $seats = (int) $this->option('seats');
        $expires = $this->option('expires');

        if (!$key) {
            $this->info("Generating new signed license key for [{$domain}]...");
            $key = LicenseService::generateLicenseKey($domain, $plan, $seats, $expires);
            $this->line('');
            $this->warn("Granted License Key:");
            $this->comment($key);
            $this->line('');
        }

        $this->info("Verifying and applying license key...");
        $result = LicenseService::verifyAndApplyLicense($key, $domain);

        if ($result['success']) {
            $this->info("SUCCESS: " . $result['message']);
            $this->table(
                ['Property', 'Value'],
                [
                    ['Domain Host', $domain],
                    ['Granted Plan', strtoupper($plan)],
                    ['Agent Seats', $seats],
                    ['Expiration Date', $expires],
                    ['License Status', 'ACTIVE & VERIFIED'],
                ]
            );
            return 0;
        } else {
            $this->error("FAILURE: " . $result['message']);
            return 1;
        }
    }
}
