<script type="text/javascript">

$(function() 
{                                              
	$( "#doj" ).datepicker({ dateFormat: 'yy-mm-dd',numberOfMonths: 1, showButtonPanel: false,minDate:0,"autoclose": true});
});

</script>
<script type="text/javascript">
function searchBus()
{
    var tktno = $("#tktno").val();    
    var email = $("#email").val(); 
    var dtt = $("#doj").val();    
    var strFilter = /^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i;
    
    if(tktno == "")
    {
        alert("Please Provide Ticket Number");
		$("#tktno").focus();
		return false;
    }
	
	/*if(email == "")
    {
        alert("Please Provide Email Id");
		$("#email").focus();
		return false;
    }  
	
	if (!strFilter.test(email))
    {
       	alert("Please provide a valid email address.");
        $("#email").focus();
        return false;
    }*/     
    
    if(dtt == "")
    {
        alert("Please Provide Journey Date");
		$("#doj").focus();
		return false;
    }
    
    else
    {
        $('#myticket').submit();       			
    }

} 
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Ticket Cancellation<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
				<form name="myticket" id="myticket" method="post" action="<?php echo site_url('Updations/confirmcancel');?>">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:15px;">
					<tr>
						<td><table width="40%" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td width="120" height="35" align="right" class="formlable">Ticket Number </td>
									<td width="26" height="35" align="center"><strong>:</strong></td>
									<td width="111" height="35"><input type="text" id="tktno" name="tktno" class="inputfield" /></td>
									<td width="46" height="35">&nbsp;</td>
								</tr>
								<tr>
									<td height="35" align="right" class="formlable">Date of Journey</td>
									<td height="35" align="center"><strong>:</strong></td>
									<td height="35"><input type="text" size='12' name="doj" id="doj" class="jdpicker inputfield" value='<?php echo(Date("Y-m-d")); ?>' style="cursor:pointer;background-image:url(<?php echo base_url('images/calendar.gif')?>);background-repeat: no-repeat; background-position:right; vertical-align: middle;"></td>
									<td height="35">&nbsp;</td>
								</tr>
								<tr>
									<td height="35" align="right" class="size">&nbsp;</td>
									<td height="35" align="center">&nbsp;</td>
									<td height="35"><span class="size">
										<input  type="button" class="btn btn-primary" name="search" id="search" value="Search" onclick="searchBus();" />
										</span></td>
									<td height="35"></td>
								</tr>
							</table></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<br />
				<br />
				<table width="700" border="0" cellpadding="0" style="display:none" cellspacing="0" align="center"  id="fa">
					<tr>
						<td width="802" height="30" style="background-color:#999999; color:#FFFFFF"><strong>Select Bus </strong></td>
					</tr>
					<br />
					<tr >
						<td id="fare">&nbsp;</td>
					</tr>
				</table>
			</form>
			</div>
			</div>
		</main>
	</section>
</div>
