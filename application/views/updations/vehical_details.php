<script type="text/javascript">
	function get_vdetails(){
		 var services = $("#services").val();
		 $.post("get_vihicle_details", {services: services}, function (res)
                {
					//alert(res);
					var res1=[];
					res1 = res.split('#');
					$("#bus_number").val(res1[0]);
					$("#driver_name").val(res1[1]);
					$("#driver_number").val(res1[2]);                 
                });
	}    
    function validation() {
        var services = $("#services").val();
        var bus_number = $('#bus_number').val();
        var driver_name = $('#driver_name').val();
        var driver_number = $('#driver_number').val();               
		var letters = /^[0-9a-zA-Z]+$/;       
        if (services == 0)
        {
            alert('Kindly select Service');
			document.getElementById('services').focus();
        }
        else if (bus_number == "")
        {
            alert('Kindly provide Bus Number');
			document.getElementById('bus_number').focus();
        }
		else if (!letters.test(bus_number)) {
                    alert('Enter Bus Number Without Spaces');
                    document.getElementById('bus_number').focus();
                    
                }
		else if (driver_name == "")
        {
            alert('Kindly provide Driver Name');
			document.getElementById('driver_name').focus();
        }else if (driver_number == "")
        {
            alert('Kindly provide Driver Number');
			document.getElementById('driver_number').focus();
        }		
        else
        {
            var con = confirm("Are You Sure You Want To Update Vehicle Details");
            if (con == true)
            {               
                $.post("vihicle_details1", {services: services, bus_number: bus_number, driver_name: driver_name, driver_number: driver_number}, function (res)
                {//alert(res);
                    if (res == 1) {
						alert('Successful');
                        window.location = '<?php echo base_url('Updations/vihicle_details'); ?>';
                    }
                    else {
						alert('Not Updated');
                       window.location = '<?php echo base_url('Updations/vihicle_details'); ?>';
                    }                    
                });
            }
        }
    }
</script>

<div class="clearfix">
	<h4>Vehicle Details </h4>
</div>
<table width="100%" border="0" cellspacing="1" cellpadding="1" style="margin-top:15px">
	<tr>
		<td>&nbsp;</td>
		<td align="center"><span class="label">Service Name :</span></td>
		<td><?php
            $id = 'id="services" class="inputlarge" onchange="get_vdetails()"';
            echo form_dropdown('services', $services, '', $id);
            ?>
		</td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center">Bus Number : </td>
		<td><label>
			<input type="text" name="bus_number" id="bus_number" />
			</label></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center"><span id="loadlayout">Driver Name</span></td>
		<td><input type="text" name="driver_name" id="driver_name" /></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="center"><span id="loadlayout">Driver Mobile No</span></td>
		<td><input type="text" name="driver_number" id="driver_number" /></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input  type="button" class="btn btn-primary" name="btn" id='btn' value="Submit" onclick="validation()" /></td>
		<td>&nbsp;</td>
	</tr>
</table>
