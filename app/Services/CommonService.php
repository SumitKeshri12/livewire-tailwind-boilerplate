<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class CommonService
{
    /**
     * Get formatted status for display
     */
    public static function getStatusFormatted($status): string
    {
        if ($status === true || $status === config('constants.user.status.key.active') || $status === config('constants.user.status.value.active')) {
            return config('constants.user.status.value.active');
        }

        if ($status === false || $status === config('constants.user.status.key.inactive') || $status === config('constants.user.status.value.inactive')) {
            return config('constants.user.status.value.inactive');
        }

        return config('constants.user.table.default_values.not_set');
    }

    /**
     * Get formatted gender for display
     */
    public static function getGenderFormatted($gender): string
    {
        return config('constants.user.gender.labels')[$gender] ?? config('constants.user.table.default_values.not_set');
    }

    /**
     * Get formatted hobbies for display (from user_hobbies relationship)
     */
    public static function getHobbiesFormattedFromRelationship($userHobbies): string
    {
        if (! $userHobbies || $userHobbies->isEmpty()) {
            return config('constants.user.table.default_values.not_set');
        }

        // Get hobby keys from the relationship
        $hobbyKeys = $userHobbies->pluck('hobby')->toArray();

        if (empty($hobbyKeys)) {
            return config('constants.user.table.default_values.not_set');
        }

        // Get hobby labels from config
        $hobbyLabels = [];
        $hobbiesConfig = config('constants.user.hobbies');

        foreach ($hobbyKeys as $key) {
            if (isset($hobbiesConfig[$key])) {
                $hobbyLabels[] = $hobbiesConfig[$key];
            } else {
                $hobbyLabels[] = $key; // Fallback to key if not found in config
            }
        }

        return implode(', ', $hobbyLabels);
    }

    /**
     * Get formatted hobbies for display (from GROUP_CONCAT hobbies_list)
     */
    public static function getHobbiesFormattedFromList($hobbiesList): string
    {
        if (! $hobbiesList) {
            return config('constants.user.table.default_values.not_set');
        }

        // Split the comma-separated hobbies
        $hobbyKeys = array_filter(explode(',', $hobbiesList));

        if (empty($hobbyKeys)) {
            return config('constants.user.table.default_values.not_set');
        }

        // Get hobby labels from config
        $hobbyLabels = [];
        $hobbiesConfig = config('constants.user.hobbies');

        foreach ($hobbyKeys as $key) {
            $key = trim($key);
            if (isset($hobbiesConfig[$key])) {
                $hobbyLabels[] = $hobbiesConfig[$key];
            } else {
                $hobbyLabels[] = $key; // Fallback to key if not found in config
            }
        }

        return implode(', ', $hobbyLabels);
    }

    /**
     * Get formatted skills for display
     */
    public static function getSkillsFormatted($skills): string
    {
        if (! $skills) {
            return config('constants.user.table.default_values.not_set');
        }

        if (is_array($skills)) {
            return implode(', ', $skills);
        }

        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return implode(', ', $decoded);
            }

            return str_replace(['[', ']', '"'], '', $skills);
        }

        return config('constants.user.table.default_values.not_set');
    }

    /**
     * Get formatted event time for display
     */
    public static function getEventTimeFormatted($eventTime): string
    {
        if (! $eventTime) {
            return config('constants.user.table.default_values.not_set');
        }

        try {
            if ($eventTime instanceof Carbon) {
                return $eventTime->format(config('constants.validation_time_format'));
            }

            if (is_string($eventTime)) {
                $cleanTime = trim($eventTime);

                try {
                    $time = Carbon::createFromFormat('H:i:s', $cleanTime);

                    return $time->format(config('constants.validation_time_format'));
                } catch (\Exception $e) {
                    try {
                        $time = Carbon::createFromFormat('H:i', $cleanTime);

                        return $time->format(config('constants.validation_time_format'));
                    } catch (\Exception $e2) {
                        return $cleanTime;
                    }
                }
            }

            return $eventTime;
        } catch (\Exception $e) {
            return config('constants.user.table.default_values.invalid_time');
        }
    }

    /**
     * Get status badge class
     */
    public static function getStatusBadgeClass($status): string
    {
        // Check for Y/N (Active/Inactive) format
        if ($status === 'Y' || $status === config('constants.email_template.status.active')) {
            return config('constants.user.table.badge_classes.active');
        }

        if ($status === 'N' || $status === config('constants.email_template.status.inactive')) {
            return config('constants.user.table.badge_classes.inactive');
        }

        // Check for user format (Y/N)
        $formattedStatus = self::getStatusFormatted($status);

        if ($formattedStatus === config('constants.user.status.value.active')) {
            return config('constants.user.table.badge_classes.active');
        }

        if ($formattedStatus === config('constants.user.status.value.inactive')) {
            return config('constants.user.table.badge_classes.inactive');
        }

        return 'bg-gray-100 text-gray-800 border-gray-200';
    }

    /**
     * Get role badge class
     */
    public static function getRoleBadgeClass($roleName): string
    {
        $roleName = $roleName ?? config('constants.user.table.default_values.no_role');
        $roleColors = config('constants.user.table.badge_classes.roles');
        $noRoleClass = config('constants.user.table.badge_classes.no_role');

        if ($roleName === config('constants.user.table.default_values.no_role')) {
            return $noRoleClass;
        }

        return $roleColors[$roleName] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    }

    /**
     * Get role badge HTML
     */
    public static function getRoleBadge($roleName): string
    {
        $roleName = $roleName ?? config('constants.user.table.default_values.no_role');
        $colorClass = self::getRoleBadgeClass($roleName);

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $colorClass . '">' . $roleName . '</span>';
    }

    /**
     * Get status badge HTML
     */
    public static function getStatusBadge($status): string
    {
        $formattedStatus = self::getStatusFormatted($status);
        $colorClass = self::getStatusBadgeClass($status);

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $colorClass . '">' . $formattedStatus . '</span>';
    }

    /**
     * Format hobbies and skills data (generic method)
     */
    public static function formatHobbiesSkills($data, $defaultValue = null): string
    {
        $defaultValue = $defaultValue ?? config('constants.user.table.default_values.not_set');

        if (! $data) {
            return $defaultValue;
        }

        // Handle JSON encoded data from Create.php
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return implode(', ', $decoded);
            }

            // If not valid JSON, treat as string
            return str_replace(['[', ']', '"'], '', $data);
        }

        // Handle array data
        if (is_array($data)) {
            return implode(', ', $data);
        }

        return $defaultValue;
    }

    /**
     * Get profile image HTML
     */
    public static function getProfileImageHtml(User $user, $size = 'w-10 h-10'): string
    {
        // Get profile data directly from database column
        $profileData = $user->getAttributes()['profile'] ?? null;

        if ($profileData && ! empty($profileData)) {
            // Simplified approach - just try the direct asset URL first
            $profileUrl = asset('storage/' . $profileData);

            return '<div class="flex justify-center">
                        <img src="' . $profileUrl . '"
                             alt="Profile Image"
                             class="' . $size . ' rounded-full object-cover border-2 border-gray-200 hover:border-gray-300 transition-colors duration-200"
                             onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">
                        <div class="' . $size . ' rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-medium border-2 border-gray-200" style="display: none;">
                            ' . strtoupper(substr($user->name ?? 'U', 0, 2)) . '
                        </div>
                    </div>';
        }

        // Show initials as fallback
        $initials = $user->initials();

        return '<div class="flex justify-center">
                    <div class="' . $size . ' rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium border-2 border-gray-200 shadow-sm">
                        ' . $initials . '
                    </div>
                </div>';
    }

    /**
     * Get user initials
     */
    public static function getInitials(User $user): string
    {
        return $user->initials();
    }

    /**
     * Common navigation method for back to list
     */
    public static function backToList($component, $routeName)
    {
        return $component->redirect(route($routeName), navigate: true);
    }
}
