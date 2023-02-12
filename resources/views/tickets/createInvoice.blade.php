@extends('layouts.app')
@section('title', 'Create Invoice')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1>Create Invoice</h1>
        </div>
        <div class="col-2"></div>
        <div class="col-2">
        </div>
    </div>   
    <form method="GET" action="{{route('tickets.createInvoice')}}" >   
        <div class="row align-items-center form-floating-no-margin">
            <div class="col-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start date" value="{{ $start_date ?? "" }}" required>
                    <label for="start_date">Start date</label>
                </div>
            </div>
            <div class="col-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End date" value="{{ $end_date ?? "" }}" required>
                    <label for="end_date">End date</label>
                </div>
            </div>
            <div class="col-2 text-left">
                <button type="submit" class="btn btn-verde2 btn-search">
                    <img src="{{ asset('imgs/search.png')}}" /> Search
                </button>
            </div>
            <div class="col-6 text-end">
                @if(!session()->has('sessionAccessToken'))
                <a href="{{$authUrl}}" class="quickbooks_ico quickbooks_login" data-toggle="tooltip" data-placement="left" title="Login to quickbooks">
                    <img src="{{ asset('imgs/C2QB.png')}}" width="300"/>
                </a>
                @endif
            </div>  
            
        </div>
    </form>
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
    
    <form method="POST" action="{{route('tickets.createInvoice')}}" id="form-tickets-selected">
        @csrf
        <h2>Select tickets to invoice</h2>
        <br>
        <div class="row">
            <div class="col-12">
                <table class="table table-striped" id="table-tickets">
                    <thead>
                        <tr>
                            <th>State</th>
                            <th>Date</th>                        
                            <th>Ticket</th>
                            <th>Truck ID</th>
                            <th>Pickup</th>
                            <th>Deliver</th>
                            <th>Tonage</th>
                            <th>Rate</th>
                            <th>Total</th>                        
                            <th width="120"></th>
                        </tr>
                    </thead>
                    <tbody>                    
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td class="text-center">@if ($ticket->fk_ticket_state == 3)
                                    <img src="{{asset('imgs/check.png')}}" />
                                @else
                                    Paid out
                                @endif</td>
                                <td>{{date("m/d/Y", strtotime($ticket->date_gen))}}</td>
                                <td>{{$ticket->number}}</td>
                                <td>{{$ticket->vehicle->unit_number}}</td>
                                <td>{{$ticket->pickup}}</td>
                                <td>{{$ticket->deliver}}</td>
                                <td>{{$ticket->tonage}}</td>
                                <td>${{number_format($ticket->rate,2)}}</td>
                                <td>${{number_format($ticket->total,2)}}</td>
                                
                                <td class="text-center cont-icons" width="120">
                                    <div class="form-check padding-ajuste">
                                        <input class="form-check-input" type="checkbox" value="{{$ticket->id}}" id="select-ticket-{{$ticket->id}}" name="select-ticket[]">
                                        <label class="form-check-label" for="select-ticket-{{$ticket->id}}">
                                            Select
                                        </label>
                                    </div>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <input type="submit" id="create-invoice" class="btn btn-verde2" value="Create invoice" />
        <br><br>
    </form>
</div>
<div class="modal fade modal-invoice" id="modal-invoice" tabindex="-1" role="dialog" aria-labelledby="modal-invoice" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="my-0">Invoice</h5>
            </div>
            <div class="modal-body">
            <form action="{{route("quickbooks.insertInvoice")}}" id="form-create-invoice" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="resp-invoice"></div>
                    </div>    
                </div>                  
                <div class="row">
                    <div class="col-sm-6 text-center">
                        <input type="button" class="btn btn-cancelar full-width" data-dismiss="modal" value="Cancel" />
                    </div>
                    <div class="col-sm-6 text-center">
                        <input type="submit" id="send-liquidate" class="btn btn-verde2 full-width" value="Send to Quickbooks" />
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/tickets/invoice.js') }}"></script>
@endsection
