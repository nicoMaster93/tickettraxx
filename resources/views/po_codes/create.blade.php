@extends('layouts.app')
@section('title', 'Create PO Code')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create PO Code</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('po_codes.index')}}">PO Codes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('po_codes.create') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" placeholder="Code" value="{{ old('code') }}">
                        <label for="code">Code</label>
                        @error('code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('pickup') is-invalid @enderror" id="pickup" name="pickup" required>
                            <option value="">Select one</option>
                            @foreach ($pickups as $pickup) 
                                <option value="{{$pickup->id}}" @if (old('pickup') == $pickup->id) selected @endif>{{$pickup->place}}</option>    
                            @endforeach
                        </select>
                        <label for="pickup">Pickup</label>
                        @error('pickup')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('deliver') is-invalid @enderror" id="deliver" name="deliver" required>
                            <option value="">Select one</option>
                            @foreach ($delivers as $deliver) 
                                <option value="{{$deliver->id}}" @if (old('deliver') == $deliver->id) selected @endif>{{$deliver->place}}</option>    
                            @endforeach
                        </select>
                        <label for="deliver">Deliver</label>
                        @error('deliver')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off"  type="text" class="form-control currency @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate') }}" placeholder="Rate" required>
                        <label for="rate">Rate</label>
                        @error('rate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create PO Code</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/po_codes/forms.js') }}"></script>
@endsection
