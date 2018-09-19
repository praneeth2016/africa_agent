<?php
$operator_title = $this->session->userdata('bktravels_operator_title');
$op_url = $this->session->userdata('bktravels_op_url');
$travel_id = $this->session->userdata('bktravels_travel_id');
$ho = $this->session->userdata('bktravels_head_office');
$agent_type = $this->session->userdata('bktravels_agent_type');
$ticket_booking = $this->session->userdata('bktravels_ticket_booking');
$changeprice = $this->session->userdata('bktravels_changeprice');
$grabrelease = $this->session->userdata('bktravels_grabrelease');
$individual_seatfare = $this->session->userdata('bktravels_individual_seatfare');
$quotaupdation = $this->session->userdata('bktravels_quotaupdation');
$cancelservice = $this->session->userdata('bktravels_cancelservice');
$ticket_cancellation = $this->session->userdata('bktravels_ticket_cancellation');
$branchcancellation = $this->session->userdata('bktravels_branchcancellation');
$ticket_modify = $this->session->userdata('bktravels_ticket_modify');
$ticket_reschedule = $this->session->userdata('bktravels_ticket_reschedule');
$discount = $this->session->userdata('bktravels_discount');
$vehicle_assignment = $this->session->userdata('bktravels_vehicle_assignment');
$boardingchart = $this->session->userdata('bktravels_boardingchart');
$detailreports = $this->session->userdata('bktravels_detailreports');
$rtalist = $this->session->userdata('bktravels_rtalist');
$branchlogins = $this->session->userdata('bktravels_branchlogins');
$postpaidlogins = $this->session->userdata('bktravels_postpaidlogins');
$prepaidlogins = $this->session->userdata('bktravels_prepaidlogins');
$createbus = $this->session->userdata('bktravels_createbus');
$activedeactive = $this->session->userdata('bktravels_activedeactive');
$modifybus = $this->session->userdata('bktravels_modifybus');
$spservice = $this->session->userdata('bktravels_spservice');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $operator_title ; ?></title>
		
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/bootstrap.min.css'); ?>" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url('jquery/js/jquery.js'); ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('jquery/css/jquery-ui.css'); ?>"/>
		<script type="text/javascript" src="<?php echo base_url('jquery/js/jquery-ui.js'); ?>"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url('js/ui-bootstrap-tpls-2.0.0.min.js'); ?>"></script>

		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/app-css.v1.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/font-awesome.min.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/jquery.dataTables.min.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/bootstrap-datepicker.css'); ?>">
		<script src="<?php echo base_url('js/respond.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/html5.js'); ?>"></script>
		<script src="<?php echo base_url('js/excanvas.js'); ?>"></script>
		<script src="<?php echo base_url('js/app-js.v1.js'); ?>"></script>
		<script src="<?php echo base_url('js/jquery.dataTables.min.js'); ?>"></script>
		<script src="<?php echo base_url('js/bootstrap-datepicker.min.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/styles.css'); ?>" />
		<script type="text/javascript">
		
    $(document).ready(function ()
    {
        var i = '<?php echo $key; ?>';
        $('#li' + i).addClass('active');
    });
</script>
    </head>
    <body>
        <!-- header -->
        <!--header id="header" class="navbar navbar-sm bg bg-black">
            <ul class="nav navbar-nav navbar-avatar pull-right">
                <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="hidden-sm-only"><?php echo $this->session->userdata('bktravels_name'); ?></span> <span class="thumb-small avatar inline"><img src="<?php echo base_url('images/avatar1.jpg'); ?>" alt="Mika Sokeil" class="img-circle"></span> <b class="caret hidden-sm-only"></b> </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url('Login/password_change'); ?>">Change Password</a></li>
                        <li class="divider">
                        <li><a href="<?php echo base_url('Login/Logout'); ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
            <a class="navbar-brand" href="<?php echo $op_url ; ?>"><?php echo $operator_title ; ?></a>
            <button type="button" class="btn btn-link nav-toggle pull-left hidden-desktop" data-toggle="class:show" data-target="#nav"> <i class="icon-reorder icon-xlarge text-default"></i> </button>
        </header-->
		
<nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo $op_url ; ?>"><?php echo $operator_title ; ?></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#" type="button" data-toggle="modal" data-target="#myModal">
          	<span class="glyphicon glyphicon-link" aria-hidden="true"></span> Quick Links</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            	<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                <span class="hidden-sm-only"><?php echo $this->session->userdata('bktravels_name'); ?></span> <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo base_url('Login/password_change'); ?>">Change Password</a></li>
			   <li role="separator" class="divider"></li>
              <li><a href="<?php echo base_url('Login/Logout'); ?>"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Log Out</a></li>
            </ul>
          </li>
        </ul>
      </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>
        <!-- / header -->
        <!-- nav -->
        <nav id="nav" class="nav-primary visible-desktop nav-vertical bg-light">
            <ul class="nav">
                <?php
                if ($ticket_booking == 'yes') {
                    ?>
                    <li><a href="<?php echo base_url('booking'); ?>" id="li1"><i class="icon-ticket icon-xlarge"></i>Booking</a></li>
                    <?php
                }
                ?>
                <li class="dropdown-submenu"> <a href="#" id="li2"><i class="icon-th icon-xlarge"></i>Seats</a>
                    <ul class="dropdown-menu">
                        <?php
                        if ($changeprice == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Seats/changepricing_home'); ?>">Change Price</a></li>
                            <?php
                        }
                        if ($grabrelease == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Seats/Grab_release'); ?>">Grab &amp; Release</a></li>
                            <?php
                        }

                        if ($individual_seatfare == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Seats/Ind_seat_fare'); ?>">Individual Seat Fare</a></li>
                            <?php
                        }

                        if ($quotaupdation == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Seats/Quata'); ?>">Quota Updation</a></li>
                            <?php
                        }

                        if ($cancelservice == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Seats/Cancel_service'); ?>">Cancel Service</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="dropdown-submenu"> <a href="#" id="li3"><i class="icon-edit icon-xlarge"></i>Updations</a>
                    <ul class="dropdown-menu">
						 <li><a href="<?php echo base_url('Updations/Ticket_print'); ?>">Ticket Print</a></li>
                        <?php
                        if ($ticket_cancellation == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/Ticket_cancel'); ?>">Ticket Cancellation</a></li>
                            <?php
                        }

                        if ($branchcancellation == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/Branch_tkt_cancel'); ?>">Branch Cancellation</a></li>
                            <?php
                        }

                        if ($ticket_modify == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/change_tkt_status'); ?>">Change Ticket Status</a></li>
                            <?php
                        }

                        if ($ticket_reschedule == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/tkt_reschedule'); ?>">Ticket Reschedule</a></li>
                            <?php
                        }
						if ($ho == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/DelayServiceSMS'); ?>">Delay SMS</a></li>
                            <?php
                        }

                        if ($discount == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/discount'); ?>">Discount</a></li>
                            <?php
                        }

                        if ($ticket_booking == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/Cc_policy'); ?>">Cancellation Policy</a></li>
                            <?php
                        }

                        if ($vehicle_assignment == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/vihicle_assignment'); ?>">Vehicle Assignment</a></li>
                            <?php
                        }
						if ($ho == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Updations/bus_numbers'); ?>">Bus Numbers</a></li>
							<li><a href="<?php echo base_url('Updations/drivers'); ?>">Drivers</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="dropdown-submenu"> <a href="#" id="li4"><i class="icon-list icon-xlarge"></i>Reports</a>
                    <ul class="dropdown-menu">
					 <li><a href="<?php echo base_url('Reports/mybookings'); ?>">My Bookings</a></li>
                        <?php
                        if ($boardingchart == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Reports/Boarding_chart'); ?>">Boarding Chart</a></li>
                            <?php
                        }

                        if ($detailreports == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Reports/generateDetailReport'); ?>">Detailed Reports</a></li>
                            <?php
                        }

                        if ($rtalist == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Reports/Rta_list'); ?>">RTA List</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="dropdown-submenu"> <a href="#" id="li5"><i class="icon-group icon-xlarge"></i>Logins</a>
                    <ul class="dropdown-menu">
                        <?php
                        if ($branchlogins == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Logins/Branch_logins'); ?>">Branch</a></li>
                            <?php
                        }

                        if ($postpaidlogins == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Logins/Postpaid_logins'); ?>">Postpaid</a></li>
                            <?php
                        }

                        if ($prepaidlogins == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Logins/prepaid_logins'); ?>">Prepaid</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="dropdown-submenu"> <a href="#" id="li6"><i class="icon-cogs icon-xlarge"></i>Bus</a>
                    <ul class="dropdown-menu">
                        <?php
                        if ($createbus == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Bus/createBus'); ?>">Creation</a></li>
                            <?php
                        }

                        if ($activedeactive == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Bus/active_deactive'); ?>">Active &amp; Deactive</a></li>
                            <?php
                        }

                        if ($modifybus == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Bus/modify_bus'); ?>">Modify</a></li>
                            <?php
                        }

                        if ($spservice == 'yes') {
                            ?>
                            <li><a href="<?php echo base_url('Bus/operator_Special_Service'); ?>">Sp Service</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- / nav -->
        <section id="content">
            <main class="main">
