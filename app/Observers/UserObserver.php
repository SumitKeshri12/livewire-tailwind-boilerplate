<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Cache\TaggedCache;

class UserObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->clearPowerGridCache($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->clearPowerGridCache($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->clearPowerGridCache($user);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->clearPowerGridCache($user);
    }

    /**
     * Clear PowerGrid cache for all users.
     *
     * Note: PowerGrid uses cache tags with format: {prefix}{customTag}
     * Example: {userId}_powergrid-user-users
     * By clearing the base tag, all user-specific caches are invalidated.
     */
    private function clearPowerGridCache(User $user): void
    {
        try {
            // Use the same custom tag as defined in PowerGrid Table component
            $cacheTag = 'powergrid-user-users';

            // Clear the cache tag
            if (Cache::getStore() instanceof TaggedCache || method_exists(Cache::getStore(), 'tags')) {
                try {
                    Cache::tags([$cacheTag])->flush();
                } catch (\Exception $tagException) {
                    Log::debug('PowerGrid cache tag clearing failed (cache driver may not support tags): ' . $tagException->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to clear PowerGrid cache for User: ' . $e->getMessage());
        }
    }
}
