<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $internal_port
 * @property int $external_port
 * @property int $allocation_id
 * @property string $type
 * @property string $method
 * @property string $description
 * @property string|null $internal_address
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Pterodactyl\Models\Allocation $allocation
 *
 * @method static \Database\Factories\AllocationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Port extends Model
{
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const RESOURCE_NAME = 'port';

    /**
     * The table associated with the model.
     */
    protected $table = 'ports';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     */
    protected $hidden = ['method', 'internal_port', 'internal_address'];

    public function setDescriptionAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['description'] = $this->attributes['description'];
        } else {
            $this->attributes['description'] = $value;
        }
    }

    /**
     * Cast values to correct type.
     */
    protected $casts = [
        'allocation_id' => 'integer',
        'internal_port' => 'integer',
        'external_port' => 'integer',
    ];

    public static array $validationRules = [
        'allocation_id' => 'required|integer|exists:allocations,id',
        'internal_port' => 'nullable|integer|unique:ports,internal_port',
        'external_port' => 'required|integer|unique:ports,external_port',
        'type' => 'in:both,tcp,udp',
        'method' => 'in:upnp,pmp',
        'description' => 'nullable|string',
        'internal_address' => 'nullable|regex:/(\d\d\d).(\d\d\d).(\d)+.(\d)+/',
    ];

    /**
     * Default values for specific columns that are generally not changed on base installs.
     */
    protected $attributes = [
        'description' => 'Pterodactyl allocated Port',
        'type' => 'both',
        'method' => 'upnp',
        'internal_address' => null,
        'internal_port' => null,
    ];

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(Allocation::class);
    }
}
