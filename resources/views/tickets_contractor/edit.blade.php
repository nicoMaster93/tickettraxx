@extends('layouts.appContractor')
@section('title', 'Edit Ticket')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Edit Ticket</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('tickets_contractor.index')}}">Tickets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    @if(isset($ticket->return_message) && !empty($ticket->return_message))
        <div class="alert alert-danger">
            <strong>Return Message: {{$ticket->return_message}}</strong>
        </div>
    @endif

    <div class="form-content">
        <form method="POST" action="{{ route('tickets_contractor.edit', ["id" => $ticket->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off"  type="date" class="form-control @error('date_gen') is-invalid @enderror" id="date_gen" name="date_gen" placeholder="Date" value="{{ old('date_gen', $ticket->date_gen) }}" required>
                        <label for="date_gen">Date</label>
                        @error('date_gen')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off"  type="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number" placeholder="Number ticket" value="{{ old('number', $ticket->number) }}" required>
                        <label for="number">Number ticket</label>
                        @error('number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select required class="form-control form-select @error('vehicle') is-invalid @enderror" id="vehicle" name="vehicle" >
                            <option value="">List of vehicles</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{$vehicle->id}}" @if (old("vehicle",$ticket->fk_vehicle) == $vehicle->id)
                                    selected
                                @endif>{{$vehicle->unit_number}}</option>
                            @endforeach
                        </select>        
                        <label for="vehicle">Vehicles</label>
                        @error('vehicle')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('pickup') is-invalid @enderror" id="pickup" name="pickup" >
                            <option value="">Select one</option>
                            @foreach ($pickups as $pickup)
                                <option value="{{$pickup->id}}" @if (old("pickup") == $pickup->id || $ticket->pickup == $pickup->place)
                                    selected
                                @endif>{{$pickup->place}}</option>
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
                        <select class="form-control form-select @error('deliver') is-invalid @enderror" id="deliver" name="deliver" >
                            <option value="">Select one</option>
                            @foreach ($delivers as $deliver)
                                <option value="{{$deliver->id}}" @if (old("deliver") == $deliver->id || $ticket->deliver == $deliver->place)
                                    selected
                                @endif>{{$deliver->place}}</option>
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
                        <select class="form-control form-select select-with-other @error('material') is-invalid @enderror" 
                         data-id-other="other_material"
                         id="material" name="material">
                            <option value="">Select one</option>
                            @foreach ($materials as $material) 
                                <option value="{{$material->id}}" @if (old('material', $ticket->fk_material) == $material->id) selected @endif>{{$material->name}}</option>    
                            @endforeach
                            <option value="other" @if (old('material') == "other") selected @endif>Other</option>
                        </select>
                        <label for="material">Material</label>
                        @error('material')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating other @if (old('material') == "other") active @endif">
                        <input autocomplete="off"  type="text" class="form-control @error('other_material') is-invalid @enderror" id="other_material" name="other_material" placeholder="Other Material" value="{{ old('other_material') }}">
                        <label for="other_material">Other Material</label>
                        @error('other_material')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off" type="text" class="form-control decimal @error('tonage') is-invalid @enderror" id="tonage" name="tonage" value="{{ old('tonage', $ticket->tonage) }}" placeholder="Tonage" required>
                        <label for="tonage">Tonage</label>
                        @error('tonage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off" readonly  type="text" class="form-control currency @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate', $ticket->rate) }}" placeholder="Rate" required>
                        <label for="rate">Rate</label>
                        @error('rate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input autocomplete="off"  type="text" class="form-control currency @error('total') is-invalid @enderror" id="total" name="total" value="{{ old('total', $ticket->total) }}" placeholder="Total" required>
                        <label for="total">Total</label>
                        @error('total')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="photo" class="upload-box-label">Picture ticket</label>
                    <div class="upload-box @error('photo_res') is-invalid @enderror" id="box">
                            <img src="{{ asset('imgs/drag_file.png') }}" /><br>
                            Drag the files<br>
                            or<br>
                        <button type="button" class="upload-btn" data-input="photo">
                            <img src="{{ asset('imgs/upload.png') }}" /> Upload image
                        </button>
                        <input autocomplete="off" type="file" name="photo" id="photo" />
                        <input autocomplete="off" type="hidden" name="photo_box_data" id="photo_box_data" />
                        <input autocomplete="off" type="hidden" name="photo_box_name" id="photo_box_name" />
                    </div>
                </div>
                <div class="col-12">
                    
                    <div class="form-group">
                        <input type="hidden" name="photo_res" class="@error('photo_res') is-invalid @enderror"/>
                        @error('photo_res')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div><br>
            
            <div class="row">               
                <div class="col-12 preview">
                    <label for="preview" >Preview</label><br>
                    @if (isset($ticket->file) && !empty($ticket->file))
                        @if(Storage::disk('local')->exists($ticket->file) && in_array(last(explode(".",$ticket->file)), ["jpg", "jpeg", "png", "gif"]))
                            <img src="{{Storage::url($ticket->file)}}"   id="preview"/>
                        @else
                        <img src="/imgs/theme/no-image.png" id="preview" style="display: none;"/>
                        @endif
                    @else 
                        <img src="/imgs/theme/no-image.png" id="preview"/>
                    @endif
                </div>
                @if (isset($ticket->file) && !empty($ticket->file))
                    @if(Storage::disk('local')->exists($ticket->file) && !in_array(last(explode(".",$ticket->file)), ["jpg", "jpeg", "png", "gif"]))
                    <span id="previewText" class="col-3">
                        {{last(explode("/",$ticket->file))}}
                    </span>
                    @else
                        <span id="previewText" class="col-3" style="display: none;"></span>
                    @endif    
                
                @else
                    <span id="previewText" class="col-3" style="display: none;"></span>
                @endif
                
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2 btn-flotante">Save</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/tickets_contractor/forms.js') }}"></script>
@endsection
