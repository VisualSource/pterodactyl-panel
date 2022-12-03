<?php 
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */
namespace Pterodactyl\Http\Controllers\Admin\Domains;

use Pterodactyl\Exceptions\Model\DataValidationException;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;
use Pterodactyl\Models\Domain;
use Pterodactyl\Http\Requests\Admin\Domain\DomainFormRequest;
use Pterodactyl\Services\Domains\DomainCreationService;
use Pterodactyl\Services\Domains\DomainDeletionService;
use Pterodactyl\Services\Domains\DomainUpdateService;

class DomainController extends Controller {
    /**
     * DomainsController constructor.
     */
    public function __construct(
        protected AlertsMessageBag $alert,
        protected DomainRepositoryInterface $repository,
        protected DomainCreationService $creationService,
        protected DomainDeletionService $deletionService,
        protected DomainUpdateService $updateService,
    ){
        $this->repository = $repository;
    }
    /**
     * Render nest listing page.
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
    */
    public function index(): View
    {
        return view('admin.domains.index', [
            'domains' => $this->repository->getWithServers(),
        ]);
    }

    public function view(Domain $domain): View {
        return view("admin.domains.view",[ "domain" => $domain ]);
    }

    public function store(DomainFormRequest $request): RedirectResponse 
    {
        try {
            $this->creationService->handle($request->normalize());
            $this->alert->success('Domain was registered. The sub-domain with be ready within 2-5 minutes.')->flash();
        } catch(DataValidationException $ex){
            $this->alert->danger($ex->getMessageBag()->get("domain"))->flash();
        }

        return redirect()->route("admin.domains");
    }

    public function update(DomainFormRequest $request, Domain $domain): Response  {

        try {
            $this->updateService->handle($domain->id, $request->normalize());
            $this->alert->success('Domain was updated successfully.')->flash();
            return response('', 204);
        } catch (DataValidationException $ex) {
            $this->alert->danager($ex->getMessageBag()->get("server_id"))->flash();
            return response('', 404);
        }
    }

    public function destroy(Domain $domain): Response 
    {
        try {
            $this->deletionService->handle($domain->id);
            $this->alert->success('Domain was unregistered. The sub-domain with be removed within 2-5 minutes.')->flash();
        } catch (DisplayException $ex) {
            $this->alert->danger($ex->getMessage())->flash();
        }

        return response('', 204);
    }
}