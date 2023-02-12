@extends('layouts.app')
@section('title', 'Create Material')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Material</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('materials.index')}}">Materials</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('materials.create') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
                        <label for="name">Name</label>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create Material</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/material/forms.js') }}"></script>
@endsection
