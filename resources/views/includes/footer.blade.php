<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-lg-3">
					<div class="inner-footer">
						<h3>OPERATOR</h3>
						<ul class="footer-list">
							<!-- <li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Appoint surveyor</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Why iMarS</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Ports</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Help (requirements etc)</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Application Requirements</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#">Savings</a></li> -->
						<?php $content= new  App\Models\Content; 
								$content_data = $content->select("*")->where('user','operator')->orderBy("title","DESC")->get();

						?>
						<?php 
						
						foreach($content_data as $data){?>
							<li><a href="{{ URL::asset('/page/operator/'.$data->slug) }}"><img src="{{ URL::asset('/public/media') }}/right-arrow.png" alt="#">{{$data->title}}</a></li>

						<?php }
						?>
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-lg-3">
					<div class="inner-footer">
						<h3>SURVEYOR</h3>
						<ul class="footer-list">
							<!-- <li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Conduct surveys</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Why iMarS</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Ports</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Help (requirements etc)</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Application Requirements</a></li>
							<li><a href="#"><img src="{{ URL::asset('/media') }}/right-arrow.png" alt="#"> Earnings</a></li> -->
							<?php 
						 $content= new  App\Models\Content; 
						$content_data1 = $content->select("*")->where('user','surveyor')->orderBy("title","DESC")->get();

			
						foreach($content_data1 as $data){?>
							<li><a href="{{ URL::asset('/page/surveyor/'.$data->slug) }}"><img src="{{ URL::asset('/public/media') }}/right-arrow.png" alt="#">{{$data->title}}</a></li>

						<?php }
						?>
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-lg-3">
					<div class="inner-footer">
						<h3>iMarS</h3>
						<ul class="footer-list">
						<?php 
						 $content= new  App\Models\Content; 
						$content_data1 = $content->select("*")->where('user','all')->orderBy("title","DESC")->get();

			
						foreach($content_data1 as $data){?>
							<li><a href="{{ URL::asset('/page/all/'.$data->slug) }}"><img src="{{ URL::asset('/public/media') }}/right-arrow.png" alt="#">{{$data->title}}</a></li>

						<?php }?>
							<!-- <li><a href="{{ URL::to('/about-us') }}"><img src="{{ URL::asset('/public/media') }}/right-arrow.png" alt="#"> About us</a></li>
							<li><a href="{{ URL::to('/contact-us') }}"><img src="{{ URL::asset('/public/media') }}/right-arrow.png" alt="#"> Contact us</a></li> -->
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-lg-3">
					<div class="inner-footer">
						<h3>DOWNLOAD</h3>
						<ul class="download-list">
							<li>
								<a href="https://apps.apple.com/in/app/imars/id1502948962" target="_blank">
									<img src="{{ URL::asset('/public/media') }}/aap_store.png" alt="#">
								</a>
							</li>
							<li>
								<a href="https://play.google.com/store/apps/details?id=com.octal.imarsproject&hl=en" target="_blank">
									<img src="{{ URL::asset('/public/media') }}/google-play.png" alt="#">
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="copy-right">
			<p>© {{date('Y')}} iMarineSurvey.com. All rights reserved.</p>
		</div>
	</footer>

	<!-- <div class="modal login-modal fade" id="exampleModal-2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Sign up</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">
	        	<form>
				  <div class="form-group">
				    <select class="custom-select form-control">
					    <option selected="">country</option>
					    <option value="1">One</option>
					    <option value="2">Two</option>
					    <option value="3">Three</option>
					  </select>
				  </div>
				  <div class="form-group">
				    <input type="text" class="form-control" placeholder="PhoneNumber">
				  </div>
				  <div class="form-group">
				    <input type="text" class="form-control" placeholder="CompanyName">
				  </div>
				  <div class="form-group">
				    <input type="text" class="form-control" placeholder="Company Tax ID">
				  </div>
				  <div class="form-group">
				    <input type="text" class="form-control" placeholder="FirstName">
				  </div>
				  <div class="form-group">
				    <input type="text" class="form-control" placeholder="LastName">
				  </div>
				  <div class="form-group">
				    <input type="Email" class="form-control" placeholder="Email">
				  </div>
				  <div class="form-group">
				    <input type="Password" class="form-control" placeholder="Password">
				  </div>
				  <div class="form-group">
				    <input type="Password" class="form-control" placeholder="Reenter Password">
				  </div>
				  <div class="form-group">
				    <span class="custom_check">I Accept the terms and conditiond and Privacy policy &nbsp; <input type="checkbox"><span class="check_indicator">&nbsp;</span></span>
				  </div>
				  <button type="submit" class="btn btn-primary">Sign Up<img src="{{ URL::asset('/media') }}/arrow.png" alt="#"></button>
				  <p>Already have an account? <a href="#">Sign in</a></p>
				</form>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>-->
	<div class="modal login-modal fade" id="exampleModal-3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Appoint Surveyors</h5>
	      </div>
	      <div class="modal-body">
	        <div class="learn-modal">
	        	<p>At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. </p>
	        	<div class="modal-btn">
	        		<button data-dismiss="modal" aria-label="Close">
	        			Close
	        		</button>
	        	</div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div> 
	<div class="modal login-modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Conduct Surveys</h5>
	      </div>
	      <div class="modal-body">
	        <div class="learn-modal">
	        	<p>At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. At a finger tip, appoint surveyors that best fit your needs. </p>
	        	<div class="modal-btn">
	        		<button data-dismiss="modal" aria-label="Close">
	        			Close
	        		</button>
	        	</div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div> 
	<div class="modal login-modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Terms & Conditions</h5>
	      </div>
	      <div class="modal-body">
	        <div class="learn-modal">
			<?php $content= new  App\Models\Content; 
								$content_data = $content->select("*")->where("slug","terms-of-service")->first();

						?>
						{!!$content_data->description!!}	        	
							<div class="modal-btn">
	        		<button data-dismiss="modal" aria-label="Close">
	        			Close
	        		</button>
	        	</div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div> 
	

    <script>
		$(document).ready(function(){
		  $(".side-btn").click(function(){
                $(".side_bar").toggleClass("main");
            });
            $(".overlay").click(function(){
                $(".side_bar").removeClass("main");
            });
            $(".side-btn").click(function(){
                $(".custon-sidebar").toggleClass("overlay_outer");
            });
            $(".overlay").click(function(){
                $(".custon-sidebar").removeClass("overlay_outer");
            });
		});
	</script>


<?php Session::forget('msg'); ?>