@extends('layouts.app')
@php $pageTitle = 'Edit Package'; @endphp

@section('content')
  @include('packages._form', [
    'mode' => 'edit',
    'submitRoute' => route('packages.update', $package->id),
    'submitMethod' => 'PUT',
  ])
@endsection
