<style type="text/css">
.pd
{
	padding-left:5px;
	font-size: 13px;
	height: 25px;
	text-align:left;
}
</style>

</head>

<body >
<?php
    if($error == "0")
    {        
        print "<script type=\"text/javascript\">alert('Invalid Parameters');</script>";
        print "<script type=\"text/javascript\">window.location = '".base_url('Updations/Branch_tkt_cancel')."'</script>";
    }
    else
    {
        $ca = ($base_fare * $cc)/100;
    	
		$ra = $paid - $ca;
?> 
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Cancelled Ticket<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">  
					<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
					  <tbody >
					  <tr >
						<td>
						<strong>&nbsp;Ticket Information</strong>
						</td>
					  </tr>
					  <tr >
						<td>
						<strong>&nbsp;</strong>
						</td>
					  </tr>
					  <tr>
						<td align="center">
						<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
						<tbody>
						<tr align="center" valign="top">
						<td colspan="6"><strong></strong></td>
						</tr>
						<tr align="center" valign="top">
						<td height="20" colspan="3" class="pd"><strong>&nbsp;Passenger Name : <?php echo $pname; ?> </strong></td>
						<td height="20" colspan="3" class="pd"><strong>&nbsp;Ticket No : <?php echo $tkt_no; ?> </strong></td>
						</tr>
						<tr align="center" valign="top">
						<td colspan="6"><strong></strong></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Travel Provider</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"> <?php echo $travels; ?></td>
						<td width="25%" class="pd">&nbsp;Journey Date</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"><?php echo $jdate; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Source</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $source; ?></td>
						<td width="25%" class="pd">&nbsp;Destination</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"><?php echo $dest; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Seat Number</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $seats; ?></td>
						<td width="25%" class="pd">&nbsp;No.Of Passengers</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"><?php echo $pass; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Start Time</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $start_time; ?></td>
						<td width="25%" class="pd">&nbsp;Bus Type</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"><?php echo $bus_type; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Land Mark</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $land_mark; ?></td>
						<td width="25%" class="pd">&nbsp;Service Number</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"><?php echo $service_no; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Status</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $status; ?></td>
						<td width="25%" class="pd">&nbsp;Base Fare</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"> <?php echo $base_fare; ?></td>
						</tr>
						<tr valign="top">
						<td width="18%" height="25" class="pd">&nbsp;Boarding Point</td>
						<td width="3%" height="25" align="center"><strong>:</strong></td>
						<td width="29%" class="pd"><?php echo $board_point; ?></td>
						<td width="25%" class="pd">&nbsp;Paid Fare</td>
						<td align="center" width="3%"><strong>:</strong></td>
						<td width="22%" class="pd"> <?php echo $paid; ?></td>
						</tr>
						<tr valign="top">
						  <td height="25" class="pd">&nbsp;</td>
						  <td height="25" align="center">&nbsp;</td>
						  <td class="pd">&nbsp;</td>
						  <td class="pd">&nbsp;Cancellation Charges</td>
						  <td align="center"><strong>:</strong></td>
						  <td class="pd"><?php echo $cc; ?> %</td>
						</tr>
						<tr valign="top">
						  <td height="25" class="pd">&nbsp;</td>
						  <td height="25" align="center">&nbsp;</td>
						  <td class="pd">&nbsp;</td>
						  <td class="pd">&nbsp;Cancelled Amount</td>
						  <td align="center"><strong>:</strong></td>
						  <td class="pd"> <?php echo $ca; ?></td>
						</tr>
						<tr valign="top">
						  <td height="25" class="pd">&nbsp;</td>
						  <td height="25" align="center">&nbsp;</td>
						  <td class="pd">&nbsp;</td>
						  <td class="pd">&nbsp;Refund Amount</td>
						  <td align="center"><strong>:</strong></td>
						  <td class="pd"> <?php echo $ra; ?></td>
						</tr>
						<tr align="center" valign="top">
						<td colspan="6">&nbsp;</td>
						</tr>
						</tbody>
						</table>
						</td>
						</tr>
						<tr>
						<td align="center" height="40" valign="bottom">&nbsp;</td>
						</tr>
						</tbody>
						</table>    
    <?php
    }
    ?>
				</div>
			</div>
		</main>
	</section>
</div>