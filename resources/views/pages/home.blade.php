@extends('layouts.master')
@section('title') iMarS | Home @stop

@section('content') 
<section class="slider">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="slider-inner">
						<h2>Sail through fixing your surveyor, <span>with just few clicks!</span></h2>
						<div class="row">
							<div class="col-md-6 col-lg-4">
								<h4>Appoint Surveyors</h4>
								<p>At a finger tip, appoint surveyors that best fit your needs.
								<?php $content= new  App\Models\Content; 
								$content_data = $content->select("slug")->where('user','op')->where('slug','why-imars')->first();
								$content_data1 = $content->select("slug")->where('user','s')->where('slug','why-imars')->first();

						?> <br><br>
									<span><a href="{{ URL::asset('/page/operator/why-imars') }}">Learn more…</a></span></p>
								<button onclick="window.location.href='{{ URL::asset('/operator-signup') }}'">

									<span>Sign up </span>
									to appoint surveyors
								</button>
							</div>
							<div class="col-md-6 col-lg-4">
								<h4>Conduct Surveys</h4>
								<p>Have access to a large pool of survey requests, and manage your own schedule.
								<span><a href="{{ URL::asset('/page/surveyor/why-imars') }}">Learn more…</a></span>
									</p>
								<button onclick="window.location.href='{{ URL::asset('/surveyor-signup') }}'">
									<span>Sign up </span>
									to conduct surveys
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@stop 