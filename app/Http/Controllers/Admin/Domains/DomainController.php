<?php 
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */
namespace Pterodactyl\Http\Controllers\Admin\Domains;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;
use Pterodactyl\Models\Domain;

class DomainController extends Controller {
    /**
     * DomainsController constructor.
     */
    public function __construct(
        protected DomainRepositoryInterface $repository,
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
            'domains' => $this->repository->getWithCounts(),
        ]);
    }

    public function view(Domain $domain): View {
        return view("admin.domains.view",[ "domain" => $domain ]);
    }

    public function create(): View {
        return view("admin.domains.new");
    }

    public function store($request): RedirectResponse {

        return redirect()->route("admin.domains");
    }

    public function update($request, Domain $domain): RedirectResponse {

        return redirect()->route("admin.domains");
    }

    public function destory(Domain $domain): RedirectResponse {

        return redirect()->route("admin.domains");
    }
}

?>