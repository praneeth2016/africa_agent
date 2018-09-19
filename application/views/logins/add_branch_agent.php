<script type="text/javascript">
    function checkUser()
    {	
        var uname = $('#user_name').val();		
        $("#un").empty();		
        $.post('<?php echo base_url("Logins/checkUser") ?>', {uname: uname}, function (res) {
		
            if (res == 1)
            {
                $("#un").html("User Name Already Exist !!");
            }

        });

    }

    function validate()
    {
        var name = $('#name').val();        
        var username = $('#user_name').val();
        var email = $('#email_address').val();
        var str = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]/;
        var pword = $('#password').val();       
        var con = $('#contact').val();
        var landline = $('#landline').val();
        var add = $('#address').val();
        var branch = $('#branch').val();
        var branch_address = $('#branch_address').val();
        var locat = $('#locat').val();
        var contact = /^\d+(,\d+)*$/;
        var lpay = /^[0-9]*\.?[0-9]+$/;        
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
            document.getElementById('user_name').focus();
            return false;
        }
        else if (email == '' || !str.test(email))
        {
            alert('Provide correct email !');
            document.getElementById('email_address').focus();
            return false;
        }
        else if (pword == '')
        {
            alert('Provide Password !');
            document.getElementById('password').focus();
            return false;
        }        
        else if (con == '' || !contact.test(con))
        {
            alert('Provide correct contact number !');
            document.getElementById('contact').focus();
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
            var payment_reports = "";
            var booking_reports = "";
            var passenger_reports = "";
            var vehicle_assignment = "";
            var ticket_booking = "";
            var check_fare = "";
            var ticket_status = "";
            var ticket_cancellation = "";
            var ticket_modify = "";           
            var ticket_reschedule = "";          

            if ($('#payment_reports').is(':checked'))
            {
                payment_reports = "yes";
            }
            else
            {
                payment_reports = "no";
            }

            if ($('#booking_reports').is(':checked'))
            {
                booking_reports = "yes";
            }
            else
            {
                booking_reports = "no";
            }

            if ($('#passenger_reports').is(':checked'))
            {
                passenger_reports = "yes";
            }
            else
            {
                passenger_reports = "no";
            }

            if ($('#vehicle_assignment').is(':checked'))
            {
                vehicle_assignment = "yes";
            }
            else
            {
                vehicle_assignment = "no";
            }

            if ($('#ticket_booking').is(':checked'))
            {
                ticket_booking = "yes";
            }
            else
            {
                ticket_booking = "no";
            }

            if ($('#check_fare').is(':checked'))
            {
                check_fare = "yes";
            }
            else
            {
                check_fare = "no";
            }

            if ($('#ticket_status').is(':checked'))
            {
                ticket_status = "yes";
            }
            else
            {
                ticket_status = "no";
            }

            if ($('#ticket_cancellation').is(':checked'))
            {
                ticket_cancellation = "yes";
            }
            else
            {
                ticket_cancellation = "no";
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
            var margin = 0;
            var agent_type = '1';
            var agent_type_name = 'inhouse';
            var api_type = '';
            var payment_type = 'postpaid';
            var limit = 0;

            var r = window.confirm("Are You Sure Want To Add Branch Agent");

            if (r == true)
            {
                $.post("<?php echo base_url("Logins/get_agent_formdetails") ?>", {
				name: name,
				username: username,
				password: pword,
				email_address: email, 
				contact: con,
				landline: landline, 
				address: add,
				branch: branch, 
				branch_address: branch_address,
				locat: locat,
				agent_type: agent_type, 
				agent_type_name: agent_type_name, 
				payment_type: payment_type, 
				limit: limit, 
				margin: margin, 
				api_type: api_type, 
				by_cash: by_cash, 
				by_phone: by_phone, 
				by_agent: by_agent, 
				by_phone_agent: by_phone_agent,				 
				payment_reports: payment_reports, 
				booking_reports: booking_reports, 
				passenger_reports: passenger_reports, 
				vehicle_assignment: vehicle_assignment, 
				ticket_booking: ticket_booking, 
				check_fare: check_fare, 
				ticket_status: ticket_status, 
				ticket_cancellation: ticket_cancellation, 
				ticket_modify: ticket_modify,
				ticket_reschedule: ticket_reschedule 
				}, function (res)
                {
                    if (res == 2)
                    {
                        
                        alert('User Name Already Exit,Try with another User Name!!');
                        $('#user_name').focus();
                    }
                    else if (res == 1)
                    {
                       
                        alert('Agent Registered Successfully!!');
                        window.location = '<?php echo base_url("Logins/add_branch_agent"); ?>';
                       
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

<div class="clearfix">
	<h4>ADD Agent</h4>
</div>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td>&nbsp;</td>		
		</tr>
		<tr>
			<td width="25%" valign="top"><table width="100%" cellspacing="0" cellpadding="0" align="center">

				<tr>
					<td>&nbsp;</td>
					<td>Name</td>
					<td><input type="text" id="name" name="name" class="inputfield" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>Username</td>
					<td><input type="text" id="user_name" name="user_name"   class="inputfield"  onchange="checkUser();" /></td>
					<td>&nbsp;</td>
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
					<td><input type="text" id="email_address" name="email_address"  class="inputfield" /></td>
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
			</table></td>
			
		</tr>
		<tr>
			<td>&nbsp;</td>
			
		</tr>
		<tr>
			<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td height="40">Booking Type</td>
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
					<td height="40">Booking Mgmt </td>
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
					<td height="40">Seat Mgmt </td>
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
					<td height="40">Updations Mgmt </td>
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
					<td height="40">Reports Mgmt </td>
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
					<td height="40">Login Mgmt </td>
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
					<td height="40">Bus Mgmt </td>
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

