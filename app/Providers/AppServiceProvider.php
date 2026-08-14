<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Services\Payment\ManualPaymentGateway;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, ManualPaymentGateway::class);
    }

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole(Role::SUPER_ADMIN) ? true : null;
        });

        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
