@extends('layouts.app')
@section('title', 'Edit Pickup/Deliver')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Edit Pickup/Deliver</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('pickup_deliver.index')}}">Pickup/Deliver</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('pickup_deliver.edit', ['id' => $pickup_deliver->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('type') is-invalid @enderror" id="type" name="type" >
                            <option value="">Select one</option>
                            <option value="0" @if (old('type', $pickup_deliver->type)=="0") selected @endif>Pickup</option>
                            <option value="1" @if (old('type', $pickup_deliver->type)=="1") selected @endif>Deliver</option>
                        </select>        
                        <label for="type">Type</label>
                        @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('place') is-invalid @enderror" id="place" name="place" placeholder="Place" value="{{ old('place', $pickup_deliver->place) }}" required>
                        <label for="place">Place</label>
                        @error('place')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Edit Pickup/Deliver</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/pickup_deliver/forms.js') }}"></script>
@endsection
