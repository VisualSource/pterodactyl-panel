<?php

namespace Pterodactyl\Console\Commands\Domain;

use Illuminate\Console\Command;

class RegisterDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:register {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers a sub domain with google domains';

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
