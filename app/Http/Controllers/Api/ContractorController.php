<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Contractor\LoginRequest;
use App\Http\Requests\UserControlRequest;
use App\Models\ContractorsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ContractorController extends Controller
{
    /**
     * Login contractor
     * Generate an access token to use in the rest of the app
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam user String required Contractor Email.
     * @bodyParam pass String required Contractor password.
     * 
     * 
	 */
    public function login(LoginRequest $request){

        $user = User::whereEmail($request->user)->first();
        if(!is_null($user) && Hash::check($request->pass, $user->password)){
            
            if($user->fk_rol == "2"){
                $user->api_token = Str::random(100);
                $user->save();
                $contractor = ContractorsModel::select(["name_contact","company_name","email"])->where("fk_user","=",$user->id)->first();
                if($contractor->fk_contractor_state == "1"){
                    return response()->json([
                        "success" => true,
                        "message" => "Login successful",
                        "data" => [
                            "token" => $user->api_token,
                            "contractor" => $contractor                        
                        ]                    
                    ], 200);
                }
                else{
                    return response()->json([
                        "success" => false,
                        "error" => "Contractor is disabled"          
                    ], 406);
                }
            }
            else{
                return response()->json([
                    "success" => false,
                    "error" => "This user is not a contractor"
                ], 406);
            }
        }
        else{
            return response()->json([
                "success" => false,
                "error" => "Wrong username or password"
            ], 406);
        }
    }

    /**
     * Edit contractor
     * Update basic fields of contractor
     * 
	 * @group  v 1.0.0
     * 
     * @bodyParam name String required Contractor Name.
     * @bodyParam email String required Contractor Email.
     * @bodyParam password String User password.
     * @bodyParam repeat_password String User password again.
     * 
     * @authenticated
	 */
    
    public function update(UserControlRequest $request){
        
        $user = auth()->user();     
        $user = User::findOrFail($user->id);

        if($request->has("password") && !empty($request->input("password"))){
            $user->password = Hash::make($request->password);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "User updated successfully!",
            "data" => []                    
        ], 200);
    }

    /**
     * Logout contractor
     * Logout contractor
     * 
	 * @group  v 1.0.0
     * 
    * @authenticated
	 */
    
    public function logout(){

        $user = auth()->user();     
        $user = User::findOrFail($user->id);
        $user->api_token = "";
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Successfully logged out",
            "data" => []                    
        ], 200);

    }
    
    
    /**
     * Get info contractor
     * Get info contractor
     * 
	 * @group  v 1.0.0
     * 
    * @authenticated
	 */
    
    public function info(){

        $user = auth()->user();     
     

        return response()->json([
            "success" => true,
            "message" => "Successfully get user",
            "user" => $user
        ], 200);

    }
    



}
