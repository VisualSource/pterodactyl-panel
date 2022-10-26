<?php

namespace Pterodactyl\Console\Commands\Port;

use Illuminate\Console\Command;

class PortStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'port:status {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the status of a port';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

    

        return 0;
    }
}
