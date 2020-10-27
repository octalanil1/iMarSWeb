<ul class="sidebar-menu">

<?php $userdt =  Auth::user();

if($userdt->user_type=="Instructor") {

 /*-----------------------------------instructor left bar ----------------------------------------------------*/

 ?>



          <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ URL::to('/') }}"><i class="dash-icn"></i>Dashboard</a></li>

          <li  class="{{ Request::is('profile') ? 'active' : '' }}"><a href="{{ URL::to('/profile') }}"><i class="usma-icn"></i>Profile Management</a></li>

          <li class="dropdwon {{ Request::is('manage-course*') || Request::is('manage-course/signup*') ? 'active' : '' }}"><a href="#" class="dropdownbtn"><i class="coma-icn"></i>Course Management  <span class="showmenu"></span></a>

          

            <ul class="submenu">

              <li class="{{ Request::is('manage-course') || Request::is('manage-course/edit*') ? 'active' : '' }}">
              <a href="{{ URL::to('manage-course') }}"><i class="coma-icn"></i>Manage Courses</a></li>
              <li class="{{ Request::is('manage-course/add') ? 'active' : '' }}"><a href="{{ URL::to('/manage-course/add') }}"><i class="swim-icn"></i>Add Module</a></li>
              <li class="{{ Request::is('manage-course/signup*') ? 'active' : '' }}"><a href="{{ URL::to('/manage-course/signup') }}"><i class="swim-icn"></i>Sign Up</a></li>

              <?php /*?><li><a href="#"><i class="fust-icn"></i>Future Stars</a></li><?php */?>

            </ul>

          </li>
          
          <li class="dropdwon {{ Request::is('manage-attendance*') ? 'active' : '' }}"><a href="#" class="dropdownbtn"><i class="coma-icn"></i>Attendance <span class="showmenu"></span></a>

          

            <ul class="submenu">

              <li class="{{ Request::is('manage-attendance/today*') ? 'active' : '' }}">
              <a href="{{ URL::to('/manage-attendance/today') }}"><i class="coma-icn"></i>Today Attendance</a></li>

              <li class="{{ Request::is('manage-attendance/view*') ? 'active' : '' }}"><a href="{{ URL::to('/manage-attendance/view') }}"><i class="swim-icn"></i>View Attendance</a></li>

            </ul>

          </li>
          <li class="{{ Request::is('notifications*') ? 'active' : '' }}"><a href="{{ URL::to('/notifications') }}"><i class="fina-icn"></i>Notifications</a></li>
          <li><a href="#"><i class="repo-icn"></i>Finance</a></li>
          <li><a href="#"><i class="eco-icn"></i>Store</a></li>
 <?php }else{ 

		/*-----------------------------------parent left bar ----------------------------------------------------*/

		 ?>

       

          <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ URL::to('/') }}"><i class="dash-icn"></i>Dashboard</a></li>

          <li  class="{{ Request::is('profile') ? 'active' : '' }}"><a href="{{ URL::to('/profile') }}"><i class="usma-icn"></i>Profile Management</a></li>

          <li class="dropdwon {{ Request::is('child*') ? 'active' : '' }}">
            <a href="#" class="dropdownbtn"><i class="coma-icn" ></i>Child Management <span class="showmenu"></span></a>

            <ul class="submenu">

              <li class="{{ Request::is('child') ? 'active' : '' }}"><a href="{{ URL::to('/child') }}"><i class="dido-icn"></i>show Children</a></li>

              <li class="{{ Request::is('child/add') ? 'active' : '' }}"><a href="{{ URL::to('/child/add') }}"><i class="swim-icn"></i>add Child</a></li>

               <li class="{{ Request::is('child/book-instructor*') ? 'active' : '' }}"><a href="{{ URL::to('/child/book-instructor') }}"><i class="fust-icn"></i>Book Instructor</a></li>

            </ul>

          </li>

          <li class="{{ Request::is('lessons*') ? 'active' : '' }}"><a href="{{ URL::to('/lessons') }}"><i class="fina-icn"></i>Lessons</a></li>
		<li class="{{ Request::is('attendance*') ? 'active' : '' }}"><a href="{{ URL::to('/attendance') }}"><i class="swim-icn"></i>View Attendance</a></li>
         <li class="{{ Request::is('notifications*') ? 'active' : '' }}"><a href="{{ URL::to('/notifications') }}"><i class="fina-icn"></i>Notifications</a></li>
          <li><a href="#"><i class="repo-icn"></i>Finance</a></li>

          <li><a href="#"><i class="eco-icn"></i>Store</a></li>

       

        <?php } ?>

       </ul>    