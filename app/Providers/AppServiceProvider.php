<?php

namespace App\Providers;

use App\Models\Organization;
use App\Models\Story;
use App\Services\ConnectionLoader;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        try {
            $stories = Story::select('id', 'title', 'slug', 'description')
                ->published()
                ->orderBy('updated_at')
                ->take(6)
                ->get();
        } catch (\Exception $exception) {
            $stories = collect();
        }

        (new ConnectionLoader())();
        Blade::if('connectible', function ($value) {
            try {
                DB::connection($value)->getPdo();
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        });

        try {
            $org = Organization::first();
        } catch (\Exception $exception) {
            $org = null;
        }

        View::share(['stories' => $stories, 'org' => $org]);

        /*if (config('dissemination.hosting')) {
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/' . config('dissemination.hosting') . '/livewire/update', $handle);
            });

            Livewire::setScriptRoute(function ($handle) {
                return Route::get('/' . config('dissemination.hosting') . '/livewire/livewire.js', $handle);
            });
        }*/
    }
}
