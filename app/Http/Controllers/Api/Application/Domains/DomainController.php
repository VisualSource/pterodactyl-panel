<?php 
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */
namespace Pterodactyl\Http\Controllers\Api\Application\Domains;

use Illuminate\Http\Response;
use Pterodactyl\Models\Domain;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Pterodactyl\Services\Domains\DomainUpdateService;
use Pterodactyl\Services\Domains\DomainCreationService;
use Pterodactyl\Services\Domains\DomainDeletionService;
use Pterodactyl\Transformers\Api\Application\DomainTransformer;
use Pterodactyl\Exceptions\Http\QueryValueOutOfRangeHttpException;
use Pterodactyl\Http\Requests\Api\Application\Domains\GetDomainsRequest;
use Pterodactyl\Http\Requests\Api\Application\Domains\StoreDomainRequest;
use Pterodactyl\Http\Requests\Api\Application\Domains\DeleteDomainRequest;
use Pterodactyl\Http\Requests\Api\Application\Domains\UpdateDomainRequest;
use Pterodactyl\Http\Controllers\Api\Application\ApplicationApiController;

class DomainController extends ApplicationApiController 
{
    /**
     * DomainsController constructor.
     */
    public function __construct(
        protected DomainCreationService $creationService,
        protected DomainDeletionService $deletionService,
        protected DomainUpdateService $updateService,
    ){
        parent::__construct();
    }

    /**
     * Return all the domains registered with the panel.
     *
    */
    public function index(GetDomainsRequest $request): array
    {
        $perPage = (int)($request->query("per_page","10"));
        if($perPage < 1 || $perPage > 100) {
            throw new QueryValueOutOfRangeHttpException('per_page', 1, 100);
        }

        $domains = QueryBuilder::for(Domain::query())
            ->allowedFilters(['domain'])
            ->allowedSorts(['id', 'domain', 'server_id'])
            ->paginate($perPage);

        return $this->fractal->collection($domains)
            ->transformWith(DomainTransformer::class)
            ->toArray();
    }

    /**
    * Store a new domain on the Panel and return an HTTP/201 response code with the
    * new domain attached.
    *
    * @throws \Pterodactyl\Exceptions\Model\DataValidationException
    */
    public function store(StoreDomainRequest $request): JsonResponse 
    {
      
        $domain = $this->creationService->handle($request->validated());
           
        return $this->fractal->item($domain)
            ->transformWith(DomainTransformer::class)
            ->respond(201);  
    }

    /**
    * Update a domain on the Panel and return the updated record to the user.
    *
    * @throws \Pterodactyl\Exceptions\Model\DataValidationException
    * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
    */
    public function update(UpdateDomainRequest $request, Domain $domain): array 
    {
        $domain = $this->updateService->handle($domain->id, $request->validated());

        return $this->fractal->item($domain)
            ->transformWith(DomainTransformer::class)
            ->toArray();
        
    }
    /**
    * Delete a domain from the Panel.
    *
    * @throws \Pterodactyl\Exceptions\Service\Location\HasActiveNodesException
    */
    public function destroy(DeleteDomainRequest $request, Domain $domain): Response 
    { 
        $this->deletionService->handle($domain->id);

        return $this->returnNoContent();
    }
}