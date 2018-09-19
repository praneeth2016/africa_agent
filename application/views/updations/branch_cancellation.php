
<script type="text/javascript">

$(function() 
{                                              
	$( "#doj").datepicker({ dateFormat: 'yy-mm-dd',numberOfMonths: 1, showButtonPanel: false,minDate: 0,"autoclose": true});
});

</script>
<script type="text/javascript">
function searchBus()
{
    var tktno = $("#tktno").val();    
    var percent = $("#percent").val(); 
    var dtt = $("#doj").val();        
    
    if(tktno == "")
    {
        alert("Please Provide Ticket Number");
		$("#tktno").focus();
		return false;
    }		  		     
    
    if(dtt == "")
    {
        alert("Please Provide Journey Date");
		$("#doj").focus();
		return false;
    }
    
	if(percent == "select")
    {
        alert("Please Provide Percentage");
		$("#percent").focus();
		return false;
    }
	
    else
    {
        var x = window.confirm("Are u sure!!! u want to Cancel the Ticket ");
	
		if(x==1)
		{
			$('#myticket').submit();
		}	
    }

} 
</script>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Branch Cancellation<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<form name="myticket" id="myticket" method="post" action="<?php echo site_url('Updations/branch_confirmcancel');?>">
						<table width="83%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><table width="50%" border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td width="106" height="35" align="left" class="size">Ticket Number </td>
											<td width="17" align="center"><strong>:</strong></td>
											<td width="144"><input type="text" id="tktno" name="tktno" class="inputfield" /></td>
											<td width="36">&nbsp;</td>
										</tr>
										<tr>
											<td height="35" align="left" class="size">Date of Journey</td>
											<td align="center"><strong>:</strong></td>
											<td><input type="text" name="doj" id="doj" class="jdpicker inputfield" value='<?php echo(Date("Y-m-d")); ?>' style="cursor:pointer;background-image:url(<?php echo base_url('images/calendar.gif')?>);background-repeat: no-repeat; background-position:right; vertical-align: middle;;"></td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td height="35" align="left" class="size">Cancel Percentage</td>
											<td align="center"><strong>:</strong></td>
											<td><select name="percent" id="percent">
													<option value="select" selected="selected">-- Select --</option>
													<option value="0">0</option>
													<option value="5">5</option>
													<option value="10">10</option>
													<option value="15">15</option>
													<option value="20">20</option>
													<option value="25">25</option>
													<option value="30">30</option>
													<option value="35">35</option>
													<option value="40">40</option>
													<option value="45">45</option>
													<option value="50">50</option>
													<option value="55">55</option>
													<option value="60">60</option>
													<option value="65">65</option>
													<option value="70">70</option>
													<option value="75">75</option>
													<option value="80">80</option>
													<option value="85">82</option>
													<option value="90">90</option>
													<option value="95">95</option>
													<option value="100">100</option>
												</select>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td align="right" class="size">&nbsp;</td>
											<td align="center">&nbsp;</td>
											<td>&nbsp;</td>
											<td></td>
										</tr>
										<tr>
											<td height="35" colspan="4" align="center" class="size"><input  type="button" class="btn btn-primary" name="search" id="search" value="Search" onclick="searchBus();" /></td>
										</tr>
										<tr>
											<td height="35" colspan="4" align="center" class="size"><span class="style1">Note</span> : if cancel percentage is 0 (<span class="style1">full refund</span>) </td>
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
