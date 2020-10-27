 <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel"></div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="{{ Request::is('admin') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/users') }}">
            <i class="fa fa-users"></i> <span>Users Manager</span>
          </a>
        </li>
        <!-- <li class="{{ Request::is('admin/survey-category*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/survey-category') }}">
            <i class="fa fa-list-alt"></i> <span>Survey Category Manager</span>
          </a>
        </li> -->
		 <li class="{{ Request::is('admin/survey-type*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/survey-type') }}">
            <i class="fa fa-list-alt"></i> <span>Survey Type Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/country*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/country') }}">
          <i class="fa fa-map-marker"></i> <span>Country Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/port*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/port') }}">
          <i class="fa fa-map-marker"></i> <span>Port Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/users-port*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/users-port') }}">
          <i class="fa fa-map-marker"></i> <span>User Port Manager</span>
          </a>
        </li>
    
        <li class="{{ Request::is('admin/survey*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/survey') }}">
            <i class="fa fa-bar-chart"></i> <span>Survey Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/users-survey-price*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/users-survey-price') }}">
            <i class="fa fa-bar-chart"></i> <span>User Survey Price Manager</span>
          </a>
        </li>
        <li class="treeview {{ Request::is('admin/dispute-request*') || Request::is('admin/payment-request*')  ? 'active' : '' }}"">
          <a href="#">
            <i class="fa fa-bullhorn"></i> <span>Request Manager</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ Request::is('admin/dispute-request*') ? 'active' : '' }}"><a href="{{ URL::to('/admin/dispute-request') }}"><i class="fa fa-circle-o"></i> Dispute Request</a></li>
            <li clas="{{ Request::is('admin/payment-request*') ? 'active' : '' }}"><a href="{{ URL::to('/admin/payment-request') }}"><i class="fa fa-circle-o"></i>Payment Request</a></li>
          </ul>
        </li>
        <li class="{{ Request::is('admin/earning*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/earning') }}">
            <i class="fa fa-money"></i> <span>Earning Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/notification*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/notification') }}">
            <i class="fa fa-bell"></i> <span>Notification Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/setting*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/Setting') }}">
            <i class="fa fa-cogs"></i> <span>Setting Manager</span>
          </a>
        </li>
        <li class="{{ Request::is('admin/content*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/content') }}">
            <i class="fa fa-file-text-o"></i> <span>Content Manager</span>
            
          </a>
        </li>
        <li class="{{ Request::is('admin/email-templates*') ? 'active' : '' }}">
          <a href="{{ URL::to('/admin/email-templates') }}">
            <i class="fa fa-envelope"></i> <span>Email Templates Manager</span>
            
          </a>
        </li> 
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
          </ul>
        </li> -->
        
      </ul>
    </section>