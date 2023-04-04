<h2>Tickets</h2>
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Ticket</th>
            <th>Truck ID</th>
            <th>Total</th>
            <th>Surcharge</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($settlements_tickets as $settlement_ticket)
        <tr>
            @php
                $ticket = $settlement_ticket->ticket;
            @endphp
            <td>{{date("m/d/Y", strtotime($ticket->date_gen))}}</td>
            <td>{{$ticket->number}}</td>
            <td>{{$ticket->vehicle->unit_number}}</td>
            <td>${{number_format($ticket->total,2)}}</td>
            <td>${{number_format($ticket->surcharge,2)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if (sizeof($settlements_deductions) > 0)
<h2>Deductions</h2>
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>Date pay</th>
            <th>Value</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($settlements_deductions as $settlement_deduction)
        <tr>
            @php
                $deduction = $settlement_deduction->deduction;
            @endphp
            <td>{{$deduction->id}}</td>
            <td>{{$deduction->type->name}}</td>
            <td>{{date("m/d/Y", strtotime($deduction->date_pay))}}</td>            
            <td>${{number_format($settlement_deduction->value,2)}}</td>
            @if ($deduction->fk_deduction_type == 2)
                <td><a href="#" class="details-fuel" data-id="{{$deduction->id}}"><img src="{{ asset('imgs/ico_details.png')}}" /></a></td>
            @else
                <td></td>
            @endif
        </tr>
        @if ($deduction->fk_deduction_type == 2)
            <tr class="table_details_fuel" data-id="{{$deduction->id}}">
                <td colspan="5">
                    <h2>Details</h2>
                    @php
                        $total = 0;
                    @endphp
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Gallons</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($deduction->deduction_vehicles as $d_veh)
                            <tr>
                                <td>{{date("m/d/Y", strtotime($d_veh->date))}}</td>
                                <td>{{$d_veh->vehicle->unit_number}}</td>
                                <td>{{$d_veh->city}}</td>
                                <td>{{$d_veh->state}}</td>
                                <td>{{$d_veh->gallons}}</td>
                                <td>${{number_format($d_veh->total,2)}}</td>
                            </tr>
                            @php
                                $total += $d_veh->total;
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                            <th>Fee</th>
                            <td>${{number_format($deduction->total_value - $total,2)}}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
        @endif
        @endforeach
    </tbody>
</table>
@endif

@if (sizeof($settlements_other_payments) > 0)
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Date pay</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($settlements_other_payments as $settlements_other_payment)
            <tr>
                <td>{{$settlements_other_payment->other_payments->id}}</td>
                <td>{{$settlements_other_payment->other_payments->description}}</td>
                <td>{{date("m/d/Y", strtotime($settlements_other_payment->other_payments->date_pay))}}</td>            
                <td>${{number_format($settlements_other_payment->other_payments->total,2)}}</td>                
            </tr>
        @endforeach
    </tbody>
</table>
@endif

