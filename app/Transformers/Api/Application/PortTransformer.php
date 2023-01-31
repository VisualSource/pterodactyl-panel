<?php

namespace Pterodactyl\Transformers\Api\Application;

use Pterodactyl\Models\Port;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\NullResource;
use Pterodactyl\Services\Acl\Api\AdminAcl;
use Pterodactyl\Transformers\Api\Transformer;

class PortTransformer extends Transformer
{
    /**
     * List of resources that can be included.
     */
    protected array $availableIncludes = ['allocation'];

    /**
     * Return the resource name for the JSONAPI output.
     */
    public function getResourceName(): string
    {
        return Port::RESOURCE_NAME;
    }

    /**
     * Return a generic transformed location array.
     */
    public function transform(Port $model): array
    {
        return [
            'id' => $model->id,
            'allocation_id' => $model->allocation_id,
            'external_port' => $model->external_port,
            'internal_port' => $model->internal_port,
            'internal_address' => $model->internal_address,
            'type' => $model->type,
            'method' => $model->method,
            'description' => $model->description,
            'created_at' => self::formatTimestamp($model->created_at),
            'updated_at' => self::formatTimestamp($model->updated_at),
        ];
    }

    /**
     * Return the allocation associated with this port.
     */
    public function includeAllocation(Port $port): Collection|NullResource
    {
        if (!$this->authorize(AdminAcl::RESOURCE_ALLOCATIONS)) {
            return $this->null();
        }

        return $this->collection($port->allocation, new AllocationTransformer());
    }
}
