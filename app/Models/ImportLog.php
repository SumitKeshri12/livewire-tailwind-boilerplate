<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'import_csv_logs';

    protected $fillable = [
        'file_name',
        'file_path',
        'model_name',
        'user_id',
        'status',
        'import_flag',
        'voucher_email',
        'redirect_link',
        'no_of_rows',
        'error_log',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'error_log' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function status()
    {
        return [
            ['status' => 'P', 'label' => 'Pending'],
            ['status' => 'S', 'label' => 'Processing'],
            ['status' => 'Y', 'label' => 'Success'],
            ['status' => 'N', 'label' => 'Failed'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
