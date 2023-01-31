<?php

namespace Pterodactyl\Services\Ports;

use Pterodactyl\Models\Port;
use Webmozart\Assert\Assert;
use Illuminate\Support\Facades\Artisan;
use Pterodactyl\Contracts\Repository\PortRepositoryInterface;

class PortDeletionService
{
    public function __construct(
        protected PortRepositoryInterface $repository
    ) {
    }
    /**
     * Unregisteds the port
     *
     *  @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle(Port|int $id): ?int
    {
        $id = ($id instanceof Port) ? $id->id : $id;

        Assert::integerish($id, 'First argument passed to handle must be numeric or an instance of ' . Port::class . ', received %s.');

        /** @var Port $port */
        $port = $this->repository->find($id);

        if (!env('TH_NETWORK_DRYRUN', false)) {
            $args = [
                'action' => 'close',
                'port' => $port->external_port,
                '-d' => $port->description,
                '-t' => $port->type,
                '-m' => $port->method,
            ];

            if (!is_null($port->internal_port)) {
                $args['internal'] = $port->internal_port;
            }

            if (!is_null($port->internal_address)) {
                $args['--i'] = $port->internal_address;
            }

            Artisan::queue('network:port', $args);
        }

        return $this->repository->delete($id);
    }
}
