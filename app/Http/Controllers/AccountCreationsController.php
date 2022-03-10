<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountCreationRequest;
use App\Libs\Utils\NamedRoute;
use App\Mail\MailableVerificationToken;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AccountCreationsController extends Controller
{
    /** Displays the Account Creation form */
    public function index(): View
    {
        return view('account.create.new-account');
    }

    /** Stores new accounts in the Database and immediately authenticates the user on Success */
    public function store(AccountCreationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // The User password/email has passed the validation (unique)
        // No further checks should be needed here (?)
        try {
            $userData = User::createNew($validated);

            Mail::send(new MailableVerificationToken($userData));
        } catch (QueryException $exception) {
            $userData['user']->delete();
            throw $exception->getMessage();
        }

        // We have to notify the new users about an additional step, which is
        // the account verification.
        flash_success(trans('account_create.created'));

        return redirect(route(NamedRoute::GET_ACCOUNT_CREATION_STATUS));
    }

    /** Account Verification "status" page */
    public function status(): View
    {
        return view('account.create.status');
    }

    /** Account verification/activation page */
    public function verify(string $token, string $email): RedirectResponse
    {
        // Check if the Token is valid. If the token is used / expired / mismatched
        if (VerificationToken::isValid($token, $email) === false) {
            // The error has already been flashed by the token validator
            return redirect(route(NamedRoute::GET_ACCOUNT_CREATION_STATUS));
        }

        // Verify the user under given token
        try {
            User::where('email', $email)->first()->verify();
        } catch (QueryException $exception) {
            throw $exception->getMessage();
        }

        // Now we can notify the user that his account has been verified
        // and he can log into his account
        flash_success(trans('account_create.verified'));

        return redirect(route(NamedRoute::GET_ACCOUNT_CREATION_STATUS));
    }
}
