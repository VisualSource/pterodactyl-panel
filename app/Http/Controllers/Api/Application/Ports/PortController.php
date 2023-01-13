<?php 
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */
namespace Pterodactyl\Http\Controllers\Api\Application\Ports;

use Pterodactyl\Models\Port;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Pterodactyl\Services\Ports\PortUpdateService;
use Pterodactyl\Services\Ports\PortCreationService;
use Pterodactyl\Services\Ports\PortDeletionService;
use Pterodactyl\Transformers\Api\Application\PortTransformer;
use Pterodactyl\Exceptions\Http\QueryValueOutOfRangeHttpException;
use Pterodactyl\Http\Requests\Api\Application\Ports\GetPortRequest;
use Pterodactyl\Http\Requests\Api\Application\Ports\GetPortsRequest;
use Pterodactyl\Http\Requests\Api\Application\Ports\StorePortRequest;
use Pterodactyl\Http\Requests\Api\Application\Ports\DeletePortRequest;
use Pterodactyl\Http\Requests\Api\Application\Ports\UpdatePortRequest;
use Pterodactyl\Http\Controllers\Api\Application\ApplicationApiController;

class PortController extends ApplicationApiController 
{
    public function __construct(
        protected PortCreationService $creationService,
        protected PortUpdateService $updateService,
        protected PortDeletionService $deletionService
    ){
        parent::__construct();
    }

    public function index(GetPortsRequest $request): array
    {
        $perPage = (int) $request->query('per_page','10');
        if($perPage < 1 || $perPage > 100) {
            return new QueryValueOutOfRangeHttpException('per_page',1,100);
        }

        $ports = QueryBuilder::for(Port::query())
            ->allowedFilters(['external_port', 'type'])
            ->allowedSorts(['id', 'type', 'external_port','internal_port','internal_address'])
            ->paginate($perPage);

        return $this->fractal->collection($ports)
            ->transformWith(PortTransformer::class)
            ->toArray();
    }

    /**
    * Return a single port.
    */
    public function view(GetPortRequest $request, Port $port): array 
    {
        return $this->fractal->item($port)
            ->transformWith(PortTransformer::class)
            ->toArray();
    }

    /**
     * Store a new port on the Panel and return an HTTP/201 response code with the
     * new port attached.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     */
    public function store(StorePortRequest $request): JsonResponse
    { 
        $port = $this->creationService->handle($request->validated());
       
        return $this->fractal->item($port) 
            ->transformWith(PortTransformer::class)
            ->respond(201);   
    }

    /**
     * Update a port on the Panel and return the updated record to the user.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(UpdatePortRequest $request, Port $port): array
    {
        $port = $this->updateService->handle($port,$request->validated());
        
        return $this->fractal->item($port)
            ->transformWith(PortTransformer::class)
            ->toArray();
    }

    /**
     * Delete a port from the Panel.
     *
     * @throws \Pterodactyl\Exceptions\Service\Location\HasActiveNodesException
     */
    public function destroy(DeletePortRequest $request, Port $port): Response 
    {
        $this->deletionService->handle($port->id);

        return $this->returnNoContent();
    }
}