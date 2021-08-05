<div class="container">
    <div class="container__header">
        @lang("forms.password-reset.form-header")
    </div>
    <div class="container__row">
        @lang("forms.password-reset.form-description")
    </div>
    <form method="POST" action="{{ route(NamedRoute::POST_PASSWORD_RESET_STORE) }}">
        @csrf

        <div class="container__row">
            <input class="text-field text-field--full" id="password-reset-email" type="email" name="email"
                placeholder="@lang('forms.password-reset.request-email')">
        </div>
        <div class="container__row">
            <button class="button button--full" type="submit" dusk="button-password-reset-request">@lang('forms.password-reset.request-button')</button>
        </div>
    </form>
</div>
