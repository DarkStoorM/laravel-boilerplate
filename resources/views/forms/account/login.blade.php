<div class="container">
    <div class="container__header">
        @lang("forms.login.form-header")
    </div>
    <form method="POST" action="{{ route(NamedRoute::POST_SESSION_STORE) }}">
        @csrf
        <div class="container__row">
            <input placeholder="@lang('forms.login.email')" class="text-field text-field--full" name="email" type="email" value="{{ old('email') }}">
        </div>
        <div class="container__row">
            <input class="text-field text-field--full" placeholder="@lang('forms.login.password')" name="password" type="password">
        </div>
        <div class="container__row">
            <button class="button button--full" type="submit" dusk="button-login">@lang('forms.login.submit')</button>
        </div>
    </form>
    <div class="container__separator"></div>
    <div class="container__row container__footer">
        <a href="{{ route(NamedRoute::GET_PASSWORD_RESET_INDEX) }}" dusk="link-forgot-password">@lang('links.login.forgot-password')</a>
        @lang("links.login.sign-up")
    </div>
</div>
