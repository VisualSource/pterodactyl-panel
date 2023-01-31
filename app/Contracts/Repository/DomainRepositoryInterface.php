<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Contracts\Repository;

use Pterodactyl\Models\Domain;
use Illuminate\Database\Eloquent\Collection;

interface DomainRepositoryInterface extends RepositoryInterface {
    /**
     * Return a domain or all domains and the count of the server for that domain.
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithCounts(int $id = null): Collection|Domain;


    public function getWithServers(int $id = null): Collection|Domain;
}
?>