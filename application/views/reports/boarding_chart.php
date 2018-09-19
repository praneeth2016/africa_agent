<script>

$(function()
{	
    $( "#doj" ).datepicker(
    {
        buttonText: 'Select date of Journey',
		buttoncursor: 'pointer',
		autoSize: true,    
    	numberOfMonths:1,	
        dateFormat: 'yy-mm-dd',
		"autoclose": true
    });		     
});

        function Report()
        {
            var service=$('#service').val();
            var dtt = $("#doj").val();
              if(dtt == "")
            {
                alert("Please Select Journey Date");
                $("#doj").focus();
                return false;
            }
            
            if(service == 0)
            {
                alert("Please Select Service Number");
	        $("#service").focus();
	        return false;
            }
            else
            {
                //window.open('GetPassReport?service='+service+'&dtt='+dtt,"width=200, height=100");
				window.open("getBoardingChart?service="+service+'&dtt='+dtt, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");        
            }
        }
        
       
        
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Boarding Chart<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="83%" border="0" cellpadding="0" cellspacing="0">  
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td><table border="0" cellpadding="0" cellspacing="0" align="center">
						  <tr>
							<td width="120" height="35" align="right" class="size">Service No/ Name:</td>
							<td width="26" align="center"><strong>:</strong></td>
							<td><?php
								$js = 'id="service" class="form-control"';
								echo form_dropdown('from', $services, '', $js);           
							?></td>
							<td width="46">&nbsp;</td>
						  </tr>
						  <tr>
							<td height="35" align="right" class="size">Date of Journey</td>
							<td align="center"><strong>:</strong></td>
							<td><input type="text" name="doj" id="doj" class="jdpicker form-control" value='<?php echo(Date("Y-m-d")); ?>' /></td>
							<td></td>
						  </tr>
						  <tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td height="35" align="center"><input  type="button" class="btn btn-primary form-control" name="search" id="search" value="Submit" onClick="Report();" /></td>
							<td>&nbsp;</td>
						  </tr>
						</table></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					</table>

				</div>
			</div>
		</main>
	</section>
</div>
