<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;


class UserController extends Controller
{
    //
    public function generateToken()
    {
        $user = User::where('email','bomeo@gmail.com')->first();

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return [
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ];
    }

    public function getUserInfo(Request $request)
    {
        $current_user = Auth::user();
        if($current_user)
        {
            return [
                'success'=>true,
                'status_code' => 200,
                'return_data' => $current_user, 
                'message'=>'success'
            ];          
        }
        else
        {
            return [
                'status_code' => 401,
                'message' => 'Unauthorized',
                'success'=>false,
                'return_data' => false, 
            ];
        }
    }

    public function testhash(Request $request)
    {
        $hashpass=Hash::make('Gcnet123456');
        return view('test',['test'=>$hashpass]);
    }

    public function testcarbon(Request $request)
    {
        $date1 = Carbon::createFromFormat('m/d/Y', '12/02/2019');
        $date2 = Carbon::createFromFormat('m/d/Y', '12/01/2018');
  
        $result = $date1->gte($date2);
        return [
            'success'=>true,
            'status_code' => 200,
            'return_data' => $result, 
            'message'=>'Login successfully'
        ];      
    }   

    public function authlogin(Request $request)
    {
        
        $credentials = array('email'=>$request->email,'password'=>$request->password);

        if (Auth::attempt($credentials)) {    
                      
            $request->session()->regenerate();
            $current_user = Auth::id();
            $user = User::where('id',$current_user)->first();
           // $tokenResult = $user->createToken('newauthToken')->plainTextToken;

          /*  $return_data=array('status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'userinfo'=>$user,
            );*/             
            return [
                'success'=>true,
                'status_code' => 200,
                'return_data' => $user, 
                'message'=>'Login successfully'
            ];          
        }
        else
        {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized',
                'success'=>false,
                'return_data' => false, 
            ]);
        }

    }

    public function logout(Request $request)
    {
        //Auth::logout();

       // $request()->user()->currentAccessToken()->delete();

        Auth::guard('web')->logout();

    	$request->session()->invalidate();

    	$request->session()->regenerateToken();

         return [
            'success'=>true,
            'status_code' => 200,            
            'message'=>'Login successfully'
        ];          
    }
}
