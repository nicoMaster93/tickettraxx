<div class="row">
    <div class="col">
        <b>Customer: </b><span>{{$customer->full_name}}</span>
        <input type="hidden" name="customer_id" value="{{$customer->id}}" />
    </div>
</div>
<div class="form-group row">
    <label for="number" class="col-sm-2 col-form-label">Number: </label>
    <div class="col-sm-4">
      <input type="text" required class="form-control" id="number" name="number">
    </div>
</div>
<div class="form-group row">
    <label for="invoice_date" class="col-sm-2 col-form-label">Invoice date: </label>
    <div class="col-sm-4">
      <input type="date" required class="form-control" id="invoice_date" name="invoice_date">
    </div>
    <label for="due_date" class="col-sm-2 col-form-label">Due date: </label>
    <div class="col-sm-4">
      <input type="date" required class="form-control" id="due_date" name="due_date">
    </div>
</div>
<div class="form-group row">
    <label for="po_code" class="col-sm-2 col-form-label">PO #: </label>
    <div class="col-sm-4">
        <input type="text" required class="form-control" id="po_code" name="po_code" @isset($po)
        value="{{$po->code}}"
        @endisset >
    </div>
</div>
<div class="form-group row">
    <label for="pickup" class="col-sm-2 col-form-label">Pickup: </label>
    <div class="col-sm-4">
      <input type="text" required class="form-control" id="pickup" name="pickup" value="{{$tickets[0]->pickup}}">
    </div>
    <label for="deliver" class="col-sm-2 col-form-label">Deliver: </label>
    <div class="col-sm-4">
      <input type="text" required class="form-control" id="deliver" name="deliver" value="{{$tickets[0]->deliver}}">
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Service Date</th>
            <th>Product service</th>
            <th>Description</th>
            <th>QTY</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php 
        $count = 1;
        @endphp
        @foreach ($tickets as $ticket)
            <tr>
                <td>
                    {{$count}}
                    <input type="hidden" name="ticket_id[]" value="{{$ticket->id}}" />
                </td>
                <td>{{date("m/d/Y", strtotime($ticket->date_gen))}}</td>
                <td>
                    <select class="form-control" required name="item[]" id="item_{{$count}}">
                        <option value="">Select one</option>
                        @foreach ($items as $item)
                            <option value="{{$item->Id}}" @if (isset($po) && strpos($item->FullyQualifiedName, $po->code) !== false)
                                selected
                            @endif>{{$item->FullyQualifiedName}}</option>
                        @endforeach
                    </select>
                </td>
                <td>{{$ticket->number}}</td>
                <td>{{$ticket->tonage}}</td>
                <td>${{number_format($ticket->rate,2)}}</td>
                <td>${{number_format($ticket->total,2)}}</td>
            </tr>
            @php 
            $count++;
            @endphp
        @endforeach
        @if (sizeof($surcharges) > 0)
        @foreach ($surcharges as $percentaje => $surcharge)
        <tr>
            <td>
                {{$count}}
            </td>
            <td></td>
            <td>
                <select class="form-control" required name="item[]" id="item_{{$count}}">
                    <option value="">Select one</option>
                    @foreach ($items as $item)
                        <option value="{{$item->Id}}" @if ($item->Id == "147")
                            selected
                        @endif>{{$item->FullyQualifiedName}}</option>
                    @endforeach
                </select>
            </td>
            <td>{{"FUEL SURCHARGE- ".$percentaje."%"}}</td>
            <td>1</td>
            <td>${{number_format($surcharge,2)}}</td>
            <td>${{number_format($surcharge,2)}}</td>
        </tr>
        @endforeach
        @endif

    </tbody>
</table>