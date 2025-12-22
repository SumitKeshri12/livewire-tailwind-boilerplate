<?php
namespace App\Models;

use App\Traits\CommonTrait;
use App\Traits\CreatedbyUpdatedby;
use App\Traits\Legendable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends Model
{
    use SoftDeletes, CommonTrait, CreatedbyUpdatedby, HasFactory, Legendable;

    protected $fillable = ['template_name', 'message', 'template_id', 'status', 'type'];

    protected $casts = [];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public static function status()
    {
        return collect(
            [
                ['key' => config('constants.sms_template.status.key.active'), 'label' => config('constants.sms_template.status.value.active')],
                ['key' => config('constants.sms_template.status.key.inactive'), 'label' => config('constants.sms_template.status.value.inactive')],
            ]
        );
    }

}