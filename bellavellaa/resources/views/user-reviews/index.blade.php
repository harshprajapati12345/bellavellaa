@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">User Reviews</h2>
      <p class="text-sm text-gray-400 mt-1">Client ↔ professional reviews kept separate from service reviews.</p>
    </div>
    <div class="flex gap-2">
      @foreach(['all' => 'All', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'] as $key => $label)
        <a href="{{ route('user-reviews.index', ['status' => $key]) }}"
          class="px-4 py-2 rounded-xl border {{ $status === $key ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[1100px]">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50/80">
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Reviewer</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Reviewed</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Booking</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Rating</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Comment</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Type</th>
            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
            <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reviews as $review)
            @php
              $reviewer = $review->reviewer_role === 'client'
                ? ($review->booking?->customer?->name ?? 'Client')
                : ($review->booking?->professional?->name ?? 'Professional');
              $reviewed = $review->reviewed_role === 'client'
                ? ($review->booking?->customer?->name ?? 'Client')
                : ($review->booking?->professional?->name ?? 'Professional');
              $videoUrl = \App\Support\MediaPathNormalizer::url($review->video_path);
            @endphp
            <tr class="border-b border-gray-50">
              <td class="px-5 py-4 text-sm text-gray-700">
                <div class="font-semibold">{{ $reviewer }}</div>
                <div class="text-xs text-gray-400 capitalize">{{ $review->reviewer_role }}</div>
              </td>
              <td class="px-5 py-4 text-sm text-gray-700">
                <div class="font-semibold">{{ $reviewed }}</div>
                <div class="text-xs text-gray-400 capitalize">{{ $review->reviewed_role }}</div>
              </td>
              <td class="px-5 py-4 text-sm text-gray-600">#{{ $review->booking_id ?? '—' }}</td>
              <td class="px-5 py-4 text-sm text-gray-700">{{ $review->rating }}/5</td>
              <td class="px-5 py-4 text-sm text-gray-600 max-w-[280px]">{{ \Illuminate\Support\Str::limit($review->comment ?? '—', 90) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">
                <div class="flex items-center gap-3">
                  <span class="capitalize">{{ $review->content_type }}</span>
                  @if($review->content_type === 'video' && $videoUrl)
                    <video class="w-16 h-12 rounded-lg border border-gray-200 object-cover" controls playsinline>
                      <source src="{{ $videoUrl }}" type="video/mp4">
                    </video>
                  @endif
                </div>
              </td>
              <td class="px-5 py-4 text-sm">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                  {{ $review->status === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($review->status === 'Rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-700') }}">
                  {{ $review->status }}
                </span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  @if($review->status === 'Pending')
                    <form method="POST" action="{{ route('user-reviews.approve', $review->id) }}">
                      @csrf
                      <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-sm font-medium">
                        Approve
                      </button>
                    </form>
                    <form method="POST" action="{{ route('user-reviews.reject', $review->id) }}">
                      @csrf
                      <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium">
                        Reject
                      </button>
                    </form>
                  @else
                    <span class="text-xs text-gray-400">No actions</span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No user reviews found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
