@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Customers</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(57, $menu_user))
            <a href="{{route('customer.create')}}" class="btn btn-verde2">Create Customer</a>
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
            <table class="table table-striped" id="table-customer">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Prefix</th>
                        <th>Id Quickbooks</th>
                        <th width="120">Detail</th>
                    </tr>
                </thead>
                <tbody>                   
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{$customer->full_name}}</td>
                            <td>{{$customer->prefix}}</td>
                            <td align="center">
                                @if(isset($customer->id_quickbooks))
                                    <span class="id_quickbooks">{{$customer->id_quickbooks}} </span>
                                    @if(session()->has('sessionAccessToken'))
                                    <a href="{{route('quickbooks.customers', ['idCustomer' => $customer->id])}}" class="quickbooks_ico quickbooks_table" data-toggle="tooltip" data-placement="left" title="Update quickbooks id">
                                        <img src="{{ asset('imgs/ico_quickbooks.png')}}" />
                                    </a>
                                    @else
                                    <a href="{{$authUrl}}" class="quickbooks_ico quickbooks_login" data-toggle="tooltip" data-placement="left" title="Sync with quickbooks">
                                        <img src="{{ asset('imgs/ico_quickbooks.png')}}" />
                                    </a>
                                    @endif
                                @elseif(session()->has('sessionAccessToken'))
                                <a href="{{route('quickbooks.customers', ['idCustomer' => $customer->id])}}" class="quickbooks_ico quickbooks_table" data-toggle="tooltip" data-placement="left" title="Sync with quickbooks">
                                    <img src="{{ asset('imgs/ico_quickbooks.png')}}" />
                                </a>
                                @else
                                <a href="{{$authUrl}}" class="quickbooks_ico quickbooks_login" data-toggle="tooltip" data-placement="left" title="Sync with quickbooks">
                                    <img src="{{ asset('imgs/ico_quickbooks.png')}}" />
                                </a>
                                @endif
                            </td>
                            <td class="text-center cont-icons" width="120">
                                @if(in_array(58, $menu_user))
                                <a href="{{route('customer.edit', ['id' => $customer->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
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
                    <h5 class="my-0">Do you really want to delete this fsc?</h5>
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
    <div class="modal fade modal-customers" tabindex="-1" role="dialog" aria-labelledby="modal-customers" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0">Select the quickbooks client for this contractor</h5>
                </div>
                <div class="modal-body">                    
                    <div class="result-custormers"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/customer/lista.js') }}"></script>
@endsection
