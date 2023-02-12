@extends('layouts.app')
@section('title', 'Create Contractor')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Contractor</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('contractors.index')}}">Contractor</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('contractors.create') }}" enctype="multipart/form-data">
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
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select select-with-other find-cities @error('state') is-invalid @enderror" 
                         data-id-other="other_state" data-select-city-id="city"
                         id="state" name="state" required>
                            <option value="">Select one</option>
                            @foreach ($location_states as $location_state)
                                <option value="{{$location_state->id}}" @if (old('state') == $location_state->id) selected @endif>{{$location_state->location_name}}</option>
                            @endforeach                            
                            <option value="other" @if (old('state') == "other") selected @endif>Other</option>
                        </select>
                        <label for="state">State</label>
                        @error('state')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating other @if (old('state') == "other") active @endif">
                        <input type="text" class="form-control @error('other_state') is-invalid @enderror" id="other_state" name="other_state" placeholder="Other State" value="{{ old('other_state') }}">
                        <label for="other_state">Other State</label>
                        @error('other_state')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select select-with-other @error('city') is-invalid @enderror" 
                         data-id-other="other_city"
                         id="city" name="city" required>
                            <option value="">Select one</option>
                            @foreach ($location_cities as $location_city)
                                <option value="{{$location_city->id}}" @if (old('city') == $location_city->id) selected @endif>{{$location_city->location_name}}</option>
                            @endforeach
                            <option value="other" @if (old('city') == "other") selected @endif>Other</option>
                        </select>
                        <label for="city">City</label>
                        @error('city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating other @if (old('city') == "other") active @endif">
                        <input type="text" class="form-control @error('other_city') is-invalid @enderror" id="other_city" name="other_city" placeholder="Other City" value="{{ old('other_city') }}">
                        <label for="other_city">Other City</label>
                        @error('other_city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" placeholder="Address" required>
                        <label for="address">Address</label>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" placeholder="Zip code" required>
                        <label for="zip_code">Zip code</label>
                        @error('zip_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('id_type') is-invalid @enderror" id="id_type" name="id_type" required>
                            <option value="">Select one</option>
                            @foreach ($typesIds as $typesId)
                                <option value="{{$typesId->id}}" @if (old('id_type') == $typesId->id) selected @endif>{{$typesId->type_name}}</option>
                            @endforeach          
                        </select>
                        <label for="id_type">ID Type</label>
                        @error('id_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('id') is-invalid @enderror" id="id" name="id" value="{{ old('id') }}" placeholder="Id" required>
                        <label for="id">ID</label>
                        @error('id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Company Name" required>
                        <label for="company_name">Company Name</label>
                        @error('company_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('company_telephone') is-invalid @enderror" id="company_telephone" name="company_telephone" value="{{ old('company_telephone') }}" placeholder="Company Telephone" required>
                        <label for="company_telephone">Company Telephone</label>
                        @error('company_telephone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                        <label for="email">Email</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('percentage') is-invalid @enderror" id="percentage" name="percentage" value="{{ old('percentage') }}" placeholder="Percentage" required>
                        <label for="percentage">Percentage</label>
                        @error('percentage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create Contractor</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/contractors/forms.js') }}"></script>
@endsection
