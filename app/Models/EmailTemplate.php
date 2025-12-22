<?php

namespace App\Models;

use App\Traits\CreatedbyUpdatedby;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use CreatedbyUpdatedby;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['id', 'type', 'label', 'subject', 'body', 'status', 'created_by', 'updated_by'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [];

    protected $casts = [
        'id' => 'string',
        'type' => 'string',
        'subject' => 'string',
        'body' => 'string',
    ];

    /**
     * Get the status relationship.
     */
    public static function status()
    {
        return collect(
            [['key' => 'N', 'label' => 'Inactive'], ['key' => 'Y', 'label' => 'Active']]
        );
    }

    /**
     * Get the type values.
     */
    public static function types()
    {
        return collect(
            config('constants.email_template.type_values')
        )->map(fn ($label, $key) => ['key' => $key, 'label' => $label])
            ->values()
            ->toArray();
    }
}
