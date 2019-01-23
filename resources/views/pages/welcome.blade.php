@extends('layouts.app')
@section('content')
	<div class="p-5 card m-2 table-responsive">
		<table class=" table table-sm table-hover btn-table">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th>Product</th>
		      <th>Name</th>
		      <th>Price</th>
		      <th>Quantity</th>
		      @if(!Auth::guest())
		      <th></th>
		      @endif
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach ($products as $product) 
		  		<tr>
			      <td scope="row"><strong>{{$loop->iteration}}</strong></td>
			      <td>{{$product->img}}</td>
			      <td>{{$product->name}}</td>
			      <td>${{$product->price}}</td>
			      <td>{{$product->quantity}}</td>
			      @if(!Auth::guest())
			      <td>
					<form action='{{ route('addItem', $product->id) }}' method='POST'/>
						 @csrf
						<button type="submit"  class="btn btn-primary"><span class="glyphicon glyphicon-shopping-cart"></span></button>
					</form>
			  	  </td>
			      @endif
			    </tr>
		  	@endforeach
		  </tbody>
		</table>
		
		<div class="text-center">{{ $products->links() }}</div>
	</div>
@endsection
