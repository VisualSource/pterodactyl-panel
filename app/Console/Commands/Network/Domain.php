<?php

namespace Pterodactyl\Console\Commands\Network;

use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Console\Command;
use Illuminate\Validation\Factory as ValidatorFactory;

class Domain extends Command implements Isolatable
{ 
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:domain {name : The name of the sub domain} {--register} {--unregister}';

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
    public function __construct(private ValidatorFactory $validator)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 
     * @throws \Illuminate\Validation\ValidationException
     * @return int
     */
    public function handle()
    {
        $register = $this->option("register") ? "create" : null;
        $unregister = $this->option("unregister") ? "remove" : null;
        $sub_domain = $this->argument("name");

        if($register && $unregister) {
            $this->output->error("Can not use --register and --unregister at the same time.");
            return Command::INVALID;
        }
        if(is_null($register) && is_null($unregister)) {
            $this->output->error("Select --register or --unregister.");
            return Command::INVALID;
        }
        
        $validator = $this->validator->make([
            'sub_domain' => $sub_domain,
        ],[
            'sub_domain' => 'string|required|between:3,60'
        ]);

        if($validator->fails()) {
            foreach ($validator->getMessageBag()->all() as $message) {
                $this->output->error($message);
            }

            return Command::INVALID;
        }

        $action = $register ? $register : $unregister;

        try {
            $output= array();
            $retval=null;

            $app = base_path("external_application/domain/dist/index.js");

            $cmd = "node " . $app . " " . $action . " " . escapeshellarg($sub_domain);

            exec($cmd,$output,$retval);

            foreach($output as $msg) {
                $this->info($msg);
            }
            
            if($retval != 0) {
                $this->output->error("Failed to proform action.");
                return Command::FAILURE;
            }

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->output->error($th->getMessage());
            return Command::FAILURE;
        }
    }
}
