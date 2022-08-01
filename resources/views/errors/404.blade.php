@extends('errors::illustrated-layout')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __($exception->getMessage() ? $exception->getMessage() : 'Not Found'))

@section('image')
<div style="background-image: url('{{asset('img/ul_logo.png')}}'); background-size:contain" class="absolute pin bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
