@extends('layouts.master')
@section('title') iMarS | Email Verify @stop
@section('content')  
<section class="static-page">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
                    <h2>Your email has already been verified. Just click on the link to log in 
                        <div class="right-header"> <a href="{{ URL::asset('/signin') }}" class="login" >Login</a></div> 
                </h2>
                    
				</div>
			</div>
        </div>
	</section>
@stop
