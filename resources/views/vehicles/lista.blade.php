@extends('layouts.app')
@section('title', 'Vehicles')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Vehicles</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('contractors.index')}}">Contractor ({{$contractor->company_name}})</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
                </ol>
            </nav>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(21, $menu_user))
                <a href="{{route('vehicles.create', ['id' => $contractor->id])}}" class="btn btn-verde2">Create Vehicle</a>
            @endif
        </div>
    </div>   
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <br>
    <div class="row">
        <div class="col-12">
            <table class="table" id="table-vehicles">
                <thead>
                    <tr>
                        <th>Unit Number</th>
                        <th>Alias</th>
                        <th>Truck</th>
                        <th>Trailer</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                   
                    @foreach ($vehicles as $vehicle)
                        
                        <tr class="@if(sizeof($vehicle->drivers) == 0) red-row @else gray-row @endif">
                            <td>{{$vehicle->unit_number}}</td>
                            <td>
                                @foreach($vehicle->alias as $vehicle_alias)
                                    <div>{{$vehicle_alias->alias}}</div>
                                @endforeach                            
                            </td>
                            <td>
                                <div><b>Model Brand: </b>{{$vehicle->truck_model_brand}}</div>
                                <div><b>Year: </b>{{$vehicle->truck_year}}</div>
                                <div><b>VIN Number: </b>{{$vehicle->truck_vin_number}}</div>
                            </td>
                            <td>
                                <div><b>Model Brand: </b>{{$vehicle->trailer_model_brand}}</div>
                                <div><b>Year: </b>{{$vehicle->trailer_year}}</div>
                                <div><b>VIN Number: </b>{{$vehicle->trailer_vin_number}}</div>
                            </td>
                            <td class="text-center cont-icons">
                                @if(sizeof($vehicle->drivers) == 0) 
                                    <a href="#"  data-toggle="tooltip" data-placement="left" class="showDetails" title="Details" data-id="{{$vehicle->id}}">
                                        <img src="{{ asset('imgs/ico_details.png')}}" />
                                    </a>
                                @endif
                                @if(in_array(22, $menu_user))
                                <a href="{{route('vehicles.edit', ['id' => $vehicle->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                @endif
                                @if(in_array(23, $menu_user))
                                <a href="{{route('vehicles.delete', ['id' => $vehicle->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
                                    <img src="{{ asset('imgs/ico_delete.png')}}" />
                                </a>  
                                @endif                              
                            </td>
                        </tr>
                        
                        
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade modal-delete" tabindex="-1" role="dialog" aria-labelledby="modal-delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0">Do you really want to delete this vehicle?</h5>
                </div>
                <div class="modal-body">                    
                    <form method="POST" action="" id="form-delete">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <input type="submit" class="btn btn-verde2 full-width" value="Delete" />
                            </div>
                            <div class="col-sm-6 text-center">
                                <input type="button" class="btn btn-cancelar full-width" data-dismiss="modal" value="Cancel" />
                            </div>
                        </div>                                                
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="modal-details" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0 text-center">This vehicle has no assigned driver</h5>
                </div>                
                <div class="modal-body">                    
                    <form action="{{route("vehicles.addDriver")}}" id="form-create-invoice" method="POST">
                        @csrf
                        <input type="hidden" name="vehicle_id" id="vehicle_id">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-control form-select"  id="driver" name="driver" required>
                                        <option value="">Select one</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{$driver->id}}">{{$driver->name}}</option>
                                        @endforeach        
                                    </select>
                                    <label for="driver">Driver</label>
                                </div>
                                <button type="submit" class="btn btn-verde2">Assign driver</button>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6 text-center">
                            <input type="button" class="btn btn-verde2 full-width" data-dismiss="modal" value="Close" />
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/vehicles/lista.js') }}"></script>
@endsection
