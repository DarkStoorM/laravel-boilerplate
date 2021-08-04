<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

use App\Libs\Utils\NamedRoute;
use App\Http\Requests\LoginRequest;
use App\Providers\RouteServiceProvider;

class SessionsController extends Controller
{
    /** Displays the Login index - shows the Login form */
    public function sessionIndex(): View
    {
        return view("account.session-create");
    }

    /** Authenticates the user and redirects back to the Dashboard Index */
    public function sessionStore(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            // The following has to be taken with caution:
            // On (MAX_ATTEMPTS - 1), the last successful attempt will go through the RateLimiter
            // When we immediately try to log into another account, we will be blocked.
            // We can reset the Login Throttle on successful attempt, but this varies between the apps,
            // but __this can be abused__ by TrialAndError max_attempt lookup and logging in in-between
            //---
            // Throttle::Clear("login-throttle");

            // Although, we can't let the user is if he's not verified yet
            if (Auth::user()->is_verified === false) {
                $this->logout($request);

                flash_error(trans("login.unverified"));
                return redirect(route(NamedRoute::GET_SESSION_INDEX));
            }

            // We have to redirect to the index
            return redirect(route(RouteServiceProvider::HOME));
        } else {
            flash_error(trans("login.failed"));
            return redirect(route(NamedRoute::GET_SESSION_INDEX));
        }
    }

    /** Logs the user out of the application / regenerates the app CSRF token */
    public function sessionDestroy(Request $request): RedirectResponse
    {
        $this->logout($request);

        return redirect(route(RouteServiceProvider::HOME));
    }

    protected function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
