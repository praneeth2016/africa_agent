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
        var contact1 = /^\d+(,\d+)*$/;
        var lpay = /^[0-9]*\.?[0-9]+$/;
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
			
            var r = window.confirm("Are You Sure Want To Add Branch Agent");

            if (r == true)
            {
                $.post("<?php echo base_url("Logins/add_agent1") ?>", {
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
				}, function (res)
                {
                    if (res == 1)
                    {                       
                        alert(key+' Agent Registered Successfully!!');
                        window.location = '<?php echo base_url("Logins/add_agent?key=".$key); ?>';
                       
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
					<h3 class="panel-title">ADD <?php echo strtoupper($key); ?> AGENT<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
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
					<td width="37%"><input type="text" id="name" name="name" class="inputfield" /></td>
					<td width="28%">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Username</td>
					<td><input type="text" id="username" name="username"   class="inputfield"  onchange="checkUser();" /></td>
					<td><span id="un" style="color:#FF0000"></span></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Password</td>
					<td><input type="text" id="password" name="password" class="inputfield" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Email</td>
					<td><input type="text" id="email" name="email"  class="inputfield" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Contact No</td>
					<td><input type="text" id="contact" name="contact" class="inputfield"  maxlength="10" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Location</td>
					<td><input type="text" id="location" name="location" class="inputfield" value="" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Address</td>
					<td><textarea rows="3" cols="18" id="address" name="address"  class="inputfield"  ></textarea></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Balance Limit </td>
					<td><input type="text" id="bal_limit" name="bal_limit" class="inputfield" value="" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Commission</td>
					<td><input type="text" id="margin" name="margin" class="inputfield" value="" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Commission Type </td>
					<td><select name="comm_type" id="comm_type">
							<option value="0">-- Select --</option>
							<option value="percent">Percent</option>
							<option value="rupees">Rupees</option>
						</select>
					</td>
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
					<td height="40"><input name="by_cash" id="by_cash" type="checkbox" value="" checked="checked" />
						By Cash</td>
					<td height="40"><input name="by_phone" id="by_phone" type="checkbox" value="" />
						By Phone</td>
					<td height="40"><input name="by_agent" id="by_agent" type="checkbox" value="" />
						By Agent</td>
					<td height="40"><input name="by_phone_agent" id="by_phone_agent" type="checkbox" value="" />
						By Phone Agent</td>
					<td height="40"><input name="by_employee" id="by_employee" type="checkbox" value="" />
						By Employee</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Booking Mgmt</strong> </td>
					<td height="40"><input name="ticket_booking" id="ticket_booking" type="checkbox" value="" />
						Booking</td>
					<td height="40"><input name="is_hover" id="is_hover" type="checkbox" value="" />
						Hover</td>
					<td height="40"><input name="agent_charge" id="agent_charge" type="checkbox" checked="checked" value="" disabled="disabled" />
						Agent Charge</td>
					<td height="40"><input name="price_edit" id="price_edit" type="checkbox" value="" />
						Price Edit</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Seat Mgmt</strong> </td>
					<td height="40"><input name="changeprice" id="changeprice" type="checkbox" value="" />
						change price</td>
					<td height="40"><input name="grabrelease" id="grabrelease" type="checkbox" value="" />
						Grab &amp; Release</td>
					<td height="40"><input name="individual_seatfare" id="individual_seatfare" type="checkbox" value="" />
						Individual Seat Fare</td>
					<td height="40"><input name="quotaupdation" id="quotaupdation" type="checkbox" value="" />
						Quota Updation</td>
					<td height="40"><input name="cancelservice" id="cancelservice" type="checkbox" value="" />
						Cancel Service</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Updations Mgmt</strong> </td>
					<td height="40"><input name="ticket_cancellation" id="ticket_cancellation" type="checkbox" value="" />
						Cancellation</td>
					<td height="40"><input name="branchcancellation" id="branchcancellation" type="checkbox" value="" />
						Branch Cancellation</td>
					<td height="40"><input name="ticket_modify" id="ticket_modify" type="checkbox" value="" />
						Ticket Modify</td>
					<td height="40"><input name="ticket_reschedule" id="ticket_reschedule" type="checkbox" value="" />
						Ticket Reschedule</td>
					<td height="40"><input name="discount" id="discount" type="checkbox" value="" />
						Discount</td>
					<td height="40"><input name="vehicle_assignment" id="vehicle_assignment" type="checkbox" value="" />
						Assign Vehicle</td>
				</tr>
				<tr>
					<td height="40"><strong>Reports Mgmt </strong></td>
					<td height="40"><input name="boardingchart" id="boardingchart" type="checkbox" value="" />
						Boarding Chart</td>
					<td height="40"><input name="detailreports" id="detailreports" type="checkbox" value="" />
						Detail Report</td>
					<td height="40"><input name="rtalist" id="rtalist" type="checkbox" value="" />
						RTA list</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Login Mgmt</strong> </td>
					<td height="40"><input name="branchlogins" id="branchlogins" type="checkbox" value="" />
						Branch</td>
					<td height="40"><input name="postpaidlogins" id="postpaidlogins" type="checkbox" value="" />
						Postpaid</td>
					<td height="40"><input name="prepaidlogins" id="prepaidlogins" type="checkbox" value="" />
						Prepaid</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
				<tr>
					<td height="40"><strong>Bus Mgmt</strong> </td>
					<td height="40"><input name="createbus" id="createbus" type="checkbox" value="" />
						Creation</td>
					<td height="40"><input name="activedeactive" id="activedeactive" type="checkbox" value="" />
						Active &amp; Deactive</td>
					<td height="40"><input name="modifybus" id="modifybus" type="checkbox" value="" />
						Modify</td>
					<td height="40"><input name="spservice" id="spservice" type="checkbox" value="" />
						Special Service</td>
					<td height="40">&nbsp;</td>
					<td height="40">&nbsp;</td>
				</tr>
			</table></td>
	</tr>
	<tr>
		<td align="center"><input type="submit" class="btn btn-primary" id="add_new" name="add_new" value="Add" onclick="validate()" /></td>
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