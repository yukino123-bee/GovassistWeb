@extends('errors::minimal')

@section('title', __('Too many requests'))
@section('code', '429')
@section('message')
    You have made too many requests in a short time. Please wait a minute and try again.
@endsection
