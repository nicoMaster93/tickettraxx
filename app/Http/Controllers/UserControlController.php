<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserControlRequest;
use App\Http\Requests\Users\CreateUserRequest;
use App\Models\MenuModel;
use App\Models\PermissionsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $user = Auth::user();        

        return view("user_control/user_control",[
            "user" => $user
        ]);
    }

    public function update(UserControlRequest $request){
        
        $user = Auth::user();     
        $user = User::findOrFail($user->id);
        if($request->has("password") && !empty($request->input("password"))){
            $user->password = Hash::make($request->password);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user_control')->with('message', 'User updated successfully!');
    }

    public function indexUsers(){

        $users = User::where("fk_rol","=","1")->get();

        return view("user_control/users_list",[
            "users" => $users
        ]);
    }

    public function createUserForm(){

        $menus = MenuModel::orderBy("fk_menu")->get();
        $arrMenu = array();
        foreach($menus as $menu){
            if(!isset($menu->fk_menu)){
                $arrMenu[$menu->id]["menu"] = $menu;
                $arrMenu[$menu->id]["subItems"] = array();
            }
            else{

                if(isset($arrMenu[$menu->fk_menu])){
                    $arrMenu[$menu->fk_menu]["subItems"][$menu->id]["menu"] = $menu;
                    $arrMenu[$menu->fk_menu]["subItems"][$menu->id]["subItems"] = array();
                }
                else{
                    foreach($arrMenu as $row1 => $menuInt){
                        foreach($menuInt["subItems"] as $subMenuID => $subMenu){
                            if($subMenuID == $menu->fk_menu){
                                $arrMenu[$row1]["subItems"][$subMenuID]["subItems"][$menu->id]["menu"] = $menu;
                                $arrMenu[$row1]["subItems"][$subMenuID]["subItems"][$menu->id]["subItems"] = array();
                            }
                        }                        
                    }
                }                
            }
        }


        return view("user_control/createUser",[
            "arrMenu" => $arrMenu
        ]);
    }
    
    public function createUser(CreateUserRequest $request){

        $user = new User();
        $user->password = Hash::make($request->password);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->fk_rol = 1;
        $user->save();
        if(isset($request->permiso)){
            $arrPermiso = array();
            foreach($request->permiso as $permiso){
                array_push($arrPermiso, ["fk_menu" => $permiso, "fk_user" => $user->id]);
            }
            PermissionsModel::insert($arrPermiso);
        }
        return redirect()->route('users.index')->with('message', 'User created successfully!');
    }
    

    public function editUserForm($id){

        $user = User::findOrFail($id);
        $menus = MenuModel::orderBy("fk_menu")->get();
        $arrMenu = array();
        foreach($menus as $menu){
            if(!isset($menu->fk_menu)){
                $arrMenu[$menu->id]["menu"] = $menu;
                $arrMenu[$menu->id]["subItems"] = array();
            }
            else{

                if(isset($arrMenu[$menu->fk_menu])){
                    $arrMenu[$menu->fk_menu]["subItems"][$menu->id]["menu"] = $menu;
                    $arrMenu[$menu->fk_menu]["subItems"][$menu->id]["subItems"] = array();
                }
                else{
                    foreach($arrMenu as $row1 => $menuInt){
                        foreach($menuInt["subItems"] as $subMenuID => $subMenu){
                            if($subMenuID == $menu->fk_menu){
                                $arrMenu[$row1]["subItems"][$subMenuID]["subItems"][$menu->id]["menu"] = $menu;
                                $arrMenu[$row1]["subItems"][$subMenuID]["subItems"][$menu->id]["subItems"] = array();
                            }
                        }                        
                    }
                }                
            }
        }

        $misMenus = PermissionsModel::where("fk_user","=",$user->id)->pluck('fk_menu')->toArray();
        
        
        return view("user_control/editUser",[
            "arrMenu" => $arrMenu,
            "user" => $user,
            "misMenus" => $misMenus
        ]);
    }

    
    public function editUser($id, UserControlRequest $request){

        $user = User::findOrFail($id);
        if($request->has("password") && !empty($request->input("password"))){
            $user->password = Hash::make($request->password);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->fk_rol = 1;
        $user->save();
        if(isset($request->permiso)){
            PermissionsModel::where("fk_user", "=", $user->id)->delete();
            $arrPermiso = array();
            foreach($request->permiso as $permiso){
                array_push($arrPermiso, ["fk_menu" => $permiso, "fk_user" => $user->id]);
            }
            PermissionsModel::insert($arrPermiso);
        }
        return redirect()->route('users.index')->with('message', 'User updated successfully!');
    }
    
    public function delete($id){
        $user = Auth::user();
        
        if($user->id != $id){
            $user = User::findOrFail($id);
            $user->delete();
            
            return redirect()->route('users.index')->with('message', 'User deleted successfully!');
        }
        else{
            return redirect()->route('users.index')->withErrors(['users' => "You can't delete your own user"]);
        }        
    }
    
    
}
