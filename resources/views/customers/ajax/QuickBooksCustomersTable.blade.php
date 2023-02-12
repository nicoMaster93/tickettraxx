<table class="table table-striped" id="table-customers">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Phone</th>
            <th width="120">Select</th>
        </tr>
    </thead>
    <tbody> 
        @foreach ($customers as $customer)
            <tr>
                <td>{{$customer->Id}}</td>
                <td>{{$customer->DisplayName}}</td>
                <td>@isset($customer->PrimaryPhone)
                    {{$customer->PrimaryPhone->FreeFormNumber}}
                @endisset</td>
                <td><a href="{{route('customer.quickbooks', ['id_customer' => $idCustomer, 'id_quickbooks' => $customer->Id])}}" class="select-customer">Select</a></td>
            </tr>
        @endforeach
    </tbody>
</table>