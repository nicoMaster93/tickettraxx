<?php

namespace Database\Seeders;

use App\Models\RolModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatosAdministrador extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rol = new RolModel();
        $rol->name = "Super Adm";
        $rol->save();

        $rol = new RolModel();
        $rol->name = "Contractor";
        $rol->save();

        $usuario = new User();
        $usuario->password = Hash::make('1900');
        $usuario->name = "Administrator";
        $usuario->email = "bryant@mdccolombia.com";
        
        $usuario->fk_rol = 1;
        $usuario->save();
    }
}
