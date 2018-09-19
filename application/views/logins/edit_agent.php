<?php
	$uid = $this->input->get('uid');
	$key = $this->input->get('key');
	foreach($result->result() as $row)
	{
		$name = $row->name;        
				$uname = $row->uname;
		        $email = $row->email;        
        		$password = $row->password;       
		        $mobile = $row->mobile;        		
		        $address = $row->address;        
        		$city = $row->city;
				$operator_id = $row->operator_id;
				$agent_type = $row->agent_type;
				$agent_type_name = $row->agent_type_name;				
				$status = $row->status;				
				$pay_type = $row->pay_type;
				$bal_limit = $row->bal_limit;				
				$margin = $row->margin;
				$comm_type = $row->comm_type;
								
				$by_cash = $row->by_cash;
				if ($by_cash == "yes") {
        			$by_cash = 'checked="checked"';
    			}
				$by_phone = $row->by_phone;
				if ($by_phone == "yes") {
        			$by_phone = 'checked="checked"';
    			} 
				$by_agent = $row->by_agent;
				if ($by_agent == "yes") {
        			$by_agent = 'checked="checked"';
    			} 
				$by_phone_agent = $row->by_phone_agent;
				if ($by_phone_agent == "yes") {
        			$by_phone_agent = 'checked="checked"';
    			}	
				$by_employee = $row->by_employee;
				if ($by_employee == "yes") {
        			$by_employee = 'checked="checked"';
    			}			 
				$ticket_booking = $row->ticket_booking;
				if ($ticket_booking == "yes") {
        			$ticket_booking = 'checked="checked"';
    			}
	            $is_hover = $row->is_hover;
				if ($is_hover == 1) {
        			$is_hover = 'checked="checked"';
    			}
    	        $agent_charge = $row->agent_charge;
				if ($agent_charge == "yes") {
        			$agent_charge = 'checked="checked"';
    			}
        	    $price_edit = $row->price_edit;
				if ($price_edit == "yes") {
        			$price_edit = 'checked="checked"';
    			}
            	$changeprice = $row->changeprice;
				if ($changeprice == "yes") {
        			$changeprice = 'checked="checked"';
    			}
	            $grabrelease = $row->grabrelease;
				if ($grabrelease == "yes") {
        			$grabrelease = 'checked="checked"';
    			}
    	        $individual_seatfare = $row->individual_seatfare;
				if ($individual_seatfare == "yes") {
        			$individual_seatfare = 'checked="checked"';
    			}
        	    $quotaupdation = $row->quotaupdation;
				if ($quotaupdation == "yes") {
        			$quotaupdation = 'checked="checked"';
    			}
            	$cancelservice = $row->cancelservice;
				if ($cancelservice == "yes") {
        			$cancelservice = 'checked="checked"';
    			}           
	            $ticket_cancellation = $row->ticket_cancellation;
				if ($ticket_cancellation == "yes") {
        			$ticket_cancellation = 'checked="checked"';
    			} 			
				$branchcancellation = $row->branchcancellation;
				if ($branchcancellation == "yes") {
        			$branchcancellation = 'checked="checked"';
    			}
        	    $ticket_modify = $row->ticket_modify;
				if ($ticket_modify == "yes") {
        			$ticket_modify = 'checked="checked"';
    			}
            	$ticket_reschedule = $row->ticket_reschedule;
				if ($ticket_reschedule == "yes") {
        			$ticket_reschedule = 'checked="checked"';
    			}
	            $discount = $row->discount;
				if ($discount == "yes") {
        			$discount = 'checked="checked"';
    			}
    	        $vehicle_assignment = $row->vehicle_assignment;
				if ($vehicle_assignment == "yes") {
        			$vehicle_assignment = 'checked="checked"';
    			}
        	    $boardingchart = $row->boardingchart;
				if ($boardingchart == "yes") {
        			$boardingchart = 'checked="checked"';
    			}
            	$detailreports = $row->detailreports;
				if ($detailreports == "yes") {
        			$detailreports = 'checked="checked"';
    			}
	            $rtalist = $row->rtalist;
				if ($rtalist == "yes") {
        			$rtalist = 'checked="checked"';
    			}
    	        $branchlogins = $row->branchlogins;
				if ($branchlogins == "yes") {
        			$branchlogins = 'checked="checked"';
    			}           
        	    $postpaidlogins = $row->postpaidlogins;
				if ($postpaidlogins == "yes") {
        			$postpaidlogins = 'checked="checked"';
    			}
				$prepaidlogins = $row->prepaidlogins;
				if ($prepaidlogins == "yes") {
        			$prepaidlogins = 'checked="checked"';
    			}
	            $createbus = $row->createbus;
				if ($createbus == "yes") {
        			$createbus = 'checked="checked"';
    			}
    	        $activedeactive = $row->activedeactive;
				if ($activedeactive == "yes") {
        			$activedeactive = 'checked="checked"';
    			}
        	    $modifybus = $row->modifybus;
				if ($modifybus == "yes") {
        			$modifybus = 'checked="checked"';
    			}
            	$spservice = $row->spservice;
				if ($spservice == "yes") {
        			$spservice = 'checked="checked"';
    			}
	}	
