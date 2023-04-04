@extends('layouts.app')
@section('title', 'Settlement')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Settlement</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
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
    <form method="POST" action="{{route('settlements.liquidate')}}" id="form-settlements">
        @csrf
        <h2>This week</h2>
        <table class="table table-striped" id="table-settlement">
            <thead>
                <tr>
                    <th>Contractor</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <th>SubTotal</th>
                    <th>Deductions</th>
                    <th>For contractor</th>
                    <th>Surcharge</th>
                    <th>State</th>
                    <th class="text-center">Details</th>
                    <th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="select-all">
                            <label class="form-check-label" for="select-all">
                                Select all
                            </label>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($settlements as $settlement)
                    <tr>
                        <td>{{$settlement->contractor->company_name}}</td>
                        <td>{{date("m/d/Y", strtotime($settlement->start_date))}}</td>
                        <td>{{date("m/d/Y", strtotime($settlement->end_date))}}</td>
                        <td>{{number_format($settlement->subtotal, 2)}}</td>
                        <td>{{number_format($settlement->deduction, 2)}}</td>
                        <td>{{number_format($settlement->for_contractor, 2)}}</td>
                        <td>{{number_format($settlement->surcharge, 2)}}</td>
                        <td>{{$settlement->settlement_state->name}}</td>
                        <td class="text-center">
                            <a href="{{route('settlements.details', $settlement->id)}}" class="details" title="Details">
                                <img src="{{ asset('imgs/ico_details.png')}}" />
                            </a>
                        </td>
                        <td>
                            <div class="form-check padding-ajuste">
                                <input class="form-check-input" type="checkbox" value="{{$settlement->id}}" id="select-settlement-{{$settlement->id}}" name="select-settlement[]">
                                <label class="form-check-label" for="select-settlement-{{$settlement->id}}">
                                    Select
                                </label>
                            </div>
                        </td>                            
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (sizeof($settlements_out_date)>0)
            <h2>Out of date</h2>
            <table class="table table-striped" id="table-settlement-out-date">
                <thead>
                    <tr>
                        <th>Contractor</th>
                        <th>Start date</th>
                        <th>End date</th>
                        <th>SubTotal</th>
                        <th>Deductions</th>
                        <th>For contractor</th>
                        <th>Surcharge</th>
                        <th>State</th>
                        <th class="text-center">Details</th>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="select-all-out-date">
                                <label class="form-check-label" for="select-all-out-date">
                                    Select all
                                </label>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($settlements_out_date as $settlement)
                        <tr>
                            <td>{{$settlement->contractor->company_name}}</td>
                            <td>{{date("m/d/Y", strtotime($settlement->start_date))}}</td>
                            <td>{{date("m/d/Y", strtotime($settlement->end_date))}}</td>
                            <td>{{number_format($settlement->subtotal, 2)}}</td>
                            <td>{{number_format($settlement->deduction, 2)}}</td>
                            <td>{{number_format($settlement->for_contractor, 2)}}</td>
                            <td>{{number_format($settlement->surcharge, 2)}}</td>
                            <td>{{$settlement->settlement_state->name}}</td>
                            <td class="text-center">
                                <a href="{{route('settlements.details', $settlement->id)}}" class="details" title="Details">
                                    <img src="{{ asset('imgs/ico_details.png')}}" />
                                </a>
                            </td>
                            <td>
                                <div class="form-check padding-ajuste">
                                    <input class="form-check-input" type="checkbox" value="{{$settlement->id}}" id="select-settlement-{{$settlement->id}}" name="select-settlement[]">
                                    <label class="form-check-label" for="select-settlement-{{$settlement->id}}">
                                        Select
                                    </label>
                                </div>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (sizeof($settlements_upcoming)>0)
            <h2>Upcoming</h2>
            <table class="table table-striped" id="table-settlement-upcoming">
                <thead>
                    <tr>
                        <th>Contractor</th>
                        <th>Start date</th>
                        <th>End date</th>
                        <th>SubTotal</th>
                        <th>Deductions</th>
                        <th>For contractor</th>
                        <th>Surcharge</th>
                        <th>State</th>
                        <th class="text-center">Details</th>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="select-all-upcoming">
                                <label class="form-check-label" for="select-all-upcoming">
                                    Select all
                                </label>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($settlements_upcoming as $settlement)
                        <tr>
                            <td>{{$settlement->contractor->company_name}}</td>
                            <td>{{date("m/d/Y", strtotime($settlement->start_date))}}</td>
                            <td>{{date("m/d/Y", strtotime($settlement->end_date))}}</td>
                            <td>{{number_format($settlement->subtotal, 2)}}</td>
                            <td>{{number_format($settlement->deduction, 2)}}</td>
                            <td>{{number_format($settlement->for_contractor, 2)}}</td>
                            <td>{{number_format($settlement->surcharge, 2)}}</td>
                            <td>{{$settlement->settlement_state->name}}</td>
                            <td class="text-center">
                                <a href="{{route('settlements.details', $settlement->id)}}" class="details" title="Details">
                                    <img src="{{ asset('imgs/ico_details.png')}}" />
                                </a>
                            </td>
                            <td>
                                <div class="form-check padding-ajuste">
                                    <input class="form-check-input" type="checkbox" value="{{$settlement->id}}" id="select-settlement-{{$settlement->id}}" name="select-settlement[]">
                                    <label class="form-check-label" for="select-settlement-{{$settlement->id}}">
                                        Select
                                    </label>
                                </div>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if(in_array(27, $menu_user))
            <input type="button" data-toggle="modal" data-target="#modal-liquidate" class="btn btn-verde2" value="Liquidate selected" />
        @endif
        <br><br>
        <input type="hidden" name="payment_date_hidden" id="payment_date_hidden" value="{{date('Y-m-d')}}" />
        
    </form>
    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="modal-details" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0 text-center">Details</h5>
                </div>
                <div class="modal-body">
                    <div class="details-body"></div>
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
<div class="modal fade modal-liquidate" id="modal-liquidate" tabindex="-1" role="dialog" aria-labelledby="modal-liquidate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="my-0">Are you sure you want to liquidate?</h5>
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="payment_date" name="payment_date" placeholder="Payment date" required value="{{date('Y-m-d')}}">
                            <label for="payment_date">Payment date</label>
                        </div>
                    </div>    
                </div>                  
                <div class="row">
                    <div class="col-sm-6 text-center">
                        <input type="button" class="btn btn-cancelar full-width" data-dismiss="modal" value="Cancel" />
                    </div>
                    <div class="col-sm-6 text-center">
                        <input type="button" id="send-liquidate" class="btn btn-verde2 full-width" value="Liquidate" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/settlement/lista.js') }}"></script>
@endsection
