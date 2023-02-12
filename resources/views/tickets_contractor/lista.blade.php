@extends('layouts.appContractor')
@section('title', 'Tickets')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-12">
            <h1>Tickets</h1>
        </div>
    </div>
    <form method="GET" action="{{route('tickets_contractor.index')}}" >   
    <div class="row align-items-center form-floating-no-margin">
        <div class="col-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start date" value="{{ $start_date ?? "" }}">
                <label for="start_date">Start date</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End date" value="{{ $end_date ?? "" }}" >
                <label for="end_date">End date</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input type="text" class="form-control" id="ticket_number" name="ticket_number" placeholder="Ticket number" value="{{ ($ticket_number ?? "") }}">
                <label for="ticket_number">Ticket number</label>
            </div>
        </div>
        <div class="col-2 text-left">
            <button type="submit" class="btn btn-verde2 btn-search">
                <img src="{{ asset('imgs/search.png')}}" /> Search
            </button>
        </div>
        <div class="col-1"></div>
        <div class="col-1 text-right">
            
        </div>
        <div class="col-2 text-left">
            <a href="{{route('tickets_contractor.create')}}" class="btn btn-verde2">Create Ticket</a>
        </div>
    </div>
    </form>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
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
                        <th>Surcharge</th>               
                        <th width="120"></th>
                    </tr>
                </thead>
                <tbody>
                   
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td class="text-center">
                                @switch($ticket->fk_ticket_state)
                                    @case(1)
                                        <img src="{{asset('imgs/minus_green.png')}}" />    
                                        @break
                                    @case(2)
                                        <img src="{{asset('imgs/rejected.png')}}" />    
                                        @break
                                    @case(3)
                                        <img src="{{asset('imgs/check.png')}}" />
                                        @break
                                    @default
                                        {{$ticket->ticket_state->name}}
                                @endswitch
                            </td>
                            <td>{{date("m/d/Y", strtotime($ticket->date_gen))}}</td>
                            <td>{{$ticket->number}}</td>
                            <td>{{$ticket->vehicle->unit_number}}</td>
                            <td>{{$ticket->pickup}}</td>
                            <td>{{$ticket->deliver}}</td>
                            <td>{{$ticket->tonage}}</td>
                            <td>${{number_format($ticket->rate,2)}}</td>
                            <td>${{number_format($ticket->total,2)}}</td>
                            <td>${{number_format($ticket->surcharge,2)}}</td>
                            <td class="text-center cont-icons" width="120">
                                @if($ticket->fk_ticket_state != 3 && $ticket->fk_ticket_state != 4)
                                    <a href="{{route('tickets_contractor.edit', ['id' => $ticket->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
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
                    <h5 class="my-0">Do you really want to delete this ticket?</h5>
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
    
    <div class="modal fade modal-upload" tabindex="-1" id="modal-upload" role="dialog" aria-labelledby="modal-upload" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0">Upload files</h5>
                </div>
                <div class="modal-body">                    
                    <form method="POST" action="{{route('tickets_contractor.upload')}}" id="form-upload">
                        @csrf
                        <div class="progress-content">
                            <div class="upload-progress">
                                <img src="{{ asset('imgs/excel_big_color.png')}}" />
                            </div>
                            Loading Files
                        </div>
                        <div class="response-content"></div>
                        <input type="hidden" name="file64" id="file64" />
                        <div class="upload-box" id="box">
                                <img src="{{ asset('imgs/excel_big.png') }}" /><br>
                                Drag the files<br>
                                or<br>
                            <button type="button" class="upload-btn" data-input="files">
                                <img src="{{ asset('imgs/upload.png') }}" /> Upload Files
                            </button>
                            <input autocomplete="off" type="file" name="files" id="files" accept=".csv" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/tickets_contractor/lista.js') }}"></script>
@endsection