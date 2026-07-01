@extends('layouts.app')

@section('title', 'Larapets: Make Adoptions')

@section('content')
    @include('partials.navbar')

    <div class="mt-6 text-white">
        <h1 class="text-4xl flex gap-2 items-center justify-center pb-4 border-b-2 border-neutral-50 mb-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="currentColor" viewBox="0 0 256 256">
                <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
            </svg>
            Make Adoptions
        </h1>

        @if ($pets->count())
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($pets as $pet)
                    <div class="card p-4 bg-slate-900/70">
                        <div class="flex flex-col items-center gap-3 text-center">
                            <img src="{{ asset($pet->image ?? 'images/dashboard/modulo-pets.png') }}" alt="{{ $pet->name }}"
                                class="w-full h-48 object-cover rounded-lg">
                            <h2 class="text-xl font-semibold">{{ $pet->name }}</h2>
                            <p>{{ $pet->kind }} - {{ $pet->breed }}</p>
                            <p>{{ $pet->location }}</p>
                            <p class="text-sm text-slate-300">{{ strlen($pet->description) > 80 ? substr($pet->description, 0, 80) . '...' : $pet->description }}</p>
                            <button class="btn btn-primary btn-sm text-white" disabled>Request Adoption</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 rounded-lg bg-slate-900/40 text-center">
                <p class="text-lg">No pets are available for adoption at the moment.</p>
            </div>
        @endif
    </div>
@endsection
