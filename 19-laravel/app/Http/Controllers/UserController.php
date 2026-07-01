<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(12);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => ['required', 'numeric', 'unique:users'],
            'fullname' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:female,male,Female,Male'],
            'birthdate' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = new User();
        $user->document = $request->document;
        $user->fullname = $request->fullname;
        $user->gender = $request->gender;
        $user->birthdate = $request->birthdate;
        
        if ($request->hasFile('photo')) {
            $photoName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('images/users'), $photoName);
            $user->photo = 'images/users/' . $photoName;
        } else {
            $user->photo = 'images/users/no-photo.png';
        }
        
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return redirect('users')
                ->with('message', '✅ The User: ' . $user->fullname . ' was added successfully!');
        }

        return back()->with('error', '❌ Error adding user.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'document' => ['required', 'unique:users,document,' . $user->id],
            'fullname' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:female,male,Female,Male'],
            'birthdate' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->document = $request->document;
        $user->fullname = $request->fullname;
        $user->gender = $request->gender;
        $user->birthdate = $request->birthdate;
        
        if ($request->hasFile('photo')) {
            if ($user->photo != 'images/users/no-photo.png' && $user->photo != 'no-photo.png') {
                if (file_exists(public_path($user->photo))) {
                    unlink(public_path($user->photo));
                }
            }
            
            $photoName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('images/users'), $photoName);
            $user->photo = 'images/users/' . $photoName;
        }
        
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', 'min:8'],
            ]);
            $user->password = Hash::make($request->password);
        }

        if ($user->save()) {
            return redirect('users')
                ->with('message', '✅ The User: ' . $user->fullname . ' was updated successfully!');
        }

        return back()->with('error', '❌ Error updating user.');
    }

    public function destroy(User $user)
    {
        if ($user->photo != 'images/users/no-photo.png' && $user->photo != 'no-photo.png') {
            if (file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }
        }
        
        if ($user->delete()) {
            return redirect('users')
                ->with('message', '✅ The User: ' . $user->fullname . ' was deleted successfully!');
        }

        return back()->with('error', '❌ Error deleting user.');
    }

    public function pdf()
    {
        try {
            $users = User::all();
            $gdInstalled = extension_loaded('gd');
            
            $pdf = Pdf::loadView('users.pdf', compact('users', 'gdInstalled'));
            
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
            ]);
            
            return $pdf->download('allusers-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            try {
                $users = User::all();
                $pdf = Pdf::loadView('users.pdf_sin_imagenes', compact('users'));
                return $pdf->download('allusers-sin-imagenes-' . date('Y-m-d') . '.pdf');
            } catch (\Exception $e2) {
                return back()->with('error', '❌ Error generating PDF: ' . $e2->getMessage());
            }
        }
    }

    public function excel()
    {
        try {
            return Excel::download(new UsersExport, 'allusers-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error generating Excel: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv']
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect('users')->with('message', '✅ Users imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error importing file: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $users = User::names($request->q)->orderBy('id', 'desc')->paginate(12);
        return view('users.search', compact('users'));
    }
}