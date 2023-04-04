@extends('layouts.app')
@section('title', 'Create Deduction')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Create Deduction</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('deductions.index')}}">Deductions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('deductions.create') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-control form-select @error('deduction_type') is-invalid @enderror" id="deduction_type" name="deduction_type" >
                            <option value="">Select one</option>
                            @foreach ($deduction_types as $deduction_type)
                                <option value="{{$deduction_type->id}}" @if (old("deduction_type") == $deduction_type->id)
                                    selected
                                @endif>{{$deduction_type->name}}</option>
                            @endforeach
                        </select>        
                        <label for="deduction_type">Deduction type</label>
                        @error('deduction_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3 for-type-1 for-type-3 for-type-4 @if (old("deduction_type") == "1" || old("deduction_type") == "3" || old("deduction_type") == "4") active @endif">
                    <div class="form-floating">
                        <input type="date" class="form-control @error('date_loan') is-invalid @enderror" id="date_loan" name="date_loan" placeholder="Date loan" value="{{ old('date_loan') }}" >
                        <label for="date_loan">Date loan</label>
                        @error('date_loan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 @if (old("deduction_type") == "1") active @endif">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('total_value') is-invalid @enderror" id="total_value" name="total_value" placeholder="Total value" value="{{ old('total_value') }}" >
                        <label for="total_value">Total value</label>
                        @error('total_value')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 @if (old("deduction_type") == "1") active @endif">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('balance_due') is-invalid @enderror" id="balance_due" name="balance_due" placeholder="Balance Due" value="{{ old('balance_due') }}" >
                        <label for="balance_due">Balance Due</label>
                        @error('balance_due')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1">
                    <div class="form-floating">
                        <select class="form-control form-select @error('charge_type') is-invalid @enderror" id="charge_type" name="charge_type" >
                            <option value="">Select one</option>
                            <option value="number_installments" @if (old("charge_type") == "number_installments")
                                selected
                            @endif>Number installments</option>
                            <option value="fixed_value" @if (old("charge_type") == "fixed_value")
                                selected
                            @endif>Fixed value</option>                            
                        </select>        
                        <label for="charge_type">Charge type</label>
                        @error('charge_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 fixed_value @if (old("deduction_type") == "1" && old("charge_type")=="fixed_value") active @endif">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('fixed_value') is-invalid @enderror" id="fixed_value" name="fixed_value" placeholder="Fixed value" value="{{ old('fixed_value') }}" >
                        <label for="fixed_value">Fixed value</label>
                        @error('fixed_value')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 number_installments  @if (old("deduction_type") == "1" && old("charge_type")=="number_installments") active @endif">
                    <div class="form-floating">
                        <input type="number" class="form-control @error('number_installments') is-invalid @enderror" id="number_installments" name="number_installments" placeholder="Number of installments" value="{{ old('number_installments') }}" >
                        <label for="number_installments">Number of installments</label>
                        @error('number_installments')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 @if (old("deduction_type") == "1") active @endif">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('days') is-invalid @enderror" id="days" name="days" placeholder="Time (days)" value="{{ old('days') }}" >
                        <label for="days">Time (days)</label>
                        @error('days')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3 for-type-1 for-type-3 for-type-4 @if (old("deduction_type") == "1" || old("deduction_type") == "3") active @endif">
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
                <div class="col-md-3 for-type-3 for-type-4 @if (old("deduction_type") == "3") active @endif">
                    <div class="form-floating">
                        <select class="form-control form-select @error('list_vehicles') is-invalid @enderror" id="list_vehicles" name="list_vehicles" >
                            <option value="">List of vehicles</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{$vehicle->id}}" @if (old("list_vehicles") == $vehicle->id)
                                    selected
                                @endif>{{$vehicle->unit_number}}</option>
                            @endforeach
                        </select>        
                        <label for="list_vehicles">Vehicles</label>
                        @error('list_vehicles')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


            </div>
            <div class="row for-type-2 @if (old("deduction_type") == "2") active @endif">
                <div class="col-md-4">                    
                    <div class="row align-items-center">
                        <div class="col-md-4 text-right"><label for="vehicles">Vehicles</label></div>
                        <div class="col-md-2 text-center">
                            <a href="#" class="minus"><img src="{{ asset('imgs/minus.png')}}" /></a>
                        </div>
                        <div class="col-md-4 text-center">
                            <input type="text" class="form-control" name="vehicles" id="vehicles" value="{{ old('vehicles','1') }}" />
                        </div>
                        <div class="col-md-2 text-center">
                            <a href="#" class="plus"><img src="{{ asset('imgs/plus.png')}}" /></a>
                        </div>
                    </div>
                </div>
                <div class="col-12" id="cont-vehicles">
                    @for ($i = 1; $i <= intval(old('vehicles',1)); $i++)

                    <div class="row item_vehicle">
                        <div class="col-md-2 vehicle">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('vehicle_'.$i) is-invalid @enderror" id="vehicle_{{$i}}" name="vehicle_{{$i}}" placeholder="Vehicle {{$i}}" value="{{ old('vehicle_'.$i) }}" >
                                <label for="vehicle_{{$i}}">Vehicle {{$i}}</label>
                                @error('vehicle_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                        <div class="col-md-2 date_vehicle">
                            <div class="form-floating">
                                <input type="date" class="form-control @error('date_vehicle_'.$i) is-invalid @enderror" id="date_vehicle_{{$i}}" name="date_vehicle_{{$i}}" placeholder="Date Vehicle {{$i}}" value="{{ old('date_vehicle_'.$i) }}" >
                                <label for="date_vehicle_{{$i}}">Date Vehicle {{$i}}</label>
                                @error('date_vehicle_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 city_vehicle">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('city_'.$i) is-invalid @enderror" id="city_{{$i}}" name="city_{{$i}}" placeholder="City {{$i}}" value="{{ old('city_'.$i) }}">
                                <label for="city_{{$i}}">City {{$i}}</label>
                                @error('city_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 state_vehicle">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('state_'.$i) is-invalid @enderror" id="state_{{$i}}" name="state_{{$i}}" placeholder="State {{$i}}" value="{{ old('state_'.$i) }}">
                                <label for="state_{{$i}}">State {{$i}}</label>
                                @error('state_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 gallons_vehicle">
                            <div class="form-floating">
                                <input type="text" class="form-control decimal @error('gallons_'.$i) is-invalid @enderror" id="gallons_{{$i}}" name="gallons_{{$i}}" placeholder="Gallons {{$i}}" value="{{ old('gallons_'.$i) }}">
                                <label for="gallons_{{$i}}">Gallons {{$i}}</label>
                                @error('gallons_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 total_vehicle">
                            <div class="form-floating">
                                <input type="text" class="form-control currency @error('total_'.$i) is-invalid @enderror" id="total_{{$i}}" name="total_{{$i}}" placeholder="Total {{$i}}"  value="{{ old('total_'.$i) }}">
                                <label for="total_{{$i}}">Total {{$i}}</label>
                                @error('total_'.$i)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endfor
                    
                </div>
            </div>

            

            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Create Deduction</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/deductions/forms.js') }}"></script>
@endsection
