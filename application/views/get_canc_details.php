<?php
$ho = $this->session->userdata('bktravels_head_office');
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
/* if ($book_pay_type == 'byphone') {
  $cc = 0;
  $ca = 0;
  $ra = $paid - $ca;
  } else { */
if ($cc == 0) {
    print "<script type=\"text/javascript\">alert('Your Ticket Cannot be Cancelled');</script>";
    /*print "<script type=\"text/javascript\">window.location = '" . base_url('agent_controller/cancellation') . "'</script>";*/
    print "<script type=\"text/javascript\">window.close();</script>";
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
//}	

$canc_time = date('Y-m-d H:i:s');
$sql2 = $this->db->query("SELECT name FROM agents_operator where operator_id='$travel_id' and id='$agent_id'");
            foreach ($sql2->result() as $row2) {
                $name = $row2->name;
            }

$ticket = $tkt_no . "!" . $pnr;

$cdate = date('Y-m-d');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>:: ::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <style type="text/css">
            table,th,tr,td
{
    font-size:14px;
}
            .newsearchbtn {

                background: #CC3300 none repeat scroll 0% 0%;

                color: #FFF;

                font-size: 15px;

                padding: 3px 25px;

                text-align: center;

                cursor: pointer;

                border: medium none #CC3300;

            }
        </style>
        <script src="<?php echo base_url('js/app-js.v1.js'); ?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $("#percent").change(function()
                {
                    var percent = $("#percent").val();
                    if (percent == "select")
                    {
                        percent = $("#cc2").val();
                    }
                    var tkt_fare = '<?php echo $tkt_fare; ?>';
                    var paid = '<?php echo $paid; ?>';
                    var ca = "";
                    var ra = "";
                    var cc = "";

                    ca = (parseInt(paid) * parseInt(percent)) / 100;
                    ra = parseInt(paid) - parseInt(ca);

                    if (paid == "")
                    {
                        ca = (parseInt(tkt_fare) * parseInt(percent)) / 100;
                        ra = parseInt(tkt_fare) - parseInt(ca);
                    }

                    $("#cc").val(0);
                    $("#ca").val(0);
                    $("#ra").val(0);
                    $("#cc").val(percent);
                    $("#ca").val(ca);
                    $("#ra").val(ra);
                    $("#cc1").html('');
                    $("#ca1").html('');
                    $("#ra1").html('');
                    $("#cc1").text(percent);
                    $("#ca1").text(ca);
                    $("#ra1").text(ra);

                });

                $("#cancel_ticket").click(function()
                {
                    var r = window.confirm("Are you sure you want to cancel the ticket");
                    if (r == true)
                    {
                        $("#confirm_cancel").submit();
                    }
                });
            });
        </script>
    </head>

    <body>
        <div>
            <a href="ticket_search?ticket=<?php echo $ticket."!".$status; ?>">Print Ticket</a> | 
            <a href="get_canc_details?ticket=<?php echo $ticket."!".$status; ?>">Cancel Ticket</a> | 
            <!--a href="shift_passenger?ticket=<?php echo $ticket; ?>">Shift Passenger</a> | 
            <a href="update_ticket?ticket=<?php echo $ticket; ?>">Update Ticket</a> | 
            <a href="ticket_history?ticket=<?php echo $ticket; ?>">Show History</a-->
        </div>
        <div id="pt">
            <form action="<?php echo base_url('booking/canc_ticket'); ?>" method="get" id="confirm_cancel" name="confirm_cancel">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px; font-family:calibri">
                    <tbody> 
                        <tr>
                            <td align="center">
                                <table width="100%" border="0" cellspacing="1" cellpadding="1" style="font-size:13px">
                                    <tr>
                                        <td height="30" colspan="2" align="center"><img src="http://ticketengine.in/operator_logo/<?php echo $travel_id; ?>.png"  alt="<?php echo $travels; ?>" width="180" height="80" /></td>
                                    </tr>
                                    <tr>
                                        <td height="30" colspan="2">Ticket No : <strong><?php echo $pnr; ?></strong>(<?php echo $tkt_no; ?>) 
                                            <input name="ticket" id="ticket" type="hidden" value="<?php echo $ticket; ?>" />
                                            <input name="cc" id="cc" type="hidden" value="<?php echo $cc; ?>" />
                                            <input name="cc2" id="cc2" type="hidden" value="<?php echo $cc; ?>" />
                                            <input name="ca" id="ca" type="hidden" value="<?php echo $ca; ?>" />
                                            <input name="ra" id="ra" type="hidden" value="<?php echo $ra; ?>" />
                                            <input name="paid" id="paid" type="hidden" value="<?php echo $paid; ?>" />
                                            <input name="canc_time" id="canc_time" type="hidden" value="<?php echo $canc_time; ?>" />		  
                                        </td>
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
                                                    <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px"><?php echo $board_point; ?><br /><?php echo $land_mark; ?></td>
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
                                        <td height="30" colspan="2">		  
                                            <table width="100%" border="0" cellspacing="1" cellpadding="1">
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

if ($ho == "yes") {
    ?>

                                                                <tr>
                                                                    <td height="30">&nbsp;</td>
                                                                    <td height="30" colspan="3" style="color:blue">                      Use This For 0 - 100% Cancellation </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="30">&nbsp;</td>
                                                                    <td height="30" colspan="3">
                                                                        <select name="percent" id="percent">
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
                                                                        </select>					</td>
                                                                </tr>
    <?php
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
                                                                <td height="30"><span id="paid"><?php if ($paid == "") {
    echo $tkt_fare;
} else {
    echo $paid;
} ?></span></td>
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
                                                <tr>
                                                    <td height="30" colspan="2" align="center">
<?php
if ($status == "confirmed") {
    echo '<input type="button" name="cancel_ticket" id="cancel_ticket" value="Cancel Ticket" class="btn btn-primary">';
}
?>
                                                    </td>
                                                </tr>
                                            </table>			
                                        </td>
                                    </tr>                       
                                    <tr>
                                        <td height="30" colspan="2" align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="30" colspan="2" align="left">
                                            <table width="505" border="0" cellpadding="0" cellspacing="0">              
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
                                            </table>		  </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>    
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>
