{{-- You can simply flash the text-only message by passing whatever to $empty parameter --}}
{{-- By default, this flasher has a body. Passing ["empty" => "value"] to thie view will 
        render the flashes without any body --}}
@if (session()->has('success-generic'))
    @if (isset($empty) === false)
        <div class="container">
            <div class="container__row">
                {{ session()->get('success-generic') }}
            </div>
        </div>
    @else
        {{ session()->get('success-generic') }}
    @endif
@endif

@if (session()->has('error-generic'))
    @if (isset($empty) === false)
        <div class="container">
            <div class="container__row container--error">
                {{ session()->get('error-generic') }}
            </div>
        </div>
    @else
        {{ session()->get('error-generic') }}
    @endif
@endif

@if (session()->has('generic'))
    @if (isset($empty) === false)
        <div class="container">
            <div class="container__row">
                {{ session()->get('generic') }}
            </div>
        </div>
    @else
        {{ session()->get('generic') }}
    @endif
@endif
