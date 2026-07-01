<?php

namespace App\Http\Controllers;

use App\Models\Adoption;
use App\Models\Pet;
use Illuminate\Http\Request;

class AdoptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::where('active', 1)->get();
        $userAdoptionsCount = Adoption::where('user_id', auth()->id())->count();
        return view('adoptions.make')->with('pets', $pets)->with('userAdoptionsCount', $userAdoptionsCount);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('adoptions.index');
    }

    /**
     * Display the current user adoptions.
     */
    public function my()
    {
        $adoptions = Adoption::with('pet')->where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(12);
        return view('adoptions.my')->with('adoptions', $adoptions);
    }

    /*
     * Show the make adoption page.
     */
    public function make()
    {
        return redirect()->route('adoptions.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
        ]);

        $pet = Pet::findOrFail($request->pet_id);

        if ($pet->adopted) {
            return redirect()->route('adoptions.index')->with('error', 'Esta mascota ya fue adoptada.');
        }

        $userAdoptionsCount = Adoption::where('user_id', auth()->id())->count();
        if ($userAdoptionsCount >= 3) {
            return redirect()->route('adoptions.index')->with('error', 'Has alcanzado el límite de 3 adopciones.');
        }

        Adoption::create([
            'user_id' => auth()->id(),
            'pet_id' => $pet->id,
        ]);

        $pet->update(['adopted' => 1]);

        return redirect()->route('adoptions.index')->with('message', 'Solicitud de adopción enviada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Adoption $adoption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Adoption $adoption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Adoption $adoption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Adoption $adoption)
    {
        //
    }
}