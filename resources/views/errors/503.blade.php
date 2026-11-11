@extends('errors::minimal')

@section('title', __('Under maintenance'))
@section('code', '503')
@section('message')
    The service is under maintenance right now. Please check back soon.
@endsection
