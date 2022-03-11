<?php

namespace App\Libs\Utils;

/**
 * Class providing Route Names.
 *
 * The structure of the route names:
 *
 * POST_PASSWORD_RESET_CHANGE_STORE
 * | 1| |      2     | |  3 | | 4 |
 *
 * 1: Method
 *
 * 2: Group 1
 *
 * 3: Group 2
 *
 * ---etc---
 *
 * 4: Action - as in: Create / Store / Update / etc
 *
 * Not required - can be completely skipped.
 */
class NamedRoute
{
    /* -- INDEX GROUP -- */
    public const GET_INDEX = 'index';

    /* -- DASHBOARD GROUP -- */
    public const GET_DASHBOARD_INDEX = 'dashboard-index';

    /* -- ACCOUNT CREATION GROUP -- */
    public const GET_ACCOUNT_CREATION_INDEX = 'account-create-new';
    public const POST_ACCOUNT_CREATION_STORE = 'account-create-store';
    /**
     * Account Verification token validation and account verification route
     */
    public const GET_ACCOUNT_CREATION_VERIFY = 'account-create-verify';
    /**
     * "Landing" / status page after Verification Token validation, account activation, and creation
     */
    public const GET_ACCOUNT_CREATION_STATUS = 'account-create-status';

    /* -- SESSION (LOGIN) GROUP -- */

    /**
     * This HAS TO remain unchanged due to Laravel Auth using "login" named route under the hood...
     */
    public const GET_SESSION_INDEX = 'login';
    public const POST_SESSION_STORE = 'account-session-store';
    public const GET_SESSION_DESTROY = 'account-session-destroy';

    /* -- PASSWORD RESET GROUP -- */

    /**
     * This route is the main view for the Password Reset: requesting a new link, where user provides his email address
     */
    public const GET_PASSWORD_RESET_INDEX = 'account-password-reset-new';
    /**
     * User Submits his requested email address, this creates a new Password Reset Token
     */
    public const POST_PASSWORD_RESET_STORE = 'account-password-reset-store';
    /**
     * This is the route that displays the results of Password Reset Token validation.
     * Note: this is not the results page displayed when user changes his password.
     *
     * @see GET_PASSWORD_RESET_CHANGE_RESULT
     */
    public const GET_PASSWORD_RESET_TOKEN_VALIDATION_RESULT = 'account-password-reset-result';
    /**
     * This route validates the code in the URI and allows changing password on success
     */
    public const GET_PASSWORD_RESET_VALIDATE_TOKEN = 'account-password-reset-validate-token';
    /**
     * This route is accessible to the users whose token is valid and email address matches the token
     */
    public const GET_PASSWORD_RESET_CHANGE_CREATE = 'account-password-reset-change-create';
    /**
     * Route accessed during password change form submission - note: this falls under Password Reset grou
     */
    public const POST_PASSWORD_RESET_CHANGE_STORE = 'account-password-reset-change-store';
    /**
     * Route displaying the password change results - note: this falls under Password Reset group
     */
    public const GET_PASSWORD_RESET_CHANGE_RESULT = 'account-password-reset-change-result';
}
