<?php

namespace App\Http\Controllers\Web;

use App\Enums\Weekday;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\CustomLogicService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SSOController extends Controller
{
    public function login(): SymfonyRedirectResponse
    {
        return Socialite::driver('azure')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try{
            $azureUser = Socialite::driver('azure')->user();
        }catch(\Exception $e) {
            $attemps = session()->get('azure_login_attempts', 0);
            if($attemps >= 3) {
                Log::warning('Azure login failed multiple times: '.$e->getMessage());
                return abort(403, 'Azure login failed multiple times. Please contact support.');
            }
            session()->put('azure_login_attempts', $attemps + 1);
            return redirect('/login')->withErrors(['login' => 'Azure login failed. Please try again.']);
        }
        if($azureUser == null || $azureUser->getEmail() == null) {
            Log::info('Azure login failed: No user or email returned');
            return redirect('/login')->withErrors(['login' => 'Azure login failed. Please try again.']);
        }
        Log::info('Azure User:', (array) $azureUser);
        $user = User::where('email', $azureUser->getEmail())->where('is_placeholder', false)->first();

        if($user === null) {
            Log::info('No user found with gmail: '.$azureUser->getEmail());
            $customLogicService = app(CustomLogicService::class);
            $password = bin2hex(random_bytes(16));
            // Log::info('Creating user '. $azureUser->getEmail() .' with random password: '.$password);
            $user = $customLogicService->createUser(
                $azureUser->getName() ?? $azureUser->getNickname() ?? 'No Name',
                $azureUser->getEmail(),
                $password, // Generate a random password
                'Europe/Copenhagen',
                Weekday::Monday,
                'DKK',
                null,
                null,
                null,
                null,
                null,
                true
            );
        }
        Auth::login($user, true);
        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        Log::info('User logged out', ['user_id' => Auth::id()]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
