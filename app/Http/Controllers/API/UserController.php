<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BLSMS;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Trait\APi\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    use GeneralTrait;
    function createUser(Request $request)
    {
        try {

            requestAdd($request, [
                'password' => ($password = rand(10000, 99999)),
                'password_confirmation' => $password,
                'username' => $username = createUsername()
            ]);



            $validator = $this->customValidator($request, [
                'name' => 'string|required',
                'phone' => 'string|required',
                'username' => 'string|required',
                'email' => 'email',
                'role_id' => 'numeric|required',
                'password' => 'required|confirmed',
            ]);

            if (isset($validator['status'])) {
                return response()->json($validator, 400);
            }

            $validator['password'] = bcrypt($validator['password']);
            unset($validator['password_confirmation']);

            $user = User::create($validator);
            if ($user) {
                Auth::login($user);
                $message = 'Dear: ' . strtoupper($user->name) . "\r\n\r\n";
                $message .= "Your Login credentials are: \r\n";
                $message .= 'Username: ' . $username . "\r\n";;
                $message .= 'Password: ' . $password;;
                $message .= "\r\n\r\nPlease do not share this credentials to anyone. Thank you";
                BLSMS::_sendMessageBLSM($message, $request->phone);

                return response()->json([
                    'status' => 'success',
                    'name' => $user->name,
                    'username' => $username,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role->name,
                    'token' => $user->createToken('user')->plainTextToken,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Something went wrong!',
                'status_code' => env('STATUS_CODE_PREFIX') . 'ERR500',
                'error' => $th->getMessage()
            ]);
        }
    }


    function login(Request $request)
    {
        try {
            $auth = Auth::attempt([
                'username' => $request->username,
                'password' => $request->password
            ]);
            if ($auth) {
                $user =  User::find(Auth::user()->id);
                return response()->json([
                    'status' => 'success',
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role->name,
                    'token' => $user->createToken('user')->plainTextToken,
                ]);
            }
            return response()->json([
                'status' => 'Either username or password is incorrect'
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Something went wrong!',
                'status_code' => env('STATUS_CODE_PREFIX') . 'ERR500',
                'error' => $th->getMessage()
            ]);
        }
    }

    function list(Request $request)
    {
        $perPage = 20;
        if ($request->filter != 'all') {
            $users = User::whereHas('role', fn($role) => $role->where('name', $request->filter))
                ->orderBy('name', 'ASC');

            $users = (bool) $request->isPaginate ? $users->paginate($perPage) : $users->get();
        } else {
            $users = User::orderBy('name', 'ASC');
            $users = (bool) $request->isPaginate ? $users->paginate($perPage) : $users->get();
        }
        $userResource = UserResource::collection($users);
        return (bool) $request->isPaginate ? $userResource : $userResource->resolve();
    }


    function logout(Request $request)
    {
        try {

            Auth::guard('sanctum')->user()->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'logged out'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Something went wrong!',
                'status_code' => env('STATUS_CODE_PREFIX') . 'ERR500',
                'error' => $th->getMessage()
            ]);
        }
    }

    function setAccess(Request $request)
    {
        try {
            $user = User::find(aes_decrypt($request->userId));
            if ($user->update(['status' => (bool) aes_decrypt($request->status)])) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'updated'
                ]);
            }
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Something went wrong!',
                'status_code' => env('STATUS_CODE_PREFIX') . 'ERR500',
                'error' => $th->getMessage()
            ]);
        }
    }
}
