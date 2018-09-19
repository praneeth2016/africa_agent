<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bus_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getservic_modify() {

        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->distinct();
        $this->db->select('service_num');
        $this->db->select('service_name');
        $this->db->where('travel_id', $travel_id);
        $this->db->where('serviceType', 'special');
        //$this->db->where('status', 1);
        $query = $this->db->get('master_buses');
        $list = '<select name="service" id="service" class="inputfield" style="width:150px;"><option value="0">---Select---</option>';
        foreach ($query->result() as $rows) {
            $list = $list . '<option value="' . $rows->service_num . '">' . $rows->service_name . '(' . $rows->service_num . ')' . '</option>';
        }
        $list .= '</select>';
        return $list;
    }

    public function get_special_services_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $srvno = $this->input->post('service');
        $res = $this->db->query("SELECT distinct serviceType,service_num,service_name,model,status FROM `master_buses` WHERE `serviceType`='special' AND `travel_id` ='$travel_id' and service_num='$srvno'");

        echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td height="43" ><strong>Service Type</strong></td>
			<td height="43" align="center" class="space" ><strong>Service Number</strong></td>
			<td height="43" align="center" class="space"><strong>Service Name</strong></td>
			<td height="43" align="center" class="space"><strong>Bus Type</strong></td>
			<!--td height="43" align="center" class="space"><strong>Status</strong></td-->
			<td align="center" class="space"><strong>Action</strong></td>
		</tr>
	<thead>
	<tbody>';
        $i = 1;
        foreach ($res->result() as $row) {
            if ($row->status == 0) {
                $status = "DeActivated";
            } else {
                $status = "Activated";
            }
            $edit = '<input type="button" class="btn btn-primary" name="act' . $travel_id . '" id="act' . $travel_id . '" value="Active" 
             onclick="activateBus(\'' . $row->service_num . '\')">';
            echo'<tr >
			<td height="38" align="center" class="space">' . $row->serviceType . '</td>
			<input type="hidden"  value="' . $row->service_num . '" id="service_num" name="service_num">                            
			<td height="38" align="center" class="space">' . $row->service_num . '</td>
			<td height="38" align="center" class="space">' . $row->service_name . '</td>
			<td height="38" align="center" class="space">' . $row->model . '</td>			
			<td align="center" class="space">' . $edit . ' </td>
		</tr>
            <tr id="tr"  style="display:none;">
            	<td height="38" colspan="5" id="dp" align="center">&nbsp;</td>
   	</tr>
		';
            $i++;
        }
        echo '<tbody>
</table>
';
    }

    function getstatusanddate1() {
        $service_num = $this->input->post('svc');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $current_date = date('Y-m-d');

        $output = "";
        $sql = $this->db->query("SELECT DISTINCT journey_date FROM `buses_list` WHERE travel_id='$travel_id' and STATUS='1' and service_num='$service_num' and journey_date >= '$current_date'");
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $jdate = $row->journey_date;
                if ($output == "") {
                    $output = $jdate;
                } else {
                    $output = $output . ", " . $jdate;
                }
            }
        } else {
            $output = "Deactivated";
        }

        echo '<table width="81%" border="0" style="font-size:14px;color:#333333;" align="center">
			<tr>
			<td height="30">Service Activated on : ' . $output . '</td>
			</tr>
  <tr>
    <td align="center">Start date :
 		<input name="txtdate" type="text" id="txtdate" style="cursor:pointer;border-radius:3px"
     value=""  /></td>
  </tr>
  <tr>
    <td align="center">End date :
 		<input name="txtdatee" type="text" id="txtdatee" style="cursor:pointer;border-radius:3px" 
     value=""/></td>
  </tr>
  <tr>
    <td id="txt"></td>
  </tr>
  <tr>
    <td align="center">	
    <input type="button" class="btn btn-primary" name="updt" id="updt" value="Update" 
        onClick="updateStatus(\'' . $service_num . '\')">
       </td>
  </tr>
  <tr>
  <td>
    <span id="spnmsg" style="font-size:12px; font-weight:bold;"></span> </td>
  </tr>
</table>';
    }

    function activatesplservice1() {
        $service_num = $this->input->post('sernum');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $fdate1 = $this->input->post('fdate');
        $tdate1 = $this->input->post('tdate');
        $fdate = date('Y-m-d', strtotime($fdate1));
        $tdate = date('Y-m-d', strtotime($tdate1));
        $this->db->distinct();
        $array = array('service_num' => $service_num, 'travel_id' => $travel_id);
        $query = $this->db->get_where('master_buses', $array);
        /* getting the values from master_layouts */
        $this->db->select('*');
        $query2 = $this->db->get_where('master_layouts', $array);
        foreach ($query->result() as $rows) {
            $from_id = $rows->from_id;
            $to_id = $rows->to_id;
            $from_name = $rows->from_name;
            $to_name = $rows->to_name;
            $service_route = $rows->service_route;
            $service_name = $rows->service_name;
            $seat_fare = $rows->seat_fare;
            $lberth_fare = $rows->lberth_fare;
            $uberth_fare = $rows->uberth_fare;
            $fcheck = $this->db->query("select * from master_price where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date is NULL") or die(mysql_error());

            if ($fcheck->num_rows() <= 0) {
                $this->db->query("insert into master_price(service_num, travel_id, from_id, from_name, to_id, to_name, service_route, service_name, seat_fare, lberth_fare, uberth_fare) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lberth_fare','$uberth_fare')");
            }
        }

        while ($fdate <= $tdate) {
            $stmt1 = "select * from buses_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$fdate'";
            $query1 = $this->db->query($stmt1);
            if ($query1->num_rows() == 0) {
                foreach ($query->result() as $rows) {
                    if ($rows->service_num == $service_num) {
                        /* inserting into buses_list */
                        $data = array('service_num' => $service_num, 'from_id' => $rows->from_id, 'to_id' => $rows->to_id, 'travel_id' => $travel_id, 'status' => 1, 'journey_date' => $fdate, 'seat_fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'available_seats' => $rows->seat_nos, 'lowerdeck_nos' => $rows->lowerdeck_nos, 'upperdeck_nos' => $rows->upperdeck_nos);
                        $this->db->insert('buses_list', $data);
                    }//if
                }//foreach
                /* inserting into layout_list */
                foreach ($query2->result() as $rows2) {
                    $data2 = array('travel_id' => $rows2->travel_id, 'layout_id' => $rows2->layout_id, 'seat_name' => $rows2->seat_name, 'row' => $rows2->row, 'col' => $rows2->col, 'seat_type' => $rows2->seat_type, 'window' => $rows2->window, 'is_ladies' => $rows2->is_ladies, 'service_num' => $rows2->service_num, 'available' => $rows2->available, 'available_type' => $rows2->available_type, 'fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'journey_date' => $fdate, 'status' => 1);
                    $this->db->insert('layout_list', $data2);
                }
            }
            $dat = strtotime("+1 day", strtotime($fdate));
            $fdate = date("Y-m-d", $dat);
        }//while
        /* updating bus status */
        $query3 = $this->db->query("update master_buses set status='1' where service_num='$service_num' and travel_id='$travel_id'");
        $query31 = $this->db->query("update master_layouts set status='1' where service_num='$service_num' and travel_id='$travel_id'");

        if ($query3 && $query31) {
            $op_title = $this->db->query("SELECT operator_title FROM registered_operators WHERE travel_id='$travel_id'");
                foreach($op_title->result() as $title){
                    $travels_name = $title->operator_title;
                }
                $subject = 'Update the service for '.$travels_name;
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
                $headers .= 'From: Ticketengine<noreplay@ticketengine.in>' . "\r\n";
                $headers .= 'Reply-To: thakurpratibha@gmail.com' . "\r\n";
                $sql = "SELECT serviceType,service_num,travel_id,from_id,from_name,to_id,to_name,service_name FROM master_buses WHERE service_num='$service_num' and travel_id='$travel_id'";
                $q3 = $this->db->query($sql);
                $message = '<p>Dear Team,</p>
<p>Kindly update below service in your list.</p>';
$message .= '<p>Activated on : '.$fdate1.'</p>';
                $message .= '<table width="50%" border="1" cellspacing="0" cellpadding="0">
	<tr>
		<td height="30" align="center" valign="middle" bgcolor="#999966">serviceType</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">service_num</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">travel_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">from_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">from_name</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">to_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">to_name</td>
	</tr>';
                foreach ($q3->result() as $val) {
                    $message .= '<tr>
		<td height="30" align="center" valign="middle">' . $val->serviceType . '</td>
		<td height="30" align="center" valign="middle">' . $val->service_num . '</td>
		<td height="30" align="center" valign="middle">' . $val->travel_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->from_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->from_name . '</td>
		<td height="30" align="center" valign="middle">' . $val->to_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->to_name . '</td>
	</tr>';
                }
                $message .= '</table>';
                $message .='<p>Best regards,<br /> 
	Ticketengine
</p>';
                $sql1 = "SELECT * FROM api_support WHERE status='1'";
                $q4 = $this->db->query($sql1);
                foreach ($q4->result() as $val1) {
                    $to = $val1->email;
                    mail($to, $subject, $message, $headers);
                }
            echo 1;
        } else {
            echo 0;
        }
    }

    public function getAllCity1() {
        $this->db->select('city_id,city_name');
        $query = $this->db->get('master_cities');
        $data = array();
        $data[0] = '-------select-------';
        foreach ($query->result() as $rows) {
            $data[$rows->city_name] = $rows->city_name;
        }
        return $data;
    }

    public function busmodel() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select("model");
        $this->db->where('travel_id', $travel_id);
        $this->db->where('status', 1);
        $this->db->order_by("model", "asc");
        $query = $this->db->get("operator_layouts");
        $data = array();
        $data['0'] = '--select--';
        foreach ($query->result() as $rows) {
            $data[$rows->model] = $rows->model;
        }
        return $data;
    }

    public function check_user() {
        $sname = $this->input->post('sname');
        $snum = $this->input->post('snum');
        $vall = $this->input->post('vall');
        if ($vall == "SNO") {
            $this->db->where('service_num', $snum);
        } else {
            $this->db->where('service_name', $sname);
        }
        $query = $this->db->get("master_buses");
        $rws = $query->num_rows();
        if ($rws > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getLayoutDb() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $busmodel = $this->input->post('busmodel');

        $query = $this->db->query("select layout_id,seat_type from operator_layouts where model='$busmodel' and travel_id='$travel_id'  ");
        foreach ($query->result() as $r) {
            $layout_id = $r->layout_id;
            $seat_type = $r->seat_type;
            $lid = explode("#", $layout_id);
        }
        $layout_type = $lid[1];
        echo '<input type="hidden" name="layout_type" id="layout_type" value="' . $layout_type . '">';

        if ($lid[1] == 'seater') {
            //getting max of row and col from master_layouts
            $sq = $this->db->query("select max(row) as mrow,max(col) as mcol from operator_layouts where model='$busmodel' and travel_id='$travel_id' ") or die(mysql_error());
            foreach ($sq->result() as $row1) {
                $rows = $row1->mrow;
                $cols = $row1->mcol;
            }
            echo "<input type='hidden' name='rows' id='rows' value='$rows' />
	        <input type='hidden' name='cols' id='cols' value='$cols' />";

            //echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;"><tr><td  colspan="2">Select All : <input type="checkbox" name="selectall" id="selectall" onClick="selectAll()"></td></tr></table>';
            echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;">';

            for ($i = 1; $i <= $rows; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $cols; $j++) {

                    $sql3 = $this->db->query("select * from operator_layouts where row='$i' and col='$j' and model='$busmodel' and travel_id='$travel_id' ") or die(mysql_error());

                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name != "") {
                        echo '<td align="center" width="40" height="40" style="border:#666666 solid 2px"><input onchange="document.getElementById(\'ltxt' . $i . '-' . $j . '\').disabled=!this.checked;" class="chkbox" type="checkbox" name="lchk' . $i . '-' . $j . '" id="lchk' . $i . '-' . $j . '"/><input type="text" name="ltxt' . $i . '-' . $j . '" class="textbox" id="ltxt' . $i . '-' . $j . '" value="' . $seat_name . '" size="1" disabled="disabled" style="text-align:center"></td>';
                    } else {
                        echo '<td width="40">&nbsp;</td>';
                    }
                }
                echo "</tr>";
                unset($seat_name);
            }
            echo "</table>";
        }//if(Seater)
        else if ($lid[1] == 'sleeper') {
            $sqsl = $this->db->query("select max(row) as upper_rows,max(col) as upper_cols from operator_layouts where model='$busmodel' and travel_id='$travel_id'  and seat_type='U'") or die(mysql_error());
            foreach ($sqsl->result() as $row2) {
                $upper_rows = $row2->upper_rows;
                $upper_cols = $row2->upper_cols;
            }
            echo "<input type='hidden' name='upper_rows' id='upper_rows' value='$upper_rows' />
	        <input type='hidden' name='upper_cols' id='upper_cols' value='$upper_cols' />";

            //echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;"><tr><td  colspan="2">Select All : <input type="checkbox" name="selectall" id="selectall" onClick="selectAll()"></td></tr></table>';
            echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;">
                    <tr>
                      <td colspan="' . $upper_cols . '">Upper Deck</td>
                    </tr>  
                 ';
            for ($i = 1; $i <= $upper_rows; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $upper_cols; $j++) {

                    $sql3 = $this->db->query("select * from operator_layouts where row='$i' and col='$j' and model='$busmodel' and travel_id='$travel_id' and seat_type='U' ") or die(mysql_error());

                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name != "") {

                        echo '<td align="center" width="40" height="40" style="border:#666666 solid 2px"><input onchange="document.getElementById(\'utxt' . $i . '-' . $j . '\').disabled=!this.checked;" class="chkbox" type="checkbox" name="uchk' . $i . '-' . $j . '" id="uchk' . $i . '-' . $j . '"/><input type="text" name="utxt' . $i . '-' . $j . '" class="textbox" id="utxt' . $i . '-' . $j . '" value="' . $seat_name . '" size="1" disabled="disabled"></td>';
                    } else {
                        echo '<td>&nbsp;</td>';
                    }
unset($seat_name);
                }
                echo "</tr>";

            }
            echo "</table><br /> ";
            $sq = $this->db->query("select max(row) as lower_rows,max(col) as lower_cols from operator_layouts where model='$busmodel' and travel_id='$travel_id'  and seat_type='L'") or die(mysql_error());
            foreach ($sq->result() as $row1) {
                $lower_rows = $row1->lower_rows;
                $lower_cols = $row1->lower_cols;
            }
            echo "<input type='hidden' name='lower_rows' id='lower_rows' value='$lower_rows' />
	        <input type='hidden' name='lower_cols' id='lower_cols' value='$lower_cols' />";
            echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;">
                    <tr>
                      <td colspan="' . $lower_cols . '">Lower Deck</td>
                    </tr>  
                 ';
            for ($i = 1; $i <= $lower_rows; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $lower_cols; $j++) {

                    $sql3 = $this->db->query("select * from operator_layouts where row='$i' and col='$j' and model='$busmodel' and travel_id='$travel_id' and seat_type='L' ") or die(mysql_error());

                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name != "") {

                        echo '<td align="center" width="40" height="40" style="border:#666666 solid 2px"><input onchange="document.getElementById(\'ltxt' . $i . '-' . $j . '\').disabled=!this.checked;" class="chkbox" type="checkbox" name="lchk' . $i . '-' . $j . '" id="lchk' . $i . '-' . $j . '" /><input type="text" name="ltxt' . $i . '-' . $j . '" class="textbox" id="ltxt' . $i . '-' . $j . '" value="' . $seat_name . '" size="1" disabled="disabled"></td>';
                    } else {
                        echo '<td>&nbsp;</td>';
                    }
