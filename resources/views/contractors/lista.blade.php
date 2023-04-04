@extends('layouts.app')
@section('title', 'Contractors')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Contractors</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(15, $menu_user))
            <a href="{{route('contractors.create')}}" class="btn btn-verde2">Create Contractor</a>
            @endif
        </div>
    </div>   
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <br>
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>{{$errors->first()}}</strong>
        </div>
        <br>
    @endif
    <div class="row">
        <div class="col-12">
            <table class="table table-striped" id="table-contractors">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Contractor</th>
                        <th>Company Name</th>
                        <th>Company Telephone</th>
                        <th>State</th>
                        <th width="120">Detail</th>
                    </tr>
                </thead>
                <tbody>                   
                    @foreach ($contractors as $contractor)
                        <tr>
                            <td>{{$contractor->unit_number ?? "No vehicles yet"}}</td>
                            <td>{{$contractor->name_contact}}</td>
                            <td>{{$contractor->company_name}}</td>
                            <td>{{$contractor->company_telephone}}</td>
                            <td>@if($contractor->state->id == 1)
                                <span class="state_1">{{$contractor->state->name}}</span>
                                @elseif($contractor->state->id == 2)
                                <span class="state_2">{{$contractor->state->name}}</span>
                                @elseif($contractor->state->id == 3)
                                <span class="state_3">{{$contractor->state->name}}</span>
                                @endif                                
                            </td>
                            <td class="text-center cont-icons" width="140">
                                @if(in_array(16, $menu_user))
                                <a href="{{route('drivers.index', ['id' => $contractor->id])}}" class="driver" data-toggle="tooltip" data-placement="left" title="Drivers">
                                    <img src="{{ asset('imgs/ico_driver.png')}}" />
                                </a>
                                @endif
                                @if(in_array(20, $menu_user))
                                <a href="{{route('vehicles.index', ['id' => $contractor->id])}}" class="vehicles" data-toggle="tooltip" data-placement="left" title="Vehicles">
                                    <img src="{{ asset('imgs/ico_vehicle.png')}}" />
                                </a>
                                @endif
                                @if(in_array(24, $menu_user))
                                <a href="{{route('contractors.edit', ['id' => $contractor->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                @endif
                                @if(in_array(25, $menu_user))
                                    @if($contractor->state->id == 1)
                                        <a href="{{route('contractors.deletePermanent', ['id' => $contractor->id, 'permanent' => true])}}" class="delete per"  data-toggle="tooltip" data-placement="left" title="Make Delete">
                                            <img src="{{ asset('imgs/icon-trash.png')}}" class="delete" />
                                        </a>
                                        <a href="{{route('contractors.delete', ['id' => $contractor->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Make Inactive">
                                            <img src="{{ asset('imgs/ico_delete.png')}}" />
                                        </a>
                                    @elseif($contractor->state->id == 2)
                                        <a href="{{route('contractors.activate', ['id' => $contractor->id])}}" class="activate"  data-toggle="tooltip" data-placement="left" title="Activate">
                                            <img src="{{asset('imgs/enable_user.png')}}" />
                                        </a>
                                    @endif
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
                    <h5 class="my-0"></h5>
                </div>
                <div class="modal-body">                    
                    <form method="POST" action="" id="form-delete">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <input type="submit" class="btn btn-verde2 full-width" id="btnSubmit" value="Make Inactive" />
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
    
</div>
<script type="text/javascript" src="{{ URL::asset('js/contractors/lista.js') }}"></script>
@endsection
