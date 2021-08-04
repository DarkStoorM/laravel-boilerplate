@php
$uri = route(NamedRoute::GET_ACCOUNT_CREATION_VERIFY, ['token' => $token->token, 'email' => $user->email]);
$timeToExpire = Constants::VERIFICATION_TOKEN_EXPIRE_TIME;
@endphp

@component('mail::message')
# @lang('mailable.common.welcome')


@lang("mailable.account-verification.header")


@lang('mailable.account-verification.body', ["timeToExpire" => $timeToExpire])


@component('mail::button', ['url' => $uri])
    @lang("mailable.account-verification.button")
@endcomponent


---


**@lang('mailable.account-verification.report')**
@endcomponent