unset($seat_name);
                }
                echo "</tr>";
            }
            echo "</table>";
        }//else if(sleeper)
        //getting sleeperSeater Layout
        else if ($lid[1] == 'seatersleeper') {
            $sq = $this->db->query("select max(row) as upper_rows,max(col) as upper_cols from operator_layouts where model='$busmodel' and travel_id='$travel_id'  and seat_type='U'") or die(mysql_error());
            foreach ($sq->result() as $row1) {
                $upper_rows = $row1->upper_rows;
                $upper_cols = $row1->upper_cols;
            }
            echo "<input type='hidden' name='upper_rows' id='upper_rows' value='$upper_rows' />
	        <input type='hidden' name='upper_cols' id='upper_cols' value='$upper_cols' />";

            //echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;"><tr><td  colspan="2">Select All : <input type="checkbox" name="selectall" id="selectall" onClick="selectAll()"></td></tr></table>';
            echo '<table border="0" cellspacing="1" cellpadding="1"  align="center" style="padding-top:10px;padding-bottom:10px;">';
            echo '<tr>
                      <td colspan="' . $upper_cols . '">Upper Deck</td>
                    </tr>  
                 ';
            for ($i = 1; $i <= $upper_rows; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $upper_cols; $j++) {

                    $sql3 = $this->db->query("select * from operator_layouts where row='$i' and col='$j' and model='$busmodel' and travel_id='$travel_id' and seat_type='U' ") or die(mysql_error());

                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name != "") {

                        echo '<td align="center" width="40" height="40" style="border:#666666 solid 2px"><input onchange="document.getElementById(\'utxt' . $i . '-' . $j . '\').disabled=!this.checked;" class="chkbox" type="checkbox" name="uchk' . $i . '-' . $j . '" id="uchk' . $i . '-' . $j . '"/><input type="text" name="utxt' . $i . '-' . $j . '" class="textbox" id="utxt' . $i . '-' . $j . '" value="' . $seat_name . '" size="1"  disabled="disabled"><input type="hidden" name="uppertype' . $i . $j . '" id="uppertype' . $i . $j . '" value="' . $seat_type . '" /></td>';
                    } else {
                        echo '<td width="40">&nbsp;</td>';
                    }
                }
                echo "</tr>";
            }
            echo "</table> <br /> <br />";

            echo '<table border="0" cellspacing="1" cellpadding="1">
                    <tr>
                      <td colspan="' . $lower_cols . '">Lower Deck</td>
                    </tr>  
                 ';
            echo '<tr>
                    <td colspan="' . $lower_cols . '">';
            $sq = $this->db->query("select max(row) as lower_rows,max(col) as lower_cols from operator_layouts where model='$busmodel' and travel_id='$travel_id'  and (seat_type='L:s' or seat_type='L:b')") or die(mysql_error());
            foreach ($sq->result() as $row1) {
                $lower_rows = $row1->lower_rows;
                $lower_cols = $row1->lower_cols;
            }
            echo "<input type='hidden' name='lower_rows' id='lower_rows' value='$lower_rows' />
	        <input type='hidden' name='lower_cols' id='lower_cols' value='$lower_cols' />";

            echo '<table border="0" cellspacing="1" cellpadding="1">';
            for ($k = 1; $k <= $lower_rows; $k++) {
                echo '<tr>';
                for ($l = 1; $l <= $lower_cols; $l++) {
                    $sql3 = $this->db->query("select * from operator_layouts where row='$k' and col='$l' and model='$busmodel' and travel_id='$travel_id' and (seat_type='L:s' or seat_type='L:b') ") or die(mysql_error());

                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name != "") {

                        echo '<td align="center" width="40" height="40" style="border:#666666 solid 2px"><input onchange="document.getElementById(\'ltxt' . $k . '-' . $l . '\').disabled=!this.checked;" class="chkbox" type="checkbox" name="lchk' . $k . '-' . $l . '" id="lchk' . $k . '-' . $l . '"/><input type="text" name="ltxt' . $k . '-' . $l . '" class="textbox" id="ltxt' . $k . '-' . $l . '" value="' . $seat_name . '" size="1" disabled="disabled"><input type="hidden" name="lowertype' . $k . $l . '" id="lowertype' . $k . $l . '" value="' . $seat_type . '" /></td>';
                    } else {
                        echo '<td width="40">&nbsp;</td>';
                    }
                }
                $i++;
                echo "</tr>";
            }
            echo "</table></td></tr></table>";
        }//else if($lid[1]=='seatersleeper')
    }

    public function getAllCity() {
        $this->db->select('city_id,city_name');
        $query = $this->db->get('master_cities');
        $data = array();
        $data[0] = '-------select-------';
        foreach ($query->result() as $rows) {
            $data[$rows->city_id] = $rows->city_name;
        }
        return $data;
    }

    public function getbustype() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $busmodel = $this->input->post('busmodel');
        $this->db->select('layout_id');
        $this->db->where('model', $busmodel);
        $this->db->where('travel_id', $travel_id);
        $query = $this->db->get('operator_layouts');
        //$stmt = "select layout_id from operator_layouts where model='$busmodel' and travel_id='$travel_id'";
        //echo $stmt;
        foreach ($query->result() as $rows) {
            $layout_id = $rows->layout_id;
        }
        //echo $layout_id."jhfj";
        $layout_id1 = explode('#', $layout_id);
        return $layout_id1[1];
    }

    public function getBoardDb($fid, $from, $snum, $halts) {
        $fid12 = explode(",", $fid);
        $fid1_imp = array_unique($fid12);
        $imp = implode(",", $fid1_imp);
        $fid1 = explode(",", $imp);
        //print_r($fid1);

        $from12 = explode(",", $from);
        $from1_imp = array_unique($from12);
        $from_imp = implode(",", $from1_imp);
        $from1 = explode(",", $from_imp);
        // print_r($from1);		
        $n = count($fid1);


        echo '
		 <script type="text/javascript" src="' . base_url('js/app-js.v1.js') . '"></script>
		 <script type="text/javascript">
		
		
		function saveBoard()
		{
			var city_name="";
			var city_id="";
			var board_point="";
			var bpid="";
			var lm="";
			var hhST="";
			var mmST="";
			var ampmST="";
			var ival=$("#nval").val();
			
			var sernum=$("#sernum").val();
			
			for(var i=0;i<ival;i++)
			{
				var jval=$("#jval"+i).val();
				for(var j=0;j<jval;j++)
				{	
					if($("#timehrST"+i+j).val()!="HH" && $("#timemST"+i+j).val()!="MM" && $("#tfmST"+i+j).val()!="AMPM")
					{					
					
						if(city_name=="")
						{
							city_name=$("#cityname"+i+j).val();
						}
						else
						{
							city_name=city_name+"#"+$("#cityname"+i+j).val();
						} 
						if(city_id=="")
						{
							city_id=$("#cityid"+i+j).val();
						}
						else
						{
							city_id=city_id+"#"+$("#cityid"+i+j).val();
						} 
						if(board_point=="")
						{
							board_point=$("#bpname"+i+j).val();
						}
						else
						{
							board_point=board_point+"#"+$("#bpname"+i+j).val();
						} 
						if(bpid=="")
						{
							bpid=$("#bpid"+i+j).val();
						}
						else
						{
							bpid=bpid+"#"+$("#bpid"+i+j).val();
						}
						if(lm=="")
						{
							lm=$("#lm"+i+j).val();
						}
						else
						{
							lm=lm+"#"+$("#lm"+i+j).val();
						}  
						if(hhST=="")
						{
							hhST=$("#timehrST"+i+j).val();
						}
						else
						{
							hhST=hhST+"#"+$("#timehrST"+i+j).val();
						}
						if(mmST=="")
						{
							mmST=$("#timemST"+i+j).val();
						}
						else
						{
							mmST=mmST+"#"+$("#timemST"+i+j).val();
						}
						if(ampmST=="")
						{
							ampmST=$("#tfmST"+i+j).val();
						}
						else
						{
							ampmST=ampmST+"#"+$("#tfmST"+i+j).val();
						}	
                                                if ($("#lm" + i + j).val() == "")
                    {
                        alert("Please Provide landmark for " + $("#bpname" + i + j).val());
                        $("#lm" + i + j).focus();
                        return false;
                    }									
					}					
					
				}				
			}				
			if(hhST=="" && mmST=="" && ampmST=="")
			{
				alert("Please select atleast one boarding point time");
				return false;
			}
			//alert(sernum);alert(city_name);alert(city_id);alert(board_point);alert(bpid);
			//alert(lm);
							$.post("saveBoard",{sernum:sernum,city_name:city_name,city_id:city_id,board_point:board_point,bpid:bpid,lm:lm,hhST:hhST,mmST:mmST,ampmST:ampmST},function(res)
			{
				//alert(res);
				if(res==1)
				{
					alert("Boarding points are saved successfully !!");
					window.close();
				}
				else
				{
					alert("There was a problem occurred, Try again");
				}
				
    		});
			 			 
		}
		</script>
		 
		 
		 
		 <table width="100%" border="1" >
		 		 <tr>
    <td width="19%" height="36" ><strong>City Name </strong></td>
    <td width="34%" >
    <strong>Board Point Name </strong></td>
    <td width="25%" ><strong>Time</strong></td>
    <td width="22%" ><strong>Landmark</strong></td>
  </tr>';

        for ($i = 0; $i < $n; $i++) {

            echo '<tr>
    				<td height="35"><strong>' . $from1[$i] . '</strong></td>
    				';
            echo'<td colspan="3">
					<table width="100%" border="0">
					';
            $sql = $this->db->query("select * from board_drop_points where city_id='$fid1[$i]' and city_name='$from1[$i]' order by board_drop")or die(mysql_error());

            $j = 0;
            foreach ($sql->result() as $row) {
                $hours = $this->getHours();
                $timehrST = 'id="timehrST' . $i . $j . '" ';
                $timenST = 'name="timehrST' . $i . $j . '" ';

                $hours1 = $this->getMinutes();

                $timemiST = 'id="timemST' . $i . $j . '"';
                $timemnST = 'name="timemST' . $i . $j . '"';

                $tfidST = 'id="tfmST' . $i . $j . '" ';
                $tfnameST = 'name="tfm' . $i . $j . '" style="width:50px"';

                $tfv = array("AMPM" => "-select-", "AM" => "AM", "PM" => "PM");

                $board_drop = $row->board_drop;
                $id = $row->id;
                echo '<tr>
        						<td width="42%" height="35"><strong>' . $board_drop . '</strong>
								<input type="hidden" name="cityname' . $i . $j . '" id="cityname' . $i . $j . '" value="' . $from1[$i] . '">
								<input type="hidden" name="cityid' . $i . $j . '" id="cityid' . $i . $j . '" value="' . $fid1[$i] . '">
								<input type="hidden" name="bpname' . $i . $j . '" id="bpname' . $i . $j . '" value="' . $board_drop . '">
								<input type="hidden" name="bpid' . $i . $j . '" id="bpid' . $i . $j . '" value="' . $id . '">
								</td>
						        <td width="31%">
								' . form_dropdown($timenST, $hours, $hr, $timehrST) . '' . form_dropdown($timemnST, $hours1, $hr1, $timemiST) . '' . form_dropdown($tfnameST, $tfv, $tf[1], $tfidST) . '</td>
						        <td width="27%"><input type="text" name="lm' . $i . $j . '" id="lm' . $i . $j . '" /></td>
							      </tr>';
                $j++;
            }

            echo'</table><input type="hidden" name="jval' . $i . '" id="jval' . $i . '" value="' . $j . '"></td>    				
  				 </tr>';
        }
        echo '<tr>
		 		   <input type="hidden" name="nval" id="nval" value="' . $n . '">
				   <input type="hidden" name="sernum" id="sernum" value="' . $snum . '">
		 		   <td height="36" colspan="4" align="center" ><input type="button" class="btn btn-primary" value="Save Boarding Poings" onClick="saveBoard()"></td>
		 		   </tr></table>';
    }

    public function saveBoardDb($sernum, $city_name, $city_id, $board_point, $bpid, $lm, $hhST, $mmST, $ampmST) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $city_names = explode("#", $city_name);
        $city_ids = explode("#", $city_id);
        $board_points = explode("#", $board_point);
        $hhSTs = explode("#", $hhST);
        $mmSTs = explode("#", $mmST);
        $ampmSTs = explode("#", $ampmST);
        $bpids = explode("#", $bpid);
        $lms = explode("#", $lm);
        $n = count($city_names);

        $sql1 = $this->db->query("delete from temp_board where service_num='$sernum' and board_or_drop_type='board'") or die(mysql_error());
        //$d =date('H:i:s', strtotime($start_time1));
        for ($i = 0; $i < $n; $i++) {
            $arr_time1 = $hhSTs[$i] . ":" . $mmSTs[$i] . " " . $ampmSTs[$i];
            $d1 = date('H:i:s', strtotime($arr_time1));
            $bpname = $board_points[$i] . "#" . $d1 . "#" . $lms[$i];

            $sql = $this->db->query("insert into temp_board(service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,board_time,bpdp_id,timing) values('$sernum','$travel_id','$city_ids[$i]','$city_names[$i]','board','$bpname','$d1','$bpids[$i]','$arr_time1')")or die(mysql_error());
        }
        if ($sql) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function getDropDb($tid, $to, $snum, $halts) {
        $tid12 = explode(",", $tid);
        $tid1_imp = array_unique($tid12);
        $imp = implode(",", $tid1_imp);
        $tid1 = explode(",", $imp);
        //print_r($tid1);

        $to12 = explode(",", $to);
        $to1_imp = array_unique($to12);
        $to_imp = implode(",", $to1_imp);
        $to1 = explode(",", $to_imp);
        //print_r($to1);
        $n = count($tid1);

        echo '
		  <script type="text/javascript" src="' . base_url('js/app-js.v1.js') . '"></script>
		 <script type="text/javascript">
		
		
		function saveDrop()
		{
			var city_name="";
			var city_id="";
			var drop_point="";
			var dpid="";
			var lm="";
			var hhST="";
			var mmST="";
			var ampmST="";
			var ival=$("#nval").val();
			
			var sernum=$("#sernum").val();
			
			for(var i=0;i<ival;i++)
			{
				var jval=$("#jval"+i).val();
				for(var j=0;j<jval;j++)
				{
					if($("#timehrST"+i+j).val()!="HH" && $("#timemST"+i+j).val()!="MM" && $("#tfmST"+i+j).val()!="AMPM")
					{					
					
						if(city_name=="")
						{
							city_name=$("#cityname"+i+j).val();
						}
						else
						{
							city_name=city_name+"#"+$("#cityname"+i+j).val();
						} 
						if(city_id=="")
						{
							city_id=$("#cityid"+i+j).val();
						}
						else
						{
							city_id=city_id+"#"+$("#cityid"+i+j).val();
						} 
						if(drop_point=="")
						{
							drop_point=$("#dpname"+i+j).val();
						}
						else
						{
							drop_point=drop_point+"#"+$("#dpname"+i+j).val();
						} 
						if(dpid=="")
						{
							dpid=$("#dpid"+i+j).val();
						}
						else
						{
							dpid=dpid+"#"+$("#dpid"+i+j).val();
						}
						if(lm=="")
						{
							lm=$("#lm"+i+j).val();
						}
						else
						{
							lm=lm+"#"+$("#lm"+i+j).val();
						}  
						if(hhST=="")
						{
							hhST=$("#timehrST"+i+j).val();
						}
						else
						{
							hhST=hhST+"#"+$("#timehrST"+i+j).val();
						}
						if(mmST=="")
						{
							mmST=$("#timemST"+i+j).val();
						}
						else
						{
							mmST=mmST+"#"+$("#timemST"+i+j).val();
						}
						if(ampmST=="")
						{
							ampmST=$("#tfmST"+i+j).val();
						}
						else
						{
							ampmST=ampmST+"#"+$("#tfmST"+i+j).val();
						}
                                                if ($("#lm" + i + j).val() == "")
                    {
                        alert("Please Provide landmark for " + $("#dpname" + i + j).val());
                        $("#lm" + i + j).focus();
                        return false;
                    }								
					}					
				}				
			}				
			if(hhST=="" && mmST=="" && ampmST=="")
			{
				alert("Please select atleast one Drop point time");
				return false;
			}
			//alert(sernum);alert(city_name);alert(city_id);alert(drop_point);alert(dpid);
			//alert(lm);
							$.post("saveDrop",{sernum:sernum,city_name:city_name,city_id:city_id,drop_point:drop_point,dpid:dpid,lm:lm,hhST:hhST,mmST:mmST,ampmST:ampmST},function(res)
			{
				//alert(res);
				if(res==1)
				{
					alert("Drop points are saved successfully !!");
					window.close();
				}
				else
				{
					alert("There was a problem occurred, Try again");
				}
				
    		});
			 			 
		}
		</script>
		 
		 <table width="100%" border="1" >
		 		 <tr>
    <td width="19%" height="36" ><strong>City Name </strong></td>
    <td width="34%" >
    <strong>Drop Point Name </strong></td>
    <td  align="center"><strong>Time</strong></td>
	<td width="22%" ><strong>Landmark</strong></td>
  </tr>';

        for ($i = 0; $i < $n; $i++) {

            echo '<tr>
    				<td height="35"><strong>' . $to1[$i] . '</strong></td>
    				';
            echo'<td colspan="3">
					<table width="100%" border="0">
					';
            $sql = $this->db->query("select * from board_drop_points where city_id='$tid1[$i]' and city_name='$to1[$i]'")or die(mysql_error());
            $j = 0;
            foreach ($sql->result() as $row) {
                $hours = $this->getHours();
                $timehrST = 'id="timehrST' . $i . $j . '" ';
                $timenST = 'name="timehrST' . $i . $j . '" ';

                $hours1 = $this->getMinutes();

                $timemiST = 'id="timemST' . $i . $j . '"';
                $timemnST = 'name="timemST' . $i . $j . '"';

                $tfidST = 'id="tfmST' . $i . $j . '" ';
                $tfnameST = 'name="tfm' . $i . $j . '" style="width:50px"';

                $tfv = array("AMPM" => "-select-", "AM" => "AM", "PM" => "PM");
                $board_drop = $row->board_drop;
                $id = $row->id;
                echo '<tr>
        						<td width="42%" height="35"><strong>' . $board_drop . '</strong>
								<input type="hidden" name="cityname' . $i . $j . '" id="cityname' . $i . $j . '" value="' . $to1[$i] . '">
								<input type="hidden" name="cityid' . $i . $j . '" id="cityid' . $i . $j . '" value="' . $tid1[$i] . '">
								<input type="hidden" name="dpname' . $i . $j . '" id="dpname' . $i . $j . '" value="' . $board_drop . '">
								<input type="hidden" name="dpid' . $i . $j . '" id="dpid' . $i . $j . '" value="' . $id . '">
								</td>
						        <td width="31%" colspan="2">
								' . form_dropdown($timenST, $hours, $hr, $timehrST) . '' . form_dropdown($timemnST, $hours1, $hr1, $timemiST) . '' . form_dropdown($tfnameST, $tfv, $tf[1], $tfidST) . '</td>
								<td width="27%"><input type="text" name="lm' . $i . $j . '" id="lm' . $i . $j . '" /></td>
						        </tr>';
                $j++;
            }

            echo'</table><input type="hidden" name="jval' . $i . '" id="jval' . $i . '" value="' . $j . '"></td>    				
  				 </tr>';
        }
        echo '<tr>
		 		   <input type="hidden" name="nval" id="nval" value="' . $n . '">
				   <input type="hidden" name="sernum" id="sernum" value="' . $snum . '">
		 		   <td height="36" colspan="4" align="center" ><input type="button" class="btn btn-primary" value="Save Drop Poings" onClick="saveDrop()"></td>
		 		   </tr></table>
';
    }

    public function saveDropDb($sernum, $city_name, $city_id, $board_point, $bpid, $lm, $hhST, $mmST, $ampmST) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $city_names = explode("#", $city_name);
        $city_ids = explode("#", $city_id);
        $board_points = explode("#", $board_point);
        $hhSTs = explode("#", $hhST);
        $mmSTs = explode("#", $mmST);
        $ampmSTs = explode("#", $ampmST);
        $bpids = explode("#", $bpid);
        $lms = explode("#", $lm);
        $n = count($city_names);

        $sql1 = $this->db->query("delete from temp_board where service_num='$sernum' and board_or_drop_type='drop'") or die(mysql_error());

        for ($i = 0; $i < $n; $i++) {
            $arr_time1 = $hhSTs[$i] . ":" . $mmSTs[$i] . " " . $ampmSTs[$i];
            $d1 = date('h:i A', strtotime($arr_time1));
            $bpname = $board_points[$i] . "#" . $d1 . "#" . $lms[$i];
			//echo $bpname;
			//echo "'$sernum','$travel_id','$city_ids[$i]','$city_names[$i]','drop','$bpname','$d1','$bpids[$i]','$arr_time1'";
            $sql = $this->db->query("insert into temp_board(service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,board_time,bpdp_id,timing) values('$sernum','$travel_id','$city_ids[$i]','$city_names[$i]','drop','$bpname','$d1','$bpids[$i]','$arr_time1')")or die(mysql_error());
			
        }
        if ($sql) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function getHours() {
        $data = array();
        $data[HH] = "HH";
        for ($i = 0; $i <= 12; $i++) {
            if ($i < 10)
                $i = "0" . $i;
            $data[$i] = $i;
        }
        return $data;
    }

    public function getMinutes() {
        $data = array();
        $data[MM] = "MM";
        for ($i = 0; $i <= 60; $i++) {
            if ($i < 10)
                $i = "0" . $i;
            $data[$i] = $i;
        }
        return $data;
    }

    public function getBoardOrDropValDb() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $snum = $this->input->post('snum');
        $fids = $this->input->post('fids');
        $tids = $this->input->post('tids');

        $fid12 = explode(",", $fids);
        $fid1_imp = array_unique($fid12);
        $imp = implode(",", $fid1_imp);
        $fid1 = explode(",", $imp);
        $m = count($fid1);

        $tid12 = explode(",", $tids);
        $tid1_imp = array_unique($tid12);
        $imp = implode(",", $tid1_imp);
        $tid1 = explode(",", $imp);
        $n = count($tid1);

        $bp = 0;
        $dp = 0;
        for ($i = 0; $i < $m; $i++) {
            $sql = $this->db->query("select count(*) as cnt from temp_board where service_num='$snum' and travel_id='$travel_id' and city_id='$fid1[$i]'")or die(mysql_error());
            foreach ($sql->result() as $row) {
                $cnt = $row->cnt;
            }
            if ($cnt > 0) {
                $bp = 1;
            }
        }

        for ($j = 0; $j < $n; $j++) {
            $sql1 = $this->db->query("select count(*) as cnt1 from temp_board where service_num='$snum' and travel_id='$travel_id' and city_id='$tid1[$j]'")or die(mysql_error());
            foreach ($sql1->result() as $row) {
                $cnt1 = $row->cnt1;
            }
            if ($cnt1 > 0) {
                $dp = 1;
            }
        }
        if ($bp == 0) {
            echo 1; //boarding points
        } else if ($dp == 0) {
            echo 2; //drop points
        } else {
            echo 3; //success
        }
    }

    public function saveBusDetailsDb() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $sname = $this->input->post('sname');
        $snum = $this->input->post('snum');
        $service_from = $this->input->post('service_from');
        $service_to = $this->input->post('service_to');
        $busmodel = $this->input->post('busmodel');
        $stype = $this->input->post('stype');
        $weeks = $this->input->post('weeks');
        $halts = $this->input->post('halts');
        $layout_type = $this->input->post('layout_type');
        $lower_seat_no1 = $this->input->post('lower_seat_no');
        $upper_seat_no1 = $this->input->post('upper_seat_no');
        $lower_rowcols1 = $this->input->post('lower_rowcols');
        $upper_rowcols1 = $this->input->post('upper_rowcols');
        $rows = $this->input->post('rows');
        $cols = $this->input->post('cols');
        $lower_rows = $this->input->post('lower_rows');
        $lower_cols = $this->input->post('lower_cols');
        $upper_rows = $this->input->post('upper_rows');
        $upper_cols = $this->input->post('upper_cols');
        $fids = $this->input->post('fids');
        $tids = $this->input->post('tids');
        $froms = $this->input->post('froms');
        $tos = $this->input->post('tos');
        $sfares = $this->input->post('sfares');
        $lbfares = $this->input->post('lbfares');
        $ubfares = $this->input->post('ubfares');
        $hhST = $this->input->post('hhST');
        $mmST = $this->input->post('mmST');
        $ampmST = $this->input->post('ampmST');
        $hhAT = $this->input->post('hhAT');
        $mmAT = $this->input->post('mmAT');
        $ampmAT = $this->input->post('ampmAT');
        $lowertype1 = $this->input->post('lowertype');
        $uppertype1 = $this->input->post('uppertype');
        $title = $this->input->post('title');
        $service_tax = $this->input->post('service_tax');

        $fids1 = explode(",", $fids);
        $tids1 = explode(",", $tids);
        $froms1 = explode(",", $froms);
        $tos1 = explode(",", $tos);
        $sfares1 = explode(",", $sfares);
        $lbfares1 = explode(",", $lbfares);
        $ubfares1 = explode(",", $ubfares);
        $hhST1 = explode(",", $hhST);
        $mmST1 = explode(",", $mmST);
        $ampmST1 = explode(",", $ampmST);
        $hhAT1 = explode(",", $hhAT);
        $mmAT1 = explode(",", $mmAT);
        $ampmAT1 = explode(",", $ampmAT);

        $sql_ser = $this->db->query("select count(*) as cnt from master_buses where service_num='$snum'") or die(mysql_error());
        foreach ($sql_ser->result() as $row1) {
            $cnt = $row1->cnt;
        }
        if ($cnt == 0) {

            /*             * ******************** Boarding Points Related ******************* */

            $sqlb = $this->db->query("select * from temp_board where service_num='$snum' and travel_id='$travel_id' and city_id<>'undefined'") or die(mysql_error());
            foreach ($sqlb->result() as $row) {
                $service_num = $row->service_num;
                $travel_id = $row->travel_id;
                $city_id = $row->city_id;
                $city_name = $row->city_name;
                $board_or_drop_type = $row->board_or_drop_type;
                $board_drop = $row->board_drop;
                $board_time = $row->board_time;
                $bpdp_id = $row->bpdp_id;
                $timing = $row->timing;

                $sqlI = $this->db->query("insert into boarding_points(is_van,service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,board_time,bpdp_id,contact,bus_no,timing) values('no','$service_num','$travel_id','$city_id','$city_name','$board_or_drop_type','$board_drop','$board_time','$bpdp_id','','','$timing')") or die(mysql_error());
            }
            /*             * ******************** Boarding Points Related End******************* */
            /*             * ************************** Layout related ***************** */

            $lower_seat_no2 = explode('#', $lower_seat_no1);
            $lower_rowcols2 = explode('#', $lower_rowcols1);
            $upper_seat_no2 = explode('#', $upper_seat_no1);
            $upper_rowcols2 = explode('#', $upper_rowcols1);
            $lowertype2 = explode('#', $lowertype1);
            $uppertype2 = explode('#', $uppertype1);

            $chkcnt = count($lower_seat_no2);

            if ($layout_type == 'seater') {

                for ($l = 0; $l < $chkcnt; $l++) {
                    $lower_seat_no = $lower_seat_no2[$l];

                    $lower_rowcols = explode('-', $lower_rowcols2[$l]);

                    $lower_row = $lower_rowcols[0];
                    $lower_col = $lower_rowcols[1];

                    if ($rows == 1) {
                        $window = 1;
                    }
                    if ($rows == 2 || $rows == 3) {
                        if ($lower_row == 1) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($rows == 4) {
                        if ($lower_row == 1 || $lower_row == 4) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($rows == 5) {
                        if ($lower_row == 1 || $lower_row == 5) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else {
                        $window = 0;
                    }

                    $seat_type = "s";
                    $is_ladies = 0;
                    $layout_id = $travel_id . "#" . $layout_type;

                    $sql2 = $this->db->query("insert into master_layouts(travel_id,layout_id,seat_name,row,col,seat_type,window,is_ladies,service_num,seat_status,available,available_type,fare,lberth_fare,uberth_fare,status) values('$travel_id','$layout_id','$lower_seat_no','$lower_col','$lower_row','$seat_type','$window','$is_ladies','$snum','0','0','0','0','0','0','0')");
                }
            }//if($layout_type=='seater')
            else if ($layout_type == 'sleeper') {
                for ($l = 0; $l < count($lower_seat_no2); $l++) {
                    $lower_seat_no = $lower_seat_no2[$l];

                    $lower_rowcols = explode('-', $lower_rowcols2[$l]);

                    $lower_row = $lower_rowcols[0];
                    $lower_col = $lower_rowcols[1];

                    if ($lower_rows == 1 || $lower_rows == 2) {
                        $window = 1;
                    } else if ($lower_rows == 4) {
                        if ($lower_row == 1 || $lower_row == 4) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($lower_rows == 5) {
                        if ($lower_row == 1 || $lower_row == 5) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    }

                    $seat_type = "L";
                    $is_ladies = 0;
                    $layout_id = $travel_id . "#" . $layout_type;

                    //echo "lower_rows".$lower_rows." # window".$window;

                    $sql2 = $this->db->query("insert into master_layouts(travel_id,layout_id,seat_name,row,col,seat_type,window,is_ladies,service_num,seat_status,available,available_type,fare,lberth_fare,uberth_fare,status) values('$travel_id','$layout_id','$lower_seat_no','$lower_col','$lower_row','$seat_type','$window','$is_ladies','$snum','0','0','0','0','0','0','0')");
                }

                for ($u = 0; $u < count($upper_seat_no2); $u++) {
                    $upper_seat_no = $upper_seat_no2[$u];

                    $upper_rowcols = explode('-', $upper_rowcols2[$u]);

                    $upper_row = $upper_rowcols[0];
                    $upper_col = $upper_rowcols[1];

                    if ($upper_rows == 1 || $upper_rows == 2) {
                        $window = 1;
                    } else if ($upper_rows == 4) {
                        if ($upper_row == 1 || $upper_row == 4) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($upper_rows == 5) {
                        if ($upper_row == 1 || $upper_row == 5) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    }

                    $seat_type = "U";
                    $is_ladies = 0;
                    $layout_id = $travel_id . "#" . $layout_type;

                    $sql3 = $this->db->query("insert into master_layouts(travel_id,layout_id,seat_name,row,col,seat_type,window,is_ladies,service_num,seat_status,available,available_type,fare,lberth_fare,uberth_fare,status) values('$travel_id','$layout_id','$upper_seat_no','$upper_col','$upper_row','$seat_type','$window','$is_ladies','$snum','0','0','0','0','0','0','0')");
                }
            }//else if($layout_type=='sleeper')
            else if ($layout_type == 'seatersleeper') {

                for ($l = 0; $l < count($lower_seat_no2); $l++) {
                    $lower_seat_no = $lower_seat_no2[$l];

                    $lower_rowcols = explode('-', $lower_rowcols2[$l]);

                    $lower_row = $lower_rowcols[0];
                    $lower_col = $lower_rowcols[1];

                    if ($lower_rows == 1 || $lower_rows == 2) {
                        $window = 1;
                    } else if ($lower_rows == 4) {
                        if ($lower_row == 1 || $lower_row == 4) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($lower_rows == 5) {
                        if ($lower_row == 1 || $lower_row == 5) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    }

                    $seat_type = $lowertype2[$l];
                    $is_ladies = 0;
                    $layout_id = $travel_id . "#" . $layout_type;

                    $sql2 = $this->db->query("insert into master_layouts(travel_id,layout_id,seat_name,row,col,seat_type,window,is_ladies,service_num,seat_status,available,available_type,fare,lberth_fare,uberth_fare,status) values('$travel_id','$layout_id','$lower_seat_no','$lower_col','$lower_row','$seat_type','$window','$is_ladies','$snum','0','0','0','0','0','0','0')");
                }

                for ($u = 0; $u < count($upper_seat_no2); $u++) {
                    $upper_seat_no = $upper_seat_no2[$u];

                    $upper_rowcols = explode('-', $upper_rowcols2[$u]);

                    $upper_row = $upper_rowcols[0];
                    $upper_col = $upper_rowcols[1];

                    if ($upper_rows == 1 || $upper_rows == 2) {
                        $window = 1;
                    } else if ($upper_rows == 4) {
                        if ($upper_row == 1 || $upper_row == 4) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    } else if ($upper_rows == 5) {
                        if ($upper_row == 1 || $upper_row == 5) {
                            $window = 1;
                        } else {
                            $window = 0;
                        }
                    }

                    $seat_type = $uppertype2[$u];
                    $is_ladies = 0;
                    $layout_id = $travel_id . "#" . $layout_type;

                    $sql3 = $this->db->query("insert into master_layouts(travel_id,layout_id,seat_name,row,col,seat_type,window,is_ladies,service_num,seat_status,available,available_type,fare,lberth_fare,uberth_fare,status) values('$travel_id','$layout_id','$upper_seat_no','$upper_col','$upper_row','$seat_type','$window','$is_ladies','$snum','0','0','0','0','0','0','0')");
                }
            }//seatersleeper





            /*             * ************************** Layout related End ***************** */
            /*             * ********* inserting master buses ************* */

            //code for seat count
            if ($layout_type == 'seater') {
                $seat_nos = count($lower_seat_no2);
                $lowerdeck_nos = 0;
                $upperdeck_nos = 0;
            } else if ($layout_type == 'sleeper') {
                $seat_nos = 0;
                $lowerdeck_nos = count($lower_seat_no2);
                $upperdeck_nos = count($upper_seat_no2);
            } else {
                $seat_nos = 0;
                $lowerdeck_nos = count($lower_seat_no2);
                $upperdeck_nos = count($upper_seat_no2);
            }
            //code for service type ex: normal or special or weekly
            if ($stype == 'daily') {
                $serviceType = "normal";
            } else if ($stype == 'special') {
                $serviceType = "special";
            } else if ($stype == 'weekly') {
                $serviceType = "weekly";
            }
            $service_num = $snum;
            $travel_id = $travel_id;
            $model = $busmodel;
            $bus_type = $layout_type;


            $status = 0;
            //$service_route = $service_from." To ".$service_to;
            $service_name = $sname;

            for ($i = 0; $i < $halts; $i++) {
                $from_id = $fids1[$i];
                $from_name = $froms1[$i];
                $to_id = $tids1[$i];
                $to_name = $tos1[$i];
                $service_route = $from_name . " To " . $to_name;
                if ($layout_type == 'seater') {
                    $seat_fare = $sfares1[$i];
                    $lberth_fare = "";
                    $uberth_fare = "";
                } else if ($layout_type == 'sleeper') {
                    $seat_fare = "";
                    $lberth_fare = $lbfares1[$i];
                    $uberth_fare = $ubfares1[$i];
                } else {
                    $seat_fare = $sfares1[$i];
                    $lberth_fare = $lbfares1[$i];
                    $uberth_fare = $ubfares1[$i];
                }

                $start_time1 = $hhST1[$i] . ":" . $mmST1[$i] . " " . $ampmST1[$i];
                $arr_time1 = $hhAT1[$i] . ":" . $mmAT1[$i] . "" . $ampmAT1[$i];
                $d = date('H:i:s', strtotime($start_time1));
                $d1 = date('H:i:s', strtotime($arr_time1));
                $d2 = strtotime($d);
                $d3 = strtotime($d1);
                $diff = $d2 - $d3;


                $start_time = $d;
                $arr_time = $arr_time1;
                $journey_time = date('H:i:s', ($diff));
                $journey_time = '0:0:0';

                //echo $start_time."#".$start_time1."#".$arr_time1."#".$d1."#".$journey_time."#".$diff;					

                $sql = $this->db->query("insert into master_buses(serviceType,service_num,travel_id,from_id,from_name,to_id,to_name,start_time,journey_time,arr_time,model,bus_type,seat_nos,lowerdeck_nos,upperdeck_nos,seat_fare,lberth_fare,uberth_fare,status,service_route,service_name,service_days,title,service_tax) values('$serviceType','$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$start_time','$journey_time','$arr_time','$model','$bus_type','$seat_nos','$lowerdeck_nos','$upperdeck_nos','$seat_fare','$lberth_fare','$uberth_fare','$status','$service_route','$service_name','$weeks','$title','$service_tax')") or die(mysql_error());

                $query = $this->db->query("insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,lberth_fare,uberth_fare) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lberth_fare','$uberth_fare')");
            }

            /*             * ********* inserting master buses ************* */
        }//service is there or not 
        else {
            echo 1;
        }
    }

    function getServicesListForActiveOrDeactive() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->distinct();
        $this->db->select('*');
        $this->db->from('master_buses m');
        $this->db->where('m.travel_id', $travel_id);
        $this->db->join('master_layouts l', 'l.service_num = m.service_num');
        $this->db->group_by('m.service_num');
        $query2 = $this->db->get();
        $slist = array();
        $slist['0'] = '- - - Select - - -';
        foreach ($query2->result() as $rows) {
            $slist[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $slist;
    }

    function getServicesListActiveOrDeactive() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $srvno = $this->input->post('service');
        $key = $this->input->post('key');
        $this->db->distinct();
        $this->db->select('*');
        $this->db->from('master_buses');
        $this->db->where('travel_id', $travel_id);
        $this->db->where("service_num", $srvno);
        $this->db->group_by('service_num');
        $query2 = $this->db->get();

        echo '<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
<thead>
  <tr>
    <td height="43" colspan="2"   ><strong>Service Type</strong></td>
     <td height="43" align="center" class="space" ><strong>Service Number</strong></td>
    <td height="43" align="center" class="space" ><strong>Service Name</strong></td>
    <td height="43" align="center" class="space" ><strong>Bus Type</strong></td>
    <td height="43" align="center" class="space"   ><strong>Status</strong></td>
    <td align="center" class="space" ><strong>Action</strong></td>
  </tr><thead><tbody>
    ';
        $i = 1;
        //print_r($query);
        foreach ($query2->result() as $row) {
            $srvtype2 = $row->serviceType;
            $srvno = $row->service_num;
            if ($srvtype2 == '' || $srvtype2 == 'normal') {
                $srvtype = "normal";
            } else if ($srvtype2 == 'special') {
                $srvtype = "special";
                $current_date = date('Y-m-d');
                $output = "";
                $sql = $this->db->query("SELECT DISTINCT journey_date FROM `buses_list` WHERE travel_id='$travel_id' and STATUS='1' and service_num='$srvno' and journey_date >= '$current_date'");
                if ($sql->num_rows() > 0) {
                    foreach ($sql->result() as $row1) {
                        $jdate = $row1->journey_date;
                        if ($output == "") {
                            $output = $jdate;
                        } else {
                            $output = $output . ", " . $jdate;
                        }
//$output = "Service Running on ".$output;
                    }
                } else {
                    $output = "Service Deactivated";
                }
            } else if ($srvtype2 == 'weekly') {
                $srvtype = "weekly";
            }
            $travid = $row->travel_id;
            if ($key == 'Delete') {
                $edit = '<input type="button" class="btn btn-primary" name="act' . $travid . $s . '" id="act' . $travid . $i . '" value="Delete" 
             onclick="deactivateBus(\'' . $srvno . '\',' . $travid . ',' . $i . ',' . $row->status . ',' . $row->from_id . ',' . $row->to_id . ',\'' . $key . '\')">';
                if ($row->status == 0 || $row->status == '') {
                    $st = 'DeActivated';
                } else {
                    $st = 'Activated';
                }
            } else {

                if ($row->status == 0 || $row->status == '') {
                    $edit = '<input type="button" class="btn btn-primary" name="act' . $travid . $s . '" id="act' . $travid . $i . '" value="Active" 
             onclick="activateBus(\'' . $srvno . '\',' . $travid . ',' . $i . ',' . $row->status . ',' . $row->from_id . ',' . $row->to_id . ')">';
                    $st = 'DeActivated';
                } else {
                    $st = 'Activated';
                    $edit = '<input type="button" class="btn btn-primary" name="act' . $travid . $s . '" id="act' . $travid . $i . '" value="DeActive" 
              onclick="deactivateBus(\'' . $srvno . '\',' . $travid . ',' . $i . ',' . $row->status . ',' . $row->from_id . ',' . $row->to_id . ',\'' . $key . '\')">';
                }
            }

            echo '<tr >
    <td height="38" colspan="2" align="center" class="space" >' . $srvtype . '</td>
    <input type="hidden"  value="' . $srvtype . '" id="sertype' . $i . '" name="sertype' . $i . '">
        <input type="hidden"  value="' . $row->model . '" id="model" name="model">
    <td height="38" align="center" class="space" >' . $srvno . '</td>
    <td height="38" align="center" class="space" >' . $row->service_name . '</td>
    <td height="38" align="center" class="space" >' . $row->model . '</td>
    <td height="38" align="center" class="space" >' . $st . ' </td>
    <td align="center" class="space" >' . $edit . ' </td>
       </tr>';
            if ($srvtype2 == 'special') {
                echo '<tr >
 <td  colspan="7"  align="center" height="30" class="space">Service Running on : ' . $output . '</td>
  </tr>';
            }
            echo '<tr  style="display:none;" >
 <td  colspan="7"  align="center" height="30" class="space"  ></td>
  </tr>
  <tr id="tr' . $i . '"  style="display:none;">
 <td  colspan="7" id="dp' . $i . '" align="center" height="30" class="space"  ></td>
  </tr>      
';
            $i++;
        }
        echo '<input type="hidden" id="hdd" value="' . $i . '" >
            <tr>
    <td  colspan="7" align="center" height="30" class="space"  >';
        echo'</td>
  </tr>';

        echo '</tr>
        <tbody>
</table>

';
    }

    function getForwordBookingDaysFromDb($travid) {
        $this->db->select('fwd');
        $this->db->where("travel_id", $travid);
        $query = $this->db->get("registered_operators");
        foreach ($query->result() as $row)
            $res = $row->fwd;
        return $res;
    }

    function activeBusStatusDb($travid, $sernum, $s, $fromid, $toid, $status, $fwd, $fdate, $tdate, $servtype) {
        /* getting the values from master_buses */
        $this->db->distinct();
        $array = array('service_num' => $sernum, 'travel_id' => $travid);
        $query = $this->db->get_where('master_buses', $array);

        /* getting the values from master_layouts */
        $this->db->select('*');
        $query2 = $this->db->get_where('master_layouts', $array);

        $fdate = date('Y-m-d', strtotime($fdate));
        $tdate = date('Y-m-d', strtotime($tdate));

        foreach ($query->result() as $rows) {
            $from_id = $rows->from_id;
            $to_id = $rows->to_id;
            $from_name = $rows->from_name;
            $to_name = $rows->to_name;
            $service_route = $rows->service_route;
            $service_name = $rows->service_name;
            $seat_fare = $rows->seat_fare;
            $lberth_fare = $rows->lberth_fare;
            $uberth_fare = $rows->uberth_fare;
            $travel_id = $travid;

            $fcheck = $this->db->query("select * from master_price where service_num='$sernum' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date is NULL") or die(mysql_error());

            if ($fcheck->num_rows() <= 0) {
                $this->db->query("insert into master_price(service_num, travel_id, from_id, from_name, to_id, to_name, service_route, service_name, seat_fare, lberth_fare, uberth_fare) values('$sernum','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lberth_fare','$uberth_fare')");
            }
        }

        while ($fdate <= $tdate) {
            //if service type is weekly 
            if ($servtype == 'weekly') {
                $sql = $this->db->query("select  distinct(service_days) from master_buses where service_num='$sernum' and  travel_id='$travid' ")or die(mysql_error());
                foreach ($sql->result() as $serdays) {
                    $ser_days1 = $serdays->service_days;
                }
                $ser_days = explode('#', $ser_days1);
                $weekday = date('l', strtotime($fdate));
                if ($weekday == $ser_days[0] || $weekday == $ser_days[1]) {
                    /* deleteing deatils if already exist for particular date */
                    $tables = array('buses_list', 'layout_list');
                    $array2 = array('service_num' => $sernum, 'travel_id' => $travid, 'journey_date' => $fdate);
                    $this->db->where($array2);
                    $this->db->delete($tables);
                    foreach ($query->result() as $rows) {
                        if ($rows->service_num == $sernum) {
                            /* inserting into buses_list */
                            $data = array('service_num' => $sernum, 'from_id' => $rows->from_id, 'to_id' => $rows->to_id, 'travel_id' => $travid, 'status' => 1, 'journey_date' => $fdate, 'seat_fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'available_seats' => $rows->seat_nos, 'lowerdeck_nos' => $rows->lowerdeck_nos, 'upperdeck_nos' => $rows->upperdeck_nos);
                            $insert_buses = $this->db->insert('buses_list', $data);
                        }//if
                    }//foreach
                    /* inserting into layout_list */
                    foreach ($query2->result() as $rows2) {
                        $data2 = array('travel_id' => $rows2->travel_id, 'layout_id' => $rows2->layout_id, 'seat_name' => $rows2->seat_name, 'row' => $rows2->row, 'col' => $rows2->col, 'seat_type' => $rows2->seat_type, 'window' => $rows2->window, 'is_ladies' => $rows2->is_ladies, 'service_num' => $rows2->service_num, 'available' => $rows2->available, 'available_type' => $rows2->available_type, 'fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'journey_date' => $fdate, 'status' => 1);
                        $insert_layouts = $this->db->insert('layout_list', $data2);
                    }
                }
            } else {
                /* deleteing deatils if already exist for particular date */
                $tables = array('buses_list', 'layout_list');
                $array2 = array('service_num' => $sernum, 'travel_id' => $travid, 'journey_date' => $fdate);
                $this->db->where($array2);
                $this->db->delete($tables);
                foreach ($query->result() as $rows) {
                    if ($rows->service_num == $sernum) {
                        /* inserting into buses_list */
                        $data = array('service_num' => $sernum, 'from_id' => $rows->from_id, 'to_id' => $rows->to_id, 'travel_id' => $travid, 'status' => 1, 'journey_date' => $fdate, 'seat_fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'available_seats' => $rows->seat_nos, 'lowerdeck_nos' => $rows->lowerdeck_nos, 'upperdeck_nos' => $rows->upperdeck_nos);
                        $insert_buses = $this->db->insert('buses_list', $data);
                    }//if
                }//foreach
                /* inserting into layout_list */
                foreach ($query2->result() as $rows2) {
                    $data2 = array('travel_id' => $rows2->travel_id, 'layout_id' => $rows2->layout_id, 'seat_name' => $rows2->seat_name, 'row' => $rows2->row, 'col' => $rows2->col, 'seat_type' => $rows2->seat_type, 'window' => $rows2->window, 'is_ladies' => $rows2->is_ladies, 'service_num' => $rows2->service_num, 'available' => $rows2->available, 'available_type' => $rows2->available_type, 'fare' => $rows->seat_fare, 'lberth_fare' => $rows->lberth_fare, 'uberth_fare' => $rows->uberth_fare, 'journey_date' => $fdate, 'status' => 1);
                    $insert_layouts = $this->db->insert('layout_list', $data2);
                }
            }


            $dat = strtotime("+1 day", strtotime($fdate));
            $fdate = date("Y-m-d", $dat);
        }//while

        if ($insert_buses && $insert_layouts) {
            /* updating bus status */
            $q1 = $this->db->query("update master_buses set status='1' where service_num='$sernum' and travel_id='$travid'");
            $q2 = $this->db->query("update master_layouts set status='1' where service_num='$sernum' and travel_id='$travid'");
            if ($q1 && $q2) {
                $op_title = $this->db->query("SELECT operator_title FROM registered_operators WHERE travel_id='$travid'");
                foreach($op_title->result() as $title){
                    $travels_name = $title->operator_title;
                }
                $subject = 'Update the service for '.$travels_name;
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
                $headers .= 'From: Ticketengine<noreplay@ticketengine.in>' . "\r\n";
                $headers .= 'Reply-To: thakurpratibha@gmail.com' . "\r\n";
                $sql = "SELECT serviceType,service_num,travel_id,from_id,from_name,to_id,to_name,service_name FROM master_buses WHERE service_num='$sernum' and travel_id='$travid'";
                $q3 = $this->db->query($sql);
                $message = '<p>Dear Team,</p>
<p>Kindly update below service in your list.</p>';
$message .= '<p>LIVE From : '.$fdate.'</p>';
                $message .= '<table width="50%" border="1" cellspacing="0" cellpadding="0">
	<tr>
		<td height="30" align="center" valign="middle" bgcolor="#999966">serviceType</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">service_num</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">travel_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">from_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">from_name</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">to_id</td>
		<td height="30" align="center" valign="middle" bgcolor="#999966">to_name</td>
	</tr>';
                foreach ($q3->result() as $val) {
                    $message .= '<tr>
		<td height="30" align="center" valign="middle">' . $val->serviceType . '</td>
		<td height="30" align="center" valign="middle">' . $val->service_num . '</td>
		<td height="30" align="center" valign="middle">' . $val->travel_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->from_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->from_name . '</td>
		<td height="30" align="center" valign="middle">' . $val->to_id . '</td>
		<td height="30" align="center" valign="middle">' . $val->to_name . '</td>
	</tr>';
                }
                $message .= '</table>';
                $message .='<p>Best regards,<br /> 
	Ticketengine
</p>';
                $sql1 = "SELECT * FROM api_support WHERE status='1'";
                $q4 = $this->db->query($sql1);
                foreach ($q4->result() as $val1) {
                    $to = $val1->email;
                    mail($to, $subject, $message, $headers);
                }
                return 1;
            }
            
        }
    }

    public function deActivateBusPermanentDb() {
        $travid = $this->input->post('travid');
        $s = $this->input->post('s');
        $sernum = $this->input->post('svc');
        $fromid = $this->input->post('fromid');
        $toid = $this->input->post('toid');
        $status = $this->input->post('status');
        $st = $this->input->post('st');
        $model = $this->input->post('model');
        //service deactive
        if ($st == 'DeActive') {
            // updating status for refund of bus cancel  
            $sql = $this->db->query("update buses_list set status='0' where service_num='$sernum' and travel_id='$travid'");
            $sql1 = $this->db->query("update layout_list set status='0' where service_num='$sernum' and travel_id='$travid'");
            $sql2 = $this->db->query("update master_buses set status='0' where service_num='$sernum' and travel_id='$travid'");
            //$sql=$this->db->update('master_buses as t3');
            //inserting cancelled service tickets in master table
            $date = date('Y-m-d');
            $curdate = date('Y-m-d');
            $now = date('Y-m-d H:i:s');
            $ip = $this->input->ip_address();

            $query5 = $this->db->query("select * from master_booking where service_no='$sernum' and travel_id='$travid' and jdate>='$date'  and LOWER(status)='confirmed'");

            foreach ($query5->result() as $val) {
                // print_r($query5->result());
                $tkt_no = $val->tkt_no;

                $query6 = $this->db->query("select count(tkt_no) as tktcnt from master_booking where service_no='$sernum' and tkt_no='$tkt_no' and travel_id='$travid' and jdate>='$date' and LOWER(status)='cancelled'");

                foreach ($query6->result() as $val6) {
                    $tktcnt = $val6->tktcnt;
                }
                if ($tktcnt == 0) {
                    if ($val->paid == '' || $val->paid == 0)
                        $paid = $val->tkt_fare;
                    else
                        $paid = $val->paid;

                    $agentid = $val->agent_id;
                    $data1 = array('tkt_no' => $val->tkt_no, 'pnr' => $val->pnr, 'service_no' => $val->service_no, 'board_point' => $val->board_point, 'bpid' => $val->bpid, 'land_mark' => $val->land_mark, 'source' => $val->source, 'dest' => $val->dest, 'travels' => $val->travels, 'bus_type' => $val->bus_type, 'bdate' => $val->bdate, 'jdate' => $val->jdate, 'seats' => $val->seats, 'gender' => $val->gender, 'start_time' => $val->start_time, 'arr_time' => $val->arr_time, 'paid' => $val->paid, 'save' => $val->save, 'tkt_fare' => $val->tkt_fare, 'base_fare' => $val->base_fare, 'service_tax_amount' => $val->service_tax_amount, 'discount_amount' => $val->discount_amount, 'convenience_charge' => $val->convenience_charge, 'promo_code' => $val->promo_code, 'pname' => $val->pname, 'pemail' => $val->pemail, 'pmobile' => $val->pmobile, 'age' => $val->age, 'refno' => $val->refno, 'status' => 'cancelled', 'pass' => $val->pass, 'cseat' => $val->cseat, 'ccharge' => '0', 'camt' => '0', 'refamt' => $paid, 'travel_id' => $val->travel_id, 'mail_stat' => $val->mail_stat, 'sms_stat' => $val->sms_stat, 'ip' => $ip, 'time' => $val->time, 'cdate' => $curdate, 'ctime' => $now, 'id_type' => $val->id_type, 'id_num' => $val->id_num, 'padd' => $val->padd, 'alter_ph' => $val->alter_ph, 'fid' => $val->fid, 'tid' => $val->tid, 'operator_agent_type' => $val->operator_agent_type, 'agent_id' => $val->agent_id, 'is_buscancel' => 'yes', 'book_pay_type' => $val->book_pay_type, 'book_pay_agent' => $val->book_pay_agent);
                    //print_r($data1);

                    $st1 = $this->db->insert('master_booking', $data1);
                    //checking for agent balance.
                    $query = $this->db->query("select * from agents_operator where id='$agentid' and operator_id='$travid' ") or die(mysql_error());

                    foreach ($query->result() as $res) {
                        $bal = $res->balance;
                    }
                    $ball = $bal + $paid;

                    $sql7 = $this->db->query("update agents_operator set balance='$ball' where id='$agentid' and operator_id='$travid' ") or die(mysql_error());
                    //sending SMS
                    $sql8 = $this->db->query("select distinct sender_id,operator_title,op_url from registered_operators where travel_id='$travid'");

                    foreach ($sql8->result() as $row8) {
                        $senderID = $row8->sender_id;
                    }

                    $user = "pridhvi@msn.com:activa1525@";
                    $receipientno = $val->pmobile;

                    $text = "BUS CANCELLED for tck No. " . $val->tkt_no . " booked in " . $val->travels . " with DOJ " . $val->jdate . "";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$text");
                    $buffer = curl_exec($ch);
                    $x = explode('=', $buffer);
                    $y = $x[1];
                    $z = explode(',', $y);
                    $stat = $z[0];
                    curl_close($ch);
                } else {
                    echo 0;
                }
            }
            if ($sql && $sql1 && $sql2) {
                echo 1;
            } else {
                echo 0;
            }
        }//if i.e service deactive
        else {
//	echo $sernum."".$travid;
            $sql1 = $this->db->query("delete from master_buses where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            $sql2 = $this->db->query("delete  from master_layouts where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            $sql3 = $this->db->query("delete  from layout_list where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            $sql4 = $this->db->query("delete  from eminities where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            $sql5 = $this->db->query("delete  from buses_list where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            $sql6 = $this->db->query("delete  from boarding_points where service_num='$sernum' and travel_id='$travid'") or die(mysql_error());
            //mysql_query("update operator_layouts set status='0' where status='1' and travel_id='$travid' and model='$model'") or die(mysql_error());

            if ($sql1 && $sql2 && $sql3 && $sql4 && $sql5 && $sql6) {
                echo 1;
            } else {
                echo 0;
            }
        }//service delete
    }

    function getServicesList() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $other_services = $this->session->userdata('bktravels_other_services');
        $today_date = date("Y-m-d");
        $sql = $this->db->query("SELECT DISTINCT t1.service_name,t2.service_num FROM master_buses t1, buses_list t2 WHERE t2.status='1' and t1.status='1' and t1.travel_id='$travel_id' AND t2.travel_id='$travel_id' and t1.service_num=t2.service_num and t2.journey_date>='$today_date'") or die(mysql_error());
        $slist = array();
        $slist['0'] = '- - - Select - - -';
        foreach ($sql->result() as $rows) {
            $slist[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $slist;
    }

    public function getHour() {
        $data = array();

        for ($i = 0; $i <= 12; $i++) {
            if ($i < 10)
                $i = "0" . $i;
            $data[$i] = $i;
        }
        return $data;
    }

    public function getHours1() {
        $data = array();
        $data['HH'] = "HH";
        for ($i = 0; $i <= 12; $i++) {
            if ($i < 10)
                $i = "0" . $i;
            $data[$i] = $i;
        }
        return $data;
    }

    public function getMinutes1() {
        $data = array();
        $data['MM'] = "MM";
        for ($i = 0; $i <= 60; $i++) {
            if ($i < 10)
                $i = "0" . $i;
            $data[$i] = $i;
        }
        return $data;
    }

    public function modifyRequirements() {
        $srvno = $this->input->post('svrno');
        $travid = $this->session->userdata('bktravels_travel_id');

        $sql1 = $this->db->query("select distinct(from_id),from_name from master_buses where travel_id='$travid' and service_num='$srvno' order by from_name")or die(mysql_error());
        echo '<table width="100%" border="1" >
		 		 <tr>
    				<td width="19%" height="36" ><strong>City Name </strong></td>
				    <td width="34%" >
				    <strong>Board Point Name </strong></td>
				    <td width="25%" ><strong>Time</strong></td>
				    <td width="22%" ><strong>Landmark</strong></td>
				</tr>';

        $i = 0;
        foreach ($sql1->result() as $row) {
            $from_id = $row->from_id;
            $from_name = $row->from_name;
            echo '<tr>
    				<td height="35"><strong>' . $from_name . '</strong></td>
    				';
            echo'<td colspan="3">
					<table width="100%" border="0">
					';
            $sql = $this->db->query("select * from board_drop_points where city_id='$from_id' order by board_drop")or die(mysql_error());
            $j = 0;
            foreach ($sql->result() as $row) {
                $hours = $this->getHours1();
                $timehrST = 'id="timehrST' . $i . $j . '" ';
                $timenST = 'name="timehrST' . $i . $j . '" style="width:50px"';

                $hours1 = $this->getMinutes1();

                $timemiST = 'id="timemST' . $i . $j . '"';
                $timemnST = 'name="timemST' . $i . $j . '" style="width:50px"';

                $tfidST = 'id="tfmST' . $i . $j . '" ';
                $tfnameST = 'name="tfm' . $i . $j . '" style="width:50px"';
                $tfv = array("AMPM" => "-select-", "AM" => "AM", "PM" => "PM");

                $board_drop = $row->board_drop;
                $id = $row->id;

                $sqlb = $this->db->query("select * from boarding_points where bpdp_id='$id' and travel_id='$travid' and service_num='$srvno'  and board_or_drop_type='board'") or die(mysql_error());

                if ($sqlb->num_rows() > 0) {
                    foreach ($sqlb->result() as $row1) {
                        $board = $row1->board_drop;
                        $y = explode("#", $board);
                        $lm = $y[2];
                        //$z=explode(":",$y[1]);								
                        $tt = date("h:i A", strtotime($y[1]));
                        $tf = explode(" ", $tt);
                        $tft = explode(":", $tf[0]);
                        $hr = $tft[0];
                        $hr1 = $tft[1];
                    }
                } else {
                    $tf = '';
                    $hr = '';
                    $hr1 = '';
                    $lm = '';
                }
                echo '<tr>
        						<td width="42%" height="35"><strong>' . $board_drop . '</strong>
								<input type="hidden" name="cityname' . $i . $j . '" id="cityname' . $i . $j . '" value="' . $from_name . '">
								<input type="hidden" name="cityid' . $i . $j . '" id="cityid' . $i . $j . '" value="' . $from_id . '">
								<input type="hidden" name="bpname' . $i . $j . '" id="bpname' . $i . $j . '" value="' . $board_drop . '">
								<input type="hidden" name="bpid' . $i . $j . '" id="bpid' . $i . $j . '" value="' . $id . '">
								</td>
						        <td width="31%">
								' . form_dropdown($timenST, $hours, $hr, $timehrST) . '' . form_dropdown($timemnST, $hours1, $hr1, $timemiST) . '' . form_dropdown($tfnameST, $tfv, $tf[1], $tfidST) . '</td>
						        <td width="27%"><input type="text" name="lm' . $i . $j . '" id="lm' . $i . $j . '" value="' . $lm . '" /></td>
							      </tr>';
                $j++;
            }

            echo'</table><input type="hidden" name="jval' . $i . '" id="jval' . $i . '" value="' . $j . '"></td>    				
  				 </tr>';
            $i++;
        }

        echo '<tr>
		 		   <input type="hidden" name="nval" id="nval" value="' . $i . '">
				   <input type="hidden" name="sernum" id="sernum" value="' . $srvno . '">
		 		   <td height="36" colspan="4" align="center" ><input type="button" class="btn btn-primary" value="Save Boarding Poings" onClick="saveBoard()"></td>
		 		   </tr></table>';
    }

    public function modify_Drop_point() {
        $srvno = $this->input->post('svrno');
        $travid = $this->session->userdata('bktravels_travel_id');
        $sql1 = $this->db->query("select distinct(to_id),to_name from master_buses where travel_id='$travid' and service_num='$srvno'")or die(mysql_error());

        echo '<table width="100%" border="1" >
		 		 <tr>
    <td width="19%" height="36" ><strong>City Name </strong></td>
    <td width="34%" >
    <strong>Drop Point Name </strong></td>
    <td  align="center"><strong>Time</strong></td>
	<td width="22%" ><strong>Landmark</strong></td>
  </tr>';
        $i = 0;
        foreach ($sql1->result() as $row) {
            $to_id = $row->to_id;
            $to_name = $row->to_name;
            echo '<tr>
    				<td height="35"><strong>' . $to_name . '</strong></td>
    				';
            echo'<td colspan="3">
					<table width="100%" border="0">
					';
            $sql = $this->db->query("select * from board_drop_points where city_id='$to_id'")or die(mysql_error());
            $j = 0;
            foreach ($sql->result() as $row) {
                $hours = $this->getHours1();
                $timehrST = 'id="timehrST' . $i . $j . '" ';
                $timenST = 'name="timehrST' . $i . $j . '" style="width:50px"';

                $hours1 = $this->getMinutes1();

                $timemiST = 'id="timemST' . $i . $j . '"';
                $timemnST = 'name="timemST' . $i . $j . '" style="width:50px"';

                $tfidST = 'id="tfmST' . $i . $j . '" ';
                $tfnameST = 'name="tfm' . $i . $j . '" style="width:50px"';

                $tfv = array("AMPM" => "-select-", "AM" => "AM", "PM" => "PM");
                $board_drop = $row->board_drop;
                $id = $row->id;
                $sqlb = $this->db->query("select * from boarding_points where bpdp_id='$id' and travel_id='$travid' and service_num='$srvno' and board_or_drop_type='drop'") or die(mysql_error());

                if ($sqlb->num_rows() > 0) {
                    foreach ($sqlb->result() as $row1) {
                        $board = $row1->board_drop;
                        $y = explode("#", $board);
                        $lm = $y[2];
                        //$z=explode(":",$y[1]);								
                        $tt = date("h:i A", strtotime($y[1]));
                        $tf = explode(" ", $tt);
                        $tft = explode(":", $tf[0]);
                        $hr = $tft[0];
                        $hr1 = $tft[1];
                    }
                } else {
                    $tf = '';
                    $hr = '';
                    $hr1 = '';
                    $lm = '';
                }

                echo '<tr>
        						<td width="42%" height="35"><strong>' . $board_drop . '</strong>
								<input type="hidden" name="cityname' . $i . $j . '" id="cityname' . $i . $j . '" value="' . $to_name . '">
								<input type="hidden" name="cityid' . $i . $j . '" id="cityid' . $i . $j . '" value="' . $to_id . '">
								<input type="hidden" name="dpname' . $i . $j . '" id="dpname' . $i . $j . '" value="' . $board_drop . '">
								<input type="hidden" name="dpid' . $i . $j . '" id="dpid' . $i . $j . '" value="' . $id . '">
								</td>
						        <td width="31%" colspan="2">
								' . form_dropdown($timenST, $hours, $hr, $timehrST) . '' . form_dropdown($timemnST, $hours1, $hr1, $timemiST) . '' . form_dropdown($tfnameST, $tfv, $tf[1], $tfidST) . '</td>
								<td width="27%"><input type="text" name="lm' . $i . $j . '" id="lm' . $i . $j . '" value="' . $lm . '" /></td>
						        </tr>';
                $j++;
            }

            echo'</table><input type="hidden" name="jval' . $i . '" id="jval' . $i . '" value="' . $j . '"></td>    				
  				 </tr>';
            $i++;
        }
        echo '<tr>
		 		   <input type="hidden" name="nval" id="nval" value="' . $i . '">
				   <input type="hidden" name="sernum" id="sernum" value="' . $srvno . '">
		 		   <td height="36" colspan="4" align="center" ><input type="button" class="btn btn-primary" value="Save Drop Poings" onClick="saveDrop()"></td>
		 		   </tr></table>
';
    }

    public function SaveDPtoDb() {

        $travel_id = $this->session->userdata('bktravels_travel_id');
        $sernum = $this->input->post('sernum');
        $city_name = $this->input->post('city_name');
        $city_id = $this->input->post('city_id');
        $board_point = $this->input->post('drop_point');
        $bpid = $this->input->post('dpid');
        $lm = $this->input->post('lm');
        $hhST = $this->input->post('hhST');
        $mmST = $this->input->post('mmST');
        $ampmST = $this->input->post('ampmST');

        $city_names = explode("#", $city_name);
        $city_ids = explode("#", $city_id);
        $board_points = explode("#", $board_point);
        $hhSTs = explode("#", $hhST);
        $mmSTs = explode("#", $mmST);
        $ampmSTs = explode("#", $ampmST);
        $bpids = explode("#", $bpid);
        $lms = explode("#", $lm);
        $n = count($city_names);

        $sql1 = $this->db->query("delete from boarding_points where service_num='$sernum' and travel_id='$travel_id' and board_or_drop_type='drop'") or die(mysql_error());


        for ($i = 0; $i < $n; $i++) {
            $arr_time1 = $hhSTs[$i] . ":" . $mmSTs[$i] . "" . $ampmSTs[$i];
            $d1 = date('h:i A', strtotime($arr_time1));
            $bpname = $board_points[$i] . "#" . $d1 . "#" . $lms[$i];

            $sql = $this->db->query("insert into boarding_points(service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,board_time,bpdp_id,timing) values('$sernum','$travel_id','$city_ids[$i]','$city_names[$i]','drop','$bpname','$d1','$bpids[$i]','$arr_time1')")or die(mysql_error());
        }
        if ($sql) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function modify_routes() {
        $srvno = $this->input->post('svrno');
        $travid = $this->session->userdata('bktravels_travel_id');
        $this->db->select('*');
        $this->db->where('travel_id', $travid);
        $this->db->where('service_num', $srvno);
        $query = $this->db->get('master_buses');
        $num = $query->num_rows();
        foreach ($query->result() as $row) {
            $bus_type = $row->bus_type;
        }


        echo '<table width="100%" border="0" align="center" id="routestb">
	<tr>
		<td class="space" height="30">&nbsp;</td>
		<td class="space" height="30">From</td>
		<td class="space" height="30">To</td>
		<td class="space" height="30">Start Time</td>
		<td class="space" height="30">Arrival Time</td>
		';
        if ($bus_type == 'seater')
            echo '
		<td class="space" height="30">Seat price</td>
		';
        if ($bus_type == 'sleeper') {
            echo '
		<td class="space" height="30">Lower birth price</td>
		<td class="space" height="30">Upper birth price</td>
		';
        }
        if ($bus_type == 'seatersleeper') {
            echo '
		<td class="space" height="30">Seat price</td>
		<td class="space" height="30">Lower birth price</td>
		<td class="space" height="30">Upper birth price</td>
		';
        }
        echo '</tr>
	';
        $i = 1;

        foreach ($query->result() as $row) {
            $serviceType = $row->serviceType;
            $service_route = $row->service_route;
            $service_name = $row->service_name;
            $from = $row->from_name;
            $to = $row->to_name;
            $fromid = $row->from_id;
            $toid = $row->to_id;
            $st_time = $row->start_time;
            $jtime = $row->journey_time;
            $arr_time = $row->arr_time;
            $seat_fare = $row->seat_fare;
            $lower_fare = $row->lberth_fare;
            $upper_fare = $row->uberth_fare;
            $bus_model = $row->model;
            $totseat = $row->seat_nos;
            $lowerseat = $row->lowerdeck_nos;
            $upperseat = $row->upperdeck_nos;
            $status = $row->status;
            $hours = $this->getHour();
            // $timehr='id="timehr'.$i.'" onChange="arrtime('.$i.')"';
            $timehr = 'id="timehr' . $i . '" ';
            $timen = 'name="timehr' . $i . '" style="width:50px"';
            $hours1 = $this->getMinutes();
            //$timemi='id="timem'.$i.'" onChange="arrtime('.$i.')"';
            $timemi = 'id="timem' . $i . '"';
            $timemn = 'name="timem' . $i . '" style="width:50px"';
            $hoursa = $this->getHour();
            // $timehrj='id="timehrj'.$i.'" onChange="arrtime('.$i.')"';
            // $timehrj='id="timehrj'.$i.'"';

            $arrth = 'id="arrth' . $i . '"';
            $arrh1 = 'name="arrth' . $i . '" style="width:50px"';
            $arrtm = 'id="arrtm' . $i . '"';
            $arrtm1 = 'name="arrtm' . $i . '" style="width:50px"';
            $hoursa1 = $this->getMinutes();
            // $timemij='id="timemj'.$i.'" onChange="arrtime('.$i.')"';
            $timemij = 'id="timemj' . $i . '"';
            $timemnj = 'name="timemj' . $i . '" style="width:50px"';


            $x = explode(":", $st_time);
            //  $h1=$x[0];
            //  $m1=$x[1];
            // $y=explode(":",$jtime);
            $y = explode(":", $arr_time);
            for ($j = 0; $j < count($y); $j++) {
                $y1 = substr($y[$j], 0, 2);
                $y2 = substr($y[$j], 2, 2);
            }


            // $hr1=$y[0];
            // $min1=$y[1];
            //start time
            $t1 = $x[0] . ":" . $x[1];
            $tt1 = date("h:i A", strtotime($t1));
            $tf1 = explode(" ", $tt1);
            $tft1 = explode(":", $tf1[0]);
            $h1 = $tft1[0];
            $m1 = $tft1[1];
            /// arr time
            $hr1 = $y[0];
            $min1 = $y1;
            // echo $tf1[1];
            // echo $hr1.":". $min1;
            //time format
            $tfid = 'id="tfms' . $i . '" ';
            $tfname = 'name="tfms' . $i . '" style="width:50px"';
            $tfid1 = 'id="tfma' . $i . '" ';
            $tfname1 = 'name="tfma' . $i . '" style="width:50px"';
            $tfv = array("0" => "-select-", "AM" => "AM", "PM" => "PM");
            ///
            echo'
	<tr id="tr' . $i . '">
		<td class="space" height="30"><input type="checkbox" name="ck' . $i . '" id="ck' . $i . '" value="' . $i . '"></td>
		<td class="space" height="30"><input type="text" size="15" name="from' . $i . '" id="from' . $i . '" value="' . $from . '" readonly>
			<input type="hidden" size="15" name="fromid' . $i . '" id="fromid' . $i . '" value="' . $fromid . '">
			<input type="hidden" size="15" name="bus" id="bus" value="' . $bus_type . '">
			<input type="hidden" size="15" name="sertype" id="sertype" value="' . $serviceType . '"></td>
		<td class="space" height="30"><input type="text" size="15" name="to' . $i . '" id="to' . $i . '" value="' . $to . '" readonly>
			<input type="hidden" size="15" name="toid' . $i . '" id="toid' . $i . '" value="' . $toid . '">
			<input type="hidden" size="15" name="seroute" id="seroute" value="' . $service_route . '">
			<input type="hidden" size="15" name="sername" id="sername" value="' . $service_name . '">
			<input type="hidden" size="15" name="busmodel" id="busmodel" value="' . $bus_model . '">
			<input type="hidden" size="15" name="tots" id="tots" value="' . $totseat . '"></td>
		<td class="space" height="30">' . form_dropdown($timen, $hours, $h1, $timehr) . '' . form_dropdown($timemn, $hours1, $m1, $timemi) . '' . form_dropdown($tfname, $tfv, $tf1[1], $tfid) . '
			<input type="hidden" size="15" name="ls" id="ls" value="' . $lowerseat . '">
			<input type="hidden" size="15" name="us" id="us" value="' . $upperseat . '">
			<input type="hidden" size="15" name="status" id="status" value="' . $status . '"></td>
		<td class="space" height="30">' . form_dropdown($arrh1, $hoursa, $hr1, $arrth) . '' . form_dropdown($arrtm1, $hoursa1, $min1, $arrtm) . '' . form_dropdown($tfname1, $tfv, $y2, $tfid1) . '</td>
		';
            if ($bus_type == 'seater') {
                echo '
		<td class="space" height="30"><input type="text" size="8" name="seat_fare' . $i . '" id="seat_fare' . $i . '" value="' . $seat_fare . '"></td>
		';
            } else if ($bus_type == 'sleeper') {
                echo '
		<td class="space" height="30"><input type="text" size="8" name="lowerseat_fare' . $i . '" id="lowerseat_fare' . $i . '" value="' . $lower_fare . '"></td>
		<td class="space" height="30"><input type="text" size="8" name="upperseat_fare' . $i . '" id="upperseat_fare' . $i . '" value="' . $upper_fare . '"></td>
		';
            } else if ($bus_type == 'seatersleeper') {
                echo '
		<td class="space" height="30"><input type="text" size="8" name="seat_fare' . $i . '" id="seat_fare' . $i . '" value="' . $seat_fare . '"></td>
		<td class="space" height="30"><input type="text" size="8" name="lowerseat_fare' . $i . '" id="lowerseat_fare' . $i . '" value="' . $lower_fare . '"></td>
		<td class="space" height="30"><input type="text" size="8" name="upperseat_fare' . $i . '" id="upperseat_fare' . $i . '" value="' . $upper_fare . '"></td>
		';
            }
            echo '
		<td class="space" height="30"><span style="cursor:pointer; font-weight:bold; color:#81BEF7; text-decoration:underline;" onClick="DeleteRoutes(' . $i . ')">Delete</span></td>
	</tr>
	';
            $i++;
        }
        $k = $i - 1;
        echo '
</table>
<table width="82%" align="center">
	<tr>
		<td align="right">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right">&nbsp;</td>
	</tr>
	<tr>
		<td align="right"><input type="button" class="btn btn-primary" id="save" value="Save" onClick="saveRoutes(\'' . $srvno . '\',' . $travid . ',' . $i . ')" style="padding:5px 20px"></td>
		<td>&nbsp;&nbsp;</td>
		<td align="right"><input type="hidden" value="' . $k . '" id="hidd" />
		<span style="cursor:pointer; font-weight:bold; text-decoration:underline;" onClick="addNewRoutes(\'' . $srvno . '\',' . $travid . ',' . $k . ',\'' . $bus_type . '\')">Add New Route</span></td>
	</tr>
	';



        echo '
	<tr>
		<td align="right">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right">&nbsp;</td>
	</tr>
</table>

 ';
    }

    function SaveModifytoDb() {
        $travid = $this->session->userdata('bktravels_travel_id');
        $sernum = $this->input->post('sernum');
        $city_name = $this->input->post('city_name');
        $city_id = $this->input->post('city_id');
        $board_point = $this->input->post('board_point');
        $bpid = $this->input->post('bpid');
        $lm = $this->input->post('lm');
        $hhST = $this->input->post('hhST');
        $mmST = $this->input->post('mmST');
        $ampmST = $this->input->post('ampmST');
        $city_names = explode("#", $city_name);
        $city_ids = explode("#", $city_id);
        $board_points = explode("#", $board_point);
        $hhSTs = explode("#", $hhST);
        $mmSTs = explode("#", $mmST);
        $ampmSTs = explode("#", $ampmST);
        $bpids = explode("#", $bpid);
        $lms = explode("#", $lm);
        $n = count($city_names);

        $sql1 = $this->db->query("delete from boarding_points where service_num='$sernum' and board_or_drop_type='board' and travel_id='$travid'") or die(mysql_error());
        //$d =date('H:i:s', strtotime($start_time1));
        for ($i = 0; $i < $n; $i++) {
            $arr_time1 = $hhSTs[$i] . ":" . $mmSTs[$i] . "" . $ampmSTs[$i];
            $d1 = date('H:i:s', strtotime($arr_time1));
            $bpname = $board_points[$i] . "#" . $d1 . "#" . $lms[$i];

            $sql = $this->db->query("insert into boarding_points(service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,board_time,bpdp_id,timing) values('$sernum','$travid','$city_ids[$i]','$city_names[$i]','board','$bpname','$d1','$bpids[$i]','$arr_time1')")or die(mysql_error());
            //echo "insert into boarding_points(service_num,travel_id,city_id,city_name,board_or_drop_type,board_drop,bpdp_id) values('$sernum','$travid','$city_ids[$i]','$city_names[$i]','board','$bpname','$bpids[$i]')";
        }
        if ($sql) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function save_routes_db() {
        $srvno = $this->input->post('service_no');
        $travid = $this->input->post('travel_id');
        $sertype = $this->input->post('sertype');
        $seroute = $this->input->post('seroute');
        $sername = $this->input->post('sername');
        $stime = $this->input->post('stime');
        $seat = $this->input->post('seat');
        $lseat = $this->input->post('lseat');
        $useat = $this->input->post('useat');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $ar = $this->input->post('art');
        $bus_type = $this->input->post('bus');
        $model = $this->input->post('model');
        $tseat = $this->input->post('tseat');
        $lseats = $this->input->post('lseats');
        $useats = $this->input->post('useats');
        $status = $this->input->post('status');
        $strt1 = explode("!", $stime);
        $arr = explode("!", $ar);
        $fr = explode("!", $from);
        $too = explode("!", $to);
        if ($bus_type == 'seater') {
            $sfare = explode("!", $seat);
        } else {
            $sfare = "";
        }

        if ($bus_type == 'sleeper' || $bus_type == 'seatersleeper') {
            if ($bus_type == 'seatersleeper') {
                $sfare = explode("!", $seat);
                $lfare = explode("!", $lseat);
                $ufare = explode("!", $useat);
            } else {
                $sfare = "";
                $lfare = explode("!", $lseat);
                $ufare = explode("!", $useat);
            }
        } else {
            $lfare = "";
            $ufare = "";
        }

        // print_r(ar1);

        for ($h = 0; $h < count($strt1); $h++) {

            $t1 = date("H:i:s", strtotime($strt1[$h]));
            $ta1 = date("H:i:", strtotime($arr[$h]));
            //journey time calculation
            $start = explode(':', $t1);
            $end = explode(':', $ta1);

            if ($start[0] > $end[0]) {
                $end[0] += 24;
            }
            $jh = abs($end[0] - $start[0]);
            $jm = abs($end[1] - $start[1]);
            $js = abs($end[2] - $start[2]);

            if ($jm == '0')
                $jm = "00";
            if ($js == '0')
                $js = "00";
            if ($start[0] == $end[0]) {
                $jt = "24" . ":" . $jm . ":" . $js;
            } else {
                $jt = $jh . ":" . $jm . ":" . $js;
            }
            //journey time calculation

            if ($h == 0) {
                $strt2 = $t1;
                $jout2 = $jt;
            } else {
                $strt2 = $strt2 . "!" . $t1;
                $jout2 = $jout2 . "!" . $jt;
            }
        }
        $strt = explode("!", $strt2);
        $jout = explode("!", $jout2);
        // print_r($strt);

        for ($s = 0; $s < count($strt); $s++) {
            $this->db->select('city_id');
            $this->db->where('city_name', $fr[$s]);
            $data1 = $this->db->get("master_cities");
            foreach ($data1->result() as $row) {
                $fromcity_id = $row->city_id;
            }

            $this->db->select('city_id');
            $this->db->where('city_name', $too[$s]);
            $data2 = $this->db->get("master_cities");
            foreach ($data2->result() as $row) {
                $tocity_id = $row->city_id;
            }
            $this->db->where("travel_id", $travid);
            $this->db->where("service_num", $srvno);
            $this->db->where("from_name", $fr[$s]);
            $this->db->where("to_name", $too[$s]);
            $query1 = $this->db->get("master_buses");

            $this->db->where("travel_id", $travid);
            $this->db->where("service_num", $srvno);
            $this->db->where("from_id", $fromcity_id);
            $this->db->where("to_id", $tocity_id);
            $this->db->where("journey_date is NULL", NULL);
            $ssql = $this->db->get("master_price");

            $this->db->query("delete from master_price where service_num='$srvno' and travel_id='$travid' and from_id='$fromcity_id' and to_id = '$tocity_id' and journey_date is not null");

            if ($ssql->num_rows() > 0) {
                $this->db->set('seat_fare', $sfare[$s]);
                $this->db->set('lberth_fare', $lfare[$s]);
                $this->db->set('uberth_fare', $ufare[$s]);
                $this->db->set('seat_fare_changed', "");
                $this->db->set('lberth_fare_changed', "");
                $this->db->set('uberth_fare_changed', "");
                $this->db->set('service_route', $fr[$s] . ' To ' . $too[$s]);
                $this->db->where("travel_id", $travid);
                $this->db->where("service_num", $srvno);
                $this->db->where("from_id", $fromcity_id);
                $this->db->where("to_id", $tocity_id);
                $this->db->where("journey_date is NULL", NULL);
                $ssql1 = $this->db->update('master_price');
            } else {
                $price = array(
                    'service_num' => $srvno,
                    'travel_id' => $travid,
                    'from_id' => $fromcity_id,
                    'from_name' => $fr[$s],
                    'to_id' => $tocity_id,
                    'to_name' => $too[$s],
                    'service_route' => $fr[$s] . ' To ' . $too[$s],
                    'service_name' => $sername,
                    'seat_fare' => $sfare[$s],
                    'lberth_fare' => $lfare[$s],
                    'uberth_fare' => $ufare[$s],
                );
                $ssql2 = $this->db->insert('master_price', $price);
            }

            if ($query1->num_rows() > 0) {

                $this->db->set('start_time', $strt[$s]);
                $this->db->set('journey_time', $jout[$s]);
                $this->db->set('arr_time', $arr[$s]);
                $this->db->set('seat_fare', $sfare[$s]);
                $this->db->set('lberth_fare', $lfare[$s]);
                $this->db->set('uberth_fare', $ufare[$s]);
                $this->db->set('service_route', $fr[$s] . ' To ' . $too[$s]);
                $this->db->where("travel_id", $travid);
                $this->db->where("service_num", $srvno);
                $this->db->where("from_name", $fr[$s]);
                $this->db->where("to_name", $too[$s]);
                $query = $this->db->update('master_buses');
            } else {
                if ($bus_type == 'seater') {
                    $data = array(
                        'serviceType' => $sertype,
                        'service_num' => $srvno,
                        'travel_id' => $travid,
                        'from_id' => $fromcity_id,
                        'from_name' => $fr[$s],
                        'to_name' => $too[$s],
                        'to_id' => $tocity_id,
                        'start_time' => $strt[$s],
                        'journey_time' => $jout[$s],
                        'arr_time' => $arr[$s],
                        'seat_fare' => $sfare[$s],
                        'lberth_fare' => $lfare[$s],
                        'uberth_fare' => $ufare[$s],
                        'model' => $model,
                        'bus_type' => $bus_type,
                        'seat_nos' => $tseat,
                        'status' => $status,
                        'service_route' => $fr[$s] . ' To ' . $too[$s],
                        'service_name' => $sername,
                    );
                } else if ($bus_type == 'sleeper') {
                    $data = array(
                        'serviceType' => $sertype,
                        'service_num' => $srvno,
                        'travel_id' => $travid,
                        'from_id' => $fromcity_id,
                        'from_name' => $fr[$s],
                        'to_id' => $tocity_id,
                        'to_name' => $too[$s],
                        'start_time' => $strt[$s],
                        'journey_time' => $jout[$s],
                        'arr_time' => $arr[$s],
                        'seat_fare' => $sfare[$s],
                        'lberth_fare' => $lfare[$s],
                        'uberth_fare' => $ufare[$s],
                        'model' => $model,
                        'bus_type' => $bus_type,
                        'lowerdeck_nos' => $lseats,
                        'upperdeck_nos' => $useats,
                        'status' => $status,
                        'service_route' => $fr[$s] . ' To ' . $too[$s],
                        'service_name' => $sername,
                    );
                } else if ($bus_type == 'seatersleeper') {
                    $data = array(
                        'serviceType' => $sertype,
                        'service_num' => $srvno,
                        'travel_id' => $travid,
                        'from_id' => $fromcity_id,
                        'from_name' => $fr[$s],
                        'to_id' => $tocity_id,
                        'to_name' => $too[$s],
                        'start_time' => $strt[$s],
                        'journey_time' => $jout[$s],
                        'arr_time' => $arr[$s],
                        'seat_fare' => $sfare[$s],
                        'lberth_fare' => $lfare[$s],
                        'uberth_fare' => $ufare[$s],
                        'model' => $model,
                        'bus_type' => $bus_type,
                        'seat_nos' => $tseat,
                        'lowerdeck_nos' => $lseats,
                        'upperdeck_nos' => $useats,
                        'status' => $status,
                        'service_route' => $fr[$s] . ' To ' . $too[$s],
                        'service_name' => $sername,
                    );
                }
                if ($status == 0) {
                    $query = $this->db->insert('master_buses', $data);
                } else {
                    $query = $this->db->insert('master_buses', $data);
                    $curdate = date("Y-m-d");
                    $this->db->select_min('journey_date');
                    $this->db->where('service_num', $srvno);
                    $this->db->where('travel_id', $travid);
                    $data3 = $this->db->get('buses_list');
                    foreach ($data3->result() as $row) {
                        $stdate2 = $row->journey_date;
                    }

                    $this->db->select_max('journey_date');
                    $this->db->where('service_num', $srvno);
                    $this->db->where('travel_id', $travid);
                    $data2 = $this->db->get('buses_list');
                    foreach ($data2->result() as $row) {
                        $todate = $row->journey_date;
                    }

                    if ($stdate2 > $curdate) {
                        $stdate = $stdate2;
                    } else {
                        $stdate = $curdate;
                    }
                    while ($stdate <= $todate) {
                        if ($bus_type == 'seater') {
                            $dataq = array(
                                'service_num' => $srvno,
                                'from_id' => $fromcity_id,
                                'to_id' => $tocity_id,
                                'travel_id' => $travid,
                                'status' => 1,
                                'journey_date' => $stdate,
                                'seat_fare' => $sfare[$s],
                            );
                        } else if ($bus_type == 'sleeper') {
                            $dataq = array(
                                'service_num' => $srvno,
                                'from_id' => $fromcity_id,
                                'to_id' => $tocity_id,
                                'travel_id' => $travid,
                                'status' => 1,
                                'journey_date' => $stdate,
                                'lberth_fare' => $lfare[$s],
                                'uberth_fare' => $ufare[$s],
                            );
                        } else if ($bus_type == 'seatersleeper') {
                            $dataq = array(
                                'service_num' => $srvno,
                                'from_id' => $fromcity_id,
                                'to_id' => $tocity_id,
                                'travel_id' => $travid,
                                'status' => 1,
                                'journey_date' => $stdate,
                                'seat_fare' => $sfare[$s],
                                'lberth_fare' => $lfare[$s],
                                'uberth_fare' => $ufare[$s],
                            );
                        }
                        $query = $this->db->insert('buses_list', $dataq);
                        $date = strtotime("+1 day", strtotime($stdate));
                        $stdate = date("Y-m-d", $date);
                    }
                }
            }
        }

        if ($query) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function delete_routes() {
        $srvno = $this->input->post('svrno');
        $travid = $this->session->userdata('bktravels_travel_id');
        $from = $this->input->post('fromid');
        $to = $this->input->post('toid');

        $del = array('master_buses', 'buses_list');
        $this->db->where('service_num', $srvno);
        $this->db->where('travel_id', $travid);
        $this->db->where('from_id', $from);
        $this->db->where('to_id', $to);
        $query = $this->db->delete($del);
        if ($query) {
            echo 1;
        } else {
            echo 0;
        }
    }

}
