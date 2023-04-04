@extends('layouts.app')
@section('title', 'Edit FSC')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Edit FSC</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('fsc.index')}}">FSC</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('fsc.edit', ['id' => $fsc->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('customer') is-invalid @enderror" id="customer" name="customer" >
                            <option value="">Select one</option>
                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}" @if (old("customer", $fsc->fk_customer) == $customer->id)
                                    selected
                                @endif>{{$customer->prefix." - ".$customer->full_name}}</option>
                            @endforeach
                        </select>        
                        <label for="customer">Customer</label>
                        @error('customer')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="date" class="form-control @error('from') is-invalid @enderror" id="from" name="from" placeholder="From" value="{{ old('from', $fsc->from) }}" required>
                        <label for="from">From</label>
                        @error('from')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="date" class="form-control @error('to') is-invalid @enderror" id="to" name="to" placeholder="To" value="{{ old('to', $fsc->to) }}" required>
                        <label for="to">To</label>
                        @error('to')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control decimal @error('percentaje') is-invalid @enderror" id="percentaje" name="percentaje" placeholder="Percentaje" value="{{ old('percentaje', $fsc->percentaje) }}" required>
                        <label for="percentaje">Percentaje</label>
                        @error('percentaje')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Edit FSC</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/fsc/forms.js') }}"></script>
@endsection
