<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Seats extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function changepricing_home() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');			
            $data['result'] = $this->Seats_m->getSericeNumbers();
            $this->load->view('seats/changepricing_view', $data);
			$this->load->view('leftSidebar.php');
            $this->load->view('footer_new');
        }
    }

    function getRoutes() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $svc = $this->input->post('svc');
            $fare_date = $this->input->post('fare_date');
            $travel_id = $this->session->userdata('bktravels_travel_id');
            $query = $this->Seats_m->getRoutesFromDb($svc);

            foreach ($query->result() as $row) {
                $srvroute = $row->service_route;
                $srno = explode("To", $srvroute);
                $model = $row->model;
            }

            echo '<style type="text/css">
		.tdborder
		{
			border:#CCCCCC solid 1px;
			padding-left:5px;
		}
		</style>
		<script type="text/javascript">
		$(function() 
		{                                              
			$( "#fdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0 ,"autoclose": true
            });
            $( "#tdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0, "autoclose": true
            });
		});

		</script>
';
            echo '<table width="545" border="0" align="center" >
		     <tr >
			 	<td width="95" height="38">From  Date:</td>
				<td width="137"><input type="text" readonly="" name="fdate" class="inputmedium" id="fdate" value="' . $fare_date . '"   /></td>
				
				<td width="29">&nbsp;</td>
				<td width="81">To    Date: </td>
				<td width="181"><input type="text" readonly="" name="tdate" class="inputmedium" id="tdate"   value="' . $fare_date . '"/></td>
			</tr>
		</table>';
            echo '<table width="822" border="0" align="center" style="border:#CCCCCC solid 1px;">
	          <tr>
			  	<td colspan="5" bgcolor="#2FA4E7" style="color:#FFFFFF"><strong>Halts And Fares</strong>*</td>
              </tr>
			  <tr style="background-color:#CCCCCC">
			  	<td width="146" align="center"><strong>Source</strong></td>
				<td width="131" align="center"><strong>Destination</strong></td>';
            if ($row->bus_type == "seater") {
                echo'<td width="167" align="center"><strong>Seat Fare </strong></td>';
            } else if ($row->bus_type == "sleeper") {
                echo'<td width="169" align="center"><strong>Lower Berth Fares</strong></td>
					<td width="185" align="center"><strong>Upper Berth Fares</strong></td>';
            } else {
                echo'<td width="167" align="center"><strong>Seat Fare </strong></td>';
                echo'<td width="169" align="center"><strong>Lower Berth Fares</strong></td>
					<td width="185" align="center"><strong>Upper Berth Fares</strong></td>';
            }
            echo'</tr></thead><tbody>';
            $i = 1;
            $current_date = date('Y-m-d');
            foreach ($query->result() as $row) {
                $from_id = $row->from_id;
                $to_id = $row->to_id;
                $srvno = $row->service_num;
                $stmt = "select * from master_price where service_num='$srvno' and travel_id='$travel_id' and journey_date='$fare_date' and from_id='$from_id' and to_id='$to_id'";
                $query = $this->db->query($stmt);

                if ($query->num_rows() == 0) {
                    $stmt = "select * from master_price where service_num='$srvno' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is NULL";
                    $query = $this->db->query($stmt);
                }
                foreach ($query->result() as $rows) {
                    $seat_fare = $rows->seat_fare;
                    $lberth_fare = $rows->lberth_fare;
                    $uberth_fare = $rows->uberth_fare;
                }
                $travid = $row->travel_id;
                $start_time = date("h:i A", strtotime($row->start_time));
                $arr_time = date("H:i A", strtotime($row->arr_time));


                if ($row->bus_type == "seater") {
                    if ($seat_fare == "") {
                        $sfare = $row->seat_fare;
                        $lfare = "";
                        $ufare = "";
                    } else {
                        $sfare = $seat_fare;
                    }
                } else if ($row->bus_type == "sleeper") {
                    $sfare = "";
                    if ($lberth_fare == "") {
                        $lfare = $row->lberth_fare;
                    } else {
                        $lfare = $lberth_fare;
                    }
                    if ($lberth_fare == "") {
                        $ufare = $row->uberth_fare;
                    } else {
                        $ufare = $uberth_fare;
                    }
                } else {
                    if ($seat_fare == "") {
                        $sfare = $row->seat_fare;
                    } else {
                        $sfare = $seat_fare;
                    }
                    if ($lberth_fare == "") {
                        $lfare = $row->lberth_fare;
                    } else {
                        $lfare = $lberth_fare;
                    }
                    if ($lberth_fare == "") {
                        $ufare = $row->uberth_fare;
                    } else {
                        $ufare = $uberth_fare;
                    }
                }

                echo '<tr>
								         <td height="30" align="center" width="146">' . $row->from_name . '</td>
										 <input type="hidden" id="fid' . $i . '" value="' . $row->from_id . '" >
										 <input type="hidden" id="tid' . $i . '" value="' . $row->to_id . '" >
										 <td align="center" width="131">' . $row->to_name . '</td>';
                if ($row->bus_type == "seater") {
                    echo '<td align="center" width="167"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>';
                } elseif ($row->bus_type == "sleeper") {
                    echo '<td align="center" width="169"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td> ';
                    echo '<td align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
                } else {
                    echo '<td align="center" width="167"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>';
                    echo '<td align="center" width="169"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td> ';
                    echo '<td align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
                }
                $i++;
            }//foreach
            $k = $i - 1;
            echo ' </tr>
			<tr>
			<td height="26" colspan="5" align="center"><input type="button" class="btn btn-primary" value="update" name="up" id="up" onClick="updateFare()"></td></tr>  ';
            echo '</table>';
            echo '<input type="hidden" id="hdd" value="' . $k . '" ><input type="hidden" id="btype" value="' . $row->bus_type . '" >';
        }
    }

    function updatePrice() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Seats_m->updateFareDb();
            return $res;
        }
    }

    public function Grab_release() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');		
            $data['services'] = $this->Seats_m->getServicesList();
            $this->load->view('seats/grab_release.php', $data);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    function GetServiceList() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $date = $this->input->post('txtdate');
            $serno = $this->input->post('serno');
            $this->Seats_m->ListOfService($date, $serno);
        }
    }

    function GrabReleaseLayout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('sernum');
            $travel_id = $this->input->post('travel_id');
            $s = $this->input->post('s');
            $date = $this->input->post('txtdate');
            $this->Seats_m->getLayoutOfGrabRelease($sernum, $travel_id, $s, $date);
        }
    }

    function GrabReleaseUpdatedLayout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('service_num');
            $travel_id = $this->input->post('travel_id');
            $date = $this->input->post('journey_date');
            $this->Seats_m->display_LayoutOfGrabRelease($sernum, $travel_id, $date);
        }
    }

    function SaveGrabRelease() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('service_num');
            $travel_id = $this->input->post('travel_id');
            $seats = $this->input->post('seat_names');
            $agent_type = $this->input->post('agent_type');
            $agent_id = $this->input->post('agent_id');
            $date = $this->input->post('date');
            $c = $this->input->post('c');
            if ($agent_type == 0) {
                $agent_id = 0;
            } else {
                $agent_id = $agent_id;
            }
            $this->Seats_m->updateGrabRelease($sernum, $seats, $travel_id, $agent_type, $agent_id, $date, $c);
        }
    }

    public function Ind_seat_fare() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['result'] = $this->Seats_m->getSericeNumbers1();
            $this->load->view('seats/changefare', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    function getroute() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service_num = $this->input->post('service_num');
            $result = $this->Seats_m->getroute1($service_num);
            return $result;
        }
    }

    function getRoutes1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $travel_id = $this->session->userdata('bktravels_travel_id');
            $service_num = $this->input->post('service_num');
            $service_name = $this->input->post('service_name');
            $current_date = date('Y-m-d');
            $city_id = $this->input->post('city_id');
            $service_route = $this->input->post('service_route');
            //echo $service_num."#".$service_route."#".$city_id;
            $query = $this->Seats_m->getRoutesFromDb1($travel_id, $service_num, $service_name, $service_route, $city_id);
            //print_r($query->result());		        	    

            foreach ($query->result() as $row) {
                $model = $row->model;
                $bus_type = $row->bus_type;
                $from_id = $row->from_id;
                $from_name = $row->from_name;
                $to_id = $row->to_id;
                $to_name = $row->to_name;
            }

            $query1 = $this->Seats_m->getfaresFromDb($travel_id, $service_num, $service_name, $current_date, $from_id, $to_id);
            foreach ($query1->result() as $row1) {
                $mseat_fare = $row1->seat_fare;
                $mlberth_fare = $row1->lberth_fare;
                $muberth_fare = $row1->uberth_fare;
            }
            $sql = $this->db->query("select * from master_price where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$current_date'") or die(mysql_error());

            if ($sql->num_rows() == 0) {
                $sql = $this->db->query("select * from master_price where service_num='$service_num'  and from_id='$from_id' and to_id='$to_id'  and travel_id='$travel_id' and journey_date IS NULL") or die(mysql_error());

                if ($sql->num_rows() == 0) {
                    $sql = $this->db->query("select * from buses_list where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$current_date'");
                }
            }
            foreach ($sql->result() as $row) {
                $seat_fare = $row->seat_fare;
                $lberth_fare = $row->lberth_fare;
                $uberth_fare = $row->uberth_fare;
                $seat_fare_changed = $row->seat_fare_changed;
                $lberth_fare_changed = $row->lberth_fare_changed;
                $uberth_fare_changed = $row->uberth_fare_changed;
            }
            echo '
	<style type="text/css">
	.tdborder
	{
            border:#CCCCCC solid 1px;
            padding-left:5px;
	}
        .input
        {
            width:40px;
            height:30px;
            text-align:center;
        }    
	</style>
	<script type="text/javascript">
	$(function() 
	{                                              
            $( "#fdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0,"autoclose": true});
	    $( "#tdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0,"autoclose": true});
	});
	
	</script>
	<script type="text/javascript"> 


$(document).ready(function()
{ 

	$("#price_mode").change(function()
	{ 
	   var price=$("#price_mode").val();
    	
		if(price=="permanently" || price == "")
		{
		$("#tbl").hide();
		}
		else
		{
		$("#tbl").show();
    
	    }
		
  	});
});
</script>
	<table width="490" align="center" cellpadding="0" cellspacing="0" style="border:#CCCCCC solid 1px;">
            <tr>
		<td height="27" colspan="4" bgcolor="#2FA4E7" style="color:#FFFFFF; padding-left:5px;"><strong>' . $service_route . '</strong></td>
            </tr>
            <tr>
                <td width="146" height="31"  class="tdborder"><strong>Source:</strong></td>
		<td class="tdborder">' . $from_name . '</td>
		<td width="88" class="tdborder"><strong>Destination:</strong></td>
		<td width="115" class="tdborder">' . $to_name . '</td>
            </tr>
            <tr>
                <td height="37" class="tdborder"><strong>Service mode: </strong></td>
		<td class="tdborder">Daily</td>
		<td class="tdborder"><strong>Bus Type: </strong></td>
		<td class="tdborder"> ' . $model . '</td>
            </tr>
	</table>
	<br/><br/>
        <table width="650" border="0" align="center">
  <tr>
    <td>Fare Save Mode</td>
    <td><select id="price_mode" name="price_mode" class="inputfield" >
      <option value=""> -- Select  Mode --</option>
      <option value="permanently"> Permanent </option>
      <option value="temporary"> Temporary </option>
    </select></td>
    <td>&nbsp;</td>
    <td>Fare Save Route</td>
    <td>';
            $result = $this->Seats_m->getroute2($service_num, $city_id);
            echo'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr id="tbl" style="display:none">
    <td>From  Date</td>
    <td><input type="text" name="fdate"  class="inputfield" id="fdate" value="' . Date("Y-m-d") . '"   /></td>
    <td>&nbsp;</td>
    <td>To    Date</td>
    <td><input type="text" name="tdate" class="inputfield" id="tdate"   value="' . Date("Y-m-d") . '"/></td>
  </tr>
</table>
        <table width="822" border="0" align="center" style="border:#CCCCCC solid 1px;">
            <tr>
                <td width="798" bgcolor="#2FA4E7" style="color:#FFFFFF"><strong>Individual Seat Fare</strong>*</td>
            </tr>
            <tr>
                <td height="26" align="center">';
            if ($bus_type == "seater") {
                //$sfare = $row->seat_fare;

                $query1 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$service_num' and travel_id='$travel_id'")or die(mysql_error());
                foreach ($query1->result() as $row1) {
                    $lower_rows = $row1->mrow;
                    $lower_cols = $row1->mcol;
                }

                echo '<table border="0" cellspacing="1" cellpadding="1" style="padding:5px 5px 5px 5px; font-size:16px; calibri;">';
                for ($i = 1; $i <= $lower_cols; $i++) {
                    echo '<tr>';
                    for ($j = 1; $j <= $lower_rows; $j++) {
                        $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='s'")or die(mysql_error());
                        foreach ($query2->result() as $row2) {
                            $seat_name = trim($row2->seat_name);
                        }
                        $seat_fare_changed2 = explode('@', $seat_fare_changed);
                        for ($a = 0; $a < count($seat_fare_changed2); $a++) {
                            $seat_fare_changed3 = explode('#', $seat_fare_changed2[$a]);
                            $fseat = $seat_fare_changed3[0];
                            $ffare = $seat_fare_changed3[1];

                            if ($fseat == $seat_name) {
                                $seat_fare1 = $ffare;
                                break;
                            } else {
                                $seat_fare1 = $seat_fare;
                            }
                        }
                        if ($seat_name == '' || $seat_name == 'GY') {
                            echo '<td width="50" height="50" align="center">&nbsp;</td>';
                           
                        } else {
							 echo '<td width="50" height="50" align="center">' . $seat_name . '<br />
							<input type="hidden" name="ltxt' . $j . '-' . $i . '" id="ltxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" />
							<input type="text" name="sfare' . $j . '-' . $i . '" id="sfare' . $j . '-' . $i . '" value="' . $seat_fare1 . '" class="input" />
							</td>';
                        }
                    }
                    unset($seat_name);
                    echo '</tr><tr><td>&nbsp;</td></tr>';
                }
                echo '</table>';
            }
            if ($bus_type == "sleeper") {
                //$lfare = $row->lberth_fare; 
                //$ufare = $row->uberth_fare;
                $query = $this->db->query("select count(distinct seat_name) as seats_count from master_layouts where service_num='$service_num' and travel_id='$travel_id'")or die(mysql_error());
                foreach ($query->result() as $row) {
                    $seats_count = $row->seats_count;
                }

                $query1 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$service_num' and travel_id='$travel_id' and seat_type='U'")or die(mysql_error());
                foreach ($query1->result() as $row1) {
                    $upper_rows = $row1->mrow;
                    $upper_cols = $row1->mcol;
                }

                $query3 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$service_num' and travel_id='$travel_id' and seat_type='L'")or die(mysql_error());
                foreach ($query3->result() as $row3) {
                    $lower_rows = $row3->mrow;
                    $lower_cols = $row3->mcol;
                }

                if ($seats_count >= 30 && $seats_count <= 40) {
                    echo '<table width="50%" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td height="30">&nbsp;</td>
    <td align="center">Double Berth </td>
    <td align="center">Single Berth </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;</td>
    <td align="center"><input type="text" name="double_berth" id="double_berth" value="" class="input" /></td>
    <td align="center"><input type="text" name="single_berth" id="single_berth" value="" class="input" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="30">&nbsp;</td>
    <td><input type="hidden" name="seats_count" id="seats_count" value="' . $seats_count . '" class="input" />
	<input type="hidden" name="max_rows" id="max_rows" value="' . $upper_rows . '" class="input" />
	<input type="hidden" name="max_cols" id="max_cols" value="' . $upper_cols . '" class="input" />
	</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
';
                    //Upper 
                    for ($i = 1; $i <= $upper_cols; $i++) {
                        for ($j = 1; $j <= $upper_rows; $j++) {
                            $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='U'")or die(mysql_error());
                            foreach ($query2->result() as $row2) {
                                $seat_name = trim($row2->seat_name);

                                echo '
									<input type="hidden" name="utxt' . $j . '-' . $i . '" id="utxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" />									                  
									<input type="hidden" name="ucol' . $j . '-' . $i . '" id="ucol' . $j . '-' . $i . '" value="' . $i . '" class="input" />';
                            }
                        }
                    }

                    //Lower                    
                    for ($i = 1; $i <= $lower_cols; $i++) {
                        for ($j = 1; $j <= $lower_rows; $j++) {
                            $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='L'")or die(mysql_error());
                            foreach ($query2->result() as $row2) {
                                $seat_name = trim($row2->seat_name);

                                echo '<input type="hidden" name="ltxt' . $j . '-' . $i . '" id="ltxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" />									                  
									<input type="hidden" name="lcol' . $j . '-' . $i . '" id="lcol' . $j . '-' . $i . '" value="' . $i . '" class="input" />';
                            }
                        }
                    }
                } else {
                    echo '<table border="0" cellspacing="1" cellpadding="1" style="padding:5px 5px 5px 5px; font-size:16px; calibri;">';
                    echo '<tr><td>Upper</td></tr><tr><td>&nbsp;</td></tr>';
                    for ($i = 1; $i <= $upper_cols; $i++) {
                        echo '<tr>';
                        for ($j = 1; $j <= $upper_rows; $j++) {
                            $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='U'")or die(mysql_error());
                            foreach ($query2->result() as $row2) {
                                $seat_name = trim($row2->seat_name);
                            }
                            $uberth_fare_changed2 = explode('@', $uberth_fare_changed);
                            for ($a = 0; $a < count($uberth_fare_changed2); $a++) {
                                $uberth_fare_changed3 = explode('#', $uberth_fare_changed2[$a]);
                                $fseat = trim($uberth_fare_changed3[0]);
                                $ffare = $uberth_fare_changed3[1];

                                if ($fseat == $seat_name) {
                                    $uberth_fare1 = $ffare;
                                    break;
                                } else {
                                    $uberth_fare1 = $uberth_fare;
                                }
                            }
                            if ($seat_name == '' || $seat_name == 'GY') {
                                
                                echo '<td width="50" height="50" align="center">&nbsp;</td>';
                            } else {
								echo '<td width="50" height="50" align="center">' . $seat_name . '<br /><input type="hidden" name="utxt' . $j . '-' . $i . '" id="utxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" /><input type="text" name="ufare' . $j . '-' . $i . '" id="ufare' . $j . '-' . $i . '" value="' . $uberth_fare1 . '" class="input" /></td>';
                            }
                        }
                        unset($seat_name);
                        echo '</tr><tr><td>&nbsp;</td></tr>';
                    }
                    echo '</table><br />';


                    echo '<table border="0" cellspacing="1" cellpadding="1" style="padding:5px 5px 5px 5px; font-size:16px; calibri;">';
                    echo '<tr><td>Lower</td></tr><tr><td>&nbsp;</td></tr>';
                    for ($i = 1; $i <= $lower_cols; $i++) {
                        echo '<tr>';
                        for ($j = 1; $j <= $lower_rows; $j++) {
                            $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='L'")or die(mysql_error());
                            foreach ($query2->result() as $row2) {
                                $seat_name = trim($row2->seat_name);
                            }
                            $lberth_fare_changed2 = explode('@', $lberth_fare_changed);
                            for ($a = 0; $a < count($lberth_fare_changed2); $a++) {
                                $lberth_fare_changed3 = explode('#', $lberth_fare_changed2[$a]);
                                $fseat = $lberth_fare_changed3[0];
                                $ffare = $lberth_fare_changed3[1];

                                if ($fseat == $seat_name) {
                                    $lberth_fare1 = $ffare;
                                    break;
                                } else {
                                    $lberth_fare1 = $lberth_fare;
                                }
                            }
                            if ($seat_name == '' || $seat_name == 'GY') {
								echo '<td width="50" height="50" align="center">&nbsp;</td>';
                                
                            } else {
                                echo '<td width="50" height="50" align="center">' . $seat_name . '<br /><input type="hidden" name="ltxt' . $j . '-' . $i . '" id="ltxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" /><input type="text" name="lfare' . $j . '-' . $i . '" id="lfare' . $j . '-' . $i . '" value="' . $lberth_fare1 . '" class="input" /></td>';
                            }
                        }
                        unset($seat_name);
                        echo '</tr><tr><td>&nbsp;</td></tr>';
                    }
                    echo '</table>';
                }
            }
            if ($bus_type == "seatersleeper") {
                //$sfare = $row->seat_fare;
                //$lfare = $row->lberth_fare; 
                //$ufare = $row->uberth_fare;

                $query1 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$service_num' and travel_id='$travel_id' and seat_type='U'")or die(mysql_error());
                foreach ($query1->result() as $row1) {
                    $upper_rows = $row1->mrow;
                    $upper_cols = $row1->mcol;
                }

                echo '<table border="0" cellspacing="1" cellpadding="1" style="padding:5px 5px 5px 5px; font-size:16px; calibri;">';
                echo '<tr><td>Upper</td></tr><tr><td>&nbsp;</td></tr>';
                for ($i = 1; $i <= $upper_cols; $i++) {
                    echo '<tr>';
                    for ($j = 1; $j <= $upper_rows; $j++) {
                        $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and seat_type='U'")or die(mysql_error());
                        foreach ($query2->result() as $row2) {
                            $seat_name = trim($row2->seat_name);
                        }
                        $uberth_fare_changed2 = explode('@', $uberth_fare_changed);
                        for ($a = 0; $a < count($uberth_fare_changed2); $a++) {
                            $uberth_fare_changed3 = explode('#', $uberth_fare_changed2[$a]);
                            $fseat = $uberth_fare_changed3[0];
                            $ffare = $uberth_fare_changed3[1];

                            if ($fseat == $seat_name) {
                                $uberth_fare1 = $ffare;
                                break;
                            } else {
                                $uberth_fare1 = $uberth_fare;
                            }
                        }
                        if ($seat_name == '' || $seat_name == 'GY') {
							echo '<td width="50" height="50" align="center">&nbsp;</td>';
                            
                        } else {
                           echo '<td width="50" height="50" align="center">' . $seat_name . '<br /><input type="hidden" name="utxt' . $j . '-' . $i . '" id="utxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" /><input type="text" name="ufare' . $j . '-' . $i . '" id="ufare' . $j . '-' . $i . '" value="' . $uberth_fare1 . '" class="input" /></td>'; 
                        }
                    }
                    unset($seat_name);
                    echo '</tr><tr><td>&nbsp;</td></tr>';
                }
                echo '</table><br />';

                $query3 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$service_num' and travel_id='$travel_id' and (seat_type='L:b' OR seat_type='L:s')")or die(mysql_error());
                foreach ($query3->result() as $row3) {
                    $lower_rows = $row3->mrow;
                    $lower_cols = $row3->mcol;
                }
                echo '<table border="0" cellspacing="1" cellpadding="1" style="padding:5px 5px 5px 5px; font-size:16px; calibri;">';
                echo '<tr><td>Lower</td></tr><tr><td>&nbsp;</td></tr>';
                for ($i = 1; $i <= $lower_cols; $i++) {
                    echo '<tr>';
                    for ($j = 1; $j <= $lower_rows; $j++) {
                        $query2 = $this->db->query("select * from master_layouts where service_num='$service_num' and travel_id='$travel_id' and row='$j' and col='$i' and (seat_type='L:b' OR seat_type='L:s')")or die(mysql_error());
                        foreach ($query2->result() as $row2) {
                            $seat_name = trim($row2->seat_name);
                            $seat_type = trim($row2->seat_type);
                        }
                        if ($seat_type == "L:s") {
                            $fare = $seat_fare;
                        } else if ($seat_type == "L:b") {
                            $fare = $lberth_fare;
                        }

                        $lberth_fare_changed2 = explode('@', $lberth_fare_changed);
                        for ($a = 0; $a < count($lberth_fare_changed2); $a++) {
                            $lberth_fare_changed3 = explode('#', $lberth_fare_changed2[$a]);
                            $fseat = $lberth_fare_changed3[0];
                            $ffare = $lberth_fare_changed3[1];

                            if ($fseat == $seat_name) {
                                $fare1 = $ffare;
                                break;
                            } else {
                                $fare1 = $fare;
                            }
                        }
                        if ($seat_name == '' || $seat_name == 'GY') {
                            echo '<td width="50" height="50" align="center">&nbsp;</td>';
                            
                        } else {
							echo '<td width="50" height="50" align="center">' . $seat_name . '<br /><input type="hidden" name="ltxt' . $j . '-' . $i . '" id="ltxt' . $j . '-' . $i . '" value="' . $seat_name . '" class="input" /><input type="text" name="lfare' . $j . '-' . $i . '" id="lfare' . $j . '-' . $i . '" value="' . $fare1 . '" class="input" /></td>';
                        }
                    }
                    unset($seat_name);
                    echo '</tr><tr><td>&nbsp;</td></tr>';
                }
                echo '</table>';
            }
            echo'</td>
            </tr>
            <tr>
                <td height="26" align="center">
                <input type="button" value="update" name="up" id="up" onClick="updateFare()" class="btn btn-primary">
                <input type="hidden" value="' . $bus_type . '" name="bus_type" id="bus_type">
                <input type="hidden" value="' . $service_num . '" name="service_num" id="service_num">
                <input type="hidden" value="' . $travel_id . '" name="travel_id" id="travel_id">
                <input type="hidden" value="' . $mseat_fare . '" name="sfare" id="sfare">
                <input type="hidden" value="' . $mlberth_fare . '" name="lfare" id="lfare">
                <input type="hidden" value="' . $muberth_fare . '" name="ufare" id="ufare">
                <input type="hidden" value="' . $lower_rows . '" name="lower_rows" id="lower_rows">
                <input type="hidden" value="' . $lower_cols . '" name="lower_cols" id="lower_cols">
                <input type="hidden" value="' . $upper_rows . '" name="upper_rows" id="upper_rows">
                <input type="hidden" value="' . $upper_cols . '" name="upper_cols" id="upper_cols">
				<input type="hidden" value="' . $from_id . '" name="from_id" id="from_id">
				<input type="hidden" value="' . $to_id . '" name="to_id" id="to_id">
				<input type="hidden" value="' . $from_name . '" name="from_name" id="from_name">
				<input type="hidden" value="' . $to_name . '" name="to_name" id="to_name">
                </td>
            </tr> 			
        </table>';
        }
    }

    public function addnewfare() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result = $this->Seats_m->addnewfare1();
            return $result;
        }
    }

    public function Quata() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Seats_m->getServicesList($key);
            $this->load->view('seats/quota_update_view.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function Cancel_service() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');			
            $data['services'] = $this->Seats_m->getServicesList();
            $this->load->view('seats/bus_breakdonw_view.php', $data);
			$this->load->view('leftSidebar.php');
            $this->load->view('footer_new');
        }
    }

    public function GetServiceReport() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Seats_m->getServicesListDetails();
        }
    }

    public function GetServiceReport_quata() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Seats_m->getServicesListDetails_quata();
        }
    }

    function DisplayLayoutForQuota() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('sernum');
            $travel_id = $this->input->post('travel_id');
            $this->Seats_m->display_LayoutOfQuota($sernum, $travel_id);
        }
    }

    function getLayoutForQuota() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('sernum');
            $travel_id = $this->input->post('travel_id');
            $s = $this->input->post('s');
            $this->Seats_m->getLayoutForQuotaDb($sernum, $travel_id, $s);
        }
    }

    function SelectAgentType() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $s = $this->input->post('s');
            $id = $this->input->post('id');
            //echo $id;
            $agent = $this->Seats_m->geAgentName($id);
            $agentid = 'id="ag' . $s . '" class="inputfield"';
            $agent_name = 'ag' . $s;
            if ($agent[0] == '--select--')
                echo form_dropdown($agent_name, $agent, "", $agentid) . 'No Agents are Created !';
            else
                echo form_dropdown($agent_name, $agent, "", $agentid);
        }
    }

    function UpdateAndValidate() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('service_num');
            $travel_id = $this->input->post('travel_id');
            $seats = $this->input->post('seat_names');
            $agent_type = $this->input->post('agent_type');
            $agent_id = $this->input->post('agent_id');
            $c = $this->input->post('c');
            $this->Seats_m->updateQuota($sernum, $travel_id, $seats, $agent_id, $agent_type, $c);
        }
    }

    public function ViewHistory() {
        //$srvno=$_GET['srvno'];
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "2";
            $this->load->view('sidebar',$result);
            $data['query'] = $this->Seats_m->detail_breakdown();
            $this->load->view('seats/view_breakdown_updation.php', $data);
        }
    }

    function deActivateBusDatePickers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $key = $this->input->post('key');
            $travid = $this->input->post('travid');
            $s = $this->input->post('s');
            $svc = $this->input->post('svc');
            $fromid = $this->input->post('fromid');
            $toid = $this->input->post('toid');
            $stat = $this->input->post('status');
            // echo $url;
            echo '<table width="460" border="0" style="border-right:#f2f2f2 solid 1px;  border-top:#f2f2f2 solid 1px; border-left:#f2f2f2 solid 1px; border-bottom:#f2f2f2 solid 1px; font-size:12px; calibri;color:#333333;" align="center">
     <tr>
     <td height="30" colspan="2" align="center"><h4><b>Select Dates to ' . $key . ' the Bus</b></h4></td>
     </tr>
         
     <tr>
     <td width="87" height="34"> From Date:       </td>
     <td width="361"><input name="txtdatee' . $s . '" type="text" id="txtdatee' . $s . '" value="" onChange="onChge(' . $s . ',\'' . $key . '\')"/></td>
    </tr>
     <tr>
    <td> To Date :&nbsp;&nbsp;</td>
    <td><input name="txtdateee' . $s . '"  type="text" id="txtdateee' . $s . '"  value=""  onChange="getFromTo(' . $s . ',\'' . $key . '\')"/></td>
   </tr>
     <tr id="radio' . $s . '" style="display:none">
       <td>&nbsp;</td>
       <td valign="middle"><input type="radio" name="ser' . $s . '" id="alternative' . $s . '" value="cancelled">
       	<strong>Service Cancelled </strong>&nbsp;</td>
     </tr>
   <tr>
    <td id="txtt' . $s . '"></td>
    <td id="txtt' . $s . '"></td>
   </tr>
   <tr>
    <td colspan="2"  align="center"><input type="button" class="btn btn-primary" name="updt' . $s . '" id="updt' . $s . '" value="Update" onClick="updateStatusAsDeAct(\'' . $key . '\',\'' . $svc . '\',' . $travid . ',' . $stat . ',' . $s . ',' . $fromid . ',' . $toid . ')">       </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><span id="spnmsg' . $s . '" style="font-size:12px; font-weight:bold;"></span> </td>
  </tr>
  </table>
';
        }
    }

    function deActivateBus() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $key = $this->input->post('key');
            $travid = $this->input->post('travid');
            $s = $this->input->post('s');
            $cnt = $this->input->post('cnt');
            $sernum = $this->input->post('sernum');
            $fromid = $this->input->post('fromid');
            $toid = $this->input->post('toid');
            $status = $this->input->post('status');
            $newfDate1 = $this->input->post('fdate');
            $newtDate1 = $this->input->post('tdate');
            $chkedRadio = $this->input->post('chkRadio');            
            $fdate = $newfDate1;
            $tdate = $newtDate1;
            $data = $this->Seats_m->deActivateBusDb($key, $sernum, $travid, $fdate, $tdate, $status, $cnt, $s, $fromid, $toid, $chkedRadio);
            return $data;  
        }
    }

}
