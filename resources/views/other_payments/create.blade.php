@extends('layouts.app')
@section('title', 'Create Other Payment')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Other Payment</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('other_payments.index')}}">Other Payments</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('other_payments.create') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Description" value="{{ old('description') }}" >
                        <label for="description">Description</label>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="date" class="form-control @error('date_pay') is-invalid @enderror" id="date_pay" name="date_pay" placeholder="Date to pay" value="{{ old('date_pay') }}" >
                        <label for="date_pay">Date to pay</label>
                        @error('date_pay')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('total') is-invalid @enderror" id="total" name="total" placeholder="Total" value="{{ old('total') }}" >
                        <label for="total">Total</label>
                        @error('total')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('contractor') is-invalid @enderror" id="contractor" name="contractor" >
                            <option value="">Select one</option>
                            @foreach ($contractors as $contractor)
                                <option value="{{$contractor->id}}" @if (old("contractor") == $contractor->id)
                                    selected
                                @endif>{{$contractor->company_name}}</option>
                            @endforeach
                        </select>        
                        <label for="contractor">Contractor</label>
                        @error('contractor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create Other Payment</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/other_payments/forms.js') }}"></script>
@endsection
