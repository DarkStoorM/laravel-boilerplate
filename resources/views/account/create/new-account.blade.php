@extends("main")

@section('content')
    @include("partials.flash.flasher")
    @include("partials.flash.errors")

    @if (session()->has('flash-success') === false)
        @include("forms.account.create")
    @endif
@endsection
