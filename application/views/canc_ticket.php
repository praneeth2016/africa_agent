<?php
foreach ($sql as $res) {
    $tkt_no = $res->tkt_no;
    $pnr = $res->pnr;
    $service_no = $res->service_no;
    $board_point = $res->board_point;
    $land_mark = $res->land_mark;
    $source = $res->source;
    $dest = $res->dest;
    $travels = $res->travels;
    $bus_type = $res->bus_type;
    $bdate = $res->bdate;
    $jdate = $res->jdate;
    $seats = $res->seats;
    $gender = $res->gender;
    $start_time = $res->start_time;
    $arr_time = $res->arr_time;
    $paid = round($res->paid, 2);
    $save = $res->save;
    $tkt_fare = round($res->tkt_fare, 2);
    $base_fare = $res->base_fare;
    $pname = $res->pname;
    $pemail = $res->pemail;
    $pmobile = $res->pmobile;
    $age = $res->age;
    $refno = $res->refno;
    $status = $res->status;
    $pass = $res->pass;
    $travel_id = $res->travel_id;
    $time = $res->time;
    $agent_id = $res->agent_id;
    $status = $res->status;
}
$seats1 = explode(',', $seats);
$pname1 = explode(',', $pname);
$age1 = explode(',', $age);
$gender1 = explode(',', $gender);
$board_point1 = explode('-', $board_point);

if ($paid == "") {
    $paid == $tkt_fare;
}

$sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$travel_id'");
foreach ($sql5->result() as $row5) {
                $op_url = $row5->op_url;
                $op_email = $row5->op_email;
                $ph = $row5->other_contact;
                $canc_terms = $row5->canc_terms;
            }

$cc = $this->input->get('cc');
$ca = $this->input->get('ca');
$ra = $this->input->get('ra');
$paid = $this->input->get('paid');
$canc_time = $this->input->get('canc_time');

$sql1 = $this->db->query("select distinct canc_terms from master_terms where service_num='$service_no' and travel_id='$travel_id' and terms_date='$jdate'");
            if ($sql1->num_rows() > 0) {
                foreach ($sql1->result() as $row1) {
                    $canc_terms = $row1->canc_terms;
                }
            } else {
                $sql1 = $this->db->query("select distinct canc_terms from master_terms where service_num='$service_no' and travel_id='$travel_id' and terms_date IS NULL");
                if ($sql1->num_rows() > 0) {
                    foreach ($sql1->result() as $row1) {
                        $canc_terms = $row1->canc_terms;
                    }
                }
            }


$sql2 = $this->db->query("SELECT name FROM agents_operator where operator_id='$travel_id' and id='$agent_id'");
            foreach ($sql2->result() as $row2) {
                $name = $row2->name;
            }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: ::</title>
</head>
<body>
<div>
	<!--a href="ticket_search?ticket=<?php echo $ticket; ?>">Print Ticket</a> | 
                                <a href="get_canc_details?ticket=<?php echo $ticket; ?>">Cancel Ticket</a> | 
                                <a href="shift_passenger?ticket=<?php echo $ticket; ?>">Shift Passenger</a> | 
                                <a href="update_ticket?ticket=<?php echo $ticket; ?>">Update Ticket</a> | 
                                <a href="ticket_history?ticket=<?php echo $ticket; ?>">Show History</a-->
</div>
<div id="pt">
	<p style="color: #d0d0d0;
  font-size: 150pt;
  -webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  width: 100%;
  height: 100%;
  z-index: -1;
  position:absolute;
  margin-top:100px;
  margin-left:400px;">Cancelled</p>
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px; font-family:calibri">
		<tbody>
			<tr>
				<td align="center"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="font-size:13px">
						<tr>
							<td height="30" colspan="2" align="center"><img src="http://ticketengine.in/operator_logo/<?php echo $travel_id; ?>.png"  alt="<?php echo $travels; ?>" width="180" height="80" /></td>
						</tr>
						<tr>
							<td height="30" colspan="2">Ticket No : <strong><?php echo $pnr; ?></strong>(<?php echo $tkt_no; ?>) </td>
						</tr>
						<tr>
							<td height="30" colspan="2">Ticket Details for <strong><?php echo $pname1[0]; ?></strong> from <strong><?php echo $source; ?></strong> to <strong><?php echo $dest; ?></strong> on service <strong><?php echo $service_no; ?></strong> </td>
						</tr>
						<tr>
							<td height="30" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="font-size:13px; border:#CCCCCC solid 1px;border-collapse: collapse;">
									<tr>
										<td width="16%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Seat Numbers </td>
										<td width="84%" height="30" style="border-bottom:#CCCCCC solid 1px">(<?php echo $pass; ?> Seats)<br />
											<?php
