<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Point $location
 * @property-read \App\Models\TypeOcurrence|null $type
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\OcurrenceFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ocurrence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ocurrence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ocurrence query()
 *
 * @mixin \Eloquent
 */
class Ocurrence extends Model
{
    use HasFactory;

    protected $table = 'ocurrences';

    protected $fillable = [
        'location',
        'type_id',
        'user_id',
        'description',
        'is_active',
        'address_name',
        'city',
        'state',
        'country',
        'solution_description',
        'type_closure',
        'resolution_date',
    ];

    protected $casts = [
        'location'        => Point::class,
        'is_active'       => 'boolean',
        'resolution_date' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeOcurrence::class, 'type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
