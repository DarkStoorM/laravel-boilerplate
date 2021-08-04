@extends('main')

@section('content')
    @include('partials.flash.flasher')
    @include('partials.flash.errors')

    @include("forms.account.password-reset.request")
@endsection
