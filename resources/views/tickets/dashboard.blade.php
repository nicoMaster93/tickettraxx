@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Tickets</h1>
    <div class="dashboard">
        @if(in_array(2, $menu_user))
        <a href="{{ route('tickets.search_list')}}">
            <img src="{{ asset('/imgs/create_tickets.png')}}"/>
            <span>Create or<br>search tickets</span>
        </a>
        @endif
        @if(in_array(6, $menu_user))
        <a href="{{ route('tickets.to_verify')}}">
            <img src="{{ asset('/imgs/tickets_to_verify.png')}}"/>
            <span>Tickets<br>to verify</span>
        </a>
        @endif
        @if(in_array(9, $menu_user))
        <a href="{{ route('tickets.createInvoice')}}">
            <img src="{{ asset('/imgs/tickets_to_verify.png')}}"/>
            <span>Create<br>Invoice</span>
        </a>
        @endif
    </div>
</div>
@endsection