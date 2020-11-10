@extends('layouts.master')
@section('title') iMarS | Signup @stop

@section('content')  
 

<section class="login-outer">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="operator-outer">
						<div class="img">
							<a href="{{ URL::asset('/operator-signup') }}">
								<img src="{{ URL::asset('/public/media') }}/icon-1.png" alt="#">
							</a>
						</div>
						<p>Operator sign up</p>
					</div>
					<div class="operator-outer">
						<div class="img">
							<a href="{{ URL::asset('/surveyor-signup') }}">
								<img src="{{ URL::asset('/public/media') }}/icon-2.png" alt="#">
							</a>
						</div>
						<p>Surveyor sign up </p>
					</div>
				</div>
			</div>
		</div>
	</section>
@stop
