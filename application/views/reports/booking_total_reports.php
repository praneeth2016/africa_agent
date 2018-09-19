<?php
	$travel_id = $this->session->userdata('bktravels_travel_id');
    $bktravels_user_id = $this->session->userdata('bktravels_user_id');
	$bktravels_api_type = $this->session->userdata('bktravels_api_type');
	$bktravels_margin = $this->session->userdata('bktravels_margin');
	$bktravels_comm_type = $this->session->userdata('bktravels_comm_type');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>operator</title>
<script type ="text/javascript" src="<?php echo base_url('js/app-js.v1.js'); ?>"></script>
<style>
table
{
border-collapse:collapse;
}
table, td, th
{
border:#f2f2f2 solid 1px;
}
</style>
<style type="text\css" media="print">
  #myFooter, #myHeader
  {
    display: none;
  }
  </style>
<script>
function printBooking()
  {
 var printButton = document.getElementById("print");
 printButton.style.visibility = "hidden";
        window.print()
printButton.style.visibility = "visible";
  }
</script>
</head>
<body>
<?php
		echo'<table width="100%" id="tbl" style="border:#cccccc solid 2px; border-collapse:collapse;">
		<tr> 
        <td height="30" colspan="18" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Booking List</b></td></tr>
		<tr>        
        <th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Booking</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare<br />(A)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST + Comm<br />(B)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare<br />(C = A - B)</th>
        </tr>';
       $i = 1;
	   $total_seats = 0;
			$total_base_fare = 0;
			$total_cgst = 0;
			$total_sgst = 0;
			$total_tcs = 0;
			$total_convenience_charge = 0;
			$total_discount_amount = 0;
			$total_tkt_fare = 0;
			$total_commission = 0;
			$total_igst_commission = 0;
			$total_net_fare = 0;
	   
	   foreach ($query as $value) {
			$tkt_no = $value->tkt_no;
			$pnr = $value->pnr;
			$service_no = $value->service_no;
			$jdate = date('Y-m-d',strtotime($value->jdate));
			$bdate = date('Y-m-d',strtotime($value->bdate));
			$source = $value->source;
			$dest = $value->dest;
			$seats = $value->seats;
			$pass = $value->pass;
			$pname = $value->pname;
			$pmobile = $value->pmobile;
			$base_fare = round(($value->base_fare),2);
			$service_tax_amount = round(($value->service_tax_amount),2);
			$cgst = round(($value->cgst),2);
			$sgst = round(($value->sgst),2);
			$tcs = round(($value->tcs),2);
			$convenience_charge = round(($value->convenience_charge),2);
			$discount_amount = round(($value->discount_amount),2);
			$tkt_fare = round(($value->tkt_fare),2);
			$save = round(($value->save),2);
			$paid = round(($value->paid),2);
			$operator_agent_type = $value->operator_agent_type;
			$agent_id = $value->agent_id;
									
			if($agent_id == '12' || $agent_id == '15' || $agent_id == '125' || $agent_id == '144' || $agent_id == '161' || $agent_id == '204') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = round(($commission * 18 /100),2);
			} else if($operator_agent_type == '4') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = 0;
			} else {				
				$commission = round($save,2);					
			}
			
			if($cgst == 0 || $cgst == "0.0") {
				$cgst = round(($service_tax_amount / 2),2);
				$sgst = round(($service_tax_amount / 2),2);
				$tcs = round(($base_fare / 100),2);
			}
			
			$tcs = 0;
			
			$stmt = "select distinct api_type from agents_operator where id='$agent_id'";
			$result = $this->db->query($stmt);
			
			foreach($result->result() as $row) {
				$api_type = $row->api_type;
			}
			
			if($operator_agent_type == 1 || $operator_agent_type == 2) {
				$igst_commission = $commission;
				$tkt_fare = $base_fare + $cgst + $sgst + $tcs + $convenience_charge - $discount_amount;
				//$net_fare = $base_fare + $cgst + $sgst + $convenience_charge - $discount_amount - $commission - $tcs;
				$net_fare = $tkt_fare - $igst_commission;
			} else {
				$igst_commission = $convenience_charge + $commission;
				$tkt_fare = $base_fare + $cgst + $sgst + $tcs + $convenience_charge - $discount_amount;
				//$net_fare = $base_fare + $cgst + $sgst + $tcs - $convenience_charge - $discount_amount - $commission;
				$net_fare = $tkt_fare - $igst_commission;
			}						
			
			$total_seats = $total_seats + $pass;
			$total_base_fare = $total_base_fare + $base_fare;
			$total_cgst = $total_cgst + $cgst;
			$total_sgst = $total_sgst + $sgst;
			$total_tcs = $total_tcs + $tcs;
			$total_convenience_charge = $total_convenience_charge + $convenience_charge;
			$total_discount_amount = $total_discount_amount + $discount_amount;
			$total_tkt_fare = $total_tkt_fare + $tkt_fare;
			$total_commission = $total_commission + $commission;
			$total_igst_commission = $total_igst_commission + $igst_commission;
			$total_net_fare = $total_net_fare + $net_fare;			
			
			echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$i.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no.'<br /> / <br />'.$pnr.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$bdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source.'<br /> - <br />'.$dest.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats.'<br/> '.$pass.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname).'<br/> '.$pmobile.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$igst_commission.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare.'</td>
        </tr>';
            $i++;
		}       
        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_igst_commission.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare.'</b></td>
        </tr>';

        echo '<tr>
        <td height="30" align="center" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Booked Seats = '.$total_seats.'</b></td>
        <td align="center" colspan="10" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Booking amount = '.$total_net_fare.'</b></td>
        </tr>';

        echo "</table><br/>";
     
