@extends('layouts.app')
@php $pageTitle = 'Create Package'; @endphp

@section('content')
  @include('packages._form', [
    'mode' => 'create',
    'submitRoute' => route('packages.store'),
    'submitMethod' => 'POST',
  ])
@endsection
