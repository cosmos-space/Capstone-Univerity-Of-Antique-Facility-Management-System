<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List users (basic).
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'role'             => 'required|in:admin,college_staff,org_staff',
            'college_name'     => 'nullable|string|max:255',
            'organization_name'=> 'nullable|string|max:255',
        ]);

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ];

        if ($validated['role'] === 'college_staff') {
            $data['college_name'] = $validated['college_name'];
        }

        if ($validated['role'] === 'org_staff') {
            $data['organization_name'] = $validated['organization_name'];
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('status', 'User account created successfully.');
    }
}
