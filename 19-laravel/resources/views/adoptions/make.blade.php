@extends('layouts.app')

@section('title', 'Larapets: Make Adoption')

@section('content')
    @include('partials.navbar')

    <h1 class="mt-6 text-4xl text-white flex gap-2 items-center justify-center pb-4 border-b-2 border-neutral-50 mb-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-12" fill="currentColor" viewBox="0 0 256 256">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
        </svg>
        Módulo de Adopciones
    </h1>

    <div class="flex justify-center mb-6">
        <a href="{{ url()->previous() }}" class="btn btn-outline btn-secondary mr-4">Volver</a>
        <a href="{{ route('adoptions.my') }}" class="btn btn-outline btn-info">Mis Adopciones</a>
    </div>

    @if(session('message'))
        <div class="p-4 mb-6 rounded-lg bg-emerald-600/80 text-white text-center">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 rounded-lg bg-red-600/80 text-white text-center">
            {{ session('error') }}
        </div>
    @endif

    @if ($pets->count())
        <div class="overflow-x-auto mb-10 rounded-box border border-base-content/5 bg-black/50">
            <table class="table w-full text-white">
                <thead class="text-white bg-black">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Kind</th>
                        <th>Weight</th>
                        <th>Age</th>
                        <th>Breed</th>
                        <th>Location</th>
                        <th class="hidden md:table-cell">Description</th>
                        <th>Adopted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pets as $pet)
                        <tr class="even:bg-white/10">
                            <td>{{ $pet->id }}</td>
                            <td>{{ $pet->name }}</td>
                            <td>
                                <div class="avatar">
                                    <div class="mask mask-squircle w-24 bg-white">
                                        <img class="object-contain w-full h-full"
                                            src="{{ asset($pet->image == 'no-image.png' ? 'images/pets/no-image.png' : $pet->image) }}" />
                                    </div>
                                </div>
                            </td>
                            <td>{{ $pet->kind }}</td>
                            <td>{{ $pet->weight }}</td>
                            <td>{{ $pet->age }}</td>
                            <td>{{ $pet->breed }}</td>
                            <td>{{ $pet->location }}</td>
                            <td class="hidden md:table-cell">{{ strlen($pet->description) > 60 ? substr($pet->description, 0, 60) . '...' : $pet->description }}</td>
                            <td>
                                @if($pet->adopted)
                                    <span class="badge badge-success">Adoptada</span>
                                @else
                                    <span class="badge badge-warning">Disponible</span>
                                @endif
                            </td>
                            <td class="flex flex-col gap-2">
                                <a href="{{ url('pets/' . $pet->id . '?from=adoptions') }}" class="btn btn-xs btn-outline btn-default">Ver</a>
                                @if(!$pet->adopted && $userAdoptionsCount < 3)
                                    <form method="POST" action="{{ url('adoptions') }}">
                                        @csrf
                                        <input type="hidden" name="pet_id" value="{{ $pet->id }}">
                                        <button type="submit" class="btn btn-xs btn-primary">Adoptar</button>
                                    </form>
                                @elseif($pet->adopted)
                                    <span class="text-sm text-gray-500">Adoptada</span>
                                @else
                                    <span class="text-sm text-gray-500">Límite alcanzado</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-8 rounded-lg bg-slate-900/40 text-center">
            <p class="text-lg">No hay mascotas disponibles en este momento.</p>
        </div>
    @endif
@endsection
