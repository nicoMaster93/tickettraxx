<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title','Bienvenido') | {{ config('app.name', 'CONCRETE REDI') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>
    <script src="{{ asset('js/constantes.js') }}" ></script>
    <script src = "https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src = "https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        @guest
            <main>
                @yield('content')
            </main>
        @else
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2 bg-fondo">
                        <div class="menu-lateral">
                            <div class="username-box">
                                <img src="{{ asset('/imgs/theme/default-user.png') }}" />
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <ul>
                                <li><a href="{{ route('tickets.index')}}" @if (str_contains(Route::current()->getName(),'tickets'))
                                    class="activo"
                                @endif>Tickets</a></li>
                                
                                <li><a href="{{ route('user_control')}}" @if (str_contains(Route::current()->getName(),'user_control'))
                                    class="activo"
                                @endif>User Control</a></li>

                                <li><a href="{{ route('payments_contractor.index')}}" @if (str_contains(Route::current()->getName(),'payments_contractor'))
                                    class="activo"
                                @endif>Payments</a></li>

                                <li><a href="{{ route('deductions_contractor.index')}}" @if (str_contains(Route::current()->getName(),'deductions_contractor'))
                                    class="activo"
                                @endif>Deductions</a></li>
                            </ul>
                            <a class="sing_out" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            
                        </div>
                    </div>
                    <div class="col">
                        <main class="min-h-100-vh">
                            @yield('content')
                        </main>
                    </div>
                </div>

            </div>
            
            
            
        @endguest
       
    </div>
</body>
</html>
