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
    <link href="{{ asset('css/style.css') }}?v=1.1" rel="stylesheet">
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
                                @if(in_array(1, $menu_user))
                                <li><a href="{{ route('tickets.index')}}" @if (str_contains(Route::current()->getName(),'tickets'))
                                    class="activo"
                                @endif>Tickets</a></li>
                                @endif
                              
                                @if(in_array(10, $menu_user))
                                <li><a href="{{ route('payments.index')}}" @if (str_contains(substr(Route::current()->getName(),0, 8),'payments'))
                                    class="activo"
                                @endif>Payments</a></li>
                                @endif


                                @if(in_array(14, $menu_user))
                                <li><a href="{{ route('contractors.index')}}" @if (str_contains(Route::current()->getName(),'contractors') || str_contains(Route::current()->getName(),'drivers') || str_contains(Route::current()->getName(),'vehicles'))
                                    class="activo"
                                @endif>Contractors</a></li>
                                @endif
                              
                                @if(in_array(26, $menu_user))
                                <li><a href="{{ route('settlements.index') }}"  @if (str_contains(Route::current()->getName(),'settlements'))
                                    class="activo"
                                @endif>Settlement</a></li>
                                @endif
                              
                                @if(in_array(28, $menu_user))
                                <li><a  href="{{ route('deductions.index')}}" @if (str_contains(Route::current()->getName(),'deductions'))
                                    class="activo"
                                @endif>Deductions</a></li>
                                @endif
                              
                                @if(in_array(32, $menu_user))
                                <li><a  href="{{ route('other_payments.index')}}" @if (str_contains(Route::current()->getName(),'other_payments'))
                                    class="activo"
                                @endif>Other payments</a></li>
                                @endif
                              
                                @if(in_array(35, $menu_user))
                                <li><a href="{{ route('materials.index')}}" @if (str_contains(Route::current()->getName(),'materials'))
                                    class="activo"
                                @endif
                                >Materials</a></li>
                                @endif
                                
                                @if(in_array(48, $menu_user))
                                <li><a href="{{ route('pickup_deliver.index')}}" @if (str_contains(Route::current()->getName(),'pickup_deliver'))
                                    class="activo"
                                @endif
                                >Pickup/Deliver</a></li>
                                @endif
                              

                                @if(in_array(44, $menu_user))
                                <li><a href="{{ route('po_codes.index')}}" @if (str_contains(Route::current()->getName(),'po_codes'))
                                    class="activo"
                                @endif
                                >PO Codes</a></li>
                                @endif
                              
                                @if(in_array(39, $menu_user))
                                <li><a href="{{ route('users.index')}}" @if (str_contains(Route::current()->getName(),'users'))
                                    class="activo"
                                @endif
                                >Users</a></li>
                                @endif
                              
                                @if(in_array(52, $menu_user))
                                <li><a href="{{ route('fsc.index')}}" @if (str_contains(Route::current()->getName(),'fsc'))
                                    class="activo"
                                @endif
                                >FSC</a></li>
                                @endif

                                @if(in_array(56, $menu_user))
                                <li><a href="{{ route('customer.index')}}" @if (str_contains(Route::current()->getName(),'customer'))
                                    class="activo"
                                @endif
                                >Customers</a></li>
                                @endif

                                @if(in_array(42, $menu_user))
                                <li><a href="{{ route('settings')}}" @if (str_contains(Route::current()->getName(),'settings'))
                                    class="activo"
                                @endif>Settings</a></li>
                                @endif
                                                              
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
