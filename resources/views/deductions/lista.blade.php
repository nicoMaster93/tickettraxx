@extends('layouts.app')
@section('title', 'Deductions')
@section('content')
<div class="container-fluid">    
    <div class="row align-items-center">
        <div class="col-4">
            <h1>Deductions</h1>
        </div>
        <div class="col-3 text-right">
            @if(in_array(29, $menu_user))
            <a class="btn"  href="{{ route('deductions.download_template') }}">
                <img src="{{ asset('imgs/download.png')}}" /> Deduction Template download
            </a>
            @endif
        </div>
        <div class="col-3 text-right">
            @if(in_array(29, $menu_user))
            <button type="button" class="btn btn-excel"  data-toggle="modal" data-target="#modal-upload">
                <img src="{{ asset('imgs/excel.png')}}" /> Deduction bulk upload
            </button>
            @endif
        </div>
        <div class="col-2">
            @if(in_array(30, $menu_user))
                <a href="{{route('deductions.create')}}" class="btn btn-verde2">Create Deduction</a>
            @endif
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

    <form method="GET" action="{{route('deductions.index')}}" >   
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
                                    <a href="{{route('deductions.details', $deduction->id)}}" class="details" title="Details">
                                        <img src="{{ asset('imgs/ico_details.png')}}" />
                                    </a>    
                                @endif
                                


                                {{-- <a href="{{route('deductions.edit', ['id' => $deduction->id])}}" class="edit" data-toggle="tooltip" data-placement="left" title="Edit">
                                    <img src="{{ asset('imgs/ico_edit.png')}}" />
                                </a> --}}
                                @if(in_array(31, $menu_user))
                                <a href="{{route('deductions.delete', ['id' => $deduction->id])}}" class="delete"  data-toggle="tooltip" data-placement="left" title="Delete">
                                    <img src="{{ asset('imgs/ico_delete.png')}}" />
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
                    <h5 class="my-0">Do you really want to delete this deduction?</h5>
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
                    <form method="POST" action="{{route('deductions.upload')}}" id="form-upload">
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
<script type="text/javascript" src="{{ URL::asset('js/deductions/lista.js') }}"></script>
@endsection
