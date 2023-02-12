@extends('layouts.app')
@section('title', 'Create Vehicles')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Vehicles</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('contractors.index')}}">Contractor ({{$contractor->company_name}})</a></li>
            <li class="breadcrumb-item"><a href="{{route('vehicles.index', ['id' => $contractor->id])}}">Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('vehicles.create', ['id' => $contractor->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row" id="cont_alias">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('unit_number') is-invalid @enderror" id="unit_number" name="unit_number" placeholder="Name" value="{{ old('unit_number') }}" required>
                        <label for="unit_number">Unit Number</label>
                        @error('unit_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    
                    <div class="row align-items-center">
                        <div class="col-md-4 text-right"><label for="num_alias">Alias</label></div>
                        <div class="col-md-2 text-center">
                            <a href="#" class="minus"><img src="{{ asset('imgs/minus.png')}}" /></a>
                        </div>
                        <div class="col-md-4 text-center">
                            <input type="text" class="form-control" name="num_alias" id="num_alias" value="{{ old('num_alias','1') }}" />
                        </div>
                        <div class="col-md-2 text-center">
                            <a href="#" class="plus"><img src="{{ asset('imgs/plus.png')}}" /></a>
                        </div>
                    </div>
                </div>
                @for ($i = 1; $i <= intval(old('num_alias',1)); $i++)
                    <div class="col-md-3 alias">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('alias_'.$i) is-invalid @enderror" id="alias_{{$i}}" name="alias_{{$i}}" placeholder="Alias {{$i}}" value="{{ old('alias_'.$i) }}" required>
                            <label for="alias_{{$i}}">Alias {{$i}}</label>
                            @error('alias_'.$i)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endfor
            </div>
            <div class="row">
                <div class="col-12"><h2>Truck</h2></div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('truck_model_brand') is-invalid @enderror" id="truck_model_brand" name="truck_model_brand" placeholder="Model Brand" value="{{ old('truck_model_brand') }}">
                        <label for="truck_model_brand">Model Brand</label>
                        @error('truck_model_brand')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('truck_year') is-invalid @enderror" id="truck_year" name="truck_year" placeholder="Year" value="{{ old('truck_year') }}">
                        <label for="truck_year">Year</label>
                        @error('truck_year')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('truck_vin_number') is-invalid @enderror" id="truck_vin_number" name="truck_vin_number" placeholder="Truck VIN Number" value="{{ old('truck_vin_number') }}">
                        <label for="truck_vin_number">Truck VIN Number</label>
                        @error('truck_vin_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="photo_truck_dot_inspection" class="upload-box-label">Picture truck DOT inspection</label>
                    <div class="upload-box @error('photo_truck_dot_inspection_res') is-invalid @enderror" id="box_truck_dot_inspection">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_truck_dot_inspection">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_truck_dot_inspection" id="photo_truck_dot_inspection" />
                        <input autocomplete="off" type="hidden" name="photo_truck_dot_inspection_box_data" id="photo_truck_dot_inspection_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_truck_dot_inspection_box_name" id="photo_truck_dot_inspection_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_truck_dot_inspection_res" class="@error('photo_truck_dot_inspection_res') is-invalid @enderror"/>
                        @error('photo_truck_dot_inspection_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_truck_dot_inspection" >Preview truck DOT inspection</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_truck_dot_inspection" />
                </div>
                <span id="preview_truck_dot_inspection_text" class="col-3" style="display: none;"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <label for="photo_truck_registration" class="upload-box-label">Picture truck registration</label>
                    <div class="upload-box @error('photo_truck_registration_res') is-invalid @enderror" id="box_truck_registration">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_truck_registration">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_truck_registration" id="photo_truck_registration" />
                        <input autocomplete="off" type="hidden" name="photo_truck_registration_box_data" id="photo_truck_registration_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_truck_registration_box_name" id="photo_truck_registration_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_truck_registration_res" class="@error('photo_truck_registration_res') is-invalid @enderror"/>
                        @error('photo_truck_registration_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_truck_registration" >Preview truck registration</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_truck_registration" />
                </div>
                <span id="preview_truck_registration_text" class="col-3" style="display: none;"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-12"><h2>Trailer</h2></div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('trailer_model_brand') is-invalid @enderror" id="trailer_model_brand" name="trailer_model_brand" placeholder="Model Brand" value="{{ old('trailer_model_brand') }}" >
                        <label for="trailer_model_brand">Model Brand</label>
                        @error('trailer_model_brand')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('trailer_year') is-invalid @enderror" id="trailer_year" name="trailer_year" placeholder="Year" value="{{ old('trailer_year') }}" >
                        <label for="trailer_year">Year</label>
                        @error('trailer_year')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('trailer_vin_number') is-invalid @enderror" id="trailer_vin_number" name="trailer_vin_number" placeholder="Trailer VIN Number" value="{{ old('trailer_vin_number') }}" >
                        <label for="trailer_vin_number">Trailer VIN Number</label>
                        @error('trailer_vin_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="photo_trailer_dot_inspection" class="upload-box-label">Picture trailer DOT inspection</label>
                    <div class="upload-box @error('photo_trailer_dot_inspection_res') is-invalid @enderror" id="box_trailer_dot_inspection">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_trailer_dot_inspection">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_trailer_dot_inspection" id="photo_trailer_dot_inspection" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_dot_inspection_box_data" id="photo_trailer_dot_inspection_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_dot_inspection_box_name" id="photo_trailer_dot_inspection_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_trailer_dot_inspection_res" class="@error('photo_trailer_dot_inspection_res') is-invalid @enderror"/>
                        @error('photo_trailer_dot_inspection_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_trailer_dot_inspection" >Preview trailer DOT inspection</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_trailer_dot_inspection" />
                </div>
                <span id="preview_trailer_dot_inspection_text" class="col-3" style="display: none;"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <label for="photo_trailer_registration" class="upload-box-label">Picture trailer registration</label>
                    <div class="upload-box @error('photo_trailer_registration_res') is-invalid @enderror" id="box_trailer_registration">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_trailer_registration">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_trailer_registration" id="photo_trailer_registration" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_registration_box_data" id="photo_trailer_registration_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_registration_box_name" id="photo_trailer_registration_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_trailer_registration_res" class="@error('photo_trailer_registration_res') is-invalid @enderror"/>
                        @error('photo_trailer_registration_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_trailer_registration" >Preview trailer registration</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_trailer_registration" />
                </div>
                <span id="preview_trailer_registration_text" class="col-3" style="display: none;"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <label for="photo_trailer_over" class="upload-box-label">Picture Overweight Permit</label>
                    <div class="upload-box @error('photo_trailer_over_res') is-invalid @enderror" id="box_trailer_over">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo_trailer_over">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo_trailer_over" id="photo_trailer_over" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_over_box_data" id="photo_trailer_over_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_trailer_over_box_name" id="photo_trailer_over_box_name" />
                    </div>
                </div>
                <div class="col-12">                    
                    <div class="form-group">
                        <input type="hidden" name="photo_trailer_over_res" class="@error('photo_trailer_over_res') is-invalid @enderror"/>
                        @error('photo_trailer_over_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview_trailer_over" >Preview trailer Overweight Permit</label><br>
                    <img src="/imgs/theme/no-image.png" id="preview_trailer_over" />
                </div>
                <span id="preview_trailer_over_text" class="col-3" style="display: none;"></span>
            </div>
            <br>    






            
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2 btn-flotante">Create Vehicle</button>
                </div>
            </footer>
            
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/vehicles/forms.js') }}"></script>
@endsection
