<div class="container">
    <div class="container__header">
        @lang("forms.account-creation.form-header")
    </div>
    <form method="POST" action="{{ route(NamedRoute::POST_ACCOUNT_CREATION_STORE) }}">
        @csrf
        <div class="container__row">
            <input class="text-field text-field--full" name="email" placeholder="@lang('forms.account-creation.email')" type="email"
                value="{{ old('email') }}">
        </div>
        <div class="container__row">
            <input class="text-field text-field--full" name="email_confirmation" placeholder="@lang('forms.account-creation.email-confirmation')" type="email"
                value="{{ old('email_confirmation') }}">
        </div>
        <div class="container__row">
            <input class="text-field text-field--full" placeholder="@lang('forms.account-creation.password')" name="password" type="password">
        </div>
        <div class="container__row">
            <input class="text-field text-field--full" placeholder="@lang('forms.account-creation.password-confirmation')" name="password_confirmation"
                type="password">
        </div>
        <div class="container__row">
            <button class="button button--full" type="submit" dusk="button-register">@lang('forms.account-creation.submit-button')</button>
        </div>
    </form>
    <div class="container__separator"></div>
    <div class="container__row container__footer">
        @lang("forms.account-creation.form-footer")
    </div>
</div>
