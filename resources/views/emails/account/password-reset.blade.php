@php
$uri = route(NamedRoute::GET_PASSWORD_RESET_VALIDATE_TOKEN, ['token' => $token->token, 'email' => $user->email]);
@endphp

@component('mail::message')
# @lang('mailable.common.hello', ['user' => $user->name])


@lang("mailable.password-reset.header", ['email' => $user->email])


@lang('mailable.password-reset.body')


@component('mail::button', ['url' => $uri])
@lang("mailable.password-reset.button")
@endcomponent


---


**@lang('mailable.common.report')**
@endcomponent
