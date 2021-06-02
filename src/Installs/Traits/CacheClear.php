<?php
namespace App\Traits;

trait CacheClear
{

    /**
     * Boot function for Laravel model events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::updated(function () {
            Artisan::call('cache:clear');
        });
    }
}
