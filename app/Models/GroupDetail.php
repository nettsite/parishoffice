<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupDetail extends Model
{
    protected $table = 'group_details';

    protected $primaryKey = 'group_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'group_id',
        'description',
        'group_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function groupType(): BelongsTo
    {
        return $this->belongsTo(GroupType::class);
    }
}
