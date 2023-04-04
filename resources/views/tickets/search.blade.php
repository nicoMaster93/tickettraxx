@extends('layouts.app')
@section('title', 'Create Tickets')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-12">
            <h1>Create Tickets</h1>
        </div>
    </div>
    <form method="GET" action="{{route('tickets.search_list')}}" >   
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
        <div class="col-1">
        </div>
        <div class="col-3 text-right">
            @if(in_array(3, $menu_user))
            <a href="{{asset('storage/plantillas/Tickets_Base.csv')}}" download="plantillaTickets.csv" title="Download template" class="btn btn-excel" data-toggle="tooltip" data-placement="bottom"  >
                <img src="{{ asset('imgs/download.png')}}" />
            </a>
            <button type="button" class="btn btn-excel"  data-toggle="modal" data-target="#modal-upload">
                <img src="{{ asset('imgs/excel.png')}}" /> Ticket bulk upload
            </button>
            @endif
        </div>
        <div class="col-2 text-left">
            @if(in_array(4, $menu_user))
            <a href="{{route('tickets.create')}}" class="btn btn-verde2">Create Ticket</a>
            @endif
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
                            <td class="text-center"><img src="{{asset('imgs/check.png')}}" /></td>
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
                                <a href="{{route('tickets.info', ['id' => $ticket->id])}}" class="detail-ticket" data-toggle="modal" data-target="#detailsModal" data-toggle="tooltip" data-placement="left" title="Details">
                                    <img src="{{ asset('imgs/ico_details.png')}}" />
                                </a>
                                
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
                    <div id="msjResponse"></div>
                    <form method="POST" data-action="" id="form-delete">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <input type="submit" data-delete="" class="btn btn-verde2 full-width" value="Delete" />
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
    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="modal-details" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-0 text-center">Verificated ticket</h5>
                        <div>
                            @if(in_array(60, $menu_user))
                                <a class="btn btn-info update" title="Update Ticket" data-toggle="tooltip" data-placement="bottom" data-update >
                                    <img src="{{ asset('imgs/ico_edit.png')}}" title="Update Ticket" alt="">
                                </a>
                            @endif
                            @if(in_array(59, $menu_user))
                                <a class="btn btn-info delete" title="Delete Ticket" data-toggle="tooltip" data-placement="bottom" >
                                    <img src="{{ asset('imgs/ico_delete.png')}}" title="Delete Ticket" alt="">
                                </a>
                            @endif
                        </div>
                </div>
                <div class="modal-body">                    
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
                    </div><br><br>
                    <div class="row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4 text-center">
                            @if(in_array(5, $menu_user))
                                <a href="#" class="btn btn-danger recheck-link">Send to recheck</a>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
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
                    <form method="POST" action="{{route('tickets.upload')}}" id="form-upload">
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
<script type="text/javascript" src="{{ URL::asset('js/tickets/lista.js') }}"></script>
@endsection
