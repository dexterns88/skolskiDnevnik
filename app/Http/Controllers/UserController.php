<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = JWTAuth::user();

        return response()->json(compact('token','user'));
    }

    public function refresh()
    {
        $oldToken = JWTAuth::getToken();
        $token = JWTAuth::refresh($oldToken);
        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,student,teacher,parent', //validate role input
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

    public function getAllUsers($role = false)
    {
      $users = DB::table('users')
        ->select('firstName', 'lastName', 'email', 'role', 'id');
      if($role) {
        if(in_array($role, ['admin','student','teacher','parent'])) {
          $users->WHERE('role', '=', $role);
        }
      }
      $result = $users->get();
      return response()->json($result);
    }

    public function deleteUser($id) {
      if (!isset($id)) {
        return response()->json(['error' => 'user id is requred'], 400);
      } else if ($id == 1) {
        return response()->json(['error' => 'Can`t delete super admin'], 400);
      }

      $users = User::find($id);

      if ($users) {
        $result = $users->delete();
        return response()->json($result);
      } else {
        return response()->json(['error' => 'selected user doesn`t exist'], 403);
      }
    }

    public function updateUser(Request $request) {
      $id = $request->get('id');
      $firstName = $request->get('firstName');
      $lastName = $request->get('lastName');
      $email = $request->get('email');
      $role = $request->get('role');

      $validator = Validator::make($request->all(), [
        'email' => 'string|email|max:255|unique:users',
        'role' => 'in:admin,student,teacher,parent', //validate role input
      ]);

      if($validator->fails()){
        return response()->json($validator->errors(), 400);
      }

      $user = User::find($id);

      if(!$user) {
        return response()->json(['error' => 'selected user doesn`t exist'], 403);
      }

      if($firstName) {
        $user->firstName = $firstName;
      }

      if($lastName) {
        $user->lastName = $lastName;
      }

      if($email) {
        $user->email = $email;
      }

      if($role) {
        $user->role = $role;
      }

      $user->save();
      return response()->json(compact('user'),201);
    }
}
