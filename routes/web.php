<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Role;
use App\Enums\Weekday;
use App\Http\Controllers\Web\SSOController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Middleware\SetOrganizationMiddleware;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use App\Service\CustomLogicService;
use App\Service\MemberService;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Laravel\Socialite\Facades\Socialite;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::pattern('tenant', '[a-zA-Z0-9\.-]+');

//TODO: middleware for SSO routes, that sets organization based on domain, and sets service configuration. Adds user to that organization.
Route::
    // domain('{tenant}')
    middleware(SetOrganizationMiddleware::class)
    ->group(function () {
        Route::get('/login', [SSOController::class, 'login'])->name('login');

        Route::get('/login/azure/callback', [SSOController::class, 'callback'])->name('login.azure.callback');

        Route::post('/logout', [SSOController::class, 'logout'])->name('logout');
});

Route::get('/', [HomeController::class, 'index']);

Route::get('/shared-report', function () {
    return Inertia::render('SharedReport');
})->name('shared-report');

Route::middleware([
    'auth:web',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/time', function () {
        return Inertia::render('Time');
    })->name('time');

    Route::get('/calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar');

    Route::get('/reporting', function () {
        return Inertia::render('Reporting');
    })->name('reporting');

    Route::get('/reporting/detailed', function () {
        return Inertia::render('ReportingDetailed');
    })->name('reporting.detailed');

    Route::get('/reporting/shared', function () {
        return Inertia::render('ReportingShared');
    })->name('reporting.shared');

    Route::get('/projects', function () {
        return Inertia::render('Projects');
    })->name('projects');

    Route::get('/projects/{project}', function () {
        return Inertia::render('ProjectShow');
    })->name('projects.show');

    Route::get('/clients', function () {
        return Inertia::render('Clients');
    })->name('clients');

    Route::get('/members', function () {
        return Inertia::render('Members', [
            'availableRoles' => array_values(Jetstream::$roles),
        ]);
    })->name('members');

    Route::get('/tags', function () {
        return Inertia::render('Tags');
    })->name('tags');

    Route::get('/import', function () {
        return Inertia::render('Import');
    })->name('import');

});
