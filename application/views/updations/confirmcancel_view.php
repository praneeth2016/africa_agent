<style type="text/css">
    .pd
    {
        padding-left:5px;
        font-size: 13px;
        height: 25px;
        text-align:left;
    }
</style>
<script type="text/javascript" language="javascript">
    function validate()
    {
        var x = window.confirm("Are u sure!!! u want to Cancel the Ticket ");
        if (x == 1)
        {
            document.myticket.submit();
        }
        else
        {
            alert("ok");
        }
    }
</script>
</head><body>
<?php
    if ($error == "0") {
        print "<script type=\"text/javascript\">alert('Invalid Parameters or this ticket is not belong to you');</script>";
        print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
    } else if ($error == "2") {
        print "<script type=\"text/javascript\">alert('Your Ticket is already Cancelled');</script>";
        print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
    } else if ($error == "3") {
        print "<script type=\"text/javascript\">alert('No Cancellation For this Ticket due to Rescheduled,contact operator for clarification!');</script>";
        print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
    } else {
        date_default_timezone_set('Asia/Kolkata');

        $cd = strtotime(date('Y-m-d H:i:s'));

        $jd1 = $jdate . " " . $start_time;

        $jd = strtotime($jd1);

        if ($jd > $cd) {
            $min = ceil(abs($cd - $jd) / 60);
        } else {
            $min = 0;
        }

        $canc_terms1 = explode('@', $canc_terms);
        $z = count($canc_terms1);

        if ($z > 1) {
            for ($i = 0; $i < $z; $i++) {
                $can_term = explode('#', $canc_terms1[$i]);
                $start_min = $can_term[0] * 60;
                $end_min = $can_term[1] * 60;

                if ($min > $start_min && $min < $end_min) {
                    $cc = $can_term[2];
                    break;
                } else if ($min > $end_min) {
                    $cc = 1;
                } else {
                    $cc = 0;
                }
            }
        } else {
            $can_term = explode('#', $canc_terms);
            $start_min = $can_term[0] * 60;
            $end_min = $can_term[1] * 60;

            if ($min > $start_min && $min < $end_min) {
                $cc = $can_term[2];
            } else if ($min > $end_min) {
                $cc = 1;
            } else {
                $cc = 0;
            }
        }
        if ($book_pay_type == 'byphone') {
            $cc = 0;
            $ca = 0;
            $ra = $paid - $ca;
        } else {
            if ($cc == 0) {
                print "<script type=\"text/javascript\">alert('Your Ticket Cannot be Cancelled');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
            } else if ($cc == 1) {
                $cc = 0;
                $ca = 0;
                $ra = $paid - $ca;
            } else {
                $ca = ($base_fare * $cc) / 100;

                $ra = $paid - $ca;
            }
            if ($ra < 0)
                $ra = 0;
        }
        ?>
<div class="content-wrapper">    <!-- Main content -->
<section class="main-content-bg">
<main class="container-fluid">		
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Confirm Cancellation<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
		</div>
		<div class="panel-body">
<form name="myticket" id="myticket" method="post" action="<?php echo site_url('Updations/cancelticket'); ?>">
	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%" style="margin-top:15px;">
		<tbody>
			<tr>
				<td><strong>&nbsp; Ticket Information</strong> </td>
			</tr>
			<tr>
				<td align="center"><table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
						<tbody>
							<tr align="center" valign="top">
								<td colspan="6"><strong></strong></td>
							</tr>
							<tr align="center" valign="top">
								<td colspan="3" class="pd"><strong>&nbsp;Passenger Name : <?php echo $pname; ?> </strong></td>
								<td colspan="3" class="pd"><strong>&nbsp;Ticket No : <?php echo $tkt_no; ?> </strong></td>
							</tr>
							<tr align="center" valign="top">
								<td colspan="6"><strong></strong></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Travel Provider</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $travels; ?></td>
								<td width="23%" class="pd">&nbsp;Journey Date</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd"><?php echo $jdate; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Source</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $source; ?></td>
								<td width="23%" class="pd">&nbsp;Destination</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd"><?php echo $dest; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Seat Number</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $seats; ?></td>
								<td width="23%" class="pd">&nbsp;No.Of Passengers</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd"><?php echo $pass; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Start Time</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $start_time; ?></td>
								<td width="23%" class="pd">&nbsp;Bus Type</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd"><?php echo $bus_type; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Land Mark</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $land_mark; ?></td>
								<td width="23%" class="pd">&nbsp;Service Number</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd"><?php echo $service_no; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Status</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $status; ?></td>
								<td width="23%" class="pd">&nbsp;Base Fare</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd">Rs. <?php echo $base_fare; ?></td>
							</tr>
							<tr valign="top">
								<td width="18%" class="pd">&nbsp;Boarding Point</td>
								<td align="center" width="3%"><strong>:</strong></td>
								<td width="29%" class="pd"><?php echo $board_point; ?></td>
								<td width="23%" class="pd">&nbsp;Paid Fare</td>
								<td align="center" width="4%"><strong>:</strong></td>
								<td width="23%" class="pd">Rs. <?php echo $paid; ?></td>
							</tr>
							<tr valign="top">
								<td class="pd">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td class="pd">&nbsp;</td>
								<td class="pd">&nbsp;Cancellation Charges</td>
								<td align="center"><strong>:</strong></td>
								<td class="pd"><?php echo $cc; ?> %</td>
							</tr>
							<tr valign="top">
								<td class="pd">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td class="pd">&nbsp;</td>
								<td class="pd">&nbsp;Cancelled Amount</td>
								<td align="center"><strong>:</strong></td>
								<td class="pd">Rs. <?php echo $ca; ?></td>
							</tr>
							<tr valign="top">
								<td class="pd">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td class="pd">&nbsp;</td>
								<td class="pd">&nbsp;Refund Amount</td>
								<td align="center"><strong>:</strong></td>
								<td class="pd">Rs. <?php echo $ra; ?></td>
							</tr>
							<tr align="center" valign="top">
								<td colspan="6"><input name="tktno" type="hidden" value="<?php echo $tkt_no; ?>" />
									<input name="jdate" type="hidden" value="<?php echo $jdate; ?>" />
									<input name="email" type="hidden" value="<?php echo $email; ?>" />
									<input name="cc" type="hidden" value="<?php echo $cc; ?>" />
								</td>
							</tr>
						</tbody>
					</table></td>
			</tr>
			<tr>
				<td align="center" height="40" valign="bottom"><input value="Confirm Cancellation" name="Submit"  type="button" class="btn btn-primary" onClick="validate();" />
				</td>
			</tr>
			<tr>
				<td align="center" height="40" valign="bottom">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</form>
		</div>
			</div>
		</main>
	</section>
</div>
<?php
    }
    ?>
