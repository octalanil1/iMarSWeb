@if($data['status']==1)
<div>Your Account has been activated. Now you can <a href="{{ $data['signin_url']}}">login </a> with your <a href="{{ $data['signin_url']}}">login </a> credential.</div>
@elseif($data['status']==2)
<div>Your Account has been deactivated. Please contact SAG RTA team.</div>
@endif