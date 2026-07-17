@extends('layouts.facilitator')

@section('title', 'Manage Announcements - GovAssist')

@section('page_title', 'Office Announcements Bulletin')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Announcements List (2/3 width) -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            Current Bulletins & Notices
        </h3>

        <div class="space-y-4">
            @forelse($announcements as $ann)
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex items-start justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-black text-slate-800 block capitalize">{{ $ann->title_en }}</span>
                            <span class="text-[10px] text-slate-400 font-bold block">{{ $ann->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $ann->content_en }}</p>
                        <p class="text-xs text-slate-400 italic border-l-2 border-slate-200 pl-2 mt-1 leading-relaxed">{{ $ann->content_ceb }}</p>
                    </div>

                    <form action="{{ route('facilitator.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-700 focus:outline-none p-1 rounded hover:bg-rose-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-6 text-center text-slate-400 font-medium">No bulletins posted yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Create Announcement (1/3 width) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 h-fit">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
            </svg>
            Create Announcement
        </h3>

        <form action="{{ route('facilitator.announcements.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Title English -->
            <div class="space-y-1.5">
                <label for="title_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Title (English)</label>
                <input type="text" name="title_en" id="title_en" placeholder="e.g. Scholarship Application Extension" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <!-- Title Cebuano -->
            <div class="space-y-1.5">
                <label for="title_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Title (Cebuano)</label>
                <input type="text" name="title_ceb" id="title_ceb" placeholder="e.g. Pagpalugway sa Aplikasyon sa Scholarship" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required>
            </div>

            <!-- Content English -->
            <div class="space-y-1.5">
                <label for="content_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Content (English)</label>
                <textarea name="content_en" id="content_en" rows="3" placeholder="Write bulletin message here..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <!-- Content Cebuano -->
            <div class="space-y-1.5">
                <label for="content_ceb" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Content (Cebuano)</label>
                <textarea name="content_ceb" id="content_ceb" rows="3" placeholder="Isulat ang mensahe sa pahibalo dinhi..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition-all text-xs text-slate-800" required></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-red-700 hover:bg-red-800 text-white rounded-xl font-bold text-xs tracking-wider shadow-md shadow-red-950/20 transition-all active:scale-[0.98]">
                Publish Bulletin
            </button>
        </form>
    </div>

</div>
@endsection
