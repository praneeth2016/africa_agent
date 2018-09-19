<?php $operator_title = $this->session->userdata('bktravels_operator_title'); ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title><?php echo $operator_title ; ?></title>
	<script>
		var selectedOnward = "<?php echo date('Y-m-d'); ?>";
	</script>
  <link rel="stylesheet" href="<?php echo base_url('css/intlTelInput.css'); ?>">
  <!--<link rel="stylesheet" href="<?php echo base_url('css/demo.css'); ?>">-->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/bootstrap.min.css'); ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/styles.css'); ?>" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url('jquery/js/jquery.js'); ?>"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('jquery/css/jquery-ui.css'); ?>"/>
  <script type="text/javascript" src="<?php echo base_url('jquery/js/jquery-ui.js'); ?>"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url('js/ui-bootstrap-tpls-2.0.0.min.js'); ?>"></script>	
  
  <script src="<?php echo base_url('js/jquery-2.2.3.min.js'); ?>"></script>
  
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/jquery.dataTables.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('css/layout.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('css/layout-styles.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('css/tooltip-styles.css'); ?>">	
		<script src="<?php echo base_url('js/bootstrap-datepicker.min.js'); ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/bootstrap-datepicker.css'); ?>">
		
	<script src="<?php echo base_url('js/jquery.dataTables.min.js'); ?>"></script>
		
	<script type="text/javascript" src="<?php echo base_url('js/app.js'); ?>"></script>
	
	<style>
	  [ng-cloak]
		{
		  display: none !important;
		}
  </style>
</head>

<body class="hold-transition skin-red sidebar-mini sidebar-collapse">
<div class="wrapper" ng-app="teAgentloginApp" ng-controller="teAgentloginCtrl" ng-cloak>
  <header class="main-header" >  
    <nav class="navbar navbar-static-top" role="navigation">
      <div class="logo-bg"><h1><?php echo $operator_title ; ?></h1></div>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
		  <li>
		  <input type="text" class="navbar-srch" name="ticket" id="ticket" placeholder="tktno/pnr/mobile/rcpno" />
		  <button name="tktstatus" id="tktstatus" onClick="ticketstatus();"><i class="fa fa-clock-o"></i></button></li>
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                <ul class="menu">
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="images/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">
                  <li><!-- start notification -->
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
         
          <!-- User Account Menu -->
		   <li><a href="<?php echo base_url('Login/password_change'); ?>" title="Change Password"><span class="glyphicon glyphicon-pencil" aria-hidden=true></span></a></li>
		  <li><a href="<?php echo base_url('Login/Logout'); ?>" title="Log Out"><span class="glyphicon glyphicon-log-out" aria-hidden=true></span></a></li>
		 
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="<?php echo base_url('images/user2-160x160.jpg'); ?>" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?php echo $this->session->userdata('bktravels_name'); ?></span>
            </a>
            
          </li>
        </ul>
      </div>
    </nav>
  </header>
 