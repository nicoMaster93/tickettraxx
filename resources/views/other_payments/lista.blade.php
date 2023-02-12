@extends('layouts.app')
@section('title', 'Other Payments')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Other Payments</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(33, $menu_user))
                <a href="{{route('other_payments.create')}}" class="btn btn-verde2">Create</a>
            @endif
        </div>
    </div>   
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if($errors->any())
         <div class="alert alert-danger">
            <strong>{{$errors->first()}}</strong>
        </div>
    @endif
    <br>

    <table class="table table-striped" id="table-other-payments">
        <thead>
            <tr>
                <th>#</th>
                <th>Date pay</th>
                <th>Contractor</th>
                <th>Description</th>
                <th>Total</th>
                <th>State</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($other_payments as $other_payment)
                <tr>
                    <td>{{$other_payment->id}}</td>
                    <td>{{date("m/d/Y", strtotime($other_payment->date_pay))}}</td>
                    <td>{{$other_payment->contractor->company_name}}</td>
                    <td>{{$other_payment->description}}</td>
                    <td>{{number_format($other_payment->total, 2)}}</td>
                    
                    <td>{{$other_payment->state->name}}</td>
                    <td class="text-center">
                        @if(in_array(34, $menu_user))
                        <a href="{{route('other_payments.delete', ['id' => $other_payment->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
                            <img src="{{ asset('imgs/ico_delete.png')}}" />
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade modal-delete" tabindex="-1" role="dialog" aria-labelledby="modal-delete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="my-0">Do you really want to delete this payment?</h5>
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
<script type="text/javascript" src="{{ URL::asset('js/other_payments/lista.js') }}"></script>
@endsection
