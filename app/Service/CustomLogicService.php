<?php

namespace App\Service;

use App\Enums\CurrencyFormat;
use App\Enums\DateFormat;
use App\Enums\IntervalFormat;
use App\Enums\NumberFormat;
use App\Enums\Role;
use App\Enums\TimeFormat;
use App\Enums\Weekday;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CustomLogicService
{
    private UserService $userService;
    private OrganizationService $organizationService;

    public function __construct(UserService $userService, OrganizationService $organizationService)
    {
        $this->userService = $userService;
        $this->organizationService = $organizationService;
    }


    public function createUser(
        string $name,
        string $email,
        string $password,
        string $timezone,
        Weekday $weekStart,
        ?string $currency,
        ?NumberFormat $numberFormat = null,
        ?CurrencyFormat $currencyFormat = null,
        ?DateFormat $dateFormat = null,
        ?IntervalFormat $intervalFormat = null,
        ?TimeFormat $timeFormat = null,
        bool $verifyEmail = false
    ): User {
        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->timezone = $timezone;
        $user->week_start = $weekStart;
        if ($verifyEmail) {
            $user->email_verified_at = Carbon::now();
        }
        $user->save();

        $organization = Organization::find(config('auth.main_organization_id'));
        if($organization !== null){
            $memberService = app(MemberService::class);
            $memberService->addMember(
                $user,
                $organization,
                Role::Employee,
                true
            );
        }

        return $user;
    }
}
