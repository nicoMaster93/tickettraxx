@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Edit Customer</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('customer.index')}}">Customers</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('customer.edit', ['id' => $customer->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" placeholder="Full Name" value="{{ old('full_name', $customer->full_name) }}" required>
                        <label for="full_name">Full Name</label>
                        @error('full_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix" name="prefix" placeholder="Prefix" value="{{ old('prefix', $customer->prefix) }}" required>
                        <label for="prefix">Prefix</label>
                        @error('prefix')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Edit Customer</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/customer/forms.js') }}"></script>
@endsection
