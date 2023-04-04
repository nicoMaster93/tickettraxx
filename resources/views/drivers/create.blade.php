@extends('layouts.app')
@section('title', 'Create Drivers')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Drivers</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('contractors.index')}}">Contractor ({{$contractor->company_name}})</a></li>
            <li class="breadcrumb-item"><a href="{{route('drivers.index', ['id' => $contractor->id])}}">Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('drivers.create', ['id' => $contractor->id]) }}" enctype="multipart/form-data">
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
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone" required>
                        <label for="phone">Phone</label>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="photo_cdl" class="upload-box-label">Picture CDL</label>
                    <div class="upload-box @error('photo_cdl_res') is-invalid @enderror" id="box_cdl">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_cdl">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_cdl" id="photo_cdl" />
                        <input autocomplete="off" type="hidden" name="photo_cdl_box_data" id="photo_cdl_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_cdl_box_name" id="photo_cdl_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_cdl_res" class="@error('photo_cdl_res') is-invalid @enderror"/>
                        @error('photo_cdl_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_cdl" >Preview CDL</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_cdl" />
                </div>
                <span id="preview_cdl_text" class="col-3" style="display: none;"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <label for="photo_medical_card" class="upload-box-label">Picture Medical Card</label>
                    <div class="upload-box @error('photo_medical_card_res') is-invalid @enderror" id="box_medical_card">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_medical_card">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_medical_card" id="photo_medical_card" />
                        <input autocomplete="off" type="hidden" name="photo_medical_card_box_data" id="photo_medical_card_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_medical_card_box_name" id="photo_medical_card_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_medical_card_res" class="@error('photo_medical_card_res') is-invalid @enderror"/>
                        @error('photo_medical_card_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_medical_card" >Preview Medical Card</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_medical_card" />
                </div>
                <span id="preview_medical_card_text" class="col-3" style="display: none;"></span>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create Driver</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/drivers/forms.js') }}"></script>
@endsection
