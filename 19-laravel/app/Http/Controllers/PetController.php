<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PetsExport;
use App\Imports\PetsImport;
use Illuminate\Support\Str;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::orderBy('id', 'desc')->paginate(12);
        return view('pets.index', compact('pets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'kind' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0'],
            'age' => ['required', 'numeric', 'min:0', 'max:50'],
            'breed' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'active' => ['required', 'boolean'],
            'adopted' => ['required', 'boolean'],
        ]);

        $pet = new Pet();
        $pet->name = $request->name;
        
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/pets'), $imageName);
            $pet->image = 'images/pets/' . $imageName;
        } else {
            $pet->image = 'images/pets/no-image.png';
        }
        
        $pet->kind = $request->kind;
        $pet->weight = $request->weight;
        $pet->age = $request->age;
        $pet->breed = $request->breed;
        $pet->location = $request->location;
        $pet->description = $request->description;
        $pet->active = $request->active;
        $pet->adopted = $request->adopted;

        if ($pet->save()) {
            return redirect('pets')
                ->with('message', '✅ The Pet: ' . $pet->name . ' was added successfully!');
        }

        return back()->with('error', '❌ Error adding pet.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        return view('pets.show', compact('pet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pet $pet)
    {
        return view('pets.edit', compact('pet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'kind' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0'],
            'age' => ['required', 'numeric', 'min:0', 'max:50'],
            'breed' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'active' => ['required', 'boolean'],
            'adopted' => ['required', 'boolean'],
        ]);

        $pet->name = $request->name;
        
        if ($request->hasFile('image')) {
            // Delete old image if it's not the default
            if ($pet->image != 'images/pets/no-image.png' && $pet->image != 'no-image.png' && file_exists(public_path($pet->image))) {
                unlink(public_path($pet->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/pets'), $imageName);
            $pet->image = 'images/pets/' . $imageName;
        }
        
        $pet->kind = $request->kind;
        $pet->weight = $request->weight;
        $pet->age = $request->age;
        $pet->breed = $request->breed;
        $pet->location = $request->location;
        $pet->description = $request->description;
        $pet->active = $request->active;
        $pet->adopted = $request->adopted;

        if ($pet->save()) {
            return redirect('pets')
                ->with('message', '✅ The Pet: ' . $pet->name . ' was updated successfully!');
        }

        return back()->with('error', '❌ Error updating pet.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        // Delete image if it's not the default
        if ($pet->image != 'images/pets/no-image.png' && $pet->image != 'no-image.png' && file_exists(public_path($pet->image))) {
            unlink(public_path($pet->image));
        }
        
        if ($pet->delete()) {
            return redirect('pets')
                ->with('message', '✅ The Pet: ' . $pet->name . ' was deleted successfully!');
        }

        return back()->with('error', '❌ Error deleting pet.');
    }

    /**
     * Generate PDF file
     */
    public function pdf()
    {
        try {
            $pets = Pet::all();
            $gdInstalled = extension_loaded('gd');
            
            $pdf = Pdf::loadView('pets.pdf', compact('pets', 'gdInstalled'));
            
            // Configurar opciones para evitar errores
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
            ]);
            
            return $pdf->download('allpets-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            // Fallback: generar PDF sin imágenes
            try {
                $pets = Pet::all();
                $pdf = Pdf::loadView('pets.pdf_sin_imagenes', compact('pets'));
                return $pdf->download('allpets-sin-imagenes-' . date('Y-m-d') . '.pdf');
            } catch (\Exception $e2) {
                return back()->with('error', '❌ Error generating PDF: ' . $e2->getMessage());
            }
        }
    }

    /**
     * Generate Excel file
     */
    public function excel()
    {
        try {
            return Excel::download(new PetsExport, 'allpets-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error generating Excel: ' . $e->getMessage());
        }
    }

    /**
     * Import Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv']
        ]);

        try {
            Excel::import(new PetsImport, $request->file('file'));
            return redirect('pets')->with('message', '✅ Pets imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Search pets
     */
    public function search(Request $request)
    {
        $search = $request->q;
        $pets = Pet::names($search)->orderBy('id', 'desc')->paginate(12);
        return view('pets.search', compact('pets'));
    }

    /**
     * Show pets by kind
     */
    public function byKind($kind)
    {
        $pets = Pet::where('kind', $kind)->orderBy('id', 'desc')->paginate(12);
        return view('pets.index', compact('pets'));
    }

    /**
     * Show available pets (not adopted and active)
     */
    public function available()
    {
        $pets = Pet::where('active', 1)->where('adopted', 0)->orderBy('id', 'desc')->paginate(12);
        return view('pets.index', compact('pets'));
    }

    /**
     * Show adopted pets
     */
    public function adopted()
    {
        $pets = Pet::where('adopted', 1)->orderBy('id', 'desc')->paginate(12);
        return view('pets.index', compact('pets'));
    }
}