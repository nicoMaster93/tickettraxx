@extends('layouts.app')
@section('title', 'Payments')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Payments</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
            @if(in_array(11, $menu_user))
            <a href="#" data-toggle="modal" data-target="#reportModal" class="btn btn-verde2">Reconciliation pdf</a>
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
    <form method="GET" action="{{route('payments.index')}}" >
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
                <th>Surcharge</th>
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
                    <td>{{number_format($settlement->surcharge, 2)}}</td>
                    <td>{{$settlement->settlement_state->name}}</td>                    
                    <td class="text-center">
                        <a href="{{route('payments.details', ['id_contrator' => $settlement->fk_contractor, 'id_payment' => $settlement->fk_payment])}}" class="details_link" title="Details">
                            <img src="{{ asset('imgs/ico_details.png')}}" />
                        </a>
                        @if(in_array(12, $menu_user))
                        <a href="{{route('payments.pdf', ['id_contrator' => $settlement->fk_contractor, 'id_payment' => $settlement->fk_payment])}}" class="details_link" title="Download PDF">
                            <img src="{{ asset('imgs/download.png')}}" />
                        </a>
                        @endif
                        @if(in_array(13, $menu_user))
                        <a href="{{route('payments.pdfMail', ['id_contrator' => $settlement->fk_contractor, 'id_payment' => $settlement->fk_payment])}}" class="details_link" title="Send email with PDF">
                            <img src="{{ asset('imgs/enviar_email.png')}}" />
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade modal-reconciliation" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="modal-reconciliation" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="my-0 text-center">Reconciliation pdf</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('payments.reconciliation') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="start_date_reconciliation" name="start_date_reconciliation" placeholder="Start date"required>
                                <label for="start_date">Start date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="end_date_reconciliation" name="end_date_reconciliation" placeholder="End date" required>
                                <label for="end_date">End date</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-5 col-md-2">
                            <button type="submit" class="btn btn-verde2">Generate</button>
                        </div>
                    </div>
                </form>               
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="{{ URL::asset('js/payments/lista.js') }}"></script>
@endsection
