@extends('layouts.appContractor')
@section('title', 'Payments Contractor')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Payments contractor</h1>
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
    <form method="GET" action="{{route('payments_contractor.index')}}" >
    <div class="row align-items-center form-floating-no-margin">
        <div class="col-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start date" value="{{$start_date}}" required>
                <label for="start_date">Start date</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End date" value="{{ $end_date }}" required>
                <label for="end_date">End date</label>
            </div>
        </div>
        <div class="col-2 text-left">
            <button type="submit" class="btn btn-verde2 btn-search">
                <img src="{{ asset('imgs/search.png')}}" /> Search
            </button>
        </div>
        <div class="col-6"></div>       
    </div>
    </form>
    <br>
    <table class="table table-striped" id="table-settlement">
        <thead>
            <tr>
                <th>Id payment</th>
                <th>Date pay</th>
                <th>Contractor</th>
                <th>Total</th>
                <th>State</th>
                
                <th class="text-center">Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($settlements as $settlement)
                <tr>
                    <td>{{$settlement->fk_payment}}</td>
                    <td>{{date("m/d/Y", strtotime($settlement->payment->date_pay))}}</td>
                    <td>{{$settlement->contractor->company_name}}</td>
                    <td>{{number_format($settlement->total, 2)}}</td>
                    <td>{{$settlement->settlement_state->name}}</td>                    
                    <td class="text-center">
                        <a href="{{route('payments_contractor.details', ['id_payment' => $settlement->fk_payment])}}" class="details_link" title="Details">
                            <img src="{{ asset('imgs/ico_details.png')}}" />
                        </a>
                        <a href="{{route('payments_contractor.pdf', ['id_payment' => $settlement->fk_payment])}}" class="details_link" title="Download PDF">
                            <img src="{{ asset('imgs/download.png')}}" />
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript" src="{{ URL::asset('js/payments/lista.js') }}"></script>
@endsection
