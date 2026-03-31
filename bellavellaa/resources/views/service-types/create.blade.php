@extends('layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Service Type</h2>
      <p class="text-sm text-gray-400 mt-0.5">Create the level 3 node under a service group.</p>
    </div>

    <form method="POST" action="{{ route('service-types.store') }}" enctype="multipart/form-data" class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
      @csrf
      @php($serviceType = $serviceType ?? null)
      @include('service-types._form')
      <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('service-types.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="rounded-xl bg-black px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Save Service Type</button>
      </div>
    </form>
  </div>
@endsection
