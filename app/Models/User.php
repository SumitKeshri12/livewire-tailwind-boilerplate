<?php

namespace App\Models;

use App\Helper;
use App\Traits\CommonTrait;
use App\Traits\CreatedbyUpdatedby;
use App\Traits\ImportTrait;
use App\Traits\Legendable;
use App\Traits\Smsable;
use App\Traits\UploadTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use CommonTrait;
    use CreatedbyUpdatedby;
    use HasApiTokens;
    use HasFactory;
    use ImportTrait;
    use Legendable;
    use Notifiable;
    use SoftDeletes;
    use UploadTrait;
    use Smsable;


    // Removed sensitive fields from fillable to prevent mass assignment vulnerabilities:
    // email_verified_at, remember_token, otp, otp_expires_at
    // These fields should only be set explicitly in code, not through mass assignment
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'role_id',
        'dob',
        'profile',
        'country_id',
        'state_id',
        'city_id',
        'gender',
        'status',
        'password',
        'description',
        'comments',
        'skills',
        'bg_color',
        'timezone',
        'event_date',
        'event_datetime',
        'event_time',
        'document',
        'age',
        'consent_data',
    ];


    public $legend = ['{{users_name}}', '{{users_email}}'];

    protected $casts = [
        'skills' => 'array',
        'event_date' => 'date',
        'event_datetime' => 'datetime',
        'dob' => 'date',
        'document' => 'array',
        'consent_data' => 'array',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Boot the model.
     */
    // booted removed in favor of UserObserver

    // clearCache removed in favor of UserObserver

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the country that owns the user.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the user.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that owns the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the hobbies for the user.
     */
    public function userHobbies(): HasMany
    {
        return $this->hasMany(UserHobby::class);
    }

    /**
     * Get the documents for the user.
     */
    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * Get the languages for the user.
     */
    public function userLanguages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Get user initials
     */
    public function initials()
    {
        $name = $this->name ?? '';
        $words = explode(' ', trim($name));

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } elseif (count($words) == 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return 'U';
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string|null
     */
    public function getProfileAttribute($value)
    {
        if (! empty($value) && $this->is_file_exists($value)) {
            return $this->getFilePathByStorage($value);
        }

        return null;
    }

    public function hasPermission($permission, $roleId)
    {
        $permissions = Helper::getCachedPermissionsByRole($roleId);

        return in_array($permission, $permissions);
    }

    /**
     * Override delete method to clean up files
     */
    public function delete()
    {
        // Clean up hobbies before deleting the user
        $this->userHobbies()->delete();

        // Clean up languages before deleting the user
        $this->userLanguages()->delete();

        // Clean up files before deleting the user
        $this->cleanupUserFiles();

        return parent::delete();
    }

    /**
     * Clean up user files (profile images and documents)
     */
    public function cleanupUserFiles()
    {
        // Capture necessary data before deferring
        $userId = $this->id;
        $profilePath = null;
        if ($this->profile) {
            $profilePath = $this->getRawOriginal('profile');
        }

        // Get document paths before deferring
        $userDocuments = $this->userDocuments()->get();
        $documentPaths = $userDocuments->pluck('document_path')->filter()->toArray();

        // Defer file cleanup operations to run after response
        defer(function () use ($userId, $profilePath, $documentPaths) {
            try {
                // Clean up profile image
                if ($profilePath && Storage::disk('public')->exists($profilePath)) {
                    Storage::disk('public')->delete($profilePath);

                    // Delete resized images (inline logic to avoid $this in closure)
                    try {
                        $pathInfo = pathinfo($profilePath);
                        $directory = $pathInfo['dirname'];
                        $filename = $pathInfo['filename'];
                        $extension = $pathInfo['extension'] ?? '';

                        // Common resized image patterns
                        $patterns = [
                            $directory . '/' . $filename . '_thumb.' . $extension,
                            $directory . '/' . $filename . '_small.' . $extension,
                            $directory . '/' . $filename . '_medium.' . $extension,
                            $directory . '/' . $filename . '_large.' . $extension,
                        ];

                        foreach ($patterns as $pattern) {
                            if (Storage::disk('public')->exists($pattern)) {
                                Storage::disk('public')->delete($pattern);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete resized images for user ' . $userId . ': ' . $e->getMessage());
                    }
                }

                // Clean up documents from user_documents table
                foreach ($documentPaths as $documentPath) {
                    if ($documentPath && Storage::disk('public')->exists($documentPath)) {
                        Storage::disk('public')->delete($documentPath);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to cleanup files for user ' . $userId . ': ' . $e->getMessage());
            }
        });
    }

    /**
     * Delete resized images (thumbnails, etc.)
     */
    private function deleteResizedImages($originalPath)
    {
        try {
            $pathInfo = pathinfo($originalPath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'] ?? '';

            // Common resized image patterns
            $patterns = [
                $directory . '/' . $filename . '_thumb.' . $extension,
                $directory . '/' . $filename . '_small.' . $extension,
                $directory . '/' . $filename . '_medium.' . $extension,
                $directory . '/' . $filename . '_large.' . $extension,
            ];

            foreach ($patterns as $pattern) {
                if (Storage::disk('public')->exists($pattern)) {
                    Storage::disk('public')->delete($pattern);
                }
            }
        } catch (\Exception $e) {
            // Defer logging to run after response
            defer(function () use ($e) {
                Log::warning('Failed to delete resized images: ' . $e->getMessage());
            });
        }
    }

    public function routeNotificationForSMS()
    {
        return $this->mobile_number; // Mobile number for SMS
    }
}
