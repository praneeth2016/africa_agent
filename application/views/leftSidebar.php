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
 <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <!-- Optionally, you can add icons to the links -->
        <!--li class="active"><a href="#"><i class="fa fa-link"></i> <span>Link</span></a></li-->
		<?php if ($ticket_booking == 'yes') { ?>
        <li><a href="<?php echo base_url('booking'); ?>" id="li1"><i class="fa fa-link"></i><span><i class="icon-ticket icon-xlarge">Booking</i></span></a></li>
		<?php } ?>
       
		<li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Ticket</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
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

				/*if ($ticket_reschedule == 'yes') {
					?>
					<li><a href="<?php echo base_url('Updations/tkt_reschedule'); ?>">Ticket Reschedule</a></li>
					<?php
				}*/
				
				?>
          </ul>
        </li>
         <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Seats</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
			<?php if ($changeprice == 'yes') {					?>
					<li><a href="<?php echo base_url('Seats/changepricing_home'); ?>">Change Price</a></li>
			<?php } if ($grabrelease == 'yes') {?>
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
				
				?>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Bus</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
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

				if ($vehicle_assignment == 'yes') {
					?>
					<li><a href="<?php echo base_url('Updations/vihicle_assignment'); ?>">Vehicle Assignment</a></li>
					<?php
				}
				if ($boardingchart == 'yes') {
    				?>
    				<li><a href="<?php echo base_url('Reports/Boarding_chart'); ?>">Boarding Chart</a></li>
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
		<li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu"><li><a href="<?php echo base_url('Reports/mybookings'); ?>">My Bookings</a></li>
			<?php

			if ($detailreports == 'yes') {
				?>
				<li><a href="<?php echo base_url('Reports/generateDetailReport'); ?>">Detailed Reports</a></li>
				<?php
			}

			/*if ($rtalist == 'yes') {
				?>
				<li><a href="<?php echo base_url('Reports/Rta_list'); ?>">RTA List</a></li>
				<?php
			}*/
			?>
          </ul>
        </li>
		<li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Logins</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
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
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <?php
				if ($ho == 'yes') {
					?>
					<li><a href="<?php echo base_url('Updations/bus_numbers'); ?>">Bus Numbers</a></li>
					<li><a href="<?php echo base_url('Updations/drivers'); ?>">Drivers</a></li>
					<?php
				}
				
				if ($ticket_booking == 'yes') {
					?>
					<li><a href="<?php echo base_url('Updations/Cc_policy'); ?>">Cancellation Policy</a></li>
					<?php
				}
				?>
          </ul>
        </li>
		
		
      </ul>
    </section>
  </aside>