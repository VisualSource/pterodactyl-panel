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
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;


class DomainsController extends Controller {
     /**
     * @var \Pterodactyl\Contracts\Repository\DomainRepositoryInterface
     */
    protected $repository;

    /**
     * DomainsController constructor.
     */
    public function __construct(
        DomainRepositoryInterface $repository
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
}

?>