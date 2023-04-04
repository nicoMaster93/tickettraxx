@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="container-fluid min-h-100-vh">
    <h1>Edit User</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
    <div class="form-content">
        <form method="POST" action="{{ route('users.edit', ['id' => $user->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name',$user->name) }}" required>
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" value="{{ old('email',$user->email) }}" required>
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
            <div class="row">
                <div class="col-12">
                    <div class="cont_permisos">
                        <ul class="permisos_lv1">
                        @foreach ($arrMenu as $menu)
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                    @if (in_array($menu["menu"]->id, $misMenus))
                                        checked
                                    @endif type="checkbox" name="permiso[]" value="{{$menu["menu"]->id}}" id="permiso_{{$menu["menu"]->id}}" /> 
                                    <label class="form-check-label" for="permiso_{{$menu["menu"]->id}}">{{$menu["menu"]->name}}</label>
                                </div>
                                
                                @if (sizeof($menu["subItems"]) > 0)
                                    <ul class="permisos_lv2">
                                    @foreach ($menu["subItems"] as $menulv2)
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                            @if (in_array($menulv2["menu"]->id, $misMenus))
                                                checked
                                            @endif
                                            type="checkbox"  name="permiso[]" value="{{$menulv2["menu"]->id}}" id="permiso_{{$menulv2["menu"]->id}}"  /> 
                                            <label class="form-check-label" for="permiso_{{$menulv2["menu"]->id}}">{{$menulv2["menu"]->name}}</label>
                                        </div>
                                        @if (sizeof($menulv2["subItems"]) > 0)
                                            <ul class="permisos_lv3">
                                            @foreach ($menulv2["subItems"] as $menulv3)
                                                <li>
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                        @if (in_array($menulv3["menu"]->id, $misMenus))
                                                            checked
                                                        @endif
                                                        type="checkbox" name="permiso[]" value="{{$menulv3["menu"]->id}}" id="permiso_{{$menulv3["menu"]->id}}" />
                                                        <label class="form-check-label" for="permiso_{{$menulv3["menu"]->id}}">{{$menulv3["menu"]->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                    @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <footer class="form-group">
                <div class="text-center">
                    <button type="submit" class="btn btn-verde2">Save</button>
                </div>
            </footer>
        
        </form>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('js/users/forms.js') }}"></script>
@endsection
