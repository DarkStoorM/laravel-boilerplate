@extends('main')

@section('content')
    <div class="container container--big">
        <div class="container__header">
            <h1>@lang("account_create.verification-page-header")</h1>
        </div>
        <div class="container__row">
            @if (session()->has('error-generic') || session()->has('success-generic'))
                @include('partials.flash.flasher', ["empty" => true])
            @else
                @lang("account_create.verified-reminder")
            @endif
        </div>
    </div>
@endsection
