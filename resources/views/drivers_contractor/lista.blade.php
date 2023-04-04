@extends('layouts.appContractor')
@section('title', 'Drivers')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Drivers</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Drivers</li>
                </ol>
            </nav>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            <a href="{{route('drivers_contractor.create')}}" class="btn btn-verde2">Create Driver</a>
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
            <table class="table" id="table-drivers">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                   
                    @foreach ($drivers as $driver)
                        
                        <tr class="@if(sizeof($driver->vehicles) == 0) red-row @else gray-row @endif">
                            <td>{{$driver->id}}</td>
                            <td>{{$driver->name}}</td>
                            <td>{{$driver->phone}}</td>
                            <td>{{$driver->email}}</td>
                            <td>{{$driver->address}}</td>
                            <td class="text-center cont-icons">
                                @if(sizeof($driver->vehicles) == 0) 
                                    <a href="#" data-toggle="modal" data-target="#detailsModal" data-toggle="tooltip" data-placement="left" title="Details">
                                        <img src="{{ asset('imgs/ico_details.png')}}" />
                                    </a>
                                @endif
                                <a href="{{route('drivers_contractor.edit', ['id' => $driver->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                <a href="{{route('drivers_contractor.delete', ['id' => $driver->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
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
                    <h5 class="my-0">Do you really want to delete this driver?</h5>
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
                    <h5 class="my-0 text-center">This driver has no assigned vehicle</h5>
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
<script type="text/javascript" src="{{ URL::asset('js/drivers/lista.js') }}"></script>
@endsection
