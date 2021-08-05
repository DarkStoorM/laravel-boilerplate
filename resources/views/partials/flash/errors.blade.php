{{-- You can simply flash the text-only message by passing whatever to $empty parameter --}}
{{-- By default, this flasher has a body. Passing ["empty" => "value"] to thie view will 
        render the flashes without any body --}}
@if ($errors->any())
    @if (isset($empty) === false)
        <div class="container">
            <div class="container__row">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="container--error">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <ul>
            @foreach ($errors->all() as $error)
                <li class="container--error">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@endif
