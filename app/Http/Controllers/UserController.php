<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request) {
        $this->validate($request, [
            'username' => 'required|min:3|alpha',
            'password' => 'required|min:3'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->firstOrFail();
        
        if (Hash::check($password, $user->password)) {
            $user->token = $this->generateToken();
            $user->save();

            return response()->json($user);
        }

        return response('Invalid Password', 401);
    }

    public function register(Request $request) {
        $this->validate($request, [
            'displayName' => 'required|min:3|max:50',
            'username' => 'required|min:3|max:50|unique:users|alpha_num',
            'password' => 'required|min:3|max:50|alpha_num'
        ]);
        

        $hashed_password = Hash::make($request->input('password'));

        User::create([
            'displayName' => $request->input('displayName'),
            'username' => $request->input('username'),
            'password' => $hashed_password
        ]);

        return response()->json(['message'=>'User Registered Successfully']);

    }

    public function updatePassword(Request $request) {
        $this->validate($request, [
            'username' => 'required|min:3|max:50|alpha_num',
            'password' => 'required|min:3|max:50|alpha_num',
            'newPassword' => 'required|min:3|max:50|alpha_num',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $newPassword = $request->input('newPassword');

        if ($password == $newPassword) {
            return response('Old and New Password Same', 406);
        }

        $user = User::where('username', $username)->firstOrFail();

        if (Hash::check($password, $user->password)) {
            $user->password = Hash::make($newPassword);
            $user->save();

            return response('Password Changed Successfully');
        }

        return response('Unauthorised', 401);
    }

    private function generateToken() {
        return base64_encode(random_bytes(189));
    }

    //
}
