@extends('errors::illustrated-layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))

@section('image')
<div style="background-image: url('{{asset('img/ul_logo.png')}}'); background-size:contain" class="absolute pin bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
