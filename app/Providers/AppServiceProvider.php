<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        View::composer('*', function ($view) {
            $viewData = $view->getData();
            if (!array_key_exists('user', $viewData) || is_null($viewData['user'])) {
                $user = Auth::user();
                if (!$user) {
                    try {
                        $user = User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();
                    } catch (\Throwable $e) {
                        $user = null;
                    }
                }
                $view->with('user', $user);
            }
        });

        // Register AuditObserver untuk pencatatan otomatis riwayat aktivitas & restore
        \App\Models\EggProduction::observe(\App\Observers\AuditObserver::class);
        \App\Models\FeedConsumption::observe(\App\Observers\AuditObserver::class);
        \App\Models\Mortality::observe(\App\Observers\AuditObserver::class);
        \App\Models\Quarantine::observe(\App\Observers\AuditObserver::class);
        \App\Models\WeightSample::observe(\App\Observers\AuditObserver::class);
        \App\Models\HealthTreatment::observe(\App\Observers\AuditObserver::class);
        \App\Models\FarmStock::observe(\App\Observers\AuditObserver::class);
        \App\Models\Coop::observe(\App\Observers\AuditObserver::class);
        \App\Models\Flock::observe(\App\Observers\AuditObserver::class);
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
        \App\Models\Setting::observe(\App\Observers\AuditObserver::class);
        \App\Models\WeeklyStandard::observe(\App\Observers\AuditObserver::class);
        \App\Models\UserPermission::observe(\App\Observers\AuditObserver::class);
    }
}
