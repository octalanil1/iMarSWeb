<header>
  <div class="container">
			<div class="row">
				<div class="col-md-12">
					<nav class="navbar">
					  <a class="navbar-brand" href="{{ URL::asset('/') }}">
					  	<img src="{{ URL::asset('/media') }}/logo.png" alt="#">
					  </a>

					  <div class="right-header">
					  <?php $user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{ ?>
						<a href="{{ URL::asset('/myaccount') }}" class="signup">My Account</a>
						<a href="{{ URL::asset('/logout') }}" class="login" >Logout</a>
						  
					  <?php } else {?>
						<a href="{{ URL::asset('/signin') }}" class="login" >Log in</a>
						  <a href="{{ URL::asset('/signup') }}" class="signup">Sign up</a>
					  <?php } ?>
					  </div>
					</nav>
				</div>
			</div>
    </div>
    </header>