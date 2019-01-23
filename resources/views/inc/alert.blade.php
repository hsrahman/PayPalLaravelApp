@if(count($errors) > 0)
	@foreach($errors->all() as $error)
		<div class="alert alert-danger alert-dismissible show mt-2 z-depth-3" role="alert">
		  <strong>Error:</strong> {{$error}}
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		  </button>
		</div>
	@endforeach
@endif

@if(session('success'))
	<div class="alert alert-success alert-dismissible show mt-2 z-depth-3"  role="alert">
	  {{session('success')}}
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
@endif

@if(session('error'))
	<div class="alert alert-danger alert-dismissible show mt-2 z-depth-3"  role="alert">
	  {{session('error')}}
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
@endif

