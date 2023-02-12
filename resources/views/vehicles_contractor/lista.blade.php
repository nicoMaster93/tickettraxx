@extends('layouts.appContractor')
@section('title', 'Vehicles')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Vehicles</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
                </ol>
            </nav>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            <a href="{{route('vehicles_contractor.create')}}" class="btn btn-verde2">Create Vehicle</a>
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
                                    <a href="#" data-toggle="modal" data-target="#detailsModal" data-toggle="tooltip" data-placement="left" title="Details">
                                        <img src="{{ asset('imgs/ico_details.png')}}" />
                                    </a>
                                @endif
                                <a href="{{route('vehicles_contractor.edit', ['id' => $vehicle->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                <a href="{{route('vehicles_contractor.delete', ['id' => $vehicle->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
                                    <img src="{{ asset('imgs/ico_delete.png')}}" />
                                </a>                                
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
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6 text-center">
                            <input type="button" class="btn btn-verde2 full-width" data-dismiss="modal" value="Accept" />
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
