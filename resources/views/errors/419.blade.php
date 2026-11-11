@extends('errors::minimal')

@section('title', __('Session expired'))
@section('code', '419')
@section('message')
    Your session expired. Your form was not sent, so please refresh the page and try again.
    <a href="{{ url('/') }}" class="underline ml-1">Go to the home page</a>
@endsection
