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

class BrandDetail extends Model
{
    use SoftDeletes, CommonTrait, CreatedbyUpdatedby, HasFactory, UploadTrait, ImportTrait, Legendable;

    protected $fillable = [ 'brand_id', 'description', 'status', 'brand_image' ];

    protected $casts = [
        
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    

    
}
