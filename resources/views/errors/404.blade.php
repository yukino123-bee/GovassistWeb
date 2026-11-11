@extends('errors::minimal')

@section('title', __('Page not found'))
@section('code', '404')
@section('message')
    We could not find that page. The link may be wrong or the page may have moved.
    <a href="{{ url('/') }}" class="underline ml-1">Go to the home page</a>
@endsection
