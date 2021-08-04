<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

use App\Models\User;
use App\Libs\Utils\RouteNames;
use App\Models\VerificationToken;
use App\Mail\MailableVerificationToken;
use App\Http\Requests\AccountCreationRequest;

class AccountCreationsController extends Controller
{
    /** Displays the Account Creation form */
    public function index(): View
    {
        return view("account.create.new-account");
    }

    /** Stores new accounts in the Database and immediately authenticates the user on Success */
    public function store(AccountCreationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // The User password/email has passed the validation (unique)
        // No further checks should be needed here (?)
        try {
            $userData = User::CreateNew($validated);

            Mail::send(new MailableVerificationToken($userData));
        } catch (\Throwable $th) {
            $userData["user"]->delete();
            throw $th;
        }

        // We have to notify the new users about an additional step, which is
        // the account verification.
        flash_success(trans("account_create.created"));

        return redirect(route(RouteNames::GET_ACCOUNT_CREATION_STATUS));
    }

    /** Account Verification "status" page */
    public function status(): View
    {
        return view("account.create.status");
    }

    /** Account verification/activation page */
    public function verify(string $token, string $email): RedirectResponse
    {
        // Check if the Token is valid. If the token is used / expired / mismatched
        if (VerificationToken::IsValid($token, $email) === false) {
            // The error has already been flashed by the token validator
            return redirect(route(RouteNames::GET_ACCOUNT_CREATION_STATUS));
        }

        // Verify the user under given token
        try {
            User::where("email", $email)->first()->verify();
        } catch (\Throwable $th) {
            throw $th;
        }

        // Now we can notify the user that his account has been verified
        // and he can log into his account
        flash_success(trans("account_create.verified"));

        return redirect(route(RouteNames::GET_ACCOUNT_CREATION_STATUS));
    }
}
