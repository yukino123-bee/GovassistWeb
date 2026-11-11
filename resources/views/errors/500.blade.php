@extends('errors::minimal')

@section('title', __('Something went wrong'))
@section('code', '500')
@section('message')
    Something went wrong on our side. Please try again in a few moments. If it keeps happening, let the facilitator know.
    <a href="{{ url('/') }}" class="underline ml-1">Go to the home page</a>
@endsection
