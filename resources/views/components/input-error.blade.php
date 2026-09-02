@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'field-error']) }} style="list-style:none; padding:0; margin:0.25rem 0 0;">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
