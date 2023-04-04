<table class="table">
    <thead>
        <tr>
            <th>Vehicle ID</th>
            <th>Date</th>            
        </tr>
    </thead>
    <tbody>
        @foreach ($deductions as $deduction)
        <tr>
            <td>{{$deduction->vehicle->unit_number}}</td>            
            <td>{{date("m/d/Y", strtotime($deduction->date))}}</td>
        </tr>
        @endforeach
    </tbody>
</table>