<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        return $user ? response()->json($user) : response()->json(['error' => 'User not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
            ]);

            $data = $request->all();
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json($user);
        }
        return response()->json(['error' => 'User not found'], 404);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted']);
        }
        return response()->json(['error' => 'User not found'], 404);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
