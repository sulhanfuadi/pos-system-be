<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all users
        $users = DB::table('users')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate input
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|string',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // insert user
        $userId = DB::table('users')->insertGetId([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'created_by' => $validated['created_by'],
            'updated_by' => $validated['updated_by'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => ['id' => $userId]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // get user by id
        $user = DB::table('users')->find($id);

        if (!$user) { // if user not found
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:8',
            'email'    => 'sometimes|required|email|unique:users,email,' . $id,
            'role'     => 'sometimes|required|string',
            'updated_by' => 'required|integer|exists:users,id',
        ]);

        // get user by id
        $user = DB::table('users')->find($id);

        if (!$user) { // if user not found
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // update data
        $updateData = array_merge(
            $validated,
            ['updated_at' => now()]
        );

        if (isset($validated['password'])) { // if password is set, hash the password
            $updateData['password'] = Hash::make($validated['password']);
        }

        // update user
        DB::table('users')->where('id', $id)->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // get user by id
        $user = DB::table('users')->find($id);

        if (!$user) { // if user not found
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // delete user
        DB::table('users')->where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
