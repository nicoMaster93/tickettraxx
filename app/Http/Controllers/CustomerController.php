<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contractor\EditQuickBooksRequest;
use App\Http\Requests\CustomerRequest;
use App\Models\CustomerModel;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $quickbooks;
    public function __construct()
    {
        $this->middleware('auth');
        $this->quickbooks = new QuickBooksController();
    }

    public function index(){
        $customers = CustomerModel::all();
        $authUrl = $this->quickbooks->getAuthUrl();

        return view('customers/lista', [
            "customers" => $customers,
            "authUrl" => $authUrl
        ]);    
    }
    
    public function update_id_quickbooks(EditQuickBooksRequest $request){
        $contractor = CustomerModel::findOrFail($request->input("id_customer"));
        $contractor->id_quickbooks = $request->input("id_quickbooks");
        $contractor->save();
        return redirect()->route('customer.index')->with('message', 'Customer modified successfully!');
    }

    public function createForm(){        
        return view('customers/create', [
        ]);
    }

    public function create(CustomerRequest $request){
        $customer = new CustomerModel;
        $customer->full_name = $request->full_name;
        $customer->prefix = $request->prefix;
        $customer->save();
        return redirect()->route('customer.index')->with('message', 'Customer created successfully!');
    }

    public function editForm($id){
        
        $customer = CustomerModel::findOrFail($id);
        
        return view('customers/edit', [
            "customer" => $customer
        ]);
    }

    public function update($id, CustomerRequest $request){
        $customer = CustomerModel::findOrFail($id);
        $customer->full_name = $request->full_name;
        $customer->prefix = $request->prefix;
        $customer->save();
        return redirect()->route('customer.index')->with('message', 'Customer updated successfully!');
    }
    
}
