<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CreatedbyUpdatedby;
use App\Traits\CommonTrait;
use App\Traits\UploadTrait;
use App\Traits\ImportTrait;
use App\Traits\Legendable;

class Brand extends Model
{
    use SoftDeletes, CommonTrait, CreatedbyUpdatedby, HasFactory, UploadTrait, ImportTrait, Legendable;

    protected $fillable = [ 'country_id', 'status', 'bob', 'start_date', 'start_time' ];

    protected $casts = [

    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


            /**
             * The status relationship.
             */
            public static function status()
            {
                return collect(
                    [['key' => 'Y', 'label' => 'Active'], ['key' => 'N', 'label' => 'Inactive']]
                );
            }


    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string|null
     */
    public function getBrandImageAttribute($value)
    {
        if (!empty($value) && $this->is_file_exists($value)) {
            return $this->getFilePathByStorage($value);
        }
        return null;
    }
}
