@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-6">
  @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <div class="flex items-center gap-4">
    <a href="{{ route('reviews.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
    <div><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Review</h2></div>
  </div>
  <form method="POST" action="{{ route('reviews.store') }}">@csrf
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6">
      <div class="px-8 pt-7 pb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-5">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-400">*</span></label><input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all" required></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Service</label><input type="text" name="service_name" value="{{ old('service_name') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all"></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Rating</label><select name="rating" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all cursor-pointer">@for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} ★</option>@endfor</select></div>
        </div>
        <div class="space-y-5">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Comment</label><textarea name="comment" rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all resize-none">{{ old('comment') }}</textarea></div>
          <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="status" checked class="w-4 h-4 accent-black"><span class="text-sm text-gray-700">Published</span></label>
        </div>
      </div>
    </div>
    <div class="flex justify-end gap-3"><a href="{{ route('reviews.index') }}" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-medium">Cancel</a><button type="submit" class="px-6 py-3 rounded-xl bg-black text-white font-medium hover:bg-gray-800 transition-all">Add Review</button></div>
  </form>
</div>
@endsection
