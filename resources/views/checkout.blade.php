@extends('layouts.app')
@section('content')
<div class="card p-2  m-2 table-responsive ">
  <table class="table table-striped table-sm table-hover btn-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Preview</th>
        <th>Quantity</th>
        <th>Price</th>
        <th></th>
      </tr>
    </thead>
    <tbody class="p-1">
      @php ($total = 0)
      @foreach($items as $item)
        @foreach($basket as $id)
         @if ($id->product_id == $item->id)
         @php($total += $item->price)
          <tr>
          <td>{{$item->name}}</td>
          <td>{{$item->img}}</td>
          <td>1</td>
          <td>${{$item->price}}</td>
          <td>
            <form action="/removeItem/{{$item->id}}" method="POST">
              @csrf
              <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>
            </form>
          </td>
        </tr>
         @endif
        @endforeach
      @endforeach
      <tr>
      	<td></td>
      	<td></td>
      	<td class="font-weight-bold">Total: {{count($basket)}}</td>
      	<td class="font-weight-bold">Total: ${{$total}}</td>
  	   </tr>
    </tbody>
  </table>

  @if (count($items) > 0)
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <div id="paypal-button"></div>
    <script>
      paypal.Button.render({
        env: 'sandbox', // Or 'production'
        // Set up the payment:
        // 1. Add a payment callback
        
        payment: function(data, actions) {
          // 2. Make a request to your server
          return actions.request.post('/api/create-payment',
            {
            userId: "{{ Auth::user()->id}}"
           })
            .then(function(res) {
              // 3. Return res.id from the response
              return res.id;
            });
        },
        // Execute the payment:
        // 1. Add an onAuthorize callback
        onAuthorize: function(data, actions) {
          // 2. Make a request to your server
          return actions.request.post('/api/execute-payment', {
            paymentID: data.paymentID,
            payerID:   data.payerID
          })
            .then(function(res) {
               actions.redirect();
            }).catch(function(err){
              console.log("MyError "+err);
            });
        }
      }, '#paypal-button');
    </script>
  @endif
</div>
@endsection