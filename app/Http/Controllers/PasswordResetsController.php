<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

use App\Models\User;
use App\Models\PasswordReset;
use App\Libs\Utils\NamedRoute;
use App\Mail\MailablePasswordReset;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetChangeRequest;
use Illuminate\Database\QueryException;

class PasswordResetsController extends Controller
{
    /**
     * Displays the password Reset Form. This view serves a purpose of acquiring the reset link
     * and displaying the information about reset status (redirecting back with errors/success flash)
     */
    public function passwordResetIndex(): View
    {
        return view('account.password-reset.request');
    }

    /** Attempts to send the user an email with a password reset link */
    public function passwordResetStore(PasswordResetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        // For 'security' reasons, this should never show a failure to prevent bruteforce attacks
        // If the provided email does not exist, we will have to return a Success message anyway
        // and return back to the reset request - and failure specifically meaning, that the provided
        // email or token is invalid or the user does not exist

        $resetRequest = PasswordReset::where('email', $validated['email']);

        // Because we don't care if the request already exists, we only have to overwrite it
        // Create a new request if there is none in the database
        if ($resetRequest !== null) {
            $resetRequest->delete();
        }

        // We create a new token - only for the users that exist in the database
        $resetToken = PasswordReset::GenerateAndInsert($validated['email']);

        // Since we don't want to create a token for non-existing users, 
        // we will have to send an email only if it has been created,
        // but we still __have to__ show the "success" message anyway
        if ($resetToken !== null) {
            Mail::to($validated["email"])->send(new MailablePasswordReset($resetToken));
        }

        flash_success(trans('password_reset.reset-sent'));

        return redirect(route(NamedRoute::GET_PASSWORD_RESET_INDEX));
    }

    /** 
     * Displays the index page for the users who visit the Password Reset Activation link
     *
     * Clarification: user clicks the password reset link from the received Email
     */
    public function passwordResetActivate(): View
    {
        return view('account.password-reset.request');
    }

    /**
     * Validates the requested activation token and allows changing the password on success.
     *
     * Redirects back with a flashed error message, informing about expired/invalid token
     *
     * @param   string  $token   Password Reset identification token
     * @param   string  $email  Email address associated with the given token
     */
    public function passwordResetValidateToken(string $token, string $email): RedirectResponse
    {
        // Since this token can be changed by the potentially malicious user, 
        // we have to validate if that token actually exists
        return PasswordReset::IsValid($token, $email) === true
            ? redirect(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_CREATE, ['token' => $token, 'email' => $email]))
            : redirect(route(NamedRoute::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT));
    }

    /** Displays the results of Password Change and information that user can log in */
    public function passwordResetChangeResult(): View
    {
        return view("account.password-reset.results");
    }

    /**
     * Displays the Password Change form after token validation
     * 
     * @param   string  $token   Password Reset identification token
     * @param   string  $email  Email address associated with the given token
     */
    public function passwordResetChangeCreate(string $token, string $email): View|RedirectResponse
    {
        // We have to validate that token again, and in the same way
        if (PasswordReset::IsValid($token, $email) === false) {
            return redirect(route(NamedRoute::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT));
        }

        return view('account.password-reset.new-password')->with(['token' => $token, 'email' => $email]);
    }

    /**
     * Changes user's password and deletes the created password reset token
     */
    public function passwordResetChangeStore(PasswordResetChangeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Since this token can also be changed during 'form submission', it has to
        // be validated even on the final step, because here user can still send invalid
        // email or the one that belongs to a different user
        if (PasswordReset::IsValid($validated['token'], $validated['email']) === false) {
            return redirect(route(NamedRoute::GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT));
        }

        // The token is valid so we can just straight up update user's password
        // and redirect back to the result screen with a flashed message
        // We can also delete the used token in the process
        try {
            User::UpdatePassword($validated['email'], $validated['password']);

            // Throttle clear should be taken with caution, please refer to:
            // SessionsController -> successful sessionStore()
            // Throttle::Clear("throttle-password-throttle");
            PasswordReset::DeleteToken($validated["token"], $validated["email"]);
        } catch (QueryException $exception) {
            flash_error($exception->getMessage());
            return redirect(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_RESULT));
        }

        flash_success(trans('password_reset.password-changed'));

        return redirect(route(NamedRoute::GET_PASSWORD_RESET_CHANGE_RESULT));
    }
}
