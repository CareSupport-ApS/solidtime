<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\RemovePlaceholder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;
use Laravel\Jetstream\Events\TeamMemberAdded;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;


class EventServiceProvider extends BaseServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TeamMemberAdded::class => [
            RemovePlaceholder::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Azure\AzureExtendSocialite::class.'@handle',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
