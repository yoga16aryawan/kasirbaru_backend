<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 200);
            }
            $user = User::all();
            return response()->json([
                'status' => true,
                'message' => 'data ditemukan',
                'total_data' => $user->count(),
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (!Gate::allows('isAdmin', Auth::user())) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 200);
            }
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|confirmed',
                    'level' => 'required'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 200);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'level' => $request->level
            ]);
            $all_user = collect(User::all());
            return response()->json([
                'status' => true,
                'message' => 'berhasil menambahkan user baru',
                'total_data' => $all_user->count(),
                'data' => $all_user->sortDesc()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::where('id', Auth::user()->id)->get();
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan'
                ], 200);
            }
            return response()->json([
                'status' => true,
                'message' => 'data ditemukan',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $input = $request->all();
            $user = User::find($id);
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'data user tidak ditemukan'
                ], 401);
            }

            $user->fill($input)->save();
            $newUser = User::find($id);

            return response()->json([
                'status' => true,
                'message' => 'update user berhasil',
                'data' => $newUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updatePassword(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'old_password' => 'required',
                    'new_password' => 'required|confirmed'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Old Password not match'
                ], 401);
            }
            $user = User::find(auth()->user()->id);
            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Update Password',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
