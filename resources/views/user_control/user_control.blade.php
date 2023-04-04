@extends('layouts.appContractor')
@section('title', 'User Control')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>User Control</h1>
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
        <form method="POST" action="{{ route('user_control') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control currency @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name', $user->name) }}" required>
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
                        <input type="email" class="form-control currency @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" value="{{ old('email', $user->email) }}" required>
                        <label for="email">Email</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <span>Do not fill the password to not change</span>
            <br><br>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="password" class="form-control currency @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" value="{{ old('password') }}" >
                        <label for="password">Password</label>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="password" class="form-control currency @error('repeat_password') is-invalid @enderror" id="repeat_password" name="repeat_password" placeholder="Repeat Password" value="{{ old('repeat_password') }}" >
                        <label for="repeat_password">Repeat Password</label>
                        @error('repeat_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Edit login</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/settings/forms.js') }}"></script>
@endsection