?>
<br/>
<br/>
<?php
            echo '<table width="100%" id="tbl" style="border:#cccccc solid 2px;border-collapse:collapse;">
        <tr> 
        <td height="30" colspan="19" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Cancellation List</b></td></tr>
		<tr>
		<th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Cancel</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare<br />(A)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Cancel Amount<br />(B)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST + Comm<br />(C)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare<br />D = A - (B + C)</th>
        </tr>';
        
        $j = 1;
		$total_seats2 = 0;
			$total_base_fare2 = 0;
			$total_cgst2 = 0;
			$total_sgst2 = 0;
			$total_tcs2 = 0;
			$total_convenience_charge2 = 0;
			$total_discount_amount2 = 0;
			$total_tkt_fare2 = 0;
			$total_commission2 = 0;
			$total_cancellation_amount2 = 0;
			$total_igst_commission2 = 0;
			$total_net_fare2 = 0;
		
        foreach ($query1 as $value2) {
			$tkt_no2 = $value2->tkt_no;
			$pnr2 = $value2->pnr;
			$service_no2 = $value2->service_no;
			$jdate2 = date('Y-m-d',strtotime($value2->jdate));
			$cdate2 = date('Y-m-d',strtotime($value2->cdate));
			$source2 = $value2->source;
			$dest2 = $value2->dest;
			$seats2 = $value2->seats;
			$pass2 = $value2->pass;
			$pname2 = $value2->pname;
			$pmobile2 = $value2->pmobile;
			$base_fare2 = round(($value2->base_fare),2);
			$service_tax_amount2 = round(($value2->service_tax_amount),2);
			$cgst2 = round(($value2->cgst),2);
			$sgst2 = round(($value2->sgst),2);
			$tcs2 = round(($value2->tcs),2);
			$convenience_charge2 = round(($value2->convenience_charge),2);
			$discount_amount2 = round(($value2->discount_amount),2);
			$tkt_fare2 = round(($value2->tkt_fare),2);
			$save2 = round(($value2->save),2);
			$paid2 = round(($value2->paid),2);
			$camt2 = round(($value2->camt),2);
			$operator_agent_type2 = $value2->operator_agent_type;
			$agent_id2 = $value2->agent_id;
			
			if($agent_id2 == '12' || $agent_id2 == '15' || $agent_id2 == '125' || $agent_id2 == '144' || $agent_id2 == '161' || $agent_id2 == '204') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = round(($commission2 * 18 /100),2);
			} else if($operator_agent_type2 == '4') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = 0;
			} else {
				$commission2 = round($save2,2);
			}
			
			if($operator_agent_type == '3' || $operator_agent_type == '4') {						
				$cancellation_amount2 = round(($camt2 / 2),2);
			} else {
				$cancellation_amount2 = round($camt2,2);
			}
			
			if($cgst2 == 0 || $cgst2 == "0.0") {
				$cgst2 = round(($service_tax_amount2 / 2),2);
				$sgst2 = round(($service_tax_amount2 / 2),2);
				$tcs2 = round(($base_fare2 / 100),2);
			}
			
			$tcs2 = 0;
			
			$stmt2 = "select distinct api_type from agents_operator where id='$agent_id'";
			$result2 = $this->db->query($stmt2);
			
			foreach($result2->result() as $row2) {
				$api_type2 = $row2->api_type;
			}
			
			if($operator_agent_type2 == 1 || $operator_agent_type2 == 2) {
				$igst_commission2 = $commission2;
				$tkt_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 + $convenience_charge2 - $discount_amount2;
				$net_fare2 = $tkt_fare2 - $igst_commission2;
			} else {
				$igst_commission2 = $convenience_charge2 + $commission2;
				$tkt_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 + $convenience_charge2 - $discount_amount2;			
				//$net_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 - $convenience_charge2 - $discount_amount2 - $cancellation_amount2 - $commission2;
				$net_fare2 = $tkt_fare2 - $cancellation_amount2 - $igst_commission2;
			}						
			
			$total_seats2 = $total_seats2 + $pass2;
			$total_base_fare2 = $total_base_fare2 + $base_fare2;
			$total_cgst2 = $total_cgst2 + $cgst2;
			$total_sgst2 = $total_sgst2 + $sgst2;
			$total_tcs2 = $total_tcs2 + $tcs2;
			$total_convenience_charge2 = $total_convenience_charge2 + $convenience_charge2;
			$total_discount_amount2 = $total_discount_amount2 + $discount_amount2;
			$total_tkt_fare2 = $total_tkt_fare2 + $tkt_fare2;
			$total_cancellation_amount2 = $total_cancellation_amount2 + $cancellation_amount2;
			$total_commission2 = $total_commission2 + $commission2;
			$total_igst_commission2 = $total_igst_commission2 + $igst_commission2;
			$total_net_fare2 = $total_net_fare2 + $net_fare2;			

            echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$j.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no2.'<br /> / <br />'.$pnr2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source2.'<br /> - <br />'.$dest2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats2.'<br/> '.$pass2.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname2).'<br/> '.$pmobile2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cancellation_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$igst_commission2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare2.'</td>
        </tr>';
            $j++;
        }
		$balance = 	$total_net_fare - $total_net_fare2;
        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cancellation_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_igst_commission2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Cancelled Seats = '.$total_seats2.'</b></td>
        <td align="center" colspan="10" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Cancellation Amount =  '.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="19" style="font-size:14px;border:#cccccc solid 1px;">&nbsp;</td>        
        </tr>
		<tr>
        <td height="30" align="center" colspan="19" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Amount to Pay =  '.$total_net_fare.' - '.$total_net_fare2.' = '.$balance.'</b></td>        
        </tr>
		</table><br /><br />';

         echo '<div align="center"><input  type="button" class="btn btn-primary" name="print" id="print" value="Print" onClick="printBooking();"></div>';
     
?>
</body>
</html>