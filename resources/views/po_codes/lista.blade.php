@extends('layouts.app')
@section('title', 'PO Codes')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>PO Codes</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(45, $menu_user))
            <a href="{{route('po_codes.create')}}" class="btn btn-verde2">Create PO Codes</a>
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
            <table class="table table-striped" id="table-po-codes">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Pickup</th>
                        <th>Deliver</th>
                        <th>Rate</th>
                        <th width="120">Detail</th>
                    </tr>
                </thead>
                <tbody>                   
                    @foreach ($po_codes as $po_code)
                        <tr>
                            <td>{{$po_code->id}}</td>
                            <td>{{$po_code->code}}</td>
                            <td>{{$po_code->pickup->place}}</td>
                            <td>{{$po_code->deliver->place}}</td>
                            <td>${{number_format($po_code->rate,2)}}</td>
                            <td class="text-center cont-icons" width="120">
                                @if(in_array(46, $menu_user))
                                <a href="{{route('po_codes.edit', ['id' => $po_code->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a>
                                @endif
                                @if(in_array(47, $menu_user))
                                <a href="{{route('po_codes.delete', ['id' => $po_code->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
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
                    <h5 class="my-0">Do you really want to delete this PO Code?</h5>
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
<script type="text/javascript" src="{{ URL::asset('js/po_codes/lista.js') }}"></script>
@endsection
