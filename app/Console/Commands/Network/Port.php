<?php

namespace Pterodactyl\Console\Commands\Network;

use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Console\Command;

class Port extends Command implements Isolatable
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:port {action} {port} {internal?} {--d|desc=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle opening, closing, and status of ports.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $app_root = base_path("external_application/upnp");

        $this->info($app_root);

        return Command::SUCCESS;
    }
}
