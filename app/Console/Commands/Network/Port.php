<?php

namespace Pterodactyl\Console\Commands\Network;

use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Console\Command;
use Illuminate\Validation\Factory as ValidatorFactory;

class Port extends Command implements Isolatable
{
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:port {action} {port} {internal?} {--d|desc=null} {--i|ip=null} {--t|type=both} {--m|method=upnp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle opening, closing, and status of ports.';

    private function getInput(): array {
        $ip = $this->option("ip");
        $desc = $this->option("desc");
        $data = [
            "action" => $this->argument("action"),
            "port" => $this->argument("port"),
            "internal" => $this->argument("internal"),
            "desc" => $desc == "null" ? null : $desc,
            "type" => $this->option("type"),
            "method" => $this->option("method"),
            "ip" =>  $ip == "null" ? null : $ip
        ];

        $vaildate = $this->validator->make($data,[
            "action" => 'required|in:open,close',
            "port" => 'required|integer',
            "internal" => 'nullable|integer',
            'desc' => "nullable|string",
            'type' => 'in:both,tcp,udp',
            'method' => 'in:upnp,pmp',
            "ip" => "nullable|string|regex:/(\d\d\d).(\d\d\d).(\d)+.(\d)+/"
        ]);

        if($vaildate->fails()) {
            foreach ($vaildate->getMessageBag()->all() as $message) {
                $this->output->error($message);
            }
            throw new \Illuminate\Validation\ValidationException($vaildate);
        }

        switch ($data["action"]) {
            case 'open':
                $data["action"] = "-o";
                break;
            case "close":
                $data["action"] = "-r";
            default:
                break;
        }

        if(is_null($data["ip"])) {
            $data["ip"] = "";
        } else {
            $data["ip"] = '-ip ' . escapeshellarg($data["ip"]);
        }

        $data["port"] = '-p ' . $data["port"];
      
        if(is_null($data["internal"])) {
            $data["internal"] = "";
        } else {
            $data["internal"] = '-i ' . $data["internal"];
        }

        
        if(is_null($data["desc"])) {
            $data["desc"] = "";
        } else {
            $data["desc"] = '-d ' . escapeshellarg($data["desc"]);
        }

        $data["type"] = '-t ' . escapeshellarg($data["type"]);

        $data["method"] = '-m ' . escapeshellarg($data["method"]);

        return $data;
    }


    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return int
     */
    public function handle()
    {
        try {
            $input = $this->getInput();
            $action = $input["action"];
            $ip = $input["ip"];
            $port = $input["port"];
            $method = $input["method"];
            $type = $input["type"];
            $desc = $input["desc"];
            $internal = $input["internal"];

            $app_root = base_path("external_application/upnp/temp.sh");

            $cmd = "$app_root $action $port $internal $ip $type $method $desc";

            $output = [];
            $code = Command::SUCCESS;

            exec($cmd,$output,$code);

            foreach ($output as $value) {
                $this->info($value);
            }

            if($code != 0) {
                return Command::FAILURE;
            } 

        } catch (\Illuminate\Validation\ValidationException $th) {
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}