?>
<script type="text/javascript">
    function checkUser()
    {	
        var username = $('#username').val();		
        $("#un").empty();		
        $.post('<?php echo base_url("Logins/checkUser") ?>', {username: username}, function (res) {
		
            if (res == 1)
            {
                $("#un").html("User Name Already Exist !!");
            }

        });

    }

    function validate()
    {
        var name = $('#name').val();        
        var username = $('#username').val();
        var email = $('#email').val();
        var str = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]/;
        var password = $('#password').val();       
        var contact = $('#contact').val();        
        var address = $('#address').val();        
        var location = $('#location').val();
		var bal_limit = $('#bal_limit').val();		
		var margin = $('#margin').val();
		var comm_type = $('#comm_type').val();
		var status = $('#status').val();
        var contact1 = /^\d+(,\d+)*$/;
        var lpay = /^[0-9]*\.?[0-9]+$/;
		var uid = '<?php echo $uid ;?>';  
		var key = '<?php echo $key ;?>';      
        if (name == '')
        {
            alert('Provide Name !');
            document.getElementById('name').focus();
            $('#name').focus();
            return false;
        }
        else if (username == '')
        {
            alert('Provide Username !');
            document.getElementById('username').focus();
            return false;
        }
        else if (email == '' || !str.test(email))
        {
            alert('Provide correct email !');
            document.getElementById('email').focus();
            return false;
        }
        else if (password == '')
        {
            alert('Provide Password !');
            document.getElementById('password').focus();
            return false;
        }        
        else if (contact == '' || !contact1.test(contact))
        {
            alert('Provide correct contact number !');
            document.getElementById('contact').focus();
            return false;
        }
		else if (bal_limit == '')
        {
            alert('Provide Balance Limit !');
            document.getElementById('bal_limit').focus();
            return false;
        }
		else if (margin == '')
        {
            alert('Provide Margin !');
            document.getElementById('margin').focus();
            return false;
        }
		else if (comm_type == '')
        {
            alert('Provide Commission Type !');
            document.getElementById('comm_type').focus();
            return false;
        }
		else if (status == '')
        {
            alert('Kindly select Status!');
            document.getElementById('status').focus();
            return false;
        }
        else
        {
            var by_cash = "";
            var by_phone = "";
            var by_phone_agent = "";
            var by_agent = "";
            var by_employee = "";

            if ($('#by_cash').is(':checked'))
            {
                by_cash = "yes";
            }
            else
            {
                by_cash = "no";
            }

            if ($('#by_phone').is(':checked'))
            {
                by_phone = "yes";
            }
            else
            {
                by_phone = "no";
            }

            if ($('#by_agent').is(':checked'))
            {
                by_agent = "yes";
            }
            else
            {
                by_agent = "no";
            }

            if ($('#by_phone_agent').is(':checked'))
            {
                by_phone_agent = "yes";
            }
            else
            {
                by_phone_agent = "no";
            }
			
			if ($('#by_employee').is(':checked'))
            {
                by_employee = "yes";
            }
            else
            {
                by_employee = "no";
            }
			
            var ticket_booking = "";
            var is_hover = "";
            var agent_charge = "";
            var price_edit = "";
            var changeprice = "";
            var grabrelease = "";
            var individual_seatfare = "";
            var quotaupdation = "";
            var cancelservice = "";           
            var ticket_cancellation = ""; 			
			var branchcancellation = "";
            var ticket_modify = "";
            var ticket_reschedule = "";
            var discount = "";
            var vehicle_assignment = "";
            var boardingchart = "";
            var detailreports = "";
            var rtalist = "";
            var branchlogins = "";           
            var postpaidlogins = "";
			var prepaidlogins = "";
            var createbus = "";
            var activedeactive = "";
            var modifybus = "";
            var spservice = "";
            
            if ($('#ticket_booking').is(':checked'))
            {
                ticket_booking = "yes";
            }
            else
            {
                ticket_booking = "no";
            }

            if ($('#is_hover').is(':checked'))
            {
                is_hover = "1";
            }
            else
            {
                is_hover = "0";
            }

            if ($('#agent_charge').is(':checked'))
            {
                agent_charge = "yes";
            }
            else
            {
                agent_charge = "no";
            }

            if ($('#price_edit').is(':checked'))
            {
                price_edit = "yes";
            }
            else
            {
                price_edit = "no";
            }

            if ($('#changeprice').is(':checked'))
            {
                changeprice = "yes";
            }
            else
            {
                changeprice = "no";
            }

            if ($('#grabrelease').is(':checked'))
            {
                grabrelease = "yes";
            }
            else
            {
                grabrelease = "no";
            }

            if ($('#individual_seatfare').is(':checked'))
            {
                individual_seatfare = "yes";
            }
            else
            {
                individual_seatfare = "no";
            }

            if ($('#quotaupdation').is(':checked'))
            {
                quotaupdation = "yes";
            }
            else
            {
                quotaupdation = "no";
            }

            if ($('#cancelservice').is(':checked'))
            {
                cancelservice = "yes";
            }
            else
            {
                cancelservice = "no";
            }
            if ($('#ticket_cancellation').is(':checked'))
            {
                ticket_cancellation = "yes";
            }
            else
            {
                ticket_cancellation = "no";
            }
			if ($('#branchcancellation').is(':checked'))
            {
                branchcancellation = "yes";
            }
            else
            {
                branchcancellation = "no";
            }
			if ($('#ticket_modify').is(':checked'))
            {
                ticket_modify = "yes";
            }
            else
            {
                ticket_modify = "no";
            }
			if ($('#ticket_reschedule').is(':checked'))
            {
                ticket_reschedule = "yes";
            }
            else
            {
                ticket_reschedule = "no";
            }
			if ($('#discount').is(':checked'))
            {
                discount = "yes";
            }
            else
            {
                discount = "no";
            }
			if ($('#vehicle_assignment').is(':checked'))
            {
                vehicle_assignment = "yes";
            }
            else
            {
                vehicle_assignment = "no";
            }
			if ($('#boardingchart').is(':checked'))
            {
                boardingchart = "yes";
            }
            else
            {
                boardingchart = "no";
            }
			if ($('#detailreports').is(':checked'))
            {
                detailreports = "yes";
            }
            else
            {
                detailreports = "no";
            }
			if ($('#rtalist').is(':checked'))
            {
                rtalist = "yes";
            }
            else
            {
                rtalist = "no";
            }
			if ($('#branchlogins').is(':checked'))
            {
                branchlogins = "yes";
            }
            else
            {
                branchlogins = "no";
            }
			if ($('#postpaidlogins').is(':checked'))
            {
                postpaidlogins = "yes";
            }
            else
            {
                postpaidlogins = "no";
            }
			if ($('#prepaidlogins').is(':checked'))
            {
                prepaidlogins = "yes";
            }
            else
            {
                prepaidlogins = "no";
            }
			if ($('#createbus').is(':checked'))
            {
                createbus = "yes";
            }
            else
            {
                createbus = "no";
            }
			if ($('#activedeactive').is(':checked'))
            {
                activedeactive = "yes";
            }
            else
            {
                activedeactive = "no";
            }
			if ($('#modifybus').is(':checked'))
            {
                modifybus = "yes";
            }
            else
            {
                modifybus = "no";
            }
			if ($('#spservice').is(':checked'))
            {
                spservice = "yes";
            }
            else
            {
                spservice = "no";
            }
			
            var r = window.confirm("Are You Sure Want To Update Agent");

            if (r == true)
            {
                $.post("<?php echo base_url("Logins/edit_agent2") ?>", {
				name:name,        
				username:username,
		        email:email,        
        		password:password,       
		        contact:contact,        		
		        address:address,        
        		location:location,
				bal_limit:bal_limit,
				margin:margin,
				comm_type:comm_type,
				status:status,        
				key : key,
				by_cash: by_cash, 
				by_phone: by_phone, 
				by_agent: by_agent, 
				by_phone_agent: by_phone_agent,	
				by_employee:by_employee,			 
				ticket_booking:ticket_booking,
	            is_hover:is_hover,
    	        agent_charge:agent_charge,
        	    price_edit:price_edit,
            	changeprice:changeprice,
	            grabrelease:grabrelease,
    	        individual_seatfare:individual_seatfare,
        	    quotaupdation:quotaupdation,
            	cancelservice:cancelservice,           
	            ticket_cancellation:ticket_cancellation, 			
				branchcancellation:branchcancellation,
        	    ticket_modify:ticket_modify,
            	ticket_reschedule:ticket_reschedule,
	            discount:discount,
    	        vehicle_assignment:vehicle_assignment,
        	    boardingchart:boardingchart,
            	detailreports:detailreports,
	            rtalist:rtalist,
    	        branchlogins:branchlogins,           
        	    postpaidlogins:postpaidlogins,
				prepaidlogins:prepaidlogins,
	            createbus:createbus,
    	        activedeactive:activedeactive,
        	    modifybus:modifybus,
            	spservice:spservice,
				uid:uid
				}, function (res)
                {
                    //alert(res);
					if (res == 1)
                    {
					  	alert('Agent Updated Successfully!!'); 
							if(key == 'branch'){
								window.location = '<?php echo base_url('Logins/Branch_logins'); ?>';
							}else if(key == 'postpaid'){
								window.location = '<?php echo base_url('Logins/Postpaid_logins'); ?>';
							}else if(key == 'prepaid'){
								window.location = '<?php echo base_url('Logins/prepaid_logins'); ?>';
							}
                    }
                    else
                    {                            
                        alert('Problem in storing, Kindly Contact us!!');
                    }
                });
            }
        }
    }
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">EDIT AGENT<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width="25%" valign="top"><table width="100%" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td width="4%">&nbsp;</td>
					<td width="31%">Name</td>
					<td width="37%"><input type="text" id="name" name="name" class="inputfield" value="<?php echo $name;?>" /></td>
					<td width="28%">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Username</td>
					<td><input type="text" id="username" name="username"   class="inputfield"  onchange="checkUser();" value="<?php echo $uname;?>" disabled="disabled"/></td>
					<td><span id="un" style="color:#FF0000"></span></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Password</td>
					<td><input type="text" id="password" name="password" class="inputfield" value="<?php echo $password;?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Email</td>
					<td><input type="text" id="email" name="email"  class="inputfield" value="<?php echo $email;?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Contact No</td>
					<td><input type="text" id="contact" name="contact" class="inputfield"  maxlength="10" value="<?php echo $mobile;?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Location</td>
					<td><input type="text" id="location" name="location" class="inputfield" value="<?php echo $city;?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Address</td>
					<td><textarea rows="3" cols="18" id="address" name="address"  class="inputfield"  ><?php echo $address;?></textarea></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Balance Limit </td>
					<td><input type="text" id="bal_limit" name="bal_limit" class="inputfield" value="<?php echo abs($bal_limit);?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Commission</td>
					<td><input type="text" id="margin" name="margin" class="inputfield" value="<?php echo $margin;?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Commission Type </td>
					<td><select name="comm_type" id="comm_type">
					<?php 
					if($comm_type == 'percent'){
					?>
							<option value="0">-- Select --</option>
							<option value="percent" selected="selected">Percent</option>
							<option value="rupees">Rupees</option>
					<?php 
					}else if($comm_type == 'rupees'){
					?>
							<option value="0">-- Select --</option>
							<option value="percent">Percent</option>
							<option value="rupees" selected="selected">Rupees</option>
					<?php 
					}
					?>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Status</td>
					<td><select name="status" id="status">
					<?php 
					if($status == 1){
					?>
							<option value="">-- Select --</option>
							<option value="1" selected="selected">Active</option>
							<option value="0">Inactive</option>
					<?php 
					}else if($status == 0){
					?>
							<option value="">-- Select --</option>
							<option value="1">Active</option>
							<option value="0" selected="selected">Inactive</option>
					<?php 
					}
					?>
						</select></td>
					<td>&nbsp;</td>
				</tr>
			</table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td height="40"><strong>Booking Type</strong></td>
					<td height="40"><input name="by_cash" id="by_cash" type="checkbox" <?php echo $by_cash; ?> value="" />
						By Cash</td>
					<td height="40"><input name="by_phone" id="by_phone" type="checkbox" <?php echo $by_phone; ?> value="" />
						By Phone</td>
					<td height="40"><input name="by_agent" id="by_agent" type="checkbox" <?php echo $by_agent; ?> value="" />
						By Agent</td>
					<td height="40"><input name="by_phone_agent" id="by_phone_agent" type="checkbox" <?php echo $by_phone_agent; ?> value="" />
						By Phone Agent</td>
					<td height="40"><input name="by_employee" id="by_employee" type="checkbox" <?php echo $by_employee; ?> value="" />
						By Employee</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Booking Mgmt</strong> </td>
					<td height="40"><input name="ticket_booking" id="ticket_booking" type="checkbox" <?php echo $ticket_booking; ?> value="" />
						Booking</td>
					<td height="40"><input name="is_hover" id="is_hover" type="checkbox" <?php echo $is_hover; ?> value="" />
						Hover</td>
					<td height="40"><input name="agent_charge" id="agent_charge" type="checkbox" <?php echo $agent_charge; ?> value="" disabled="disabled" />
						Agent Charge</td>
					<td height="40"><input name="price_edit" id="price_edit" type="checkbox" <?php echo $price_edit; ?> value="" />
						Price Edit</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Seat Mgmt </strong></td>
					<td height="40"><input name="changeprice" id="changeprice" type="checkbox" <?php echo $changeprice; ?> value="" />
						change price</td>
					<td height="40"><input name="grabrelease" id="grabrelease" type="checkbox" <?php echo $grabrelease; ?> value="" />
						Grab &amp; Release</td>
					<td height="40"><input name="individual_seatfare" id="individual_seatfare" type="checkbox" <?php echo $individual_seatfare; ?> value="" />
						Individual Seat Fare</td>
					<td height="40"><input name="quotaupdation" id="quotaupdation" type="checkbox" <?php echo $quotaupdation; ?> value="" />
						Quota Updation</td>
					<td height="40"><input name="cancelservice" id="cancelservice" type="checkbox" <?php echo $cancelservice; ?> value="" />
						Cancel Service</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Updations Mgmt</strong> </td>
					<td height="40"><input name="ticket_cancellation" id="ticket_cancellation" type="checkbox" <?php echo $ticket_cancellation; ?> value="" />
						Cancellation</td>
					<td height="40"><input name="branchcancellation" id="branchcancellation" type="checkbox" <?php echo $branchcancellation; ?> value="" />
						Branch Cancellation</td>
					<td height="40"><input name="ticket_modify" id="ticket_modify" type="checkbox" <?php echo $ticket_modify; ?> value="" />
						Ticket Modify</td>
					<td height="40"><input name="ticket_reschedule" id="ticket_reschedule" type="checkbox" <?php echo $ticket_reschedule; ?> value="" />
						Ticket Reschedule</td>
					<td height="40"><input name="discount" id="discount" type="checkbox" <?php echo $discount; ?> value="" />
						Discount</td>
					<td height="40"><input name="vehicle_assignment" id="vehicle_assignment" type="checkbox" <?php echo $vehicle_assignment; ?> value="" />
						Assign Vehicle</td>
				</tr>
				<tr>
					<td height="40"><strong>Reports Mgmt</strong> </td>
					<td height="40"><input name="boardingchart" id="boardingchart" type="checkbox" <?php echo $boardingchart; ?> value="" />
						Boarding Chart</td>
					<td height="40"><input name="detailreports" id="detailreports" type="checkbox" <?php echo $detailreports; ?> value="" />
						Detail Report</td>
					<td height="40"><input name="rtalist" id="rtalist" type="checkbox" <?php echo $rtalist; ?> value="" />
						RTA list</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Login Mgmt </strong></td>
					<td height="40"><input name="branchlogins" id="branchlogins" type="checkbox" <?php echo $branchlogins; ?> value="" />
						Branch</td>
					<td height="40"><input name="postpaidlogins" id="postpaidlogins" type="checkbox" <?php echo $postpaidlogins; ?> value="" />
						Postpaid</td>
					<td height="40"><input name="prepaidlogins" id="prepaidlogins" type="checkbox" <?php echo $prepaidlogins; ?> value="" />
						Prepaid</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
			<!--	<tr>
					<td height="40"><strong>Bus Mgmt</strong> </td>
					<td height="40"><input name="createbus" id="createbus" type="checkbox" <?php echo $createbus; ?> value="" />
						Creation</td>
					<td height="40"><input name="activedeactive" id="activedeactive" type="checkbox" <?php echo $activedeactive; ?> value="" />
						Active &amp; Deactive</td>
					<td height="40"><input name="modifybus" id="modifybus" type="checkbox" <?php echo $modifybus; ?> value="" />
						Modify</td>
					<td height="40"><input name="spservice" id="spservice" type="checkbox" <?php echo $spservice; ?> value="" />
						Special Service</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr> -->
			</table></td>
	</tr>
	<tr>
		<td align="center"><input type="submit" class="btn btn-primary" id="update" name="update" value="Update" onclick="validate()" /></td>
	</tr>
</table>

</main>

		</section>
    <!-- /.content -->
  </div>
				</div>
			</div>
		</main>
	</section>
</div>