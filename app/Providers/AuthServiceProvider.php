<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\TaskType;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use App\Policies\ClientPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ReceiptPolicy;
use App\Policies\TaskTypePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Task::class => TaskPolicy::class,
        TaskType::class => TaskTypePolicy::class,
        Invoice::class => InvoicePolicy::class,
        Receipt::class => ReceiptPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
