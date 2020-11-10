<ul>
@if(Auth::user()->type=="0" || Auth::user()->type=="1")
<li class="icon_surveys"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/mysurvey')}}');"><span class="name">My Surveys  </span></a></li>

    @if(Auth::user()->status=="1")
    <li class="icon_appoint"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/appoint-surveyor')}}');"><span class="name">Appoint</span></a></li>
    <li class="icon_vessels"><a href="javascript:void(0);"  onclick="showpage('{{URL::asset('/myship')}}');"><span class="name">Vessels</span></a></li>
    <li class="icon_surveyors"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myagent')}}');"><span class="name">Agents</span></a></li>
    
    @if(Auth::user()->type=="0")
        <li class="icon_surveyors"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myoperator')}}');"><span class="name">Operators</span></a></li>
        <li class="icon_earning"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myfinance')}}');"><span class="name">Finance</span></a></li>

        @endif
        @if(Auth::user()->type=="1")
        <li class="icon_earning"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myfinance')}}');"><span class="name">Finance</span></a></li>
        @endif
    @endif
    <li class="icon_report"><a href="javascript:void(0);"onclick="showpage('{{URL::asset('/report-issue')}}');"><span class="name">Report an Issue</span></a></li> 

    <!-- <li class="icon_surveyors"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/')}}');"><span class="name">Earnings</span></a></li> -->


@else
<li class="icon_surveys"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/mysurvey')}}');"><span class="name">My Surveys</span></a></li>
    @if(Auth::user()->status=="1")
        @if(Auth::user()->type=="2" ||  Auth::user()->type=="4")
        @if(Auth::user()->type=="2")
          <li class="icon_calendar"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/mysurveyor')}}');"><span class="name">Surveyors</span></a></li>
         
         @endif
          <li class="icon_services"><a href="javascript:void(0);"  onclick="showpage('{{URL::asset('/my-survey-types')}}');"><span class="name"><span class="name">My Survey Types</span></a></li>
        <li class="icon_ports"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myport')}}');"><span class="name">Ports</span></a></li>
       
      @endif
      
      <li class="icon_calendar"><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/mycalendar')}}');"><span class="name">My Calendar</span></a></li>

      @endif
    
       <li class="icon_report"><a href="javascript:void(0);"onclick="showpage('{{URL::asset('/report-issue')}}');"><span class="name">Report an Issue</span></a></li> 
      
        @if(Auth::user()->status=="1" && (Auth::user()->type=="2" ||  Auth::user()->type=="4"))        
        <li class="icon_profile active"><a><span class="name">Earning Manager</span><span class="icon-down"><i class="fas fa-chevron-up"></i></span></a>
        <ul class="submenu" style="display:none;">
        <li class=""><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/payment-detail')}}');"><span class="name">Payment Detail</span></a></li>
        <li><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myearning')}}');"><span class="name">Earning</span></a></li>

    </ul>
    </li>
      @endif
      
  @endif
  <li class="icon_profile active"><a><span class="name">Account</span><span class="icon-down"><i class="fas fa-chevron-up"></i></span></a>
            <ul class="submenu" style="display:none;">
                <li class=""><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/change-password')}}');"><span class="name">Change Password</span></a></li>
                <li><a href="javascript:void(0);" onclick="showpage('{{URL::asset('/myprofile')}}');"><span class="name">My Profile</span></a></li>
            </ul>
        </li>
      <li class="icon_logout"><a href="{{URL::to('logout')}}"><span class="name">Logout</span></a></li>
</ul>
