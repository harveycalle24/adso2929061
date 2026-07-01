@extends('layouts.app')

@section('title', 'Larapets: My Adoptions')

@section('content')
    @include('partials.navbar')

    <h1 class="mt-6 text-4xl text-white flex gap-2 items-center justify-center pb-4 border-b-2 border-neutral-50 mb-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="currentColor" viewBox="0 0 256 256">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
        </svg>
        Mis Adopciones
    </h1>

    <div class="flex justify-center mb-6">
        <a href="{{ url()->previous() }}" class="btn btn-outline btn-secondary">Volver</a>
    </div>

    <div class="w-full">
        @if ($adoptions->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($adoptions as $adoption)
                    <div class="card bg-black/50 backdrop-blur-md shadow-xl text-white">
                        <figure class="relative">
                            <img src="{{ asset(optional($adoption->pet)->image == 'no-image.png' ? 'images/pets/no-image.png' : optional($adoption->pet)->image) }}" alt="{{ optional($adoption->pet)->name }}" class="w-full h-48 object-cover rounded-t-lg" />
                            <div class="absolute top-2 left-2 w-12 h-12 rounded-full overflow-hidden border-2 border-white">
                                <img src="{{ asset(auth()->user()->photo ?? 'images/users/default.png') }}" alt="Tu foto" class="w-full h-full object-cover" />
                            </div>
                        </figure>
                        <div class="card-body p-4">
                            <h2 class="card-title text-lg">{{ optional($adoption->pet)->name ?? 'Desconocida' }}</h2>
                            <p class="text-sm text-gray-300">Raza: {{ optional($adoption->pet)->breed ?? 'Desconocida' }}</p>
                            <p class="text-sm text-gray-300">Tipo: {{ optional($adoption->pet)->kind ?? 'Desconocido' }}</p>
                            <p class="text-sm text-gray-300">Solicitado: {{ $adoption->created_at?->format('d/m/Y') ?? '-' }}</p>
                            <div class="card-actions justify-end mt-4">
                                <a href="{{ url('pets/' . optional($adoption->pet)->id . '?from=myadoptions') }}" class="btn btn-primary btn-sm">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 flex justify-center">
                {{ $adoptions->links() }}
            </div>
        @else
            <div class="p-8 rounded-lg bg-slate-900/40 text-center">
                <p class="text-lg">No se encontraron solicitudes de adopción para tu cuenta.</p>
            </div>
        @endif
    </div>
@endsection
