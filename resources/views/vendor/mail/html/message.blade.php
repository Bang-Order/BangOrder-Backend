@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => env('FRONTEND_URL')])
{{--{{ config('app.name') }}--}}
{{--<img src="{{asset('assets/BangOrder_logo.png')}}" alt="{{config('app.name')}}">--}}
<img src="https://firebasestorage.googleapis.com/v0/b/bangorder-db7d2.appspot.com/o/assets%2FBangOrder_logo.png?alt=media" alt="{{config('app.name')}}">
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
