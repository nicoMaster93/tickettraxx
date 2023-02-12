@extends('layouts.app')
@section('title', 'Tickets to Verify')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-12">
            <h1>Tickets to Verify</h1>
        </div>
    </div>   
    <form method="GET" action="{{route('tickets.to_verify')}}">
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
            <div class="col-2"></div>
            <div class="col-2"></div>
            <div class="col-2"></div>
        </div>
    </form>

    @if (session('message'))
        <br>
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
                                @if ($ticket->fk_ticket_state == "2")
                                    <img src="{{asset('imgs/rejected.png')}}" />    
                                @else
                                <img src="{{asset('imgs/minus_green.png')}}" />    
                                @endif                                
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
                                @if ($ticket->fk_ticket_state == "2")
                                Reply sent    
                                @else
                                <a href="{{route('tickets.info', ['id' => $ticket->id])}}" class="detail-ticket" data-toggle="modal" data-target="#detailsModal" data-toggle="tooltip" data-placement="left" title="Details">
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
                    <h5 class="my-0 text-center">Verificated ticket</h5>
                </div>
                <div class="modal-body">  
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="row">
                                <div class="col-sm-3 col-info">
                                    <h6>Date</h6>
                                    <span id="info-date"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Number ticket</h6>
                                    <span id="info-number"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Unit</h6>
                                    <span id="info-unit"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Material</h6>
                                    <span id="info-material"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Tonage</h6>
                                    <span id="info-tonage"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Rate</h6>
                                    <span id="info-rate"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Total</h6>
                                    <span id="info-total"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Pickup</h6>
                                    <span id="info-pickup"></span>
                                </div>
                                <div class="col-sm-3 col-info">
                                    <h6>Deliver</h6>
                                    <span id="info-deliver"></span>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6>Image ticket</h6>
                                    <div class="info-preview">
                                        <a href="#" target="_blank" id="info-link-preview">
                                            <img src="" id="info-img-preview" class="activo"/>    
                                            <span id="info-file-preview"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="approve text-center">
                                <div class="img-approve"></div>
                                <form method="post" id="form-approve" action="{{route('tickets.change_state')}}">
                                    @csrf
                                    <input type="hidden" name="state" value="3"/>
                                    <input type="hidden" name="id" id="id-approve"/>
                                    @if(in_array(7, $menu_user))
                                        <input type="submit" class="btn btn-verde" value="Approve" />
                                    @endif
                                </form>                                
                            </div>
                            <div class="denied">
                                <h4>Add Comment</h4>
                                <form method="post" id="form-denied" action="{{route('tickets.change_state')}}">
                                    @csrf
                                    <input type="hidden" name="state" value="2"/>
                                    <input type="hidden" name="id" id="id-denied"/>
                                    <div class="message-box">
                                        <textarea id="message" name="message" placeholder="Add Comment"></textarea>
                                        <div class="n-caract-box"><span class="n-caract">0</span> / 200</div>
                                    </div>
                                    @if(in_array(8, $menu_user))
                                        <input type="submit" class="btn btn-danger" value="Denied" />
                                    @endif
                                </form>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/tickets/lista.js') }}"></script>
@endsection
