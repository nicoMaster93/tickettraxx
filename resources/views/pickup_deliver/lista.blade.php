@extends('layouts.app')
@section('title', 'Pickup/Deliver')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Pickup/Deliver</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(49, $menu_user))
            <a href="{{route('pickup_deliver.create')}}" class="btn btn-verde2">Create Pickup/Deliver</a>
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
            <table class="table table-striped" id="table-pickup-deliver">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Pickup/Deliver</th>
                        <th width="120">Detail</th>
                    </tr>
                </thead>
                <tbody>                   
                    @foreach ($pickups_delivers as $pickup_deliver)
                        <tr>
                            <td>{{$pickup_deliver->id}}</td>
                            <td>{{($pickup_deliver->type==0 ? "Pickup" : "Deliver")}}</td>
                            <td>{{$pickup_deliver->place}}</td>
                            <td class="text-center cont-icons" width="120">
                                @if(in_array(50, $menu_user))
                                <a href="{{route('pickup_deliver.edit', ['id' => $pickup_deliver->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                @endif
                                @if(in_array(51, $menu_user))
                                <a href="{{route('pickup_deliver.delete', ['id' => $pickup_deliver->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
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
                    <h5 class="my-0">Do you really want to delete this pickup/deliver?</h5>
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
</div>
<script type="text/javascript" src="{{ URL::asset('js/pickup_deliver/lista.js') }}"></script>
@endsection
