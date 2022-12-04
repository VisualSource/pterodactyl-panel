<?php 
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */
namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Contracts\Repository\NodeRepositoryInterface;
use Pterodactyl\Contracts\Repository\PortRepositoryInterface;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Exceptions\Model\DataValidationException;
use Pterodactyl\Services\Ports\PortCreationService;
use Pterodactyl\Services\Ports\PortDeletionService;
use Pterodactyl\Services\Ports\PortUpdateService;
use Pterodactyl\Models\Port;
use Pterodactyl\Http\Requests\Admin\PortFormRequest;

class PortController extends Controller 
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected NodeRepositoryInterface $nodeRepository,
        protected PortRepositoryInterface $repository,
        protected PortCreationService $creationService,
        protected PortUpdateService $updateService,
        protected PortDeletionService $deletionService
    ){
        $this->repository = $repository;
    }

    public function index(): View 
    {
        return view('admin.ports.index',[
            'ports' => $this->repository->getWithAllocations()
        ]);
    }

    public function view(Port $port) 
    {

        $nodes = $this->nodeRepository->getNodesForServerCreation();

        $openAllocations = [];

        foreach($nodes->getIterator() as $node){
            foreach($node["allocations"]->getIterator() as $allocation){
                array_push($openAllocations,[ 
                    "address" => $allocation["text"], 
                    "id" => $allocation["id"],
                    'node' => $node["text"]
                ]);
            }
        }
        return view("admin.ports.view",[
            'port' => $port,
            'allocations' => $openAllocations
        ]);
    }

    public function create(): View 
    {
        $nodes = $this->nodeRepository->getNodesForServerCreation();

        $openAllocations = [];

        foreach($nodes->getIterator() as $node){
            foreach($node["allocations"]->getIterator() as $allocation){
                array_push($openAllocations,[ 
                    "address" => $allocation["text"], 
                    "id" => $allocation["id"],
                    'node' => $node["text"]
                ]);
            }
        }

        return view("admin.ports.new",[
            'allocations'=> $openAllocations
        ]);
    }

    public function store(PortFormRequest $request): RedirectResponse
    { 
      
        $this->creationService->handle($request->normalize());
        $this->alert->success('Port was registered. The port will be ready within 2-5 minutes.')->flash();
      
        return redirect()->route("admin.ports");
    }

    public function update(PortFormRequest $request, Port $port): RedirectResponse 
    {

        $this->updateService->handle($port,$request->normalize());
        $this->alert->success('Port was updated successfully.')->flash();
      
        return redirect()->route("admin.ports");
    }
    public function destroy(Port $port): Response 
    {
        try {
            $this->deletionService->handle($port->id);
            $this->alert->success('Port was closed The port will be removed within 2-5 minutes.')->flash();
        } catch (DisplayException $ex) {
            $this->alert->danger($ex->getMessage())->flash();
        }

        return response('',204);
    }
}