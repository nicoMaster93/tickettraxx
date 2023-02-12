@extends('layouts.appContractor')
@section('title', 'Deductions')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-6">
            <h1>Deductions</h1>
        </div>
        <div class="col-4"></div>
        <div class="col-2">            
            <a href="{{route('deductions_contractor.create')}}" class="btn btn-verde2">Create Deduction</a>            
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

    <form method="GET" action="{{route('deductions_contractor.index')}}" >   
        <div class="row align-items-center form-floating-no-margin">
            <div class="col-2">
                <div class="form-floating">
                    <select class="form-control form-select" id="deduction_type" name="deduction_type">
                        <option value="">Select one</option>
                        @foreach ($deduction_types as $deduction_item)
                            <option value="{{$deduction_item->id}}" @if ($deduction_type == $deduction_item->id)
                                selected
                            @endif>{{$deduction_item->name}}</option>
                        @endforeach
                    </select>        
                    <label for="deduction_type">Deduction type</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start date" value="{{ $start_date ?? "" }}">
                    <label for="start_date">Start date</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End date" value="{{ $end_date ?? "" }}">
                    <label for="end_date">End date</label>
                </div>
            </div>
            <div class="col-2 text-left">
                <button type="submit" class="btn btn-verde2 btn-search">
                    <img src="{{ asset('imgs/search.png')}}" /> Search
                </button>
            </div>
        </div>
    </form>
    <br>

    <div class="row">
        <div class="col-12">
            <table class="table table-striped" id="table-deductions">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Contractor</th>
                        <th>Value</th>
                        <th>Date to pay</th>
                        <th>Type</th>
                        <th>State</th>
                        <th width="120">Detail</th>
                    </tr>
                </thead>
                <tbody>                   
                    @foreach ($deductions as $deduction)
                        <tr>
                            <td>{{$deduction->id}}</td>
                            <td>{{$deduction->contractor->company_name}}</td>
                            <td>{{$deduction->balance_due}}</td>
                            <td>{{date("m/d/Y", strtotime($deduction->date_pay))}}</td>
                            <td>{{$deduction->type->name}}</td>
                            <td>{{$deduction->state->name}}</td>
                            <td class="text-center cont-icons" width="120">
                                @if ($deduction->fk_deduction_type == "2")
                                    <a href="{{route('deductions_contractor.details', $deduction->id)}}" class="details" title="Details">
                                        <img src="{{ asset('imgs/ico_details.png')}}" />
                                    </a>    
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
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
<script type="text/javascript" src="{{ URL::asset('js/deductions_contractor/lista.js') }}"></script>
@endsection
