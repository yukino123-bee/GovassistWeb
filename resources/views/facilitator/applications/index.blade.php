@extends('layouts.facilitator')

@section('title', 'Manage Applications - GovAssist')

@section('page_title', 'Resident Applications')

@section('content')
<div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
    <!-- Section Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/40">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center">
            <span class="w-2.5 h-2.5 bg-red-700 mr-2 block"></span>
            Application Submissions Manager
        </h3>

        <!-- Program Filter Dropdown -->
        <form action="{{ route('facilitator.applications') }}" method="GET" class="flex items-center space-x-2">
            <label for="service_id" class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Filter by Program</label>
            <select name="service_id" id="service_id" onchange="this.form.submit()" class="bg-white border border-slate-200 text-xs px-3.5 py-1.5 rounded-xl font-bold text-slate-700 cursor-pointer focus:outline-none focus:border-red-700">
                <option value="">All Programs</option>
                @foreach($services as $svc)
                    <option value="{{ $svc->id }}" {{ request('service_id') == $svc->id ? 'selected' : '' }}>{{ $svc->service_name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50">
                    <th class="px-6 py-3.5">Resident</th>
                    <th class="px-6 py-3.5">Service Program</th>
                    <th class="px-6 py-3.5">Date Applied</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800 block text-xs">{{ $app->user->name }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">{{ $app->user->email }}</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700 text-xs">{{ $app->service->name_en }}</td>
                        <td class="px-6 py-4 text-slate-400 text-xs">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4">
                            @if($app->status === 'pending')
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-extrabold rounded-xl border border-amber-200 uppercase tracking-wide">Pending</span>
                            @elseif($app->status === 'approved')
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-xl border border-emerald-200 uppercase tracking-wide">Approved</span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-xs font-extrabold rounded-xl border border-rose-200 uppercase tracking-wide">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('facilitator.applications.show', $app->id) }}" class="px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-extrabold uppercase rounded-xl shadow-xs tracking-wider transition-all inline-block">
                                Process &rarr;
                            </a>
                            <form action="{{ route('facilitator.applications.destroy', $app->id) }}" method="POST" class="inline ml-2" id="delete-app-{{ $app->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showConfirmModal('Are you sure you want to delete this application?', () => document.getElementById('delete-app-{{ $app->id }}').submit())" class="text-xs font-extrabold uppercase tracking-widest text-red-400 hover:text-red-700 transition-colors focus:outline-none ml-2">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">No application submissions received yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
