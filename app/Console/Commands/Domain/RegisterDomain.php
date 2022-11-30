<?php

namespace Pterodactyl\Console\Commands\Domain;

use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Factory as ValidatorFactory;
use Pterodactyl\Services\Domains\DomainCreationService;


class RegisterDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:register {name : The name of the sub domain}';

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
    public function __construct(private ValidatorFactory $validator, private DomainCreationService $creationService)
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
        $sub_domain = $this->argument("name");

        $validator = $this->validator->make([
            'sub_domain' => $sub_domain,
        ],[
            'sub_domain' => 'string|required|min:3'
        ]);

        if($validator->fails()) {
            foreach ($validator->getMessageBag()->all() as $message) {
                $this->output->error($message);
            }

            throw new ValidationException($validator);
        }

        try {
            $output=null;
            $retval=null;

            $dir = dirname(__FILE__) . '/../../../../external_application/domain/';

            $cmd = "node " . $dir. "index.js create " . escapeshellarg($sub_domain);

            exec($cmd,$output,$retval);

            if($retval != 0) {
                throw new \Exception("Error Processing Request", 1);
            }

            return $retval;
            //code...
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            //throw $th;
            return 1;
        }
    }
}
