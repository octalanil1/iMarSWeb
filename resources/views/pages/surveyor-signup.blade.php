@extends('layouts.master')
@section('title') iMarS | Signup @stop

@section('content')  
 

<section class="login-outer">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="operator-outer">
						<div class="img">
							<a href="{{ URL::asset('/individual-surveyor-signup') }}">
								<img src="{{ URL::asset('/public/media') }}/icon-3.png" alt="#">
							</a>
						</div>
						<p>Individual Surveyor Sign Up</p>
					</div>
					<div class="operator-outer">
						<div class="img">
							<a href="{{ URL::asset('/company-surveyor-signup') }}">
								<img src="{{ URL::asset('/public/media') }}/icon-4.png" alt="#">
							</a>
						</div>
						<p>Survey Company Sign Up</p>
					</div>
				</div>
			</div>
		</div>
	</section>
@stop
