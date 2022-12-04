<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Contracts\Repository;

use Pterodactyl\Models\Port;
use Illuminate\Database\Eloquent\Collection;

interface PortRepositoryInterface extends RepositoryInterface {
     /**
     * Return a port or all ports along with their allocation.
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithAllocations(int $id = null): Collection|Port;
}
