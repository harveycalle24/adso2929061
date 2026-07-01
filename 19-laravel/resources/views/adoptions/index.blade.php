@extends('layouts.app')

@section('title', 'Larapets: Adoptions')

@section('content')
    @include('partials.navbar')

    <h1 class="mt-6 text-4xl text-white flex gap-2 items-center justify-center pb-4 border-b-2 border-neutral-50 mb-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="currentColor" viewBox="0 0 256 256">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
        </svg>
        Make Adoptions
    </h1>

    <div class="card text-white w-full overflow-x-auto">
        @if ($adoptions->count())
            <table class="table w-full text-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Pet</th>
                        <th>Breed</th>
                        <th>Status</th>
                        <th>Requested</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($adoptions as $adoption)
                        <tr>
                            <td>{{ $adoption->id }}</td>
                            <td>{{ optional($adoption->user)->fullname ?? 'Unknown' }}</td>
                            <td>{{ optional($adoption->pet)->name ?? 'Unknown' }}</td>
                            <td>{{ optional($adoption->pet)->breed ?? 'Unknown' }}</td>
                            <td>{{ $adoption->status ?? 'Pending' }}</td>
                            <td>{{ $adoption->created_at?->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-6">
                {{ $adoptions->links() }}
            </div>
        @else
            <div class="p-8 rounded-lg bg-slate-900/40 text-center">
                <p class="text-lg">No adoption requests have been registered yet.</p>
            </div>
        @endif
    </div>
@endsection
