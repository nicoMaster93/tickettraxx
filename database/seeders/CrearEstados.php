<?php

namespace Database\Seeders;

use App\Models\ContractorStatesModel;
use App\Models\DeductionStatesModel;
use App\Models\DeductionTypesModel;
use App\Models\DriverStatesModel;
use App\Models\MaterialsModel;
use App\Models\SettlementStatesModel;
use App\Models\TicketStatesModel;
use App\Models\TypeIdsModel;
use App\Models\VehicleStatesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrearEstados extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContractorStatesModel::insert([["name" => "Active"], ["name" => "Inactive"],["name" => "Deleted"]]);
        TypeIdsModel::insert([["type_name" => "FEIN"],["type_name" => "Social Security Number"]]);
        DriverStatesModel::insert([["name" => "Active"], ["name" => "Deleted"]]);
        VehicleStatesModel::insert([["name" => "Active"], ["name" => "Deleted"]]);
        TicketStatesModel::insert([["name" => "To check"], ["name" => "Returned"], ["name" => "To pay"], ["name" => "Paid out"]]);
        MaterialsModel::insert([["name" => "Iron"],["name" => "Sand"]]);
        SettlementStatesModel::insert([["name" => "To pay"],["name" => "In payment"],["name" => "Paid out"]]);
        DeductionTypesModel::insert([["name" => "Loan"], ["name" => "Fuel"], ["name" => "Insurance"]]);        
        DeductionStatesModel::insert([["name" => "To add to the settlement"], ["name" => "Added to settlement"], ["name" => "Paid out"],["name" => "Deleted"]]);        
    }
}