for ($i = 0; $i < $pass; $i++) {
    echo $seats1[$i] . '(' . $pname1[$i] . ') (' . $age1[$i] . ') (' . $gender1[$i] . ')<br />';
}
?></td>
									</tr>
									<tr>
										<td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Journey Date </td>
										<td height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $jdate; ?></td>
									</tr>
									<tr>
										<td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Dep Time </td>
										<td height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $board_point1[1]; ?> Report atleast 15 minutes prior to the departure time at this boarding point.</td>
									</tr>
									<tr>
										<td width="16%" height="30" style="border-right:#CCCCCC solid 1px;">Total Fare</td>
										<td height="30"><?php echo $tkt_fare; ?></td>
									</tr>
								</table></td>
							<td height="30" valign="top" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="font-size:13px;border:#CCCCCC solid 1px">
									<tr>
										<td width="13%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Boarding @ </td>
										<td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $board_point; ?><br />
											<?php echo $land_mark; ?></td>
									</tr>
									<tr>
										<td width="13%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Booked On </td>
										<td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $time; ?></td>
									</tr>
									<tr>
										<td width="13%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Booked By </td>
										<td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $name; ?></td>
									</tr>
									<tr>
										<td height="30" style="border-right:#CCCCCC solid 1px;">Status</td>
										<td height="30"><?php echo $status; ?></td>
									</tr>
								</table></td>
						</tr>
						<tr>
							<td height="30" colspan="2"><table width="100%" border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td width="50%" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="210" height="30">Seat No </td>
													<td width="135" height="30">Name</td>
													<td width="135">Age</td>
													<td width="135">Gender</td>
												</tr>
												<?php
for ($i = 0; $i < $pass; $i++) {
    echo '
                  <tr>
                <td height="30">' . $seats1[$i] . '</td>
                    <td height="30">' . $pname1[$i] . '</td>
                    <td>' . $age1[$i] . '</td>
                    <td>' . $gender1[$i] . '</td>
                  </tr>
                  ';
}
?>
											</table></td>
										<td width="50%"><table width="70%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td height="30" colspan="2" align="center">Cancellation Charge Details </td>
												</tr>
												<tr>
													<td width="205" height="30">Cancellation Charge(per seat)</td>
													<td width="195" height="30"><span id="cc1"><?php echo $cc; ?></span>%</td>
												</tr>
												<tr>
													<td height="30">Total Fare </td>
													<td height="30"><span id="paid">
														<?php if ($paid == "") {
                                                            echo $tkt_fare;
                                                        } else {
                                                            echo $paid;
                                                        } ?>
														</span></td>
												</tr>
												<tr>
													<td height="30">Total Cancellation Charge </td>
													<td height="30"><span id="ca1"><?php echo $ca; ?></span></td>
												</tr>
												<tr>
													<td height="30">Total Refund Amount </td>
													<td height="30"><span id="ra1"><?php echo $ra; ?></span></td>
												</tr>
												<tr>
													<td height="30">Cancellation Time </td>
													<td height="30"><?php echo $canc_time; ?></td>
												</tr>
											</table></td>
									</tr>
								</table></td>
						</tr>
						<tr>
							<td height="30" colspan="2" align="left">&nbsp;</td>
						</tr>
						<tr>
							<td height="30" colspan="2" align="left"><table width="505" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td style="padding:5px 0px 5px 10px; font-size:14px; color:#CC3300; text-decoration:underline;">Cancellation Policy </td>
									</tr>
<?php
if($canc_terms=="100" || $canc_terms=='' || $canc_terms=='0')
{
	 echo '<tr>
                <td style="padding:5px 0px 5px 10px; font-size:14px; color:#000000">';
				 echo '100% cancellation charges, no refund will be provided.</td>
                </tr>';
}
else{
$canc_terms1 = explode('@', $canc_terms);
for ($i = 0; $i < count($canc_terms1); $i++) {
    echo '<tr>
                <td style="padding:5px 0px 5px 10px; font-size:14px; color:#000000">';
    $canc_terms2 = explode('#', $canc_terms1[$i]);
    echo $canc_terms2[0] . " To " . $canc_terms2[1] . " Hours " . $canc_terms2[2] . "% shall be deducted";
    echo '</td>
                </tr>';
}
}
?>
								</table></td>
						</tr>
					</table></td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
