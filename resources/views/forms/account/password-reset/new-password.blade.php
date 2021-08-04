<div class="container">
    <div class="container__header">
        @lang("forms.password-reset.change-password-header")
    </div>
    <form method="POST" action="{{ route(NamedRoute::POST_PASSWORD_RESET_CHANGE_STORE, ['token' => $token, 'email' => $email]) }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="container__row">
            <input class="text-field text-field--full" id="new-password" type="password" name="password"
                placeholder="@lang('forms.password-reset.new-password')">
        </div>

        <div class="container__row">
            <input class="text-field text-field--full" id="new-password-confirmation" type="password" name="password_confirmation"
                placeholder="@lang('forms.password-reset.new-password-confirmation')">
        </div>
        <div class="container__row">
            <button class="button button--full" type="submit"
                dusk="button-password-reset-new-password">@lang('forms.password-reset.new-password-button')</button>
        </div>
    </form>
</div>
