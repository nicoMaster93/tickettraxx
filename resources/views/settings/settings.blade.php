@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
    </nav>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <br>
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>{{$errors->first()}}</strong>
        </div>
        <br>
    @endif


    <div class="form-content">
        <form method="POST" action="{{ route('settings') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('fee') is-invalid @enderror" id="fee" name="fee" placeholder="Fee" value="{{ old('fee', $config->fee) }}" required>
                        <label for="fee">Fee</label>
                        @error('fee')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('insurance') is-invalid @enderror" id="insurance" name="insurance" placeholder="Insurance" value="{{ old('insurance', $config->insurance) }}" required>
                        <label for="insurance">Insurance</label>
                        @error('insurance')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('gpsDashCam') is-invalid @enderror" id="gpsDashCam" name="gpsDashCam" placeholder="Gps/DashCam" value="{{ old('gpsDashCam', $config->gpsDashCam) }}" required>
                        <label for="gpsDashCam">GPS/Dashcam</label>
                        @error('gpsDashCam')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2 btn-flotante">Edit settings</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.inputmask.numeric.extensions.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/settings/forms.js') }}"></script>
@endsection