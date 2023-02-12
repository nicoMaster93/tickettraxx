@extends('layouts.appContractor')
@section('title', 'Resume')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-8">
            <h1><a class="back" href="{{route('payments_contractor.index')}}"><img src="{{asset('imgs/lower.png')}}" /></a>Resume</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('payments_contractor.index')}}">Payments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
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
    <h2>{{$settlements[0]->contractor->company_name}}</h2>
    <table class="table table-striped" id="table-settlement">
        <thead>
            <tr>
                <th>Start date</th>
                <th>End date</th>
                <th>Total</th>
                <th>State</th>
                <th class="text-center">Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($settlements as $settlement)
                <tr>
                    <td>{{$settlement->start_date}}</td>
                    <td>{{$settlement->end_date}}</td>
                    <td>{{number_format($settlement->total, 2)}}</td>
                    <td>{{$settlement->settlement_state->name}}</td>
                    <td class="text-center">
                        <a href="{{route('settlements.details', $settlement->id)}}" class="details" title="Details">
                            <img src="{{ asset('imgs/ico_details.png')}}" />
                        </a>
                    </td>                                    
                </tr>
            @endforeach
        </tbody>
    </table>
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

<script type="text/javascript" src="{{ URL::asset('js/payments/lista.js') }}"></script>
@endsection
