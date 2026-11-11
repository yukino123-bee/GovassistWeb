@extends('errors::minimal')

@section('title', __('Access denied'))
@section('code', '403')
@section('message')
    You do not have access to this page. If you think this is a mistake, sign in with the right account.
    <a href="{{ url('/') }}" class="underline ml-1">Go to the home page</a>
@endsection
