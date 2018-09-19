<?php

/**
 * Description of booking_m
 *
 * @author SVPRASADK
 */
class Booking_m extends CI_Model {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function from_cities() {
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $stmt = "select distinct from_id,from_name from master_buses where travel_id='$travel_id' and status='1' order by from_name";
        $query = $this->db->query($stmt);

        return $query;
    }

    function to_city() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $from_id = $this->input->post('from_id');

        $stmt = "select distinct to_id,to_name from master_buses where from_id='$from_id' and travel_id='$travel_id' and status='1' order by to_name";
        $query = $this->db->query($stmt);

        echo'<option value="0">-- Select --</option>';
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $to_city) {

                echo'<option value="' . $to_city->to_id . '">' . $to_city->to_name . '</option>';
            }
        }
    }

    function get_service_num($source_id, $destination_id, $onward_date, $travel_id) {
        $stmt = "select distinct service_num from buses_list where status='1' and from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id'";
        $query = $this->db->query($stmt);
        //echo $stmt;
        foreach ($query->result() as $row) {
            $service_num1[] = $row->service_num;
        }
        $service_num = implode('#', $service_num1);
        return $service_num;
    }

    function agent_api_seats($service_num1, $source_id, $destination_id, $onward_date, $travel_id) {
        $this->db->query("delete from agent_api_seat where travel_id='$travel_id'");
        $service_num = explode('#', $service_num1);
        //print_r($service_num);
        for ($n = 0; $n < count($service_num); $n++) {
            // set HTTP header
            $headers = array(
                'Content-Type: application/json',
            );

            // query string
            $fields = array(
                'srvno' => $service_num[$n],
            );

            $url = 'http://ticketengine.in:8080/ticketengine_live4/api/getSeatingAgent.json?api_key=cvcjEkX2fTXc6k!s&' . http_build_query($fields) . '&from_id=' . $source_id . '&to_id=' . $destination_id . '&date=' . $onward_date . '';
            //echo $url;
            $response = json_decode(file_get_contents($url), true);
            //print_r($response);
            foreach ($response as $key1 => $r) {
                $code = $r['code'];
                if ($code == '') {
                    foreach ($r as $r1) {
                        $service_num1 = $r1['service_num'];
                        $coach_layout = $r1['coach_layout'];

                        $seat_details = $coach_layout['seat_details'];
                        $c = count($seat_details);

                        for ($i = 0; $i < $c; $i++) {
                            $seati = $seat_details[$i];
                            $seat = $seati['seat'];
                            $number = $seat['number'];
                            $type = $seat['type'];
                            $fare = $seat['fare'];

                            $available = $seat['available'];
                            $available_for = $seat['available_for'];
                            $available_type = $seat['available_type'];
                            $is_ladies_seat = $seat['is_ladies_seat'];
                            $row_id = $seat['row_id'];
                            $col_id = $seat['col_id'];
                            $discount_amount = $seat['discount_amount'];
                            $service_tax_amount = $seat['service_tax_amount'];
                            $base_fare = $seat['base_fare'];
                            $convenience_charge = $seat['convenience_charge'];
                            $status = $seat['status'];

                            $rc = $row_id . "-" . $col_id;

                            if ($available == "") {
                                $available = 0;
                            }

                            if ($is_ladies_seat == "") {
                                $is_ladies_seat = 0;
                            }

                            if ($available == 1) {
                                $seat_status = 0;
                            }

                            if ($available == 0) {
                                $seat_status = 1;
                            }

                            $this->db->query("insert into agent_api_seat(travel_id, service_num, source_id, destination_id, journey_date, seat, seat_type, fare,base_fare,service_tax_amount, discount_amount, convenience_charge, available, available_type, ladies, row, col, rc,seat_status,status)values('$travel_id','$service_num1','$source_id','$destination_id','$onward_date','$number','$type','$fare','$base_fare','$service_tax_amount','$discount_amount','$convenience_charge','$available_for','$available_type','$is_ladies_seat','$row_id','$col_id','$rc','$seat_status','$status')") or die(mysql_error());
                        }
                    }
                }
            }
        }
    }

    function ServiceList1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_charge = $this->session->userdata('bktravels_agent_charge');
        $ho = $this->session->userdata('bktravels_head_office');
        $changeprice = $this->session->userdata('bktravels_changeprice');
        $boardingchart = $this->session->userdata('bktravels_boardingchart');
        $vehicle_assignment = $this->session->userdata('bktravels_vehicle_assignment');
        $grabrelease = $this->session->userdata('bktravels_grabrelease');
        $source_id = $this->input->post('source_id');
        $source = $this->input->post('source');
        $destination_id = $this->input->post('destination_id');
        $destination = $this->input->post('destination');
        $onward_date = $this->input->post('onward_date');
        $return_date = 0;
        $trip = $this->input->post('trip');

        $ct = $this->booking_m->onward_buses_count($source_id, $destination_id, $onward_date);
        $cdt = date('Y-m-d');
        $this->db->query("delete from agent_api_seat where journey_date < '$cdt'") or die(mysql_error());
        $i = 1;

        if ($ct == '0' || $ct == '') {
            echo 'No Services were found for the selected Date';
        } else if ($trip == 'O') {
            echo '<form name="frmseats" id="frmseats" action="' . base_url('booking/seatDetails') . '" method="POST">';
            $way = 'O';
            $stmt = "select distinct t1.travel_id,t1.service_num,t1.status,t1.from_id,t1.to_id,t2.from_name,t2.to_name,t2.model,t2.bus_type,t2.seat_nos,t2.lowerdeck_nos,t2.upperdeck_nos,t1.available_seats,t1.lowerdeck_nos,t1.upperdeck_nos,t2.start_time,t2.journey_time,t2.arr_time,t1.seat_fare,t1.lberth_fare,t1.uberth_fare,t2.service_name,t2.service_tax from buses_list t1,master_buses t2 where t1.service_num=t2.service_num and t1.status='1' and t2.status='1' and t1.from_id='$source_id' and t1.to_id='$destination_id' and t2.from_id='$source_id' and t2.to_id='$destination_id' and t1.journey_date='$onward_date' and t1.travel_id='$travel_id' and t2.travel_id='$travel_id'";
            $query = $this->db->query($stmt);

            foreach ($query->result() as $row) {
                $bus_type = $row->bus_type;
                $start_time = date('h:i A', strtotime($row->start_time));
                $arr_time = trim($row->arr_time);
                $model = $row->model;
                $service_num = $row->service_num;

                $stmt1 = "select count(distinct seat_name) as seats_count from layout_list where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' and seat_status <> '1'";
                $query1 = $this->db->query($stmt1);

                foreach ($query1->result() as $row1) {
                    $seats = $row1->seats_count;
                }

                $stmt2 = "select distinct seat_fare,lberth_fare,uberth_fare,seat_fare_changed,lberth_fare_changed,uberth_fare_changed from master_price where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and from_id='$source_id' and to_id='$destination_id'";
                $query2 = $this->db->query($stmt2);
                if ($query2->num_rows() == 0) {
                    $stmt2 = "select distinct seat_fare,lberth_fare,uberth_fare,seat_fare_changed,lberth_fare_changed,uberth_fare_changed from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$source_id' and to_id='$destination_id' and journey_date is NULL";
                    $query2 = $this->db->query($stmt2);
                }

                foreach ($query2->result() as $row2) {
                    $seat_fare = $row2->seat_fare;
                    $lberth_fare = $row2->lberth_fare;
                    $uberth_fare = $row2->uberth_fare;
                    $seat_fare_changed = $row2->seat_fare_changed;
                    $lberth_fare_changed = $row2->lberth_fare_changed;
                    $uberth_fare_changed = $row2->uberth_fare_changed;
                }

                if ($seat_fare_changed != "") {
                    $changedSeatFare1 = explode('@', $seat_fare_changed);
                    for ($a = 0; $a < count($changedSeatFare1); $a++) {
                        $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);

                        $changed_fare[] = $changedSeatFare2[1];
                    }
                    $fare1 = array_unique($changed_fare);
                    $fare2 = $seat_fare . "/" . implode('/', $fare1);
                } else {
                    $fare2 = $seat_fare;
                }
                if ($lberth_fare_changed != "") {
                    $changedSeatFare1 = explode('@', $lberth_fare_changed);
                    for ($a = 0; $a < count($changedSeatFare1); $a++) {
                        $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);

                        $changed_fare[] = $changedSeatFare2[1];
                    }
                    $fare3 = array_unique($changed_fare);
                    $fare4 = $lberth_fare . "/" . implode('/', $fare3);
                } else {
                    $fare4 = $lberth_fare;
                }
                if ($uberth_fare_changed != "") {
                    $changedSeatFare1 = explode('@', $uberth_fare_changed);
                    for ($a = 0; $a < count($changedSeatFare1); $a++) {
                        $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);

                        $changed_fare[] = $changedSeatFare2[1];
                    }
                    $fare5 = array_unique($changed_fare);
                    $fare6 = $uberth_fare . "/" . implode('/', $fare5);
                } else {
                    $fare6 = $uberth_fare;
                }

                if ($bus_type == "seater") {
                    $temp = $fare2;
                    $temp1 = explode('/', $temp);

                    $fare1 = array_unique($temp1);
                    $fare = implode('/', $fare1);
                } else if ($bus_type == "sleeper") {
                    $temp = $fare4 . "/" . $fare6;
                    $temp1 = explode('/', $temp);

                    $fare1 = array_unique($temp1);
                    $fare = implode('/', $fare1);
                } else if ($bus_type == "seatersleeper") {
                    $temp = $fare2 . "/" . $fare4 . "/" . $fare6;
                    $temp1 = explode('/', $temp);

                    $fare1 = array_unique($temp1);
                    $fare = implode('/', $fare1);
                }

                echo '<style type="text/css">
				/* tooltip legend */
a.legendtooltip {outline:none; }
a.legendtooltip strong {line-height:20px;}
a.legendtooltip:hover {text-decoration:none;} 
a.legendtooltip span {
    z-index:10;display:none; padding:10px;
    margin-top:43px; margin-left:-55px;
    line-height:16px;
}
a.legendtooltip:hover span{
    display:inline; position:absolute; 
    border:1px solid #000;  color:#000; border-radius:0px;
    background:#fff;
}
.callout {z-index:20;position:absolute;border:0;top:-15px;left:25px;}
    
/*CSS3 extras*/
a.legendtooltip span
{
    border-radius:2px;        
   /* box-shadow: 0px 0px 8px 4px #666;*/
    /*opacity: 0.8;*/
}
				</style><input type="hidden" name="fare" id="fare" value="' . $fare . '" />';
                echo '<div class="span11">
  <section class="panel">
    <div class="pull-out">
      <table class="table table-striped m-b-none text-small">
        <thead>
          <tr id="loadlayout' . $way . $i . '" onClick="javascript:layout(\'' . $service_num . '\',\'' . $source_id . '\',\'' . $destination_id . '\',\'' . $onward_date . '\',\'' . $return_date . '\',\'' . $trip . '\',\'' . $fare . '\',\'' . $i . '\',\'' . $way . '\',\'' . $ct . '\',0)">
            <th width="14%">' . $service_num . '</th>
            <th width="22%">' . $source . ' - ' . $destination . '</th>
            <th width="25%">' . $model . '</th>
            <th width="8%">' . $start_time . '</th>
            <th width="8%">' . $arr_time . '</th>
            <th width="6%">' . $seats . '</th>
            <th width="11%">' . $fare . '</th>
            <th width="6%"><img src="' . base_url('images/sidearrow.png') . '" /></th>
          </tr>
        </thead>
        <tr id="layoutanddata' . $way . $i . '" style="display:none">
          <td colspan="8"><table class="table table-striped m-b-none text-small">
              <tr>
                <td width="75%"><section class="toolbar clearfix m-t-large m-b" id="icons' . $way . $i . '" style="display:none"> <a class="legendtooltip"><i class="icon-th-large"></i>
<span>
        <img class="callout" src="' . base_url("images/callout_black.gif") . '" />            
<table border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td colspan="7" align="center">LEGEND</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Available</td>
    <td><div class="bg-available-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Selected</td>
    <td><div class="bg-selected-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Unavailable</td>
    <td><div class="bg-unavailable-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Ladies </td>
    <td><div class="bg-ladies-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Branch Quota </td>
    <td><div class="bg-branchquota-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Agent Quota </td>
    <td><div class="bg-agentquota-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Byphone Pending</td>
    <td><div class="bg-pending-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Blocked</td>
    <td><div class="bg-blocked-seat"></div></td>
    <td>&nbsp;</td>    
  </tr>
</table>
</span></a>';
                if ($changeprice == 'yes') {
                    echo'<a href="javascript:;" onClick="layout_change_price(\'' . $service_num . '\',\'' . $source_id . '\',\'' . $destination_id . '\',\'' . $onward_date . '\')" class="btn btn-mini btn-circle btn-success"><i class="fa fa-inr"></i>Price</a>';
                }if ($boardingchart == 'yes') {
                    echo '<a href="javascript:;" class="btn btn-mini btn-facebook btn-circle" onClick="Report(\'' . $service_num . '\',\'' . $onward_date . '\')"><i class="icon-file"></i>Chart</a> ';
                }if ($vehicle_assignment == 'yes') {
                    echo '<a href="javascript:;" onClick="layout_assign()" class="btn btn-mini btn-warning btn-circle"><i class="icon-group"></i>Assign</a> ';
                }if ($grabrelease == 'yes') {
                    echo '<a href="javascript:;" onClick="layout_grab_release(\'' . $service_num . '\',\'' . $onward_date . '\')" class="btn btn-mini btn-danger btn-circle"><i class="fa fa-table"></i>Block</a>';
                }
                echo'</section>
                  <span id="lay' . $way . $i . '" style="display:none;">
                  <!--img src="' . base_url('images/loading.png') . '" /-->
                  </span>
                  <div id="layout' . $way . $i . '" style="margin-top:10px;" align="center"></div></td>
                <td width="25%"><section id="content">
                    <main class="main" id="details' . $way . $i . '" style="display:none">
                      <div class="row-fluid">
                        <div class="span12">
                          <section class="panel">
                            <div class="form-horizontal">
                              <div class="row-fluid">
                                <div class="span7">
                                  <div class="control-group">
                                    <label class="control-label">Base Fare</label>
                                    <div class="controls"> <span id="base_fare1' . $way . $i . '">0</span>
									<input type="hidden" name="base_fare' . $way . $i . '" id="base_fare' . $way . $i . '"/>
									</div>
                                  </div>
                                  <div class="control-group">
                                    <label class="control-label">Service Tax</label>
                                    <div class="controls"> <span id="service_tax1' . $way . $i . '">0</span>
									<input type="hidden" name="service_tax' . $way . $i . '" id="service_tax' . $way . $i . '"/>
									</div>
                                  </div>
                                  ';
                if ($agent_charge == "yes") {
                    echo'
                                  <div class="control-group">
                                    <label class="control-label">Agent Charge</label>
                                    <div class="controls">
                                      <input name="agentcharge' . $way . $i . '" id="agentcharge' . $way . $i . '" type="text" size="5" onKeyUp="validateagentcharge(this.value,\'' . $way . '\',\'' . $i . '\')" />
                                    </div>
                                    ';
                } else {
                    echo'<input name="agentcharge' . $way . $i . '" id="agentcharge' . $way . $i . '" type="hidden" size="5" value="0" />';
                }
				$by_cash = $this->session->userdata('bktravels_by_cash');
                $by_phone = $this->session->userdata('bktravels_by_phone');
                $by_agent = $this->session->userdata('bktravels_by_agent');
                $by_phone_agent = $this->session->userdata('bktravels_by_phone_agent');
                $by_employee = $this->session->userdata('bktravels_by_employee');
                echo'</div>
                                  <div class="control-group">
                                    <label class="control-label">Total Fare</label>
                                    <div class="controls"> <span id="totalfare1' . $way . $i . '">0</span>
									<input type="hidden" name="totalfare' . $way . $i . '" id="totalfare' . $way . $i . '"/>
									</div>
                                  </div>
                                  <div class="control-group">
                                    <label class="control-label">Payment Type</label>
                                    <div class="controls">
                                      <select name="paytyp' . $way . $i . '" id="paytyp' . $way . $i . '" onChange="javascript:pay_type(\'' . $way . '\',\'' . $i . '\')">';
                
                echo '<option value="bycash">By Cash</option>';
                if ($by_phone == "yes") {
                    echo '
                                        <option value="byphone">By Phone</option>
                                        ';
                }
                if ($by_agent == "yes") {
                    echo '
                                        <option value="byagent">By Agent</option>
                                        ';
                }
                if ($by_phone_agent == "yes") {
                    echo '
                                        <option value="byphoneagent">By Phone Agent</option>
                                        ';
                }
                if ($by_employee == "yes") {
                    echo '
                                        <option value="byemployee">By Employee</option>
                                        ';
                }
				if ($ho == "yes") {
                    echo '
                                        <option value="smsinvoice">SMS Invoice</option>
                                        ';
                }
                echo'            
                        
                                      </select>
                                    </div>
                                  </div>
                                  <div class="control-group" id="pay' . $way . $i . '"> </div>
                                  <div class="control-group">
                                    <label class="control-label">Mobile</label>
                                    <div class="controls">
                                      <input type="text" name="mobile' . $way . $i . '" id="mobile' . $way . $i . '" maxlength="10" />
                                    </div>
                                  </div>
                                  <div class="control-group">
                                    <label class="control-label">Alternate No.</label>
                                    <div class="controls">
                                      <input type="text" name="altph' . $way . $i . '" id="altph' . $way . $i . '"/>
                                    </div>
                                  </div>
                                  <div class="control-group">
                                    <label class="control-label">Email</label>
                                    <div class="controls">
                                      <input type="text" name="email' . $way . $i . '" id="email' . $way . $i . '" />
                                    </div>
                                  </div>
                                  <div class="control-group">
                                    <div class="controls">
                                      <input class="btn btn-primary" type="button" name="BtnBook' . $way . $i . '" id="BtnBook' . $way . $i . '" value="Book Ticket" onClick="javascript:validate(\'' . $trip . '\',\'' . $i . '\');">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </section>
                        </div>
                      </div>
                    </main>
                  </section></td>
              </tr>
            </table></td>
        </tr>
      </table>
    </div>
  </section>
</div>
';
                $i++;
            }
        }
    }

    function onward_buses_count($source_id, $destination_id, $onward_date) {
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $stmt = "select count(DISTINCT service_num) as onward_buses_count from buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id' and status='1'";
        $query = $this->db->query($stmt) or die(mysql_error());

        foreach ($query->result() as $row) {
            $onward_buses_count = $row->onward_buses_count;
        }

        return $onward_buses_count;
    }

    function release_block() {
        $service_num = $this->input->post('service_num');
        $source_id = $this->input->post('source_id');
        $destination_id = $this->input->post('destination_id');
        $onward_date = $this->input->post('onward_date');
        $return_date = $this->input->post('return_date');
        $way = $this->input->post('way');

        if ($way == "O") {
            $stmt = "select distinct seat_status,seat_name,blocked_time,row,col from layout_list where service_num='$service_num' and journey_date='$onward_date'";
            $query = $this->db->query($stmt)or die(mysql_error());
            foreach ($query->result() as $row) {
                $seat_status = $row->seat_status;
                $blocked_time = $row->blocked_time;
                $seat_name = $row->seat_name;
            }
            if ($seat_status == 2) {
                date_default_timezone_set('Asia/Kolkata');
                $cd = strtotime(date('Y-m-d H:i:s'));
                $jd = strtotime($blocked_time);
                $min = ceil(abs($cd - $jd) / 60);
                if ($min > 10) {
                    $this->db->query("update layout_list set  seat_status='0' where service_num='$service_num' and journey_date='$onward_date' and  seat_status='2' and seat_name='$seat_name'")or die(mysql_error());
                }
            }
        }
    }

    function ServiceLayout1() {
        date_default_timezone_set('Asia/Kolkata');

        $agent_type = $this->session->userdata('bktravels_agent_type');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        //echo $travel_id." travel_id";
        $agent_id = $this->session->userdata('bktravels_user_id');
        $op_comm = $this->session->userdata('bktravels_op_comm');
        $price_edit = $this->session->userdata('bktravels_price_edit');
        $agent_charge = $this->session->userdata('bktravels_agent_charge');

        $service_num = $this->input->post('service_num');
        $source_id = $this->input->post('source_id');
        $destination_id = $this->input->post('destination_id');
        $onward_date = $this->input->post('onward_date');
        $return_date = $this->input->post('return_date');
        $trip = $this->input->post('trip');
        $fare = $this->input->post('fare');
        $k = $this->input->post('j');
        $way = $this->input->post('way');
        $cnt = $this->input->post('cnt');
        $key = $this->input->post('key');

        $cdt = date('Y-m-d');

        $this->db->query("delete from agent_api_seat where journey_date < '$cdt'") or die(mysql_error());

        $stmt = "select * from agents_operator where id='$agent_id' and agent_type='$agent_type' and operator_id='$travel_id'";
        $query = $this->db->query($stmt) or die(mysql_error());

        foreach ($query->result() as $row) {
            $is_hover = $row->is_hover;
            $is_pay = $row->is_pay;
            $ho = $row->head_office;
            $otherservices = $row->other_services;
            $bal = $row->balance;
            $limit = $row->bal_limit;
            $margin = $row->margin;
            $pay_type = $row->pay_type;
        }

        $stmt1 = "select distinct show_avail_seat,show_quota,available_type from layout_list where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' and available='2' and available_type='$agent_id'";
        //echo $stmt;
        $query1 = $this->db->query($stmt1);
        if ($query1->num_rows() > 0) {
            if ($ho == "yes") {
                $show_avail_seat = "yes";
                $show_quota = "no";
            } else {
                foreach ($query1->result() as $row1) {
                    $show_avail_seat = $row1->show_avail_seat;
                    $show_quota = $row1->show_quota;
                }
            }
        } else {
            if ($ho == "yes") {
                $show_avail_seat = "yes";
                $show_quota = "no";
            } else {
                $show_avail_seat = "no";
                $show_quota = "no";
            }
        }

        $stmt2 = "select distinct t1.travel_id,t1.service_num,t1.status,t1.from_id,t1.to_id,t2.from_name,t2.to_name,t2.model,t2.bus_type,t2.seat_nos,t2.lowerdeck_nos,t2.upperdeck_nos,t1.available_seats,t1.lowerdeck_nos,t1.upperdeck_nos,t2.start_time,t2.journey_time,t2.arr_time,t1.seat_fare,t1.lberth_fare,t1.uberth_fare,t2.service_name,t2.service_tax from buses_list t1,master_buses t2 where t1.service_num='$service_num' and t2.service_num='$service_num' and t1.status='1' and t2.status='1' and t1.from_id='$source_id' and t1.to_id='$destination_id' and t2.from_id='$source_id' and t2.to_id='$destination_id' and t1.journey_date='$onward_date' and t1.travel_id='$travel_id' and t2.travel_id='$travel_id'";
        $query2 = $this->db->query($stmt2) or die(mysql_error());

        foreach ($query2->result() as $row2) {
            $status = $row2->status;
            $bus_type = $row2->bus_type;
            $start_time = date('h:i A', strtotime($row2->start_time));
            $arr_time = trim($row2->arr_time);
            $model = $row2->model;
            $service_num = $row2->service_num;
            $service_name = $row2->service_name;
            $source_name = $row2->from_name;
            $destination_name = $row2->to_name;
            $service_tax = $row2->service_tax;
            $start_time1 = date('H,i,s', strtotime($row2->start_time));
        }

        //$current_time1 = date('Y,m,d,H,i,s', strtotime("-1 month"));
        $current_tim = date('Y-m-d H:i:s', strtotime($onward_date . ' ' . date('H:i:s')));
        //echo $current_tim;
        $datetime = new DateTime($current_tim);
        $month = $datetime->format('n'); //without zeroes
        $day = $datetime->format('j'); //without zeroes
        if ($day == 31) {
            $datetime->modify('last day of current month');
        } else if ($day == 29 || $day == 30) {
            if ($month == 1) {
                $datetime->modify('last day of current month');
            } else {
                $datetime->modify('-1 month');
            }
        } else {
            $datetime->modify('-1 month');
        }
        $current_time1 = $datetime->format('Y,m,d,H,i,s');

        echo '<style type="text/css">
/* tooltip */
a.tooltip {outline:none; color:#FFFFFF}
a.tooltip strong {line-height:20px;}
a.tooltip:hover {text-decoration:none;} 
a.tooltip span {
    z-index:10;display:none; padding:10px;
    margin-top:30px; margin-left:-47px;
    line-height:16px;
}
a.tooltip:hover span{
    display:inline; position:absolute; 
    border:1px solid #000;  color:#FFF; border-radius:0px;
    background:#000;
}
.callout {z-index:20;position:absolute;border:0;top:-15px;left:25px;}
    
/*CSS3 extras*/
a.tooltip span
{
    border-radius:2px;        
   /* box-shadow: 0px 0px 8px 4px #666;*/
    /*opacity: 0.8;*/
}
/* tooltip1 */
a.tooltip1 {outline:none; color:#FFFFFF}
a.tooltip1 strong {line-height:20px;}
a.tooltip1:hover {text-decoration:none;} 
a.tooltip1 span {
    z-index:10;display:none; padding:18px 20px;
    margin-top:-280px; margin-left:-160px;
     line-height:16px;
}
a.tooltip1:hover span{
    display:inline; position:absolute; 
    border:1px solid #000;  color:#000; border-radius:0px;
    background:#fff;
}
.callout1 {z-index:20;position:absolute;border:0;top:185px;left:137px; margin:0px;}
    
/*CSS3 extras*/
a.tooltip1 span
{
    border-radius:2px;    
	z-index:1000;	   
   /*box-shadow: 0px 0px 8px 4px #666;*/
    opacity: 0.9;
}

</style>
		<input type="hidden" name="j" id="j" size="7" value="' . $k . '" />
            <input type="hidden" name="cnt" id="cnt" size="7" value="' . $cnt . '" />
            <input  type="hidden" name="key" id="cnt" size="7" value="' . $key . '" />
            <input type="hidden" name="bal12" id="bal12" value="' . $bal . '"/>
            <input type="hidden" name="limit" id="limit" value="' . $limit . '"/>
            <input type="hidden" name="margin" id="margin" value="' . $margin . '"/>
            <input type="hidden" name="paytype" id="paytype" value="' . $pay_type . '"/>
            <input type="hidden" name="agent_type" id="agent_type" value="' . $agent_type . '"/>';

        if ($trip == "O") {
            $journey = "onward_";
            $way = "O";
        } else if ($trip == "R") {
            $journey = "return_";
            $way = "R";
        }

        $stmt3 = "select count(distinct seat) as api_seat_count from agent_api_seat where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' order by id desc";
        $query3 = $this->db->query($stmt3) or die(mysql_error());
        foreach ($query3->result() as $row3) {
            $api_seat_count = $row3->api_seat_count;
        }

        if ($api_seat_count == 0) {
            //$this->booking_m->agent_api_seats($service_num, $source_id, $destination_id, $onward_date, $travel_id);
        }

        $stmt4 = "select count(distinct seat_name) as seat_count from layout_list where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' order by id desc";
        $query4 = $this->db->query($stmt4) or die(mysql_error());
        foreach ($query4->result() as $row4) {
            $seat_count = $row4->seat_count;
        }

        /* $cc = $this->db->query("select * from registered_operators where travel_id='$travel_id'")or die(mysql_error());
          //echo "2 : select * from registered_operators where travel_id='$travel_id'";
          $cc1 = mysql_fetch_array($cc);
          $convenience_charge1 = $cc1['convenience_charge']; */
        $convenience_charge1 = 0;

        $stmt5 = "select distinct layout_id,max(row) as row from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date'";
        $query5 = $this->db->query($stmt5) or die(mysql_error());
        foreach ($query5->result() as $row5) {
            $layout_id = explode('#', $row5->layout_id);
            $layout_type = $layout_id[1];
            $maxrow = $row5->row;
        }

        $seat_name = '';
        if ($api_seat_count == $seat_count) { //from api			
            if ($layout_type == 'seater') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='s'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                echo '<div class="box">';
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat,fare,base_fare,service_tax_amount,discount_amount,convenience_charge,available,available_type,ladies,seat_status,status from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='s'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat);
                            $fare1 = trim($row7->fare);
                            $base_fare = trim($row7->base_fare);
                            $service_tax_amount = trim($row7->service_tax_amount);
                            //$discount_amount = trim($row7->discount_amount);
                            $discount_amount = 0;
                            //$convenience_charge = trim($row7->convenience_charge);
                            $convenience_charge = 0;
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                            $is_ladies = trim($row7->ladies);
                            $seat_status = trim($row7->seat_status);
                            $status = trim($row7->status);

                            $fare = $base_fare + $service_tax_amount + $convenience_charge - $discount_amount;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                echo '<span class="label bg-info-seat">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($ho == "yes" && $available == 1) {
                                    $class = "label bg-branchquota-seat";
                                } else if ($ho == "yes" && $available == 2) {
                                    $class = "label bg-agentquota-seat";
                                } else if ($available_type == $agent_id) {
                                    $class = "label bg-branchquota-seat";
                                } else {
                                    $class = "label bg-available-seat";
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-ladies-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-ladies-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip">' . $seat_name . '</a></span>';
                                }
                            } else if ($seat_status == 1 && $status == "confirm") {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-unavailable-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-unavailable-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip">' . $seat_name . '</a></span>';
                                }
                            } else if ($seat_status == 1 && $status == "block") {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-blocked-seat"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "pend") {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-pending-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '
						<span id="' . $way . '' . $seat_name . '"></span>
						</a></span>';
                                } else {
                                    echo '<span class="label bg-pending-seat">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    echo '<span class="label bg-info-seat">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    echo '<span class="label bg-available-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    echo '<span class="label bg-unavailable-seat">' . $seat_name . '' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    echo '<span class="label bg-pennding-seat">' . $seat_name . '' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    echo '<span class="label bg-info-seat">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    echo '<span class="label bg-ladies-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    echo '<span class="label bg-pending-seat">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
                echo '</div>';
            } else if ($layout_type == 'sleeper') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='U'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                echo 'Upper <br /><div class="box">';
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat,fare,base_fare,service_tax_amount,discount_amount,convenience_charge,available,available_type,ladies,seat_status,status from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='U'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat);
                            $fare1 = trim($row7->fare);
                            $base_fare = trim($row7->base_fare);
                            $service_tax_amount = trim($row7->service_tax_amount);
                            //$discount_amount = trim($row7->discount_amount);
                            $discount_amount = 0;
                            //$convenience_charge = trim($row7->convenience_charge);
                            $convenience_charge = 0;
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                            $is_ladies = trim($row7->ladies);
                            $seat_status = trim($row7->seat_status);
                            $status = trim($row7->status);

                            $fare = $base_fare + $service_tax_amount + $convenience_charge - $discount_amount;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($maxrow == 12) {
                                    $class = 'label bg-info-berth-v';
                                } else {
                                    $class = 'label bg-info-berth';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($maxrow == 12) {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth-v";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else {
                                        $class = "label bg-available-berth-v";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-ladies-berth-v';
                                } else {
                                    $class = 'label bg-ladies-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip">' . $seat_name . '</a></span>';
                                }
                            } else if ($seat_status == 1 && $status == "confirm") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-unavailable-berth-v';
                                } else {
                                    $class = 'label bg-unavailable-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip">' . $seat_name . '</a></span>';
                                }
                            } else if ($seat_status == 1 && $status == "block") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-blocked-berth-v';
                                } else {
                                    $class = 'label bg-blocked-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '
						<span id="' . $way . '' . $seat_name . '"></span>
						</a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "pend") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-pending-berth-v';
                                } else {
                                    $class = 'label bg-pending-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pennding-berth-v';
                                    } else {
                                        $class = 'label bg-pennding-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-ladies-berth-v';
                                    } else {
                                        $class = 'label bg-ladies-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pending-berth-v';
                                    } else {
                                        $class = 'label bg-pending-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
                echo '</div><br />Lower<br /><div class="box">';
                $stmt6 = "select max(row) as mrow,max(col) as mcol from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='L'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat,fare,base_fare,service_tax_amount,discount_amount,convenience_charge,available,available_type,ladies,seat_status,status from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='L'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat);
                            $fare1 = trim($row7->fare);
                            $base_fare = trim($row7->base_fare);
                            $service_tax_amount = trim($row7->service_tax_amount);
                            //$discount_amount = trim($row7->discount_amount);
                            $discount_amount = 0;
                            //$convenience_charge = trim($row7->convenience_charge);
                            $convenience_charge = 0;
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                            $is_ladies = trim($row7->ladies);
                            $seat_status = trim($row7->seat_status);
                            $status = trim($row7->status);

                            $fare = $base_fare + $service_tax_amount + $convenience_charge - $discount_amount;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($maxrow == 12) {
                                    $class = 'label bg-info-berth-v';
                                } else {
                                    $class = 'label bg-info-berth';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($maxrow == 12) {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth-v";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else {
                                        $class = "label bg-available-berth-v";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-ladies-berth-v';
                                } else {
                                    $class = 'label bg-ladies-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "confirm") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-unavailable-berth-v';
                                } else {
                                    $class = 'label bg-unavailable-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "block") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-blocked-berth-v';
                                } else {
                                    $class = 'label bg-blocked-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "pend") {
                                if ($maxrow == 12) {
                                    $class = 'label bg-pending-berth-v';
                                } else {
                                    $class = 'label bg-pending-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pennding-berth-v';
                                    } else {
                                        $class = 'label bg-pennding-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-ladies-berth-v';
                                    } else {
                                        $class = 'label bg-ladies-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pending-berth-v';
                                    } else {
                                        $class = 'label bg-pending-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
echo '</div><br />';
            } else if ($layout_type == 'seatersleeper') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='U'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                echo 'Upper <br />';
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat,fare,base_fare,service_tax_amount,discount_amount,convenience_charge,available,available_type,ladies,seat_status,status from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='U'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat);
                            $fare1 = trim($row7->fare);
                            $base_fare = trim($row7->base_fare);
                            $service_tax_amount = trim($row7->service_tax_amount);
                            //$discount_amount = trim($row7->discount_amount);
                            $discount_amount = 0;
                            //$convenience_charge = trim($row7->convenience_charge);
                            $convenience_charge = 0;
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                            $is_ladies = trim($row7->ladies);
                            $seat_status = trim($row7->seat_status);
                            $status = trim($row7->status);

                            $fare = $base_fare + $service_tax_amount + $convenience_charge - $discount_amount;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                $class = 'label bg-info-berth';
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($ho == "yes" && $available == 1) {
                                    $class = "label bg-branchquota-berth";
                                } else if ($ho == "yes" && $available == 2) {
                                    $class = "label bg-agentquota-berth";
                                } else if ($available_type == $agent_id) {
                                    $class = "label bg-branchquota-berth";
                                } else {
                                    $class = "label bg-available-berth";
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {

                                $class = 'label bg-ladies-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "confirm") {

                                $class = 'label bg-unavailable-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "block") {

                                $class = 'label bg-blocked-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "pend") {

                                $class = 'label bg-pending-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {

                                    $class = 'label bg-info-berth';

                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {

                                    $class = 'label bg-available-berth';

                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {

                                    $class = 'label bg-available-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {

                                    $class = 'label bg-unavailable-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {

                                    $class = 'label bg-blocked-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {

                                    $class = 'label bg-pennding-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {

                                    $class = 'label bg-info-berth';

                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {

                                            $class = 'label bg-available-berth';

                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {

                                            $class = 'label bg-unavailable-berth';

                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {

                                            $class = 'label bg-available-berth';

                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            $class = 'label bg-unavailable-berth';

                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {

                                    $class = 'label bg-ladies-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {

                                    $class = 'label bg-unavailable-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {

                                    $class = 'label bg-blocked-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {

                                    $class = 'label bg-pending-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
                echo '<br />Lower<br />';
                $stmt6 = "select max(row) as mrow,max(col) as mcol from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and (seat_type='L:s' or seat_type='L:b')";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat,fare,base_fare,service_tax_amount,discount_amount,convenience_charge,available,available_type,ladies,seat_status,status from agent_api_seat where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and (seat_type='L:s' or seat_type='L:b')";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat);
                            $fare1 = trim($row7->fare);
                            $base_fare = trim($row7->base_fare);
                            $service_tax_amount = trim($row7->service_tax_amount);
                            //$discount_amount = trim($row7->discount_amount);
                            $discount_amount = 0;
                            //$convenience_charge = trim($row7->convenience_charge);
                            $convenience_charge = 0;
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                            $is_ladies = trim($row7->ladies);
                            $seat_status = trim($row7->seat_status);
                            $status = trim($row7->status);

                            $fare = $base_fare + $service_tax_amount + $convenience_charge - $discount_amount;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-info-berth';
                                } else {
                                    $class = 'label bg-info-seat';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($seat_type == "L:b") {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-seat";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-seat";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-seat";
                                    } else {
                                        $class = "label bg-available-seat";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-ladies-berth';
                                } else {
                                    $class = 'label bg-ladies-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "confirm") {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-unavailable-berth';
                                } else {
                                    $class = 'label bg-unavailable-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "block") {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-blocked-berth';
                                } else {
                                    $class = 'label bg-blocked-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1 && $status == "pend") {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-pending-berth';
                                } else {
                                    $class = 'label bg-pending-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-info-berth';
                                    } else {
                                        $class = 'label bg-info-seat';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-available-berth';
                                    } else {
                                        $class = 'label bg-available-seat';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-available-berth';
                                    } else {
                                        $class = 'label bg-available-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-unavailable-berth';
                                    } else {
                                        $class = 'label bg-unavailable-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-blocked-berth';
                                    } else {
                                        $class = 'label bg-blocked-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-pennding-berth';
                                    } else {
                                        $class = 'label bg-pennding-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-info-berth';
                                    } else {
                                        $class = 'label bg-info-seat';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-available-berth';
                                            } else {
                                                $class = 'label bg-available-seat';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-unavailable-berth';
                                            } else {
                                                $class = 'label bg-unavailable-seat';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-available-berth';
                                            } else {
                                                $class = 'label bg-available-seat';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-unavailable-berth';
                                            } else {
                                                $class = 'label bg-unavailable-seat';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1 && $status == "confirm") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-ladies-berth';
                                    } else {
                                        $class = 'label bg-ladies-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "confirm") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-unavailable-berth';
                                    } else {
                                        $class = 'label bg-unavailable-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "block") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-blocked-berth';
                                    } else {
                                        $class = 'label bg-blocked-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $status == "pend") {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-pending-berth';
                                    } else {
                                        $class = 'label bg-pending-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
            }
        } else { //from database
            $seat_fare = "";
            $seat_fare1 = "";
            $changedSeatFare = "";
            $changed_fare_discount_amt = 0;
            $seat_fare_discount_amt = 0;

            //getting update fare
            $stmt = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and from_id='$source_id' and to_id='$destination_id'";
            $query = $this->db->query($stmt);
            if ($query->num_rows() == 0) {
                $stmt = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$source_id' and to_id='$destination_id' and journey_date is NULL";
                $query = $this->db->query($stmt);
            }

            if ($layout_type == 'seater') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='s'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }

                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat_name,seat_status,is_ladies,available,available_type from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='s'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat_name);
                            $seat_status = trim($row7->seat_status);
                            $is_ladies = trim($row7->is_ladies);
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                        }

                        //getting update fare 
                        foreach ($query->result() as $row) {
                            $changedSeatFare = $row->seat_fare_changed;
                            $seat_fare1 = trim($row->seat_fare);
                        }

                        // service tax amount on base fare
                        $seat_fare_tax_amt = ($seat_fare1 * $service_tax) / 100;

                        $convenience_charge = ($convenience_charge1 * $seat_fare1) / 100;

                        //fare with discount
                        /* if($discount_type=="percent")
                          {
                          $seat_fare_discount_amt = ($seat_fare1 * $discount)/100;
                          }
                          else
                          {
                          $seat_fare_discount_amt = $discount;
                          } */

                        //individual seat fare
                        if ($changedSeatFare != "") {
                            $changedSeatFare1 = explode('@', $changedSeatFare);
                            for ($a = 0; $a < count($changedSeatFare1); $a++) {
                                $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);
                                $changed_seat = $changedSeatFare2[0];
                                $changed_fare = $changedSeatFare2[1];

                                // service tax amount on changed fare
                                $changed_fare_tax_amt = ($changed_fare * $service_tax) / 100;

                                $convenience_charge = round(($convenience_charge1 * $changed_fare) / 100);

                                //fare with discount
                                /* if($discount_type == "percent")
                                  {
                                  $changed_fare_discount_amt = ($changed_fare * $discount)/100;
                                  }
                                  else
                                  {
                                  $changed_fare_discount_amt = $discount;
                                  } */
                                if ($changed_seat == $seat_name) {
                                    $fare = $changed_fare + $changed_fare_tax_amt + $convenience_charge - $changed_fare_discount_amt;
                                    $base_fare = $changed_fare;
                                    $discount_amount = $changed_fare_discount_amt;
                                    $service_tax_amount = $changed_fare_tax_amt;
                                    break;
                                } else {
                                    $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                                    $base_fare = $seat_fare1;
                                    $discount_amount = $seat_fare_discount_amt;
                                    $service_tax_amount = $seat_fare_tax_amt;
                                }
                            }
                        } else {
                            $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                            $base_fare = $seat_fare1;
                            $discount_amount = $seat_fare_discount_amt;
                            $service_tax_amount = $seat_fare_tax_amt;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                echo '<span class="label bg-info-seat">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($ho == "yes" && $available == 1) {
                                    $class = "label bg-branchquota-seat";
                                } else if ($ho == "yes" && $available == 2) {
                                    $class = "label bg-agentquota-seat";
                                } else if ($available_type == $agent_id) {
                                    $class = "label bg-branchquota-seat";
                                } else {
                                    $class = "label bg-available-seat";
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1) {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-ladies-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-ladies-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1) {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-unavailable-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-unavailable-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 2) {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-blocked-seat"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 3) {
                                if ($is_hover == 1) {
                                    echo '<span class="label bg-pending-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="label bg-pending-seat" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    echo '<span class="label bg-info-seat">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    echo '<span class="label bg-available-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    echo '<span class="label bg-pennding-seat">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    echo '<span class="label bg-info-seat">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            echo '<span class="label bg-available-seat" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    echo '<span class="label bg-ladies-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    echo '<span class="label bg-unavailable-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    echo '<span class="label bg-blocked-seat">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    echo '<span class="label bg-pending-seat">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
            } else if ($layout_type == 'sleeper') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='U'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                echo 'Upper <br />';
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat_name,seat_status,is_ladies,available,available_type from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='U'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat_name);
                            $seat_status = trim($row7->seat_status);
                            $is_ladies = trim($row7->is_ladies);
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                        }

                        //getting update fare 
                        foreach ($query->result() as $row) {
                            $changedSeatFare = $row->uberth_fare_changed;
                            $seat_fare1 = trim($row->uberth_fare);
                        }

                        // service tax amount on base fare
                        $seat_fare_tax_amt = ($seat_fare1 * $service_tax) / 100;

                        $convenience_charge = ($convenience_charge1 * $seat_fare1) / 100;

                        //fare with discount
                        /* if($discount_type=="percent")
                          {
                          $seat_fare_discount_amt = ($seat_fare1 * $discount)/100;
                          }
                          else
                          {
                          $seat_fare_discount_amt = $discount;
                          } */

                        //individual seat fare
                        if ($changedSeatFare != "") {
                            $changedSeatFare1 = explode('@', $changedSeatFare);
                            for ($a = 0; $a < count($changedSeatFare1); $a++) {
                                $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);
                                $changed_seat = $changedSeatFare2[0];
                                $changed_fare = $changedSeatFare2[1];

                                // service tax amount on changed fare
                                $changed_fare_tax_amt = ($changed_fare * $service_tax) / 100;

                                $convenience_charge = round(($convenience_charge1 * $changed_fare) / 100);

                                //fare with discount
                                /* if($discount_type == "percent")
                                  {
                                  $changed_fare_discount_amt = ($changed_fare * $discount)/100;
                                  }
                                  else
                                  {
                                  $changed_fare_discount_amt = $discount;
                                  } */
                                if ($changed_seat == $seat_name) {
                                    $fare = $changed_fare + $changed_fare_tax_amt + $convenience_charge - $changed_fare_discount_amt;
                                    $base_fare = $changed_fare;
                                    $discount_amount = $changed_fare_discount_amt;
                                    $service_tax_amount = $changed_fare_tax_amt;
                                    break;
                                } else {
                                    $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                                    $base_fare = $seat_fare1;
                                    $discount_amount = $seat_fare_discount_amt;
                                    $service_tax_amount = $seat_fare_tax_amt;
                                }
                            }
                        } else {
                            $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                            $base_fare = $seat_fare1;
                            $discount_amount = $seat_fare_discount_amt;
                            $service_tax_amount = $seat_fare_tax_amt;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($maxrow == 12) {
                                    $class = 'label bg-info-berth-v';
                                } else {
                                    $class = 'label bg-info-berth';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($maxrow == 12) {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth-v";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else {
                                        $class = "label bg-available-berth-v";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-ladies-berth-v';
                                } else {
                                    $class = 'label bg-ladies-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-unavailable-berth-v';
                                } else {
                                    $class = 'label bg-unavailable-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span>
											</a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 2) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-blocked-berth-v';
                                } else {
                                    $class = 'label bg-blocked-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 3) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-pending-berth-v';
                                } else {
                                    $class = 'label bg-pending-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pennding-berth-v';
                                    } else {
                                        $class = 'label bg-pennding-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-ladies-berth-v';
                                    } else {
                                        $class = 'label bg-ladies-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pending-berth-v';
                                    } else {
                                        $class = 'label bg-pending-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
                echo '<br />Lower<br />';
                $stmt6 = "select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='L'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat_name,seat_status,is_ladies,available,available_type from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='L'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat_name);
                            $seat_status = trim($row7->seat_status);
                            $is_ladies = trim($row7->is_ladies);
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                        }

                        //getting update fare 
                        foreach ($query->result() as $row) {
                            $changedSeatFare = $row->lberth_fare_changed;
                            $seat_fare1 = trim($row->lberth_fare);
                        }

                        // service tax amount on base fare
                        $seat_fare_tax_amt = ($seat_fare1 * $service_tax) / 100;

                        $convenience_charge = ($convenience_charge1 * $seat_fare1) / 100;

                        //fare with discount
                        /* if($discount_type=="percent")
                          {
                          $seat_fare_discount_amt = ($seat_fare1 * $discount)/100;
                          }
                          else
                          {
                          $seat_fare_discount_amt = $discount;
                          } */

                        //individual seat fare
                        if ($changedSeatFare != "") {
                            $changedSeatFare1 = explode('@', $changedSeatFare);
                            for ($a = 0; $a < count($changedSeatFare1); $a++) {
                                $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);
                                $changed_seat = $changedSeatFare2[0];
                                $changed_fare = $changedSeatFare2[1];

                                // service tax amount on changed fare
                                $changed_fare_tax_amt = ($changed_fare * $service_tax) / 100;

                                $convenience_charge = round(($convenience_charge1 * $changed_fare) / 100);

                                //fare with discount
                                /* if($discount_type == "percent")
                                  {
                                  $changed_fare_discount_amt = ($changed_fare * $discount)/100;
                                  }
                                  else
                                  {
                                  $changed_fare_discount_amt = $discount;
                                  } */
                                if ($changed_seat == $seat_name) {
                                    $fare = $changed_fare + $changed_fare_tax_amt + $convenience_charge - $changed_fare_discount_amt;
                                    $base_fare = $changed_fare;
                                    $discount_amount = $changed_fare_discount_amt;
                                    $service_tax_amount = $changed_fare_tax_amt;
                                    break;
                                } else {
                                    $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                                    $base_fare = $seat_fare1;
                                    $discount_amount = $seat_fare_discount_amt;
                                    $service_tax_amount = $seat_fare_tax_amt;
                                }
                            }
                        } else {
                            $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                            $base_fare = $seat_fare1;
                            $discount_amount = $seat_fare_discount_amt;
                            $service_tax_amount = $seat_fare_tax_amt;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($maxrow == 12) {
                                    $class = 'label bg-info-berth-v';
                                } else {
                                    $class = 'label bg-info-berth';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($maxrow == 12) {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth-v";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth-v";
                                    } else {
                                        $class = "label bg-available-berth-v";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-ladies-berth-v';
                                } else {
                                    $class = 'label bg-ladies-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-unavailable-berth-v';
                                } else {
                                    $class = 'label bg-unavailable-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 2) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-blocked-berth-v';
                                } else {
                                    $class = 'label bg-blocked-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 3) {
                                if ($maxrow == 12) {
                                    $class = 'label bg-pending-berth-v';
                                } else {
                                    $class = 'label bg-pending-berth';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-available-berth-v';
                                    } else {
                                        $class = 'label bg-available-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pennding-berth-v';
                                    } else {
                                        $class = 'label bg-pennding-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-info-berth-v';
                                    } else {
                                        $class = 'label bg-info-berth';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-available-berth-v';
                                            } else {
                                                $class = 'label bg-available-berth';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($maxrow == 12) {
                                                $class = 'label bg-unavailable-berth-v';
                                            } else {
                                                $class = 'label bg-unavailable-berth';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-ladies-berth-v';
                                    } else {
                                        $class = 'label bg-ladies-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-unavailable-berth-v';
                                    } else {
                                        $class = 'label bg-unavailable-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-blocked-berth-v';
                                    } else {
                                        $class = 'label bg-blocked-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($maxrow == 12) {
                                        $class = 'label bg-pending-berth-v';
                                    } else {
                                        $class = 'label bg-pending-berth';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
            } else if ($layout_type == 'seatersleeper') {
                $stmt6 = "select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and seat_type='U'";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                echo 'Upper <br />';
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat_name,seat_status,is_ladies,available,available_type from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and seat_type='U'";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat_name);
                            $seat_status = trim($row7->seat_status);
                            $is_ladies = trim($row7->is_ladies);
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                        }

                        //getting update fare 
                        foreach ($query->result() as $row) {
                            $changedSeatFare = $row->uberth_fare_changed;
                            $seat_fare1 = trim($row->uberth_fare);
                        }

                        // service tax amount on base fare
                        $seat_fare_tax_amt = ($seat_fare1 * $service_tax) / 100;

                        $convenience_charge = ($convenience_charge1 * $seat_fare1) / 100;

                        //fare with discount
                        /* if($discount_type=="percent")
                          {
                          $seat_fare_discount_amt = ($seat_fare1 * $discount)/100;
                          }
                          else
                          {
                          $seat_fare_discount_amt = $discount;
                          } */

                        //individual seat fare
                        if ($changedSeatFare != "") {
                            $changedSeatFare1 = explode('@', $changedSeatFare);
                            for ($a = 0; $a < count($changedSeatFare1); $a++) {
                                $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);
                                $changed_seat = $changedSeatFare2[0];
                                $changed_fare = $changedSeatFare2[1];

                                // service tax amount on changed fare
                                $changed_fare_tax_amt = ($changed_fare * $service_tax) / 100;

                                $convenience_charge = round(($convenience_charge1 * $changed_fare) / 100);

                                //fare with discount
                                /* if($discount_type == "percent")
                                  {
                                  $changed_fare_discount_amt = ($changed_fare * $discount)/100;
                                  }
                                  else
                                  {
                                  $changed_fare_discount_amt = $discount;
                                  } */
                                if ($changed_seat == $seat_name) {
                                    $fare = $changed_fare + $changed_fare_tax_amt + $convenience_charge - $changed_fare_discount_amt;
                                    $base_fare = $changed_fare;
                                    $discount_amount = $changed_fare_discount_amt;
                                    $service_tax_amount = $changed_fare_tax_amt;
                                    break;
                                } else {
                                    $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                                    $base_fare = $seat_fare1;
                                    $discount_amount = $seat_fare_discount_amt;
                                    $service_tax_amount = $seat_fare_tax_amt;
                                }
                            }
                        } else {
                            $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                            $base_fare = $seat_fare1;
                            $discount_amount = $seat_fare_discount_amt;
                            $service_tax_amount = $seat_fare_tax_amt;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                $class = 'label bg-info-berth';
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($ho == "yes" && $available == 1) {
                                    $class = "label bg-branchquota-berth";
                                } else if ($ho == "yes" && $available == 2) {
                                    $class = "label bg-agentquota-berth";
                                } else if ($available_type == $agent_id) {
                                    $class = "label bg-branchquota-berth";
                                } else {
                                    $class = "label bg-available-berth";
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1) {

                                $class = 'label bg-ladies-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1) {

                                $class = 'label bg-unavailable-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 2) {

                                $class = 'label bg-blocked-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 3) {

                                $class = 'label bg-pending-berth';

                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {

                                    $class = 'label bg-info-berth';

                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {

                                    $class = 'label bg-available-berth';

                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1) {

                                    $class = 'label bg-available-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {

                                    $class = 'label bg-unavailable-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {

                                    $class = 'label bg-blocked-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {

                                    $class = 'label bg-pennding-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {

                                    $class = 'label bg-info-berth';

                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {

                                            $class = 'label bg-available-berth';

                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {

                                            $class = 'label bg-unavailable-berth';

                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {

                                            $class = 'label bg-available-berth';

                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            $class = 'label bg-unavailable-berth';

                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1) {

                                    $class = 'label bg-ladies-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {

                                    $class = 'label bg-unavailable-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {

                                    $class = 'label bg-blocked-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {

                                    $class = 'label bg-pending-berth';

                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
                echo '<br />Lower<br />';
                $stmt6 = "select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and (seat_type='L:s' or seat_type='L:b')";
                $query6 = $this->db->query($stmt6) or die(mysql_error());
                foreach ($query6->result() as $row6) {
                    $mrow = $row6->mrow;
                    $mcol = $row6->mcol;
                }
                for ($i = 1; $i <= $mcol; $i++) {
                    for ($j = 1; $j <= $mrow; $j++) {
                        $stmt7 = "select distinct seat_name,seat_status,is_ladies,available,available_type from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and row='$j' and col='$i' and (seat_type='L:s' or seat_type='L:b')";
                        $query7 = $this->db->query($stmt7) or die(mysql_error());
                        foreach ($query7->result() as $row7) {
                            $seat_name = trim($row7->seat_name);
                            $seat_status = trim($row7->seat_status);
                            $is_ladies = trim($row7->is_ladies);
                            $available = trim($row7->available);
                            $available_type = trim($row7->available_type);
                        }

                        //getting update fare 
                        foreach ($query->result() as $row) {
                            $changedSeatFare = $row->lberth_fare_changed;
                            if ($seat_type == "L:s") {
                                $seat_fare1 = trim($row->seat_fare);
                            } else if ($seat_type == "L:b") {
                                $seat_fare1 = trim($row->lberth_fare);
                            }
                        }

                        // service tax amount on base fare
                        $seat_fare_tax_amt = ($seat_fare1 * $service_tax) / 100;

                        $convenience_charge = ($convenience_charge1 * $seat_fare1) / 100;

                        //fare with discount
                        /* if($discount_type=="percent")
                          {
                          $seat_fare_discount_amt = ($seat_fare1 * $discount)/100;
                          }
                          else
                          {
                          $seat_fare_discount_amt = $discount;
                          } */

                        //individual seat fare
                        if ($changedSeatFare != "") {
                            $changedSeatFare1 = explode('@', $changedSeatFare);
                            for ($a = 0; $a < count($changedSeatFare1); $a++) {
                                $changedSeatFare2 = explode('#', $changedSeatFare1[$a]);
                                $changed_seat = $changedSeatFare2[0];
                                $changed_fare = $changedSeatFare2[1];

                                // service tax amount on changed fare
                                $changed_fare_tax_amt = ($changed_fare * $service_tax) / 100;

                                $convenience_charge = round(($convenience_charge1 * $changed_fare) / 100);

                                //fare with discount
                                /* if($discount_type == "percent")
                                  {
                                  $changed_fare_discount_amt = ($changed_fare * $discount)/100;
                                  }
                                  else
                                  {
                                  $changed_fare_discount_amt = $discount;
                                  } */
                                if ($changed_seat == $seat_name) {
                                    $fare = $changed_fare + $changed_fare_tax_amt + $convenience_charge - $changed_fare_discount_amt;
                                    $base_fare = $changed_fare;
                                    $discount_amount = $changed_fare_discount_amt;
                                    $service_tax_amount = $changed_fare_tax_amt;
                                    break;
                                } else {
                                    $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                                    $base_fare = $seat_fare1;
                                    $discount_amount = $seat_fare_discount_amt;
                                    $service_tax_amount = $seat_fare_tax_amt;
                                }
                            }
                        } else {
                            $fare = $seat_fare1 + $seat_fare_tax_amt + $convenience_charge - $seat_fare_discount_amt;
                            $base_fare = $seat_fare1;
                            $discount_amount = $seat_fare_discount_amt;
                            $service_tax_amount = $seat_fare_tax_amt;
                        }

                        if ($ho == "yes" && $show_avail_seat == "yes") {
                            if ($seat_name == '') {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-info-berth';
                                } else {
                                    $class = 'label bg-info-seat';
                                }
                                echo '<span class="' . $class . '">&nbsp;</span>';
                            } else if ($seat_status == 0) {
                                if ($seat_type == "L:b") {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-berth";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-berth";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-berth";
                                    } else {
                                        $class = "label bg-available-berth";
                                    }
                                } else {
                                    if ($ho == "yes" && $available == 1) {
                                        $class = "label bg-branchquota-seat";
                                    } else if ($ho == "yes" && $available == 2) {
                                        $class = "label bg-agentquota-seat";
                                    } else if ($available_type == $agent_id) {
                                        $class = "label bg-branchquota-seat";
                                    } else {
                                        $class = "label bg-available-seat";
                                    }
                                }
                                echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                            } else if ($seat_status == 1 && $is_ladies == 1) {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-ladies-berth';
                                } else {
                                    $class = 'label bg-ladies-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 1) {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-unavailable-berth';
                                } else {
                                    $class = 'label bg-unavailable-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 2) {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-blocked-berth';
                                } else {
                                    $class = 'label bg-blocked-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else if ($seat_status == 3) {
                                if ($seat_type == "L:b") {
                                    $class = 'label bg-pending-berth';
                                } else {
                                    $class = 'label bg-pending-seat';
                                }
                                if ($is_hover == 1) {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '<span id="' . $way . '' . $seat_name . '"></span></a></span>';
                                } else {
                                    echo '<span class="' . $class . '" onClick="seatoptions(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $onward_date . '\')">' . $seat_name . '</span>';
                                }
                            }
                        } else {
                            if ($show_avail_seat == "yes") {//external agent
                                if ($seat_name == '') {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-info-berth';
                                    } else {
                                        $class = 'label bg-info-seat';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-available-berth';
                                    } else {
                                        $class = 'label bg-available-seat';
                                    }
                                    echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-available-berth';
                                    } else {
                                        $class = 'label bg-available-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-unavailable-berth';
                                    } else {
                                        $class = 'label bg-unavailable-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-blocked-berth';
                                    } else {
                                        $class = 'label bg-blocked-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-pennding-berth';
                                    } else {
                                        $class = 'label bg-pennding-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            } else {
                                if ($seat_name == '') {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-info-berth';
                                    } else {
                                        $class = 'label bg-info-seat';
                                    }
                                    echo '<span class="' . $class . '">&nbsp;</span>';
                                } else if ($seat_status == 0) {
                                    if ($show_quota == 'yes') {
                                        if ($available_type == $agent_id) {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-available-berth';
                                            } else {
                                                $class = 'label bg-available-seat';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-unavailable-berth';
                                            } else {
                                                $class = 'label bg-unavailable-seat';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    } else {
                                        if ($available_type == "" || $available_type == 0) {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-available-berth';
                                            } else {
                                                $class = 'label bg-available-seat';
                                            }
                                            echo '<span class="' . $class . '" id="' . $seat_name . '' . $way . '" onClick="selectseat(\'' . $seat_name . '\',\'' . $fare . '\',\'' . $base_fare . '\',\'' . $discount_amount . '\',\'' . $service_tax_amount . '\',\'' . $convenience_charge . '\',\'' . $way . '\',\'' . $k . '\')">' . $seat_name . '</span>';
                                        } else {
                                            if ($seat_type == "L:b") {
                                                $class = 'label bg-unavailable-berth';
                                            } else {
                                                $class = 'label bg-unavailable-seat';
                                            }
                                            echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                        }
                                    }
                                } else if ($seat_status == 1 && $is_ladies == 1) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-ladies-berth';
                                    } else {
                                        $class = 'label bg-ladies-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 1) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-unavailable-berth';
                                    } else {
                                        $class = 'label bg-unavailable-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 2) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-blocked-berth';
                                    } else {
                                        $class = 'label bg-blocked-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                } else if ($seat_status == 3) {
                                    if ($seat_type == "L:b") {
                                        $class = 'label bg-pending-berth';
                                    } else {
                                        $class = 'label bg-pending-seat';
                                    }
                                    echo '<span class="' . $class . '">' . $seat_name . '</span>';
                                }
                            }
                        }
                        unset($seat_name);
                    }
                    echo '<br />';
                }
            }
        }
        echo '<br />
		<div><select name="onward_bp" id="onward_bp" onChange="' . $journey . 'landmark()" class="inputfield">
                <option value="0">Boarding Point</option>';

        $sql1 = $this->db->query("select * from boarding_points where service_num='$service_num' and city_id='$source_id' and board_or_drop_type='board' and travel_id='$travel_id' order by timing") or die(mysql_error());
        foreach ($sql1->result() as $row) {
            $board = $row->board_drop;
            $bpdp_id = $row->bpdp_id;
            $board1 = explode('#', $board);
            $btime = date('h:i A', strtotime($board1['1']));
            echo '<option value="' . $bpdp_id . '">' . $board1['0'] . ' - ' . $btime . '</option>';
        }

        echo '</select>
		<p id="onward_lm" class="landmark">&nbsp;</p>
		</div>
		<br />
		<table border="0" cellpadding="1" cellspacing="1" id="' . $journey . 'det" style="display:none">
          <tr>
            <td width="76">Seat</td>
            <td width="177">Name</td>
			<td width="175">Gender</td>
            <td width="59">Age</td>
            
          </tr>
          <tr>
            <td>
              <input type="text" name="onward_seat0" id="onward_seat0" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20"><input name="onward_name0" type="text" id="onward_name0" size="15"  value="" />                          </td>
            <td><input name="onward_sex0" id="onward_sex0" type="radio" value="M" checked> Male <input name="onward_sex0" id="onward_sex0" type="radio" value="F"> Female
			</td>
            <td><input name="onward_age0" type="text" id="onward_age0" class="inputsmall" size="4" maxlength="2"  value="" /></td>
          </tr>
          <tr id="' . $journey . 'pdata1" style="display:none">
            <td>
              <input type="text" name="onward_seat1" id="onward_seat1" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20">
              <input name="onward_name1" type="text" id="onward_name1" size="15"  value="" />            </td>
            <td><input name="onward_sex1" id="onward_sex1" type="radio" value="M" checked> Male <input name="onward_sex1" id="onward_sex1" type="radio" value="F"> Female</td>
            <td><input name="onward_age1" type="text" id="onward_age1" class="inputsmall" size="4" maxlength="2"  value="" /></td>
          </tr>
          <tr id="' . $journey . 'pdata2" style="display:none">
            <td>
              <input type="text" name="onward_seat2" id="onward_seat2" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20"><input name="onward_name2" type="text" id="onward_name2" size="15"  value=""/></td>
            <td><input name="onward_sex2" id="onward_sex2" type="radio" value="M" checked> Male <input name="onward_sex2" id="onward_sex2" type="radio" value="F"> Female</td>
            <td><input name="onward_age2" type="text" id="onward_age2" size="4" class="inputsmall" maxlength="2"  value="" /></td>
          </tr>
          <tr id="' . $journey . 'pdata3" style="display:none">
            <td>
              <input type="text" name="onward_seat3" id="onward_seat3" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20">
              <input name="onward_name3" type="text" id="onward_name3" size="15" value="" />            </td>
            <td><input name="onward_sex3" id="onward_sex3" type="radio" value="M" checked> Male <input name="onward_sex3" id="onward_sex4" type="radio" value="F"> Female</td>
            <td><input name="onward_age3" type="text" id="onward_age3" class="inputsmall" size="4" maxlength="2"  value="" /></td>
          </tr>
          <tr id="' . $journey . 'pdata4" style="display:none">
            <td>
              <input type="text" name="onward_seat4" id="onward_seat4" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20">
              <input name="onward_name4" type="text" id="onward_name4" size="15" value="" />            </td>
            <td><input name="onward_sex4" id="onward_sex4" type="radio" value="M" checked> Male <input name="onward_sex4" id="onward_sex4" type="radio" value="F"> Female</td>
            <td><input name="onward_age4" type="text" id="onward_age4" class="inputsmall" size="4" maxlength="2"  value="" /></td>
          </tr>
          <tr id="' . $journey . 'pdata5" style="display:none">
            <td>
              <input type="text" name="onward_seat5" id="onward_seat5" size="4" value="" height="8" readonly="" class="inputsmall" />            </td>
            <td height="20">
              <input name="onward_name5" type="text" id="onward_name5" size="15" value="" />            </td>
            <td><input name="onward_sex5" id="onward_sex5" type="radio" value="M" checked> Male <input name="onward_sex5" id="onward_sex5" type="radio" value="F"> Female</td>
            <td><input name="onward_age5" type="text" id="onward_age5" class="inputsmall" size="4" maxlength="2"  value="" /></td>
          </tr>
        </table>	
<input name="onward_seats" id="onward_seats" type="hidden" value="" />
					<input name="onward_fare" id="onward_fare" type="hidden" value="" />
					<input name="base_fare" id="base_fare" type="hidden" value="" />
					<input name="discount' . $way . $k . '" id="discount' . $way . $k . '" type="hidden" value="0" />
					<input name="service_tax" id="service_tax" type="hidden" value="" />
					<input name="convenience_charge' . $way . $k . '" id="convenience_charge' . $way . $k . '" type="hidden" value="" />
					<input type="hidden" name="onward_pass" id="onward_pass" size="7" value="" />
            		<input type="hidden" name="onward_start_time" id="onward_start_time" value="' . $start_time . '" />
            		<input type="hidden" name="onward_arr_time" id="onward_arr_time" value="' . $arr_time . '" />
            		<input type="hidden" name="onward_source_id" id="onward_source_id" value="' . $source_id . '" />
		            <input type="hidden" name="onward_destination_id" id="onward_destination_id" value="' . $destination_id . '" />
		            <input type="hidden" name="onward_source_name" id="onward_source_name" value="' . $source_name . '" />
		            <input type="hidden" name="onward_destination_name" id="onward_destination_name" value="' . $destination_name . '" />
		            <input type="hidden" name="onward_date" id="onward_date" value="' . $onward_date . '" />
		            <input type="hidden" name="onward_service_num" id="onward_service_num" value="' . $service_num . '" />
		            <input type="hidden" name="onward_bus_type" id="onward_bus_type" value="' . $bus_type . '" />
		            <input type="hidden" name="onward_model" id="onward_model" value="' . $model . '" />
		            <input type="hidden" name="trip" id="trip" value="' . $trip . '" />
		            <input type="hidden" name="onward_way" id="onward_way" value="' . $way . '" />
		            <input type="hidden" name="onward_travel_id" id="onward_travel_id" value="' . $travel_id . '" />
		            <input type="hidden" name="return_way" id="return_way" value="0" />
		            <input type="hidden" name="return_fare" id="return_fare" value="0" />
		            <input type="hidden" name="return_pass" id="return_pass" size="7" value="0" />
		            <input type="hidden" name="return_seats" id="return_seats" size="7" value="" />
                            <input type="hidden" name="start_time" id="start_time" size="7" value="' . $start_time1 . '" />
                            <input type="hidden" name="current_time" id="current_time" size="7" value="' . $current_time1 . '" />
				
			</form>';
    }

    function SeatPassDetails_view() {
        $sno = $this->input->post('sno');
        $serno = $this->input->post('serno');
        $jdate = $this->input->post('jdate');

        $sql = $this->db->query("select distinct seats from master_booking where service_no='$serno' and jdate='$jdate' and seats like '%$sno%' and (status='confirmed' or status='pending') order by id desc LIMIT 1") or die(mysql_error());
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $ress) {
                $seats1 = $ress->seats;
                $ss = explode(',', $seats1);
                //echo "passs".$pass." / seats".$seats1."<br />";	
                if (in_array($sno, $ss, true)) {
                    $sql1 = $this->db->query("select * from master_booking where service_no='$serno' and jdate='$jdate' and seats='$seats1' and (status='confirmed' or status='pending') order by id desc LIMIT 1") or die(mysql_error());
                }
            }
            foreach ($sql1->result() as $res) {
                $seats = $res->seats;
                $pn = $res->pname;
                $tkt_no = $res->tkt_no;
                $source = $res->source;
                $dest = $res->dest;
                $pmobile = $res->pmobile;
                $board_points = $res->board_point;
                $time = $res->time;
                $agent_id = $res->agent_id;
                $travel_id = $res->travel_id;
            }

            $seat = explode(',', $seats);
            $pn1 = explode(',', $pn);

            $cnt = count($seat);
            for ($i = 0; $i < $cnt; $i++) {
                if ($seat[$i] == $sno) {
                    $seatname = $seat[$i];
                    $passname = $pn1[$i];
                    break;
                }
            }

            $sql1 = $this->db->query("select distinct name,uname from agents_operator where id='$agent_id'") or die(mysql_error());
            foreach ($sql1->result() as $row) {
                $agName = $row->name;
                $agbranch = $row->uname;
            }

            $sql2 = $this->db->query("select distinct seat_status from layout_list where service_num='$serno' and journey_date='$jdate' and seat_name='$seatname'") or die(mysql_error());
            foreach ($sql2->result() as $row2) {
                $seat_status = $row2->seat_status;
            }
        } else {
            $seat_status = 2;
        }
        echo '
<!--img class="callout1" src="' . base_url("images/callout_black4.gif") . '" /-->';
        if ($seat_status == 2) {
            $sql = $this->db->query("select * from seat_blocking_det where service_num='$serno' and journey_date='$jdate' and seats like '%$sno%'") or die(mysql_error());
            //echo "select * from seat_blocking_det where service_num='$serno' and journey_date='$jdate' and seats like '%$sno%'";
            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $ress) {
                    $seats1 = $ress->seats;
                    $ss = explode(',', $seats1);
                    //echo "passs".$pass." / seats".$seats1."<br />";	
                    if (in_array($sno, $ss, true)) {
                        $sql1 = $this->db->query("select * from seat_blocking_det where service_num='$serno' and journey_date='$jdate' and seats='$seats1' order by pnr desc") or die(mysql_error());
                    }
                }

                foreach ($sql1->result() as $res) {
                    $seats = $res->seats;
                    $agent_id = $res->agent_id;
                    $blocked_time = $res->blocked_time;
                    $pnr = $res->pnr;
                }

                $seat = explode(',', $seats);

                $cnt = count($seat);
                for ($i = 0; $i < $cnt; $i++) {
                    if ($seat[$i] == $sno) {
                        $seatname = $seat[$i];

                        break;
                    }
                }

                $sql1 = $this->db->query("select name,uname from agents_operator where id='$agent_id'") or die(mysql_error());

                foreach ($sql1->result() as $row) {
                    $agName = $row->name;
                    $agbranch = $row->uname;
                }

                $sql2 = $this->db->query("select distinct blocked_time from layout_list where service_num='$serno' and journey_date='$jdate' and seat_name='$seatname'") or die(mysql_error());
                $row2 = mysql_fetch_array($sql2);
                foreach ($sql2->result() as $row2) {
                    $blocked_time1 = $row2->blocked_time;
                }
                if ($blocked_time == $blocked_time1) {
                    echo'<table border="0" cellpadding="0" cellspacing="0" width="300">
<tr>
  <td height="25" colspan="3" align="center"><strong>' . $agName . '</strong> (' . $blocked_time . ') Blocked</td>
  </tr>
<tr>
</table>';
                } else {
                    echo'<table border="0" cellpadding="0" cellspacing="0" width="300">
<tr>
  <td height="25" colspan="3" align="center"><strong>Website Blocked</td>
  </tr>
<tr>
</table>';
                }
            } else {
                echo'<table border="0" cellpadding="0" cellspacing="0" width="300">
<tr>
  <td height="25" colspan="3" align="center"><strong>Website Blocked</td>
  </tr>
<tr>
</table>';
            }
        } else {
            echo'<table border="0" cellpadding="0" cellspacing="0" width="300">';
            if ($seat_status == 3) {
                echo'<tr>
  <td height="25" colspan="3" align="center"><strong>' . $agName . '</strong> (' . $time . ') Pending</td>
  </tr>';
            } else if ($seat_status == 1) {
                echo'<tr>
  <td height="25" colspan="3" align="center"><strong>' . $agName . '</strong> (' . $time . ') Confirmed</td>
  </tr>';
            }
            echo'<tr>
    <td width="95" height="25" align="left"><strong>Ticket/Seat No</strong></td>
    <td width="10"><strong>:</strong></td>
    <td width="195" height="25" align="left">' . $tkt_no . ' / ' . $seatname . '</td>
  </tr>
  <tr>
    <td height="25" align="left"><strong>Route</strong></td>
    <td width="10"><strong>:</strong></td>
    <td height="25" align="left">' . $source . ' - ' . $dest . '</td>
  </tr>
  <tr>
    <td height="25" align="left"><strong>Passenger</strong></td>
    <td width="10"><strong>:</strong></td>
    <td height="25" align="left">' . $passname . '<br />' . $pmobile . '</td>
  </tr>
  <tr>
    <td height="25" align="left"><strong>Boarding Point</strong></td>
    <td><strong>:</strong></td>
    <td height="25" align="left">' . $board_points . '</td>
  </tr>
     <tr>
    <td height="25" align="left"><strong>Journey Date</strong></td>
    <td><strong>:</strong></td>
    <td height="25" align="left">' . $jdate . '</td>
  </tr>
</table>';
        }
    }

    function seat_options1() {
        $seat_name = $this->input->post('seat_name');
        $service_num = $this->input->post('service_num');
        $journey_date = $this->input->post('journey_date');
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $sql = $this->db->query("select distinct seats from master_booking where service_no='$service_num' and jdate='$journey_date' and seats like '%$seat_name%' and (status='confirmed' or status='pending') order by id desc LIMIT 1") or die(mysql_error());

        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $seats1 = $row->seats;
                $ss = explode(',', $seats1);
                if (in_array($seat_name, $ss, true)) {
                    $sql1 = $this->db->query("select distinct seats,tkt_no,pnr,status from master_booking where service_no='$service_num' and jdate='$journey_date' and seats='$seats1' and (status='confirmed' or status='pending') order by id desc LIMIT 1") or die(mysql_error());
                }
            }
        }

        foreach ($sql1->result() as $res1) {
            $seats = $res1->seats;
            $tkt_no = $res1->tkt_no;
            $pnr = $res1->pnr;
            $status = $res1->status;
        }

        $seats1 = explode(',', $seats);

        for ($i = 0; $i < count($seats1); $i++) {
            if ($seats1[$i] == $seat_name) {
                $sql2 = $this->db->query("select distinct seat_status from layout_list where service_num='$service_num' and journey_date='$journey_date' and seat_name='$seats1[$i]'") or die(mysql_error());
                foreach ($sql2->result() as $row2) {
                    $seat_status1[] = $row2->seat_status;
                    break;
                }
            }
        }


        /* for ($i = 0; $i < count($seats1); $i++) {
          $sql2 = $this->db->query("select distinct seat_status from layout_list where service_num='$service_num' and journey_date='$journey_date' and seat_name='$seats1[$i]'") or die(mysql_error());
          $row2 = mysql_fetch_array($sql2);
          $seat_status1[] = $row2['seat_status'];
          } */
        //print_r($seat_status1);
        $seat_status2 = array_unique($seat_status1);
        $seat_status = implode(',', $seat_status2);
        //print_r($seat_status);

        if ($seat_status == 1 || $seat_status == 3) {
            echo $tkt_no . "!" . $pnr . "!" . $status;
        } else {
            echo 0;
        }
    }

    function show_landmark() {
        $service_num = $this->input->post('service_num');
        $source_id = $this->input->post('source_id');
        $travel_id = $this->input->post('travel_id');
        $lm = $this->input->post('lm');
        //echo $service_num."#".$source_id."#".$travel_id."#".$lm;
        if ($lm != 0) {
            $stmt = "select * from boarding_points where service_num='$service_num' and city_id='$source_id' and board_or_drop_type='board' and travel_id='$travel_id' and bpdp_id='$lm' order by board_drop";
            $sql = $this->db->query($stmt);
            foreach ($sql->result() as $row) {
                $board = $row->board_drop;
            }
            $board1 = explode('#', $board);
            echo '<i class="icon-map-marker"></i> Landmark : ' . $board1['2'];
        }
    }

    function show_paytype() {
        $journey_date = $this->input->post('journey_date');
        $travel_id = $this->input->post('travel_id');
        $paytyp = $this->input->post('paytyp');

        if ($paytyp == 'byagent' || $paytyp == 'byphoneagent') {

            //echo $service_num."#".$source_id."#".$travel_id."#".$lm;
            echo '<label class="control-label">Agent Name</label>
			<div class="controls"><select name="pay_agent" id="pay_agent">
			<option value="0">-Select-</option>';
            $stmt = "select distinct appname,name,id from agents_operator where operator_id='$travel_id' and agent_type='2'";
            $query = $this->db->query($stmt);

            foreach ($query->result() as $row) {
                $appname = $row->appname;
                $name = $row->name;
                $id = $row->id;
                $agent = $name;

                echo '<option value="' . $id . '">' . $agent . '</option>';
            }
            echo '</select></div>              
                <label class="control-label">Reciept No</label>
                <div class="controls"><input type="text" name="receiptno" id="receiptno" /></div>';
        } else if ($paytyp == 'byemployee') {
            //echo $service_num."#".$source_id."#".$travel_id."#".$lm;
            echo '<label class="control-label">Employee Name</label>
			<div class="controls"><select name="pay_agent" id="pay_agent">
			<option value="0">-Select-</option>';
            $stmt = "select distinct appname,name,id from agents_operator where operator_id='$travel_id' and agent_type='1'";
            $query = $this->db->query($stmt);

            foreach ($query->result() as $row) {
                $appname = $row->appname;
                $name = $row->name;
                $id = $row->id;

                $agent = $name;

                echo '<option value="' . $id . '">' . $agent . '</option>';
            }
            echo '</select></div>';
        } else if ($paytyp == 'byphone') {
            $fb = $this->booking_m->forward_booking();
            //echo $service_num."#".$source_id."#".$travel_id."#".$lm;
            echo '<script>$(function(){
				$( "#reqphone" ).datepicker({autoclose: true});});</script>';
            echo '<label class="control-label"><input type="text" name="reqphone" id="reqphone" value="' . $journey_date . '" readonly="" style="width:80px" /></label>
			<div class="controls"><select name="reqphonehr" id="reqphonehr" style="width:55px;float:left">
			<!--option value="HH">HH</option-->';
            for ($i = 1; $i < 13; $i++) {
                if (strlen($i) == 1) {
                    $j = "0" . $i;
                } else {
                    $j = $i;
                }
                echo '<option value="' . $j . '">' . $j . '</option>';
            }
            echo '</select>
			<select name="reqphonemin" id="reqphonemin" style="width:55px;float:left">
			<option value="MM">MM</option>			
			<option value="00">00</option>
			<option value="05">05</option>
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
            </select>
			<select name="reqphonet" id="reqphonet" style="width:55px;float:left">
			<option value="0">--</option>			          			
     		<option value="AM">AM</option>
			<option value="PM">PM</option>							                                     
            </select></div>';
        }
    }

    function forward_booking() {
        $stmt = "select fwd from registered_operators where travel_id='" . $this->session->userdata('bktravels_travel_id') . "'";
        $query = $this->db->query($stmt);
        foreach ($query->result() as $row) {
            $fwd = $row->fwd;
        }
        return $fwd;
    }

    function gender_check() {
        $onward_seats = $this->input->post('onward_seats');
        $return_seats = $this->input->post('return_seats');
        $trip = $this->input->post('trip');
        $onward_service_num = $this->input->post('onward_service_num');
        $return_service_num = $this->input->post('return_service_num');
        $onward_date = $this->input->post('onward_date');
        $return_date = $this->input->post('return_date');
        $onward_gender = $this->input->post('onward_gender');
        $return_gender = $this->input->post('return_gender');
        $travel_id = $this->input->post('travel_id');
        //echo $onward_seats."#".$return_seats."#".$trip."#".$onward_service_num."#".$return_service_num."#".$onward_date."#".$return_date;
        $fem1 = array();
        $fem = array();
        $fem3 = array();
        $fem4 = array();
        $fem5 = array();
        $gen1 = array();
        $regen1 = array();

        $gender = $onward_gender;
        $re_gen = $return_gender;

        $seat = $onward_seats;
        $reseat = $return_seats;

        $srno = $onward_service_num;
        $resrno = $return_service_num;

        $date = $onward_date;
        $redate = $return_date;

        $tid = $travel_id;
        $retid = $travel_id;


        if ($trip == 'O') {
            $gen = explode(",", $gender);
            $st = explode(",", $seat);

            for ($i = 0; $i < count($st); $i++) {
                //echo $i." ";
                $this->db->select('*');
                $this->db->where('seat_name', $st[$i]);
                $this->db->where('travel_id', $tid);
                $this->db->where('service_num', $srno);
                $this->db->where('journey_date', $date);

                $query = $this->db->get('layout_list');

                foreach ($query->result() as $rows) {
                    $rowno = $rows->col;
                    $colno = $rows->row;
                    $window = $rows->window;
                    $rowinc = $rowno + 1;
                    $rowdec = $rowno - 1;
                    $seat_type = $rows->seat_type;

                    //echo $rowno." ".$colno." ";

                    $this->db->select_max('col', 'mcol');
                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);
                    $query12 = $this->db->get('layout_list');

                    foreach ($query12->result() as $rows1) {
                        $rowmax = $rows1->mcol;
                    }

                    $this->db->select_min('col', 'mcol1');
                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);
                    $query11 = $this->db->get('layout_list');

                    foreach ($query11->result() as $rows2) {
                        $rowmin = $rows2->mcol1;
                        $rowmininc = $rowmin + 1;
                    }

                    $this->db->select('is_ladies');
                    $this->db->where('row', $colno);

                    if ($window == 1 && $rowno != 1) {
                        $this->db->where('col', $rowdec);
                    } else if ($window != 1 && $rowno == $rowmininc) {
                        $this->db->where('col', $rowdec);
                    } else if ($window == 1 && $rowno == $rowmax) {
                        $this->db->where('col', $rowdec);
                    } else {
                        $this->db->where('col', $rowinc);
                    }

                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);
                    $this->db->where('seat_type', $seat_type);
                    //echo $st[$i];
                    $query1 = $this->db->get('layout_list');
                    //echo $this->db->last_query();
                    foreach ($query1->result() as $rows3) {
                        $fem[] = $rows3->is_ladies;
                        //echo $fem."#".$st[$i]."|";
                        //echo "fem is : ".$fem;
                    }
                }
            }

            // print_r ($fem);
            $z = count($fem);
            //echo $z;
            $s = "";

            for ($i = 0; $i < $z; $i++) {
                //echo "fem is ".$fem[$i]."# ";
                //echo "seat is ".$st[$i]."#";
                $onewayGen = $gen[$i];
                //echo $onewayGen;
                if ($fem[$i] == 1 && $onewayGen == 'M') {
                    if ($s == "") {
                        $s = $st[$i];
                    } else {
                        $s = $s . "," . $st[$i];
                    }

                    $fem1[] = $fem[$i];
                    $gen1[] = $gen[$i];
                }
            }
            //print_r($fem1);
            $fem2 = array();
            $fem2 = array_unique($fem1);
            $fem22 = implode(',', $fem2);
            //echo "fems[0] is ".$fem2[0];
            //print_r($fem2);

            $gen2 = array();
            $gen2 = array_unique($gen1);
            //echo $s;
            if ($fem2 == 1 && $gen2 == 'M') {
                break;
            }

            if ($fem22 == 1) {
                //echo 'if';    
                echo "O!" . $s . "#"; //ladies seat
            } else {
                echo 1;
            }
        }
        if ($trip == 'R') {
            $gen = explode(",", $gender);
            $st = explode(",", $seat);

            for ($i = 0; $i < count($st); $i++) {
                //$onewayGen = $gen[$i];
                //echo $onewayGen."#";

                $this->db->select('*');
                $this->db->where('seat_name', $st[$i]);
                $this->db->where('travel_id', $tid);
                $this->db->where('service_num', $srno);
                $this->db->where('journey_date', $date);

                $query = $this->db->get('layout_list');

                foreach ($query->result() as $rows) {
                    $rowno = $rows->col;
                    $colno = $rows->row;
                    $window = $rows->window;
                    $rowinc = $rowno + 1;
                    $rowdec = $rowno - 1;

                    $this->db->select_max('col', 'mcol');
                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);

                    $query12 = $this->db->get('layout_list');

                    foreach ($query12->result() as $rows) {
                        $rowmax = $rows->mcol;
                    }

                    $this->db->select_min('col', 'mcol1');
                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);

                    $query11 = $this->db->get('layout_list');

                    foreach ($query11->result() as $rows) {
                        $rowmin = $rows->mcol1;
                        $rowmininc = $rowmin + 1;
                    }

                    $this->db->select('is_ladies');
                    $this->db->where('row', $colno);

                    if ($window == 1 && $rowno != 1) {
                        $this->db->where('col', $rowdec);
                    } else if ($window != 1 && $rowno == $rowmininc) {
                        $this->db->where('col', $rowdec);
                    } else if ($window == 1 && $rowno == $rowmax) {
                        $this->db->where('col', $rowdec);
                    } else {
                        $this->db->where('col', $rowinc);
                    }

                    $this->db->where('travel_id', $tid);
                    $this->db->where('service_num', $srno);
                    $this->db->where('journey_date', $date);

                    $query1 = $this->db->get('layout_list');

                    foreach ($query1->result() as $rows) {
                        $fem[] = $rows->is_ladies;
                        //echo $fem."#".$st[$i]."|";
                        //echo "fem is : ".$fem;
                    }
                }
            }
            //print_r ($fem);
            $z = count($fem);
            //echo $z;
            $s = "";

            for ($i = 0; $i < $z; $i++) {
                //echo "fem is ".$fem[$i]."# ";
                //echo "seat is ".$st[$i]."#";
                $onewayGen = $gen[$i];

                if ($fem[$i] == 1 && $onewayGen == 'M') {
                    if ($s == "") {
                        $s = $st[$i];
                    } else {
                        $s = $s . "," . $st[$i];
                    }

                    $fem1[] = $fem[$i];
                    $gen1[] = $gen[$i];
                }
            }
            $fem2 = array();
            $fem2 = array_unique($fem1);
            $fem22 = implode(',', $fem2);

            $gen2 = array();
            $gen2 = array_unique($gen1);

            //echo $s;

            if ($fem2 == 1 && $gen2 == 'M') {
                break;
            }

            if ($fem22 == 1) {
                //echo 'if';    
                $res = "O!" . $s . "#"; //ladies seat
            } else {
                $res = "";
            }

            $regen = explode(",", $re_gen);
            $st1 = explode(",", $reseat);

            for ($i = 0; $i < count($st1); $i++) {
                //$returnGen = $regender[$i];
                //echo $returnGen."#";

                $this->db->select('*');
                $this->db->where('seat_name', $st1[$i]);
                $this->db->where('travel_id', $retid);
                $this->db->where('service_num', $resrno);
                $this->db->where('journey_date', $redate);

                $query2 = $this->db->get('layout_list');

                foreach ($query2->result() as $rows) {
                    $rowno1 = $rows->col;
                    $colno1 = $rows->row;
                    $window1 = $rows->window;
                    $rowinc1 = $rowno1 + 1;
                    $rowdec1 = $rowno1 - 1;

                    $this->db->select_max('col', 'mcol');
                    $this->db->where('travel_id', $retid);
                    $this->db->where('service_num', $resrno);
                    $this->db->where('journey_date', $redate);

                    $query13 = $this->db->get('layout_list');

                    foreach ($query13->result() as $rows) {
                        $rowmax1 = $rows->mcol;
                    }

                    $this->db->select_min('col', 'mcol1');
                    $this->db->where('travel_id', $retid);
                    $this->db->where('service_num', $resrno);
                    $this->db->where('journey_date', $redate);

                    $query14 = $this->db->get('layout_list');

                    foreach ($query14->result() as $rows) {
                        $rowmin1 = $rows->mcol1;
                        $rowmininc1 = $rowmin1 + 1;
                    }

                    $this->db->select('is_ladies');
                    $this->db->where('row', $colno1);

                    if ($window1 == 1 && $rowno1 != 1) {
                        $this->db->where('col', $rowdec1);
                    } else if ($window1 != 1 && $rowno1 == $rowmininc1) {
                        $this->db->where('col', $rowdec1);
                    } else if ($window1 == 1 && $rowno1 == $rowmax1) {
                        $this->db->where('col', $rowdec1);
                    } else {
                        $this->db->where('col', $rowinc1);
                    }

                    $this->db->where('travel_id', $retid);
                    $this->db->where('service_num', $resrno);
                    $this->db->where('journey_date', $redate);

                    $query3 = $this->db->get('layout_list');

                    foreach ($query3->result() as $rows) {
                        $fem3[] = $rows->is_ladies;
                        //echo $fem."#".$st[$i]."|";
                        //echo "fem is : ".$fem;
                    }
                }
            }
            //print_r ($fem3);
            $z = count($fem3);
            //echo $z;
            $s1 = "";

            for ($i = 0; $i < $z; $i++) {

                //echo "fem is ".$fem[$i]."# ";
                //echo "seat is ".$st[$i]."#";
                $returnGen = $regen[$i];
                if ($fem3[$i] == 1 && $returnGen == 'M') {
                    if ($s1 == "") {
                        $s1 = $st1[$i];
                    } else {
                        $s1 = $s1 . "," . $st1[$i];
                    }

                    $fem4[] = $fem3[$i];
                    $regen1[] = $regen[$i];
                }
            }

            //$fem2  =  array();
            $fem5 = array_unique($fem4);
            $fem55 = implode(',', $fem5);

            $regen2 = array();
            $regen2 = array_unique($regen1);
            //echo $s;
            if ($fem5 == 1 && $regen2 == 'M') {
                break;
            }

            if ($fem55 == 1) {
                //echo 'elseif';
                echo $res . "R!" . $s1;
            } else {
                echo 1;
            }
        }
    }

    function blocked() {
        $onward_seats1 = $this->input->post('onward_seats');
        $return_seats = $this->input->post('return_seats');
        $trip = $this->input->post('trip');
        $onward_service_num = $this->input->post('onward_service_num');
        $return_service_num = $this->input->post('return_service_num');
        $onward_date = $this->input->post('onward_date');
        $return_date = $this->input->post('return_date');
        $paytyp = $this->input->post('paytyp');
        $reqphone = $this->input->post('reqphone');
        $reqphonehr = $this->input->post('reqphonehr');
        $reqphonemin = $this->input->post('reqphonemin');
        $reqphonet = $this->input->post('reqphonet');
        $key = $this->input->post('key');
        //echo $onward_seats."#".$return_seats."#".$trip."#".$onward_service_num."#".$return_service_num."#".$onward_date."#".$return_date;        
        date_default_timezone_set('Asia/Kolkata');
        if ($trip == "O") {
            $onward_seats = explode(',', $onward_seats1);
            $z = count($onward_seats);
            $reqphone1 = date("Y-m-d", strtotime($reqphone));

            for ($i = 0; $i < $z; $i++) {
                if ($paytyp == "byphone") {
                    $stmt = "select seat_status,blocked_time from layout_list where service_num='$onward_service_num' and journey_date='$reqphone1' and seat_name='$onward_seats[$i]'";
                } else {
                    $stmt = "select seat_status,blocked_time from layout_list where service_num='$onward_service_num' and journey_date='$onward_date' and seat_name='$onward_seats[$i]'";
                }
                $query = $this->db->query($stmt)or die(mysql_error());

                foreach ($query->result() as $row) {
                    $seat_status = $row->seat_status;
                    $blocked_time = $row->blocked_time;
                    $blockedtime = date('Y-m-d H:i:s');
                }
                //echo $paytyp."#".$onward_seats1."#".$seat_status;

                if ($paytyp == "byphone") {
                    $release_time = date("Y-m-d", strtotime($reqphone)) . " " . $reqphonehr . ":" . $reqphonemin . ":00";

                    if ($seat_status == "0") {
                        if ($key == "S") {
                            $stmt1 = "update layout_list set seat_status='3',blocked_time='$blockedtime',release_time ='$release_time',status1='pending' where journey_date='$reqphone1' and service_num='$onward_service_num' and seat_name='$onward_seats[$i]' and seat_status='0'";
                            $this->db->query($stmt1)or die(mysql_error());
                        }
                        $onward_seat_name1[] = "true";
                    } else {
                        $onward_seat_name1[] = $onward_seats[$i];
                    }
                } else {
                    if ($seat_status == "0") {
                        if ($key == "S") {
                            $stmt1 = "update layout_list set seat_status='2',blocked_time='$blockedtime' where journey_date='$onward_date' and service_num='$onward_service_num' and seat_name='$onward_seats[$i]' and seat_status='0'";
                            $this->db->query($stmt1)or die(mysql_error());
                        }
                        $onward_seat_name1[] = "true";
                    } else {
                        $onward_seat_name1[] = $onward_seats[$i];
                    }
                }
            }
            $y = array_unique($onward_seat_name1);
            $onward_seat_name = implode(',', $y);

            $return_seat_name1[] = "true";
            $y = array_unique($return_seat_name1);
            $return_seat_name = implode(',', $y);
        }
        echo $onward_seat_name . "#" . $return_seat_name;
        unset($onward_seat_name1);
        unset($return_seat_name1);
    }

    function boarding_points_mouseover() {
        $srv = $this->input->post('srvno');
        $tid = $this->input->post('tid');
        // echo $srv."#".$tid;
        $sql = $this->db->query("select board_drop from boarding_points where service_num='$srv' and travel_id='$tid' and board_or_drop_type='board' order by board_drop");

        echo '<table  border="0" cellpadding="0" cellspacing="0" style="calibri;">
  <tr>
    <td height="25" colspan="5" align="center"><span style="color:#666666; font-weight:bold; font-size:12px;">Departures</span></td>
  </tr>';
        foreach ($sql->result() as $rows) {
            $board_name = $rows->board_drop;
            $board = explode("#", $board_name);
            $b_name = $board[0];
            $con_board1 = $board[1];
            $con_board = date('h:i A', strtotime($con_board1));


            echo '<tr>
    <td height="25" align="left" style="padding-left:12px;">' . $b_name . '</td>
    <td width="4">&nbsp;</td>
    <td height="25" align="left" style="padding-left:7px; padding-right:12px;">' . $con_board . '</td>
  </tr>';
        }
        echo '</table>';
    }

    function dropping_points_mouseover() {
        $srv = $this->input->post('srvno');
        $tid = $this->input->post('tid');
        // echo $srv."#".$tid;
        $sql = $this->db->query("select board_drop from boarding_points where service_num='$srv' and travel_id='$tid' and board_or_drop_type='drop' order by board_drop");

        echo '<table  border="0" cellpadding="0" cellspacing="0" style="calibri;">
  <tr>
    <td height="25" colspan="5" align="center"><span style="color:#666666; font-weight:bold; font-size:12px;">Arrivals</span></td>
  </tr>';
        foreach ($sql->result() as $rows) {
            $drop_name = $rows->board_drop;
            $drop = explode("#", $drop_name);
            $drop_name1 = $drop[0] . "  " . $drop[1];

            echo '<tr>
    <td height="25" align="left" style="padding-left:12px;calibri;font-size:12px;">' . $drop_name1 . '</td>
    <td width="4">&nbsp;</td>
  </tr>';
        }
        echo '</table>';
    }

    function service_mouseover() {
        $srv = $this->input->post('srvno');
        $tid = $this->input->post('tid');
        // echo $srv."#".$tid;
        $stmt = "select from_name,to_name,seat_fare,lberth_fare,uberth_fare,bus_type from master_buses where service_num='$srv' and travel_id='$tid'";
        $sql = $this->db->query($stmt);

        echo '<table  border="0" cellpadding="0" cellspacing="0" style="calibri;" width="100%">
  <tr>
    <td height="25" colspan="5" align="center"><span style="color:#666666; font-weight:bold; font-size:12px;">Service</span></td>
  </tr>
  <tr>
                    <td height="25" align="left" style="padding-left:5px;">Service Name </td>
                    <td>&nbsp;</td>
                    <td height="25" align="left" style="padding-left:5px;">Fare</td>
                  </tr>';
        foreach ($sql->result() as $rows) {
            $from_name = $rows->from_name;
            $to_name = $rows->to_name;
            $bus_type = $rows->bus_type;
            echo'<tr>
    <td height="25" align="left" style="padding-left:5px;">' . $from_name . ' - ' . $to_name . '</td>
    <td width="4">&nbsp;</td>
    <td height="25" align="left" style="padding-left:5px;">';
            if ($bus_type == "seater") {
                $fare = $rows->seat_fare;
            } else if ($bus_type == "sleeper") {
                $fare = $rows->lberth_fare . "/" . $rows->uberth_fare;
            } else if ($bus_type == "seatersleeper") {
                $fare = $rows->seat_fare . "/" . $rows->lberth_fare . "/" . $rows->uberth_fare;
            }
            echo $fare;
            echo'</td>
  </tr>';
        }
        echo '</table>
';
    }

    function booked() {
        $onward_date = $this->input->post('onward_date');
        //echo "$onward_date";
        if ($onward_date < date("Y-m-d")) {
            return 2;
        } else {
            $k = $this->input->post('j');
            $travel_id = $this->session->userdata('bktravels_travel_id');
            $agent_id1 = $this->session->userdata('bktravels_user_id');
            $agent_type = $this->session->userdata('bktravels_agent_type');
            $name = $this->session->userdata('bktravels_name');
            $agent_type_name = $this->session->userdata('bktravels_agent_type_name');
            //$is_pay =  $this->session->userdata('is_pay');
            $op_comm = $this->session->userdata('bktravels_op_comm');
            $pay_agent = $this->input->post('pay_agent');
            $trip = $this->input->post('trip');
            //$pay_phone = $this->input->post('phonebook');
            $onward_pass = $this->input->post('onward_pass');
            $paytyp = $this->input->post('paytyp' . $trip . $k);

            /*             * *********** based on selection of payment details in agent ** */
            if (($paytyp == 'bycash' || $paytyp == 'byphone') && $agent_type == '1') {
                $agent_id = $agent_id1;
            } else if ($paytyp == 'byagent' || $paytyp == 'byphoneagent') {
                $agent_id = $pay_agent;
                $agent_type = 2;
            } else if ($paytyp == 'byemployee') {
                $agent_id = $pay_agent;
            } else {
                $agent_id = $agent_id1;
            }
            $book_pay_agent = $agent_id1;
            /*             * *********** based on selection of payment details in agent close ** */

            //checking for agent balance.
            if ($paytyp == 'byagent' || $paytyp == 'byphoneagent' || $paytyp == 'byemployee') {
                $stmt = "select * from agents_operator where id='$agent_id' and  operator_id='$travel_id'";
            } else {
                $stmt = "select * from agents_operator where id='$agent_id' and agent_type='$agent_type' and operator_id='$travel_id'";
            }
            $query = $this->db->query($stmt) or die(mysql_error());

            $query21 = $this->db->query("select * from operator_commission where agent_id='$agent_id' and travel_id='$travel_id'") or die(mysql_error());
            //$res1 = mysql_fetch_array($query21);

            foreach ($query->result() as $res) {
                $bal = $res->balance;
                $limit = $res->bal_limit;
                $margin = $res->margin;
                $pay_type = $res->pay_type;
                $comm_type = $res->comm_type;
            }
            /* if($travel_id==$travelid)
              {
              $bal=$res->balance;
              $limit=$res->bal_limit;
              $margin=$res->margin;
              $pay_type=$res->pay_type;
              }
              else
              {
              $bal_os=$res->balance1;
              $limit=$res->bal_limit1;
              $pay_type_os=$res->pay_type1;
              if($op_comm=='yes')
              {
              $margin_os=$res1->commission;
              }
              else
              {
              $margin_os=$res->margin1;
              }
              } */

            if ($trip == "") {
                print "<script type=\"text/javascript\">window.location = '" . base_url('booking') . "'</script>";
            }

            if ($trip == "O") {
                $onward_fare = $this->input->post('onward_fare');
                $fare = $onward_fare; //seatfare+convenience_charge+tax-discount
                $onward_travel_id = $this->input->post('onward_travel_id');
                $onward_base_fare = $this->input->post('base_fare' . $trip . $k); //seatfare
                $onward_discount_amount = $this->input->post('discount' . $trip . $k); //discount
                $onward_service_tax_amount = $this->input->post('service_tax' . $trip . $k); //servicetax
                $onward_convenience_charge = $this->input->post('convenience_charge' . $trip . $k); //convenience_charge
                $agentcharge = $this->input->post('agentcharge' . $way . $k); //agentcharge

                $base_fare = $onward_base_fare - $onward_discount_amount;

                if ($agentcharge != "") {
                    $onward_discount_amount = $agentcharge;
                }
                //echo "onward_fare : ".$onward_fare." onward_travel_id : ".$onward_travel_id." onward_base_fare : ".$onward_base_fare." onward_discount_amount : ".$onward_discount_amount." onward_service_tax_amount : ".$onward_service_tax_amount." onward_service_tax_amount : ".$onward_service_tax_amount." onward_convenience_charge : ".$onward_convenience_charge." base_fare : ".$base_fare;
            } else if ($trip == "R") {
                $onward_fare = $this->input->post('onward_fare');
                $return_fare = $this->input->post('return_fare');

                $fare = $onward_fare + $return_fare;
            }

            // checking the balance..
            /* if ($travel_id == $onward_travel_id) { */
            if ($pay_type == 'postpaid') {
                if ($comm_type == "percent") {
                    if ($onward_fare > $base_fare) {
                        $save = ($base_fare * $margin) / 100;
                    } else {
                        $save = ($onward_fare * $margin) / 100;
                    }
                } else {
                    $save = $margin * $onward_pass;
                }

                if ($onward_fare > $base_fare) {
                    $fare1 = $base_fare - $save;
                } else {
                    $fare1 = $onward_fare - $save;
                }
                $paid = $fare1;
                $bal1 = $bal - $fare1;
            } else {
                if ($onward_fare > $base_fare) {
                    $bal1 = $bal - $base_fare;
                    $fare = $base_fare;
                } else {
                    $bal1 = $bal - $onward_fare;
                    $fare = $onward_fare;
                }
                $paid = $fare;
                $save = '0';
            }
            //echo "bal1 : ".$bal1." paid : ".$paid." save : ".$save;
            /* } else {

              $save = ($base_fare * $margin_os) / 100;
              $fare1 = $base_fare - $save;
              $paid = $fare1;
              $bal1 = $bal_os - $fare1;
              } */
            //echo $bal1."bal1".$limit."limit".$agent_type;
            //allowing for booking based on limit and balance.        
            if (($bal1 > $limit || ($agent_type == 1 && $travel_id == $onward_travel_id))) {
                $name = $this->input->post('name');
                $mobile = $this->input->post('mobile' . $trip . $k);
                $altph = $this->input->post('altph' . $trip . $k);
                $email = $this->input->post('email' . $trip . $k);
                $address = $this->input->post('address');
                $cardtype = $_POST->cardtype;
                $cardnum = $_POST->cardnum;
                $issuer = $_POST->issuer;
                $onward_way = $this->input->post('onward_way');
                $return_way = $this->input->post('return_way');
                $trip = $this->input->post('trip');
                $idtype = $this->input->post('idtype');
                $idno = $this->input->post('idno');
                $booked_date = date('Y-m-d');
                $booked_time = date('Y-m-d H-i-s');
                $ip = $_SERVER['REMOTE_ADDR'];

                if ($onward_way == "O") {
                    $onward_seats = $this->input->post('onward_seats');
                    $onward_fare = $this->input->post('onward_fare');
                    $onward_bpid = $this->input->post('onward_bpid');
                    $onward_pass = $this->input->post('onward_pass');
                    $onward_start_time = $this->input->post('onward_start_time');
                    $onward_arr_time = $this->input->post('onward_arr_time');
                    $onward_source_id = $this->input->post('onward_source_id');
                    $onward_destination_id = $this->input->post('onward_destination_id');
                    $onward_source_name = $this->input->post('onward_source_name');
                    $onward_destination_name = $this->input->post('onward_destination_name');
                    $onward_date = $this->input->post('onward_date');
                    $onward_service_num = $this->input->post('onward_service_num');
                    $onward_bus_type = $this->input->post('onward_bus_type');
                    $onward_model = $this->input->post('onward_model');
                    $onward_bpid = $this->input->post('onward_bp');
                    $onward_dpid = $this->input->post('onward_dp');
                    $j = $this->input->post('j');
                    $cnt = $this->input->post('cnt');
                    $key = $this->input->post('key');
                    $receiptno = $this->input->post('receiptno');

                    $reqphone = date('Y-m-d', strtotime($this->input->post('reqphone')));
                    $onward_seats1 = explode(',', $onward_seats);
                    $statusinfo = array();
                    for ($i = 0; $i < count($onward_seats1); $i++) {
                        $sql = $this->db->query("select seat_status from layout_list where service_num='$onward_service_num' and journey_date='$onward_date' and seat_name='$onward_seats1[$i]'");
                        foreach ($sql->result() as $status) {
                            $statusinfo[] = $status->seat_status;
                        }
                    }
                    
                    if (count(array_unique($statusinfo)) === 1 && (end($statusinfo) === '2' || end($statusinfo) === '3')) {                        
                        //echo "reqphone ".$reqphone;
                        $bp = $this->db->query("select * from boarding_points where service_num='$onward_service_num' and city_id='$onward_source_id' and board_or_drop_type='board' and travel_id='$onward_travel_id' and bpdp_id='$onward_bpid'");
                        foreach ($bp->result() as $bp1) {
                            $board = $bp1->board_drop;
                        }
                        $bplm = explode('#', $board);
                        $onward_bp = $bplm[0] . '-' . (date('h:i A', strtotime($bplm[1])));
                        $onward_lm = $bplm[2];

                        $dp = $this->db->query("select * from boarding_points where service_num='$onward_service_num' and city_id='$onward_destination_id' and board_or_drop_type='drop' and travel_id='$onward_travel_id' and bpdp_id='$onward_dpid'");
                        foreach ($dp->result() as $dp1) {
                            $onward_dp = $dp1->board_drop;
                        }

                        if (($paytyp == "byagent" || $paytyp == "byphoneagent" || $paytyp == "byemployee") && $pay_agent != "") {
                            $agentid = $pay_agent;
                        } else {
                            $agentid = $agent_id1;
                        }

                        $psgname = array();
                        $psgage = array();
                        $psggen = array();

                        for ($n = 0; $n < $onward_pass; $n++) {
                            if ($_POST['onward_name' . $n] != "") {
                                array_push($psgname, $_POST['onward_name' . $n]);
                            } else {
                                array_push($psgname, $name);
                            }
                            if ($_POST['onward_age' . $n] != "") {
                                array_push($psgage, $_POST['onward_age' . $n]);
                            } else {
                                array_push($psgage, $_POST['onward_age0']);
                            }

                            array_push($psggen, $_POST['onward_sex' . $n]);
                        }

                        $parr = array_filter($psgname);
                        $onward_names = implode(",", $parr);

                        $sarr = array_filter($psgage);
                        $onward_ages = implode(",", $sarr);

                        $aarr = array_filter($psggen);
                        $onward_genders = implode(",", $aarr);

                        $sql = $this->db->query("select max(id) as id from master_booking");
                        foreach ($sql->result() as $row) {
                            $id = $row->id;
                        }
                        $id1 = $id + 1;
                        $sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$onward_travel_id'");

                        foreach ($sql5->result()as $row5) {
                            $onward_travels = $row5->operator_title;
                            $tk_no = $row5->tkt_no;
                            $op_url = $row5->op_url;
                            $op_email = $row5->op_email;
                            $ph = $onward_lm . "" . $row5->other_contact;
                            $senid = $row5->sender_id;
                        }
                        $onward_tktno = $tk_no . $id1;
                        $onward_pnr = $tk_no . $id1;
                        $onward_refno = $tk_no . $id1;

                        if ($paytyp == "byphone") {
                            //inserting proceed details
                            $this->db->query("insert into master_proceed_details(travel_id,custname,ip,refno,email,mobile,source,dest,jdate,bdate,pass,fare,paid,brdpt,drpt,seatno,tim,altph,service_no,tktno,pnr,book_pay_type,book_pay_agent) values('$onward_travel_id','$onward_names','$ip','$onward_refno','$email','$mobile','$onward_source_name','$onward_destination_name','$reqphone','$booked_date','$onward_pass','$onward_fare','$onward_fare','$onward_bp','$onward_dp','$onward_seats','$booked_time','','$onward_service_num','$onward_tktno','$onward_pnr','$paytyp','$book_pay_agent')") or die(mysql_error());

                            //inserting master booking details..				
                            $sql1 = $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,dpid,drop_point,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,base_fare,service_tax_amount,discount_amount,convenience_charge,pname,pemail,pmobile,age,refno,status,pass,travel_id,ip,time,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,bus_model,book_pay_type,book_pay_agent) values('$onward_tktno','$onward_pnr','$onward_service_num','$onward_bp','$onward_bpid','$onward_lm','$onward_dpid','$onward_dp','$onward_source_name','$onward_destination_name','$onward_travels','$onward_bus_type','$booked_date','$reqphone','$onward_seats','$onward_genders','$onward_start_time','$onward_arr_time','$paid','$save','$onward_fare','$base_fare','$onward_service_tax_amount','$onward_discount_amount','$onward_convenience_charge','$onward_names','$email','$mobile','$onward_ages','$onward_refno','pending','$onward_pass','$onward_travel_id','$ip','$booked_time','$cardtype','$cardnum','$address','$altph','$onward_source_id','$onward_destination_id','$agent_type','$agentid','$onward_model','$paytyp','$book_pay_agent')") or die(mysql_error());
                            $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,net_amt,bal,dat,ip,agent_id,travel_id,status,book_pay_type,book_pay_agent) values('$onward_tktno','$onward_pnr','$onward_names','$onward_source_name','$onward_destination_name','$booked_time','Debit','$onward_fare','$save','$paid','$bal1','$booked_time','$ip','$agentid','$travel_id','pending','$paytyp','$book_pay_agent')") or die(mysql_error());

                            $reqphonehr = $this->input->post('reqphonehr');
                            $reqphonemin = $this->input->post('reqphonemin');
                            $reqphonet = $this->input->post('reqphonet');

                            if ($reqphonet == "PM") {
                                if ($reqphonehr != 12) {
                                    $reqphonehr = $reqphonehr + 12;
                                }
                            }

                            $release_time = $reqphone . " " . $reqphonehr . ":" . $reqphonemin . ":00";
                            $msg_time = date("d-m-Y h:i A", strtotime('-1 hour', strtotime($release_time)));

                            $text = "PNR " . $onward_pnr . " " . $onward_source_name . "-" . $onward_destination_name . " on " . $reqphone . " for seats " . $onward_seats . " in " . $onward_travels . " of amount " . $onward_fare . " is pending, Pay before " . $msg_time . " to confirm ticket.";

                            $user = "pridhvi@msn.com:activa1525@";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senid&receipientno=$mobile&msgtxt=$text");
                            $buffer = curl_exec($ch);
                            $x = explode('=', $buffer);
                            $y = $x[1];
                            $z = explode(',', $y);
                            $stat = $z[0];

                            if ($stat == 0) {
                                $msg = "sent";
                            } else {
                                $msg = "notsent";
                            }
                            curl_close($ch);
                        } else if ($paytyp == "smsinvoice") {
                            if($email == '') {
                                $email = 'info@ticketengine.in';
                            }
                            
                            $blockedtime = date('Y-m-d H:i:s');
                            $release_time = date("Y-m-d H:i:s", strtotime('+2 hours', strtotime($blockedtime)));

                            $ss1 = explode(',', $onward_seats);

                            for ($k = 0; $k < $onward_pass; $k++) {
                                $sea = $ss1[$k];
                                $pgen = $psggen[$k];

                                if ($pgen == 'M') {
                                    $pgen1 = 0;
                                } else {
                                    $pgen1 = 1;
                                }

                                $sql = $this->db->query("update layout_list set seat_status='3',blocked_time='$blockedtime',release_time ='$release_time',status1='pending' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'") or die(mysql_error());
                            }
                            //inserting proceed details
                            $this->db->query("insert into master_proceed_details(travel_id,custname,ip,refno,email,mobile,source,dest,jdate,bdate,pass,fare,paid,brdpt,drpt,seatno,tim,altph,service_no,tktno,pnr,book_pay_type,book_pay_agent) values('$onward_travel_id','$onward_names','$ip','$onward_refno','$email','$mobile','$onward_source_name','$onward_destination_name','$onward_date','$booked_date','$onward_pass','$onward_fare','$onward_fare','$onward_bp','$onward_dp','$onward_seats','$booked_time','','$onward_service_num','$onward_tktno','$onward_pnr','$paytyp','$book_pay_agent')") or die(mysql_error());
                            //inserting master booking details..				
                            $sql1 = $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,dpid,drop_point,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,base_fare,service_tax_amount,discount_amount,convenience_charge,pname,pemail,pmobile,age,refno,status,pass,travel_id,ip,time,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,pay,bus_model,book_pay_type,book_pay_agent) values('$onward_tktno','$onward_pnr','$onward_service_num','$onward_bp','$onward_bpid','$onward_lm','$onward_dpid','$onward_dp','$onward_source_name','$onward_destination_name','$onward_travels','$onward_bus_type','$booked_date','$onward_date','$onward_seats','$onward_genders','$onward_start_time','$onward_arr_time','$paid','$save','$onward_fare','$base_fare','$onward_service_tax_amount','$onward_discount_amount','$onward_convenience_charge','$onward_names','$email','$mobile','$onward_ages','$onward_refno','pending','$onward_pass','$onward_travel_id','$ip','$booked_time','$cardtype','$cardnum','$address','$altph','$onward_source_id','$onward_destination_id','$agent_type','$agentid','junosms','$onward_model','$paytyp','$book_pay_agent')") or die(mysql_error());
                            $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,net_amt,bal,dat,ip,agent_id,travel_id,status,book_pay_type,book_pay_agent) values('$onward_tktno','$onward_pnr','$onward_names','$onward_source_name','$onward_destination_name','$booked_time','Debit','$onward_fare','$save','$paid','$bal1','$booked_time','$ip','$agentid','$travel_id','pending','$paytyp','$book_pay_agent')") or die(mysql_error());

                            $onward_name = explode(',', $onward_names);
                            $merchantid = "101";
                            $cur = "INR";
                            $productinfo = "Bus Ticket";
                            $response_url = base_url('booking/junoresponse');

                            $hash_data = $merchantid . $onward_fare . $productinfo . $mobile . $email . $onward_refno;
                            $secret_key = 'j016!elvc3t3x0ru$v!v505ud7yio@t8b3twmq4';
                            $hash = hash_hmac('sha512', $hash_data, $secret_key);
                            $nettype = '';
                            $subtype = 'SMS';
                            $udf2 = $onward_name[0].';'.$onward_source_name . '_' . $onward_destination_name . ';' . $onward_genders . ';' . $onward_seats . ';' . $onward_date;

                            $data1 = array("merchantid" => $merchantid, "txnid" => $onward_refno, "amount" => $onward_fare, "cur" => $cur, "productinfo" => $productinfo, "firstname" => $onward_name[0], "email" => $email, "msisdn" => $mobile, "surl" => $response_url, "furl" => $response_url, "hash" => $hash, "nettype" => $nettype, "subtype" => $subtype, "udf2" => $udf2);
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://onej.uno/JunoFS/JunoPurchase");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data1));
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            $result = curl_exec($ch);
                            //var_dump($result);
                        } else {

                            $ss1 = explode(',', $onward_seats);

                            for ($k = 0; $k < $onward_pass; $k++) {
                                $sea = $ss1[$k];
                                $pgen = $psggen[$k];

                                if ($pgen == 'M') {
                                    $pgen1 = 0;
                                } else {
                                    $pgen1 = 1;
                                }

                                $sql = $this->db->query("update layout_list set seat_status='1',is_ladies='$pgen1' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'") or die(mysql_error());
                            }
                            if ($travel_id == $onward_travel_id) {
                                $sql7 = $this->db->query("update agents_operator set balance='$bal1' where id='$agentid' and operator_id='$travel_id' ") or die(mysql_error());
                            } else {
                                $sql7 = $this->db->query("update agents_operator set balance1='$bal1' where id='$agentid' and operator_id='$travel_id' ") or die(mysql_error());
                            }

                            $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'") or die(mysql_error());

                            foreach ($sql3->result() as $row3) {
                                $as1 = $row3->available_seats;
                                $as = $as1 - $onward_pass;
                            }

                            $sql4 = $this->db->query("update buses_list set available_seats='$as' where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'") or die(mysql_error());

                            //inserting proceed details
                            $proceed = $this->db->query("insert into master_proceed_details(travel_id,custname,ip,refno,email,mobile,source,dest,jdate,bdate,pass,fare,paid,brdpt,drpt,seatno,tim,altph,service_no,book_pay_type,book_pay_agent) values('$onward_travel_id','$onward_names','$ip','$onward_refno','$email','$mobile','$onward_source_name','$onward_destination_name','$onward_date','$booked_date','$onward_pass','$onward_fare','$onward_fare','$onward_bp','$onward_dp','$onward_seats','$booked_time','','$onward_service_num','$paytyp','$book_pay_agent')") or die(mysql_error());

                            //inserting master booking details..				
                            $sql1 = $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,dpid,drop_point,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,base_fare,service_tax_amount,discount_amount,convenience_charge,pname,pemail,pmobile,age,refno,status,pass,travel_id,ip,time,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,bus_model,book_pay_type,book_pay_agent,receiptno) values('$onward_tktno','$onward_pnr','$onward_service_num','$onward_bp','$onward_bpid','$onward_lm','$onward_dpid','$onward_dp','$onward_source_name','$onward_destination_name','$onward_travels','$onward_bus_type','$booked_date','$onward_date','$onward_seats','$onward_genders','$onward_start_time','$onward_arr_time','$paid','$save','$onward_fare','$base_fare','$onward_service_tax_amount','$onward_discount_amount','$onward_convenience_charge','$onward_names','$email','$mobile','$onward_ages','$onward_refno','confirmed','$onward_pass','$onward_travel_id','$ip','$booked_time','$cardtype','$cardnum','$address','$altph','$onward_source_id','$onward_destination_id','$agent_type','$agentid','$onward_model','$paytyp','$book_pay_agent','$receiptno')") or die(mysql_error());
                            $sql6 = $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,net_amt,bal,dat,ip,agent_id,travel_id,status,book_pay_type,book_pay_agent) values('$onward_tktno','$onward_pnr','$onward_names','$onward_source_name','$onward_destination_name','$booked_time','Debit','$onward_fare','$save','$paid','$bal1','$booked_time','$ip','$agentid','$travel_id','confirmed','$paytyp','$book_pay_agent')") or die(mysql_error());

                            $user = "pridhvi@msn.com:activa1525@";
                            $receipientno = "$mobile";
                            $senderID = $senid;

                            $text = "TKT No: " . $onward_tktno . "->" . $onward_travels . "-" . $onward_source_name . "-" . $onward_destination_name . "->" . $onward_service_num . " , DOJ: " . $onward_date . " , Seats: " . $onward_seats . " , At-" . $onward_bp . " , Ph: " . $ph . "";

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

                            if ($stat == 0) {
                                $msg = "sent";
                            } else {
                                $msg = "notsent";
                            }

                            curl_close($ch);

                            /* if ($this->session->userdata('travel_id') == $onward_travel_id) {
                              $img = base_url("images/logo.png");
                              } else { */
                            //$img = "http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png"
                            //}

                            $subject = "Ticket Confirmation";
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
                            $headers .= 'From: ' . $onward_travels . ' <' . $op_email . '>' . "\r\n";
                            $headers .= 'Reply-To: ' . $op_email . '' . "\r\n";

                            $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: ' . $onward_travels . ' ::</title>
</head>

<body>
<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
  <tbody>
	<tr align="left" valign="top">
	  <td colspan="6" style="color:#c42124;padding-left:5px;font-size:13px;">
	  <img src="http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png" />
	  	</td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;
	  
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">
	  <strong>Onward Journey Details</strong>
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;	  
	  </td>
    </tr>     
    <tr align="center" valign="top">
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Passenger Name :  ' . $name . '</strong></td>
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Ticket No : ' . $onward_tktno . ' </strong></td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Travel Provider</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;"> ' . $onward_travels . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Journey Date</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_date . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Source</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_source_name . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Destination</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_destination_name . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Seat Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_seats . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;"> Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_names . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Start Time</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_start_time . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">No.Of Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_pass . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Land Mark</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_lm . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Bus Type</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_bus_type . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Status</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Confirmed</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Total Fare</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Rs. ' . $onward_fare . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Boarding Point</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_bp . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Service Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_service_num . '</td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
	</tbody>
    </table>
</body>
</html>
';
                            mail($email, $subject, $message, $headers);
                        }

                        if ($sql1) {
                            $sql2 = $this->db->query("select * from master_booking where tkt_no = '$onward_tktno'");

                            foreach ($sql2->result() as $res) {
                                $onward_bp = $res->board_point;
                                $onward_lm = $res->land_mark;

                                $data['onward_travels'] = $res->travels;
                                $data['onward_name'] = $name;
                                $data['onward_names'] = $res->pname;
                                $data['onward_tktno'] = $res->tkt_no;
                                $data['onward_date'] = $res->jdate;
                                $data['onward_source_name'] = $res->source;
                                $data['onward_destination_name'] = $res->dest;
                                $data['onward_seats'] = $res->seats;
                                $data['onward_pass'] = $res->pass;
                                $data['onward_start_time'] = $res->start_time;
                                $data['onward_bus_type'] = $res->bus_type;
                                $data['onward_land_mark'] = $onward_lm;
                                $data['onward_fare'] = $res->tkt_fare;
                                $data['onward_status'] = $res->status;
                                $data['onward_board_point'] = $onward_bp;
                                $data['onward_service_num'] = $res->service_no;
                                $data['onward_way'] = $onward_way;
                                $data['return_way'] = $return_way;
                                $data['trip'] = $trip;
                                $data['op_url'] = $op_url;
                                $data['op_email'] = $op_email;
                                $data['ph'] = $ph;
                                $data['travel_id'] = $onward_travel_id;
                                $data['j'] = $j;
                                $data['cnt'] = $cnt;
                                $data['key'] = $key;
                                $data['pay_type'] = $paytyp;
                            }
                            return $data;
                        }
                    } else {
                        $booked = 'no';
                        return $booked;
                    }
                }
            } else {
                $limit_exceed = '0';
                return $limit_exceed;
            }
        }
    }

    function confirmed_ticket_db() {
        $onward_tktno = $this->input->get('onward_tktno');
        $onward_date = $this->input->get('onward_date');
        $onward_service_num = $this->input->get('onward_service_num');
        $onward_way = $this->input->get('onward_way');
        $return_tktno = $this->input->get('return_tktno');
        $return_date = $this->input->get('return_date');
        $return_service_num = $this->input->get('return_service_num');
        $return_way = $this->input->get('return_way');
        $travel_id = $this->input->get('travel_id');
        $j = $this->input->get('j');
        $cnt = $this->input->get('cnt');
        $fare = $this->input->get('fare');
        $key = $this->input->get('key');
        $travel_id = $this->input->get('travel_id');
        $pay_type = $this->input->get('pay_type');

        if ($onward_way = "O") {
            $sql = $this->db->query("select * from master_booking where tkt_no='$onward_tktno'");

            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $res) {
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
                    $paid = $res->paid;
                    $save = $res->save;
                    $tkt_fare = $res->tkt_fare;
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

                    $seats1 = explode(',', $seats);
                    $pname1 = explode(',', $pname);
                    $age1 = explode(',', $age);
                    $gender1 = explode(',', $gender);
                    $board_point1 = explode('-', $board_point);

                    $sql5 = $this->db->query("SELECT distinct op_url,op_email,other_contact,canc_terms FROM registered_operators where travel_id='$travel_id'");
                    foreach ($sql5->result() as $row5) {
                        $op_url = $row5->op_url;
                        $op_email = $row5->op_email;
                        $ph = $row5->other_contact;
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
                        } else {
                            foreach ($sql5->result() as $row5) {
                                $canc_terms = $row5->canc_terms;
                            }
                        }
                    }

                    $sql2 = $this->db->query("SELECT name FROM agents_operator where operator_id='$travel_id' and id='$agent_id'");
                    foreach ($sql2->result() as $row2) {
                        $name = $row2->name;
                    }
                    echo '<html>
                  <head>
                  <title>:: ::</title>
                  </head>
			<style type="text/css">
			a {
				text-decoration:none;
				color:#0002CC;
			}	
.btn btn-primary {

    background: #CC3300 none repeat scroll 0% 0%;

    color: #FFF;

    font-size: 15px;

    padding: 3px 25px;

    text-align: center;

    cursor: pointer;

    border: medium none #CC3300;

}
table,th,tr,td
{
    font-size:14px;
	font-family:calibri;
}
			</style>
                        <script src="' . base_url('js/app-js.v1.js') . '" type="text/javascript"></script>
			<script type="text/javascript">
			$(function() {
  				//print(pt); //works
			});
			
			function print(elem)
		    {
        		Popup($(elem).html());
		    }

		    function Popup(data) 
		    {
        		var mywindow = window.open("", "my div", "height=400,width=600");
		        mywindow.document.write("<html><head>");
				mywindow.document.write("<style type=\"text/css\">");
				mywindow.document.write("table,th,tr,td{font-size:15px;font-family:calibri;}");
				mywindow.document.write("</style>");
				mywindow.document.write("<title>My Ticket</title>");        		
		        mywindow.document.write("</head><body >");
        		mywindow.document.write(data);
		        mywindow.document.write("</body></html>");

        		mywindow.print();
		        mywindow.close();

        		return true;
		    }			                    
			</script>
                 </head>
                 <body>
			<div align="right"><a href="' . base_url('booking') . '">Back To Booking</a></div>
			<div align="center">';
                    $ho = $this->session->userdata('bktravels_head_office');
                    if ($status == "confirmed") {
                        echo '<a href="javascript:void()" onClick="javascript:print(\'#pt\');">PRINT TICKET</a> ';
                    }
                    echo '</div><br />
			<div id="pt">
			
<p style="color: #d0d0d0;
  font-size: 130pt;
  -webkit-transform: rotate(-35deg);
  -moz-transform: rotate(-35deg);
  width: 90%;
  z-index: -1;
  position:absolute;
  text-align:center;
  margin-top:325px;
  opacity:0.6;
filter:alpha(opacity=60);">';
                    if ($status == "confirmed") {
                        echo "Confirmed";
                    } else if ($status == "pending") {
                        echo "pending";
                    }
                    echo '</p>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
  <tbody> 
  <tr>
    <td align="center">
      <table width="100%" border="0" cellspacing="1" cellpadding="1" >
        <tr>
          <td height="30" colspan="2" align="center"><img src="http://ticketengine.in/operator_logo/' . $travel_id . '.png"  alt="' . $travels . '" width="180" height="80" /></td>
        </tr>
        <tr>
          <td height="30" colspan="2">Ticket No : <strong>' . $pnr . '</strong>(' . $onward_tktno . ') </td>
          </tr>
        <tr>
          <td height="30" colspan="2">Ticket Details for <strong>' . $pname1[0] . '</strong> from <strong>' . $source . '</strong> to <strong>' . $dest . '</strong> on service <strong>' . $service_no . '</strong> </td>
          </tr>
        <tr>
          <td height="30" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style=" border:#CCCCCC solid 1px;border-collapse: collapse;">
            <tr>
              <td width="16%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Seat Numbers </td>
              <td width="84%" height="30" style="border-bottom:#CCCCCC solid 1px">(' . $pass . ' Seats)<br />
                ';
                    for ($i = 0; $i < $pass; $i++) {
                        echo $seats1[$i] . ' (' . $pname1[$i] . ') (' . $age1[$i] . ') (' . $gender1[$i] . ')<br />';
                    }
                    echo'</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Journey Date </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $jdate . '</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Dep Time </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point1[1] . ' Report atleast 15 minutes prior to the departure time at this boarding point.</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;">Total Fare</td>
              <td height="30">' . $tkt_fare . '</td>
            </tr>
          </table></td>
          <td height="30" valign="top" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="border:#CCCCCC solid 1px">
            <tr>
              <td width="13%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Boarding @ </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point . '<br />' . $land_mark . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Booked On </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $time . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;">Booked By </td>
              <td width="87%" height="30">' . $name . '</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="30" colspan="2"><strong>Customer Service</strong></td>
          </tr>
        <tr>
          <td height="30" colspan="2">' . $ph . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">' . $op_email . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="30" colspan="2">
		  <b>You need to produce the hard copy of this ticket or a Mobile Ticket at the time of Journey.</b><br />
          <b>Terms and Conditions:</b><br/>
		  <ul>
<li style="text-align: justify;">The arrival and departure times mentioned on the ticket are only tentative timings. Busses may be delayed due to some unavoidable reasons like traffic jams etc.; However the bus will not leave the source before the time that is mentioned on the ticket</li>
<li style="text-align: justify;">
<p style=" text-align: left;">Next to ladies seat should be ladies only if in case gents are there should adjusted with different seat*&nbsp;</p>
</li>
</ul>
<p style="color: #1d1d1d; line-height: 18px; margin: 0px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>a.) A copy of the ticket. (A print out of the ticket or the print out of the ticket e-mail.)</strong></p>
<ul>
<li style="text-align: justify;">property of the passengers and accident.</li>
</ul>
<ul>
<li style="text-align: justify;">The company shall not be responsible for any delay or inconvenience during the journey due to breakdown of the vehicle or other reasons beyond the control of the company.</li>
</ul>
<ul>
<li>The tickets booked through ' . $travels . ' are cancellable with respect to cancellation policy.</li>
</ul>
<ul>
<li>The cancellation refund will be transfered to your respective bank accounts.</li>
</ul>		  </td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="center"><strong>For Complaints and Suggestions Please Contact us @ ' . $ph . ',' . $op_email . '</strong></td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="left">
		  <table width="505" border="0" cellpadding="0" cellspacing="0">              
              <tr>
                <td style="padding:5px 0px 5px 10px;  color:#CC3300; text-decoration:underline;">Cancellation Policy </td>
              </tr>';
                    $canc_terms1 = explode('@', $canc_terms);
                    for ($i = 0; $i < count($canc_terms1); $i++) {
                        echo '<tr>
                <td style="padding:5px 0px 5px 10px;  color:#000000">';
                        $canc_terms2 = explode('#', $canc_terms1[$i]);
                        echo $canc_terms2[0] . " To " . $canc_terms2[1] . " Hours " . $canc_terms2[2] . "% shall be deducted";
                        echo '</td>
                </tr>';
                    }
                    echo'</table>		  </td>
        </tr>        
      </table>
      </td>
  </tr>    
  </tbody>
</table>	

</div><br />
<div align="center">';
                    $ho = $this->session->userdata('bktravels_head_office');
                    if ($status == "confirmed") {
                        echo '<a href="javascript:void()" onClick="javascript:print(\'#pt\');">PRINT TICKET</a> ';
                    }
                    echo '</div>
</body>
</html>';
                }
            }
        }
    }

    function layout_change_price2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $type = $this->input->post('type');
        $service_num = $this->input->post('service_no');
        $from_id = $this->input->post('from_id');
        $to_id = $this->input->post('to_id');
        $journey_date = $this->input->post('dtt');

        echo '<script type="text/javascript">
		$(function() 
		{                                              
			$( "#fdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0,"autoclose": true
            });
            $( "#tdate" ).datepicker({ dateFormat: "yy-mm-dd",numberOfMonths: 1, showButtonPanel: false,minDate: 0,"autoclose": true
            });
		});
		</script>';
        echo '<table width="545" border="0" align="center" >
		     <tr >
			 	<td width="95" height="38">From  Date:</td>
				<td width="137"><input type="text" name="fdate" readonly="" class="inputmedium" id="fdate" value="' . $journey_date . '"   /></td>
				
				<td width="29">&nbsp;</td>
				<td width="81">To    Date: </td>
				<td width="181"><input type="text" name="tdate" readonly="" class="inputmedium" id="tdate"   value="' . $journey_date . '"/></td>
			</tr>
		</table>';

        if ($type == "service") {
            $stmt = "select distinct service_name,bus_type from master_buses where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id'";
            $query = $this->db->query($stmt);
            foreach ($query->result() as $row) {
                $service_name = $row->service_name;
                $bus_type = $row->bus_type;
            }
            echo '<table width="100%" border="0" cellspacing="2" cellpadding="2" style="border:#CCCCCC solid 1px;">
  <tr>
    <td height="30" colspan="7" bgcolor="#C30" style="color:#FFFFFF">Service : ' . $service_num . ' <span style="float:right">Journey Date : ' . $journey_date . '</span></td>
  </tr>
  <tr style="background-color:#CCCCCC">
    <td height="30" align="center">Service Name</td>';
            if ($bus_type == "seater") {
                echo'<td height="30" align="center">Seat Fare</td>';
            } else if ($bus_type == "sleeper") {
                echo'<td height="30" align="center">Lower Berth Fare</td>
    <td height="30" align="center">Upper Berth Fare</td>';
            } else {
                echo'<td height="30" align="center">Seat Fare</td>
    <td height="30" align="center">Lower Berth Fare</td>
    <td height="30" align="center">Upper Berth Fare</td>';
            }
            echo'</tr>
  <tr>
    <td height="30" align="center">' . $service_name . '</td>';
            $i = 1;
            $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and journey_date='$journey_date' and from_id='$from_id' and to_id='$to_id'";
            $query1 = $this->db->query($stmt1);
            if ($query1->num_rows() == 0) {
                $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is NULL";
                $query1 = $this->db->query($stmt1);
            }

            foreach ($query1->result() as $rows) {
                $seat_fare = $rows->seat_fare;
                $lberth_fare = $rows->lberth_fare;
                $uberth_fare = $rows->uberth_fare;
            }

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

            if ($bus_type == "seater") {
                echo'<td height="30" align="center"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>';
            } elseif ($bus_type == "sleeper") {
                echo'<td height="30" align="center"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
            } else {
                echo'<td height="30" align="center"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
            }
            echo'</tr>
  <tr>
    <td height="30" colspan="7" align="center"><input type="hidden" name="hdd" id="hdd" value="' . $i . '"><input type="hidden" name="bus_type" id="bus_type" value="' . $bus_type . '"><input type="button" class="btn btn-primary" value="update" name="up" id="up" onClick="updateFare()"></td>
  </tr>
  <tr>
    <td height="30" colspan="7" align="center"><div id="ress"></div></td>
  </tr>
</table>
';
        } else if ($type == "route") {
            $stmt = "select * from master_buses where service_num='$service_num' and travel_id='$travel_id'";
            $query = $this->db->query($stmt);
            foreach ($query->result() as $row) {
                $bus_type = $row->bus_type;
            }
            echo '<table width="100%" border="0" cellspacing="2" cellpadding="2" style="border:#CCCCCC solid 1px;">
  <tr>
    <td height="30" colspan="7" bgcolor="#C30" style="color:#FFFFFF">Service : ' . $service_num . ' <span style="float:right">Journey Date : ' . $journey_date . '</span></td>
  </tr>
  <tr style="background-color:#CCCCCC">
    <td height="30" align="center">Service Name</td>';
            if ($bus_type == "seater") {
                echo'<td height="30" align="center">Seat Fare</td>';
            } else if ($bus_type == "sleeper") {
                echo'<td height="30" align="center">Lower Berth Fare</td>
    <td height="30" align="center">Upper Berth Fare</td>';
            } else {
                echo'<td height="30" align="center">Seat Fare</td>
    <td height="30" align="center">Lower Berth Fare</td>
    <td height="30" align="center">Upper Berth Fare</td>';
            }
            echo'</tr>';
            $i = 1;
            foreach ($query->result() as $row) {
                $from_id = $row->from_id;
                $from_name = $row->from_name;
                $to_id = $row->to_id;
                $to_name = $row->to_name;
                echo'<tr>
    <td height="30" align="center">' . $from_name . ' To ' . $to_name . '</td>';
                $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and journey_date='$journey_date' and from_id='$from_id' and to_id='$to_id'";
                $query1 = $this->db->query($stmt1);
                if ($query1->num_rows() == 0) {
                    $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is NULL";
                    $query1 = $this->db->query($stmt1);
                }
                foreach ($query1->result() as $rows) {
                    $seat_fare = $rows->seat_fare;
                    $lberth_fare = $rows->lberth_fare;
                    $uberth_fare = $rows->uberth_fare;
                }
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

                if ($bus_type == "seater") {
                    echo'<td height="30" align="center"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>';
                } elseif ($bus_type == "sleeper") {
                    echo'<td height="30" align="center"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
                } else {
                    echo'<td height="30" align="center"><input type="text" class="inputfield" name="sfare' . $i . '" id="sfare' . $i . '" value="' . $sfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="lbfare' . $i . '" id="lbfare' . $i . '" value="' . $lfare . '"></td>
    <td height="30" align="center"><input type="text" class="inputfield" name="ubfare' . $i . '" id="ubfare' . $i . '" value="' . $ufare . '"></td>';
                }
                echo'</tr>';
                $i++;
            }
            $k = $i - 1;
            echo'<tr>
    <td height="30" colspan="7" align="center"><input type="hidden" name="hdd" id="hdd" value="' . $k . '"><input type="hidden" name="bus_type" id="bus_type" value="' . $bus_type . '"><input type="button" class="btn btn-primary" value="update" name="up" id="up" onClick="updateFare()"></td>
  </tr>
  <tr>
    <td height="30" colspan="7" align="center"><div id="ress"></div></td>
  </tr>
</table>
';
        }
    }

    function layout_updatePrice1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $type = $this->input->post('type');
        $service_num = $this->input->post('service_num');
        $fdate = $this->input->post('fdate');
        $tdate = $this->input->post('tdate');
        $lbfare = $this->input->post('lbfare');
        $ubfare = $this->input->post('ubfare');
        $sfare = $this->input->post('sfare');
        $bus_type = $this->input->post('bus_type');

        //echo $service_num . "#" . $journey_date . "#" . $sfare . "#" . $lbfare . "#" . $ubfare;

        $sfare1 = explode("/", $sfare);
        $lbfare1 = explode("/", $lbfare);
        $ubfare1 = explode("/", $ubfare);
        $ip = $this->input->ip_address();
        $time = date('Y-m-d H:m:s', time());
        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_user_name');

        if ($bus_type == "sleeper") {
            $count = count($lbfare1);
        } else {
            $count = count($sfare1);
        }

        $dt = array();

        $iDateFrom = mktime(1, 0, 0, substr($fdate, 5, 2), substr($fdate, 8, 2), substr($fdate, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($tdate, 5, 2), substr($tdate, 8, 2), substr($tdate, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($dt, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($dt, date('Y-m-d', $iDateFrom));
            }
        }

        $stmt = "select distinct distinct from_id,to_id,from_name,to_name,service_route,service_name from master_buses where service_num='$service_num' and travel_id='$travel_id'";
        $query = $this->db->query($stmt);

        if ($type == "service") {
            for ($i = 0; $i < count($dt); $i++) {
                $j = 0;
                foreach ($query->result() as $row) {
                    $from_id = $row->from_id;
                    $to_id = $row->to_id;
                    $from_name = $row->from_name;
                    $to_name = $row->to_name;
                    $service_route = $row->service_route;
                    $service_name = $row->service_name;

                    $stmt1 = "select * from master_price where from_id='$from_id' and to_id='$to_id' and service_num='$service_num' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                    $query1 = $this->db->query($stmt1);
                    if ($query1->num_rows() > 0) {
                        $stmt2 = "update master_price set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]',seat_fare_changed='',lberth_fare_changed='',uberth_fare_changed='' where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                        $query2 = $this->db->query($stmt2);
                    } else {
                        $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,lberth_fare,uberth_fare,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$dt[$i]')";
                        $query2 = $this->db->query($stmt2);
                    }

                    $stmt3 = "insert into master_change_pricing(travel_id,service_num,from_id,to_id,new_seat_fare,new_lberth_fare,new_uberth_fare,change_time,ip_address,journey_date,updated_by_id,updated_by)values('$travel_id','$service_num','$from_id','$to_id','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$time','$ip','$dt[$i]','$user_id','$name')";
                    $query3 = $this->db->query($stmt3);

                    $stmt4 = "update buses_list set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]' where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                    $query4 = $this->db->query($stmt4);
                }
                if ($query2 && $query3 && $query4) {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        } else if ($type == "route") {
            for ($i = 0; $i < count($dt); $i++) {
                $j = 0;
                foreach ($query->result() as $row) {
                    $from_id = $row->from_id;
                    $to_id = $row->to_id;
                    $from_name = $row->from_name;
                    $to_name = $row->to_name;
                    $service_route = $row->service_route;
                    $service_name = $row->service_name;

                    $stmt1 = "select * from master_price where from_id='$from_id' and to_id='$to_id' and service_num='$service_num' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                    $query1 = $this->db->query($stmt1);

                    if ($query1->num_rows() > 0) {
                        $stmt2 = "update master_price set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]',seat_fare_changed='',lberth_fare_changed='',uberth_fare_changed='' where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                        $query2 = $this->db->query($stmt2);
                    } else {
                        $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,lberth_fare,uberth_fare,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$dt[$i]')";
                        $query2 = $this->db->query($stmt2);
                    }

                    $stmt3 = "insert into master_change_pricing(travel_id,service_num,from_id,to_id,new_seat_fare,new_lberth_fare,new_uberth_fare,change_time,ip_address,journey_date,updated_by_id,updated_by)values('$travel_id','$service_num','$from_id','$to_id','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$time','$ip','$dt[$i]','$user_id','$name')";
                    $query3 = $this->db->query($stmt3);

                    $stmt4 = "update buses_list set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]' where service_num='$service_num' and from_id='$from_id' and to_id='$to_id' and travel_id='$travel_id' and journey_date='$dt[$i]'";
                    $query4 = $this->db->query($stmt4);

                    $j++;
                }
                if ($query2 && $query3 && $query4) {
                    echo 1;
                } else {
                    echo 0;
                }
            }
        }
    }

    function layout_grab_release2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $sernum = $this->input->post('service_no');
        $date = $this->input->post('dtt');
        $type = $this->input->post('type');
        $s = 1;

        echo '<script type="text/javascript">
    $(function()
    {       
	$("#from_date").datepicker({dateFormat: "yy-mm-dd", numberOfMonths: 1, showButtonPanel: false, minDate: 0,"autoclose": true});
	$("#to_date").datepicker({dateFormat: "yy-mm-dd", numberOfMonths: 1, showButtonPanel: false, minDate: 0,"autoclose": true});
    });
</script>
';
        echo '<table width="100%" border="0" align="center">          
          <tr>
          <td height="40" align="center"><span style="padding-right:25px;">Service Number : ' . $sernum . '</span>
              Journey Date : ' . $date . '</td>
          </tr>          
          <tr>
          <td height="40" align="center"><span style="padding-right:25px;">From Date : <input type="text" name="from_date" id="from_date" value="' . $date . '" size="12" readonly="" /></span>
              To Date : <input type="text" name="to_date" id="to_date" value="' . $date . '" size="12" readonly="" /></td>
          </tr>
          </table>';

        if ($type == "all") {
            echo'<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="30"> Type </td>
    <td><select name="block_type" id="block_type" onChange="hideopentoall()">
      <option value="">-- Select --</option>
	  <option value="block">Block</option>
      <option value="release">Release</option>
    </select>    </td>
  </tr>
  <tr>
    <td height="30">Select Branch Name to Update the Quota</td>
    <td><select name="agent_id" id="agent_id">
      <option value="">-- Select --</option>';
            echo '<option value="" id="opentoall" style="display:none">Open to all</option>';
            $stmt = "select id,name from agents_operator WHERE status='1' and operator_id = '$travel_id' and agent_type='1' and head_office='yes'";
            $query = $this->db->query($stmt);
            foreach ($query->result() as $rows) {
                echo'<option value="' . $rows->id . '">' . $rows->name . '</option>';
            }
            echo'</select></td>
  </tr>
  <tr>
    <td height="30" colspan="2" align="center"><input type="button" name="allseats" id="allseats" class="btn btn-primary" value="Update" onClick="Updateallseats()"></td>
  </tr>
  <tr>
    <td height="30" colspan="2" align="center"><span id="resallseats"></span></td>
  </tr>
</table>';
        } else if ($type == "individual") {
            //query for getting seat_type
            $seat_name = '';
            $res_seats = '';
            $query = $this->db->query("select layout_id,seat_type from layout_list where service_num='$sernum' and travel_id='$travel_id' and journey_date='$date' ");
            foreach ($query->result() as $r) {
                $layout_id = $r->layout_id;
                $seat_type = $r->seat_type;
                $lid = explode("#", $layout_id);
            }

            echo '<table width="100%" border="0" align="center">         
     <tr>
      <td align="center">';
            if ($lid[1] == 'seater') {
                //getting max of row and col from master_layouts
                $sq = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$sernum' and travel_id='$travel_id' and journey_date='$date' ") or die(mysql_error());
                foreach ($sq->result() as $row1) {
                    $mrow = $row1->mrow;
                    $mcol = $row1->mcol;
                }
                echo "<input type='hidden' name='mrow' id='mrow' value='$mrow' />
		<input type='hidden' name='mcol' id='mcol' value='$mcol' />";

                echo "<table border='0' cellpadding='10' cellspacing='4' align='center'>";

                for ($i = 1; $i <= $mcol; $i++) {
                    echo "<tr>";
                    for ($j = 1; $j <= $mrow; $j++) {
                        $sql3 = $this->db->query("select * from layout_list where row='$j' and col='$i' and service_num='$sernum' and travel_id='$travel_id' and journey_date='$date' ") or die(mysql_error());
                        $sql3->result();
                        foreach ($sql3->result() as $row2) {
                            $seat_name = $row2->seat_name;
                            $seat_type = $row2->seat_type;
                            $available = $row2->available;
                            $seat_status = $row2->seat_status;
                        }
                        if ($seat_name == '') {
                            echo "<td style='border:none;' align='center'>&nbsp;</td>";
                        } else { //if($available==1)
                            if (($available != 1 || $available != 2) && $seat_status == 0) {//available for booking
                                $id = "c$i$j";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" onClick="chkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';
                            }
                            if ($seat_status == 1) {
                                $ck = "<input type='checkbox' name='c$i$j' id='c$i$j' value='$seat_name' checked='checked'  disabled='disabled' />";
                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            if (($available == 1 || $available == 2) && $seat_status == 0) {
                                //$x=explode("#",$available_type);
                                $style = "style='background-color: #f2f2f2; width:20px'";
                                $id = "c$i$j";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" checked="checked" onClick="unchkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';

                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            echo "<td class='gruseat'>$seat_name$ck";

                            echo "</td>";
                        }
                        $seat_name = '';
                    }
                    echo "</tr>";
                }
                echo '</table>
                               <tr>
    <td align="center">';
                echo '<table width="100%" border="0" id="chkd' . $s . '" style="font-size:12px; display:none;">
        <tr>
          <td height="27" align="center">&nbsp;</td>
            <td align="right">New Quota Seats are : </td>
            <td style="max-width:10px;" id="gb' . $s . '" align="left"></td>
          </tr>
          <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="230" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ',1)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
            </select></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="uqa' . $s . '" >Select Agent Name TO Give  the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="uqi' . $s . '" >Select Branch Name to Update the Quota </span>   </td>
            <td> <span id="uqii' . $s . '"></span></td>
          </tr>
         <tr>
            <td colspan="3" align="center"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" />
            <input type="button" class="btn btn-primary" name="gbupdt' . $s . '" id="gbupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',1)"></td>
          </tr>
        </table>

<table width="100%" border="0" id="unchkd' . $s . '" style="font-size:12px;  display:none;">
        <tr>
            <td width="137" height="31" align="right">&nbsp;</td>
            <td width="182" align="left">Quota Removing Seats are : </td>
            <td width="180" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
      </tr>
      
      <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="270" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to Release the Seats :</span></td>
            <td width="200"><select name="res_atype' . $s . '" id="res_atype' . $s . '" onChange="agentType(' . $s . ',2)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
              <option value="0">Open to all</option>
            </select></td>
          </tr>
     <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="rsuqa' . $s . '" >Select Agent Name TO Remove the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="rsuqi' . $s . '" >Select Branch Name to Remove the Quota </span>   </td>
            <td> <span id="rsuqii' . $s . '"></span></td>
          </tr>
         <tr>
		 <td height="34"></td>
            <td align="right"><input type="button" class="btn btn-primary" name="rlupdt' . $s . '" id="rlupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',2)" /></td>
            <td colspan="1" align="left"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" /></td>
          </tr>
        </table>
</td>
  </tr>
  <tr>
    <td align="center">
    <span id="updtspan' . $s . '"  style="font-size:12; font-weight:normal;"></span></td>
  </tr>
  </td>
  </tr>
</table>';
            }
            else if ($lid[1] == 'sleeper') {
                //getting max of row and col from master_layouts
                //UpperDeck
                $sq2 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$sernum' and travel_id='$travel_id' and seat_type='U' and journey_date='$date'");
                foreach ($sq2->result() as $row1) {
                    $mrow = $row1->mrow;
                    $mcol = $row1->mcol;
                }
                echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
                echo "<table border='0' cellpadding='10' cellspacing='4'>";
                for ($k = 1; $k <= $mcol; $k++) {
                    echo "<tr>";
                    for ($l = 1; $l <= $mrow; $l++) {
                        $sq3 = $this->db->query("select * from layout_list where row='$l' and col='$k' and service_num='$sernum' and travel_id='$travel_id' and seat_type='U' and journey_date='$date' ");
                        foreach ($sq3->result() as $row2) {
                            $seat_name = $row2->seat_name;
                            $seat_type = $row2->seat_type;
                            $available = $row2->available;
                            $seat_status = $row2->seat_status;
                        }
                        if ($seat_name == '') {
                            echo "<td style='border:none;'>&nbsp;</td>";
                        } else { //if($available==1)
                            if (($available != 1 || $available != 2) && $seat_status == 0) {//available for booking
                                $id = "cu$k$l";

                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" onClick="chkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';
                            }
                            if ($seat_status == 1) {
                                $ck = "<input type='checkbox' name='cu$k$l' id='cu$k$l' value='$seat_name' checked='checked'  disabled='disabled' />";
                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            if (($available == 1 || $available == 2) && $seat_status == 0) {
                                //$x=explode("#",$available_type);
                                $style = "style='background-color: #E4E4E4; width:20px'";
                                $id = "cu$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" checked="checked" onClick="unchkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';

                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            echo "<td style='background-color: #E4E4E4;'>$seat_name$ck";

                            echo "</td>";
                        }
                        $seat_name = '';
                    }
                    echo "</tr>";
                }
                echo "</table><br/>";


                // Lower Deck
                $sq4 = $this->db->query("select max(row) as mroww,max(col) as mcoll from layout_list where service_num='$sernum' and travel_id='$travel_id' and seat_type='L' and journey_date='$date'") or die(mysql_error());
                foreach ($sq4->result() as $roww) {
                    $mroww = $roww->mroww;
                    $mcoll = $roww->mcoll;
                }
                echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
                echo "<table border='0' cellpadding='10' cellspacing='4'>";
                for ($k = 1; $k <= $mcoll; $k++) {
                    echo "<tr>";
                    for ($l = 1; $l <= $mroww; $l++) {
                        $sql3 = $this->db->query("select * from layout_list where row='$l' and col='$k' and service_num='$sernum' and travel_id='$travel_id' and seat_type='L' and journey_date='$date'") or die(mysql_error());
                        $sql3->result();
                        foreach ($sql3->result() as $row2) {
                            $seat_name = $row2->seat_name;
                            $seat_type = $row2->seat_type;
                            $available = $row2->available;
                            $seat_status = $row2->seat_status;
                        }

                        if ($seat_name == '') {
                            echo "<td style='border:none;' align='center'>&nbsp;</td>";
                        } else { //if($available==1)
                            if (($available != 1 || $available != 2) && $seat_status == 0) {//available for booking
                                $id = "cl$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" onClick="chkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';
                            }
                            if ($seat_status == 1) {
                                $ck = "<input type='checkbox' name='cl$k$l' id='cl$k$l' value='$seat_name' checked='checked'  disabled='disabled' />";
                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            if (($available == 1 || $available == 2) && $seat_status == 0) {
                                //$x=explode("#",$available_type);
                                $style = "style='background-color: #f2f2f2; width:20px'";
                                $id = "cl$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" checked="checked" onClick="unchkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';

                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            echo "<td style='background-color: #E4E4E4;'>$seat_name$ck";

                            echo "</td>";
                        }
                        $seat_name = '';
                    }
                    echo "</tr>";
                }
                echo '</table><tr>
    <td align="center">';
                echo '<table width="600" border="0" id="chkd' . $s . '" style="font-size:12px; display:none;">
        <tr>
          <td height="27" align="center">&nbsp;</td>
            <td align="right">New Quota Seats are : </td>
            <td style="max-width:10px;" id="gb' . $s . '" align="left"></td>
          </tr>
          <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="230" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ',1)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
            </select></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="uqa' . $s . '" >Select Agent Name TO Give  the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="uqi' . $s . '" >Select Branch Name to Update the Quota </span>   </td>
            <td> <span id="uqii' . $s . '"></span></td>
          </tr>
         <tr>
            <td colspan="3" align="center"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" />
            <input type="button" class="btn btn-primary" name="gbupdt' . $s . '" id="gbupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',1)"></td>
          </tr>
        </table>

<table width="593" border="0" id="unchkd' . $s . '" style="font-size:12px;  display:none;">
        <tr>
            <td width="137" height="31" align="right">&nbsp;</td>
            <td width="182" align="left">Quota Removing Seats are : </td>
            <td width="180" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
  </tr>
  
   <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="270" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to Release the Seats :</span></td>
            <td width="200"><select name="res_atype' . $s . '" id="res_atype' . $s . '" onChange="agentType(' . $s . ',2)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
              <option value="0">Open to all</option>
            </select></td>
          </tr>
     <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="rsuqa' . $s . '" >Select Agent Name TO Remove the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="rsuqi' . $s . '" >Select Branch Name to Remove the Quota </span>   </td>
            <td> <span id="rsuqii' . $s . '"></span></td>
          </tr>
         <tr>
		 <td height="34"></td>
            <td align="right"><input type="button" class="btn btn-primary" name="rlupdt' . $s . '" id="rlupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',2)" /></td>
            <td colspan="1" align="left"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" /></td>
          </tr>
        </table>
</td>
  </tr>
  <tr>
    <td align="center">
    <span id="updtspan' . $s . '"  style="font-size:12; font-weight:normal;"></span></td>
  </tr>
  </td>
  </tr>
</table>';
            }// else if(sleeper)
            else if ($lid[1] == 'seatersleeper') {
                //getting max of row and col from master_layouts
                //UpperDeck
                $this->db->select_max('row', 'mrow');
                $this->db->select_max('col', 'mcol');
                $this->db->where('service_num', $sernum);
                $this->db->where('travel_id', $travel_id);
                $this->db->where("(seat_type='U' OR seat_type='U')");
                $this->db->where('journey_date', $date);
                $sqll = $this->db->get('layout_list');

                foreach ($sqll->result() as $row1) {
                    $mrow = $row1->mrow;
                    $mcol = $row1->mcol;
                }
                echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
                echo "<table border='0' cellpadding='10' cellspacing='4'>";
                for ($k = 1; $k <= $mcol; $k++) {
                    echo "<tr>";
                    for ($l = 1; $l <= $mrow; $l++) {
                        $this->db->select('*');
                        $this->db->where('row', $l);
                        $this->db->where('col', $k);
                        $this->db->where('service_num', $sernum);
                        $this->db->where('travel_id', $travel_id);
                        $this->db->where("(seat_type='U' OR seat_type='U')");
                        $this->db->where('journey_date', $date);
                        $sql3 = $this->db->get('layout_list');

                        foreach ($sql3->result() as $row2) {
                            $seat_name = $row2->seat_name;
                            $available = $row2->available;
                            $seat_type = $row2->seat_type;
                            $seat_status = $row2->seat_status;
                        }
                        if ($seat_type == 'U')
                            $st = "(B)";
                        else if ($seat_type == 'U')
                            $st = "(S)";


                        if ($seat_name == '') {
                            echo "<td style='border:none;' align='center'>&nbsp;</td>";
                        } else { //if($available==1)
                            if (($available != 1 || $available != 2) && $seat_status == 0) {//available for booking
                                $id = "cu$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" onClick="chkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';
                            }
                            if ($seat_status == 1) {
                                $ck = "<input type='checkbox' name='cu$k$l' id='cu$k$l' value='$seat_name' checked='checked'  disabled='disabled' />";
                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            if (($available == 1 || $available == 2) && $seat_status == 0) {
                                //$x=explode("#",$available_type);
                                $style = "style='background-color: #E4E4E4; width:20px'";
                                $id = "cu$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" checked="checked" onClick="unchkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';

                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            echo "<td style='background-color: #E4E4E4;'>$seat_name$ck";

                            echo "</td>";
                        }
                        $seat_name = '';
                    }
                    echo "</tr>";
                }
                echo "</table><br/>";


                // Lower Deck


                $this->db->select_max('row', 'mroww');
                $this->db->select_max('col', 'mcoll');
                $this->db->where('service_num', $sernum);
                $this->db->where('travel_id', $travel_id);
                $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
                $this->db->where('journey_date', $date);
                $sql3 = $this->db->get('layout_list');
                foreach ($sql3->result() as $roww) {
                    $mroww = $roww->mroww;
                    $mcoll = $roww->mcoll;
                }

                echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
                echo "<table border='1'  cellpadding='2' cellspacing='4'>";
                for ($k = 1; $k <= $mcoll; $k++) {
                    echo "<tr>";
                    for ($l = 1; $l <= $mroww; $l++) {
                        $this->db->select('*');
                        $this->db->where('row', $l);
                        $this->db->where('col', $k);
                        $this->db->where('service_num', $sernum);
                        $this->db->where('travel_id', $travel_id);
                        $this->db->where('journey_date', $date);
                        $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
                        $sql3 = $this->db->get('layout_list');
                        foreach ($sql3->result() as $row2) {
                            $seat_name = $row2->seat_name;
                            $available = $row2->available;
                            $seat_type = $row2->seat_type;
                            $seat_status = $row2->seat_status;
                        }
                        if ($seat_type == 'L:b')
                            $st = "(B)";
                        else if ($seat_type == 'L:s')
                            $st = "(S)";
                        if ($seat_name == '') {
                            echo "<td style='border:none;' align='center'>&nbsp;</td>";
                        } else { //if($available==1)
                            if (($available != 1 || $available != 2) && $seat_status == 0) {//available for booking
                                $id = "cl$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" onClick="chkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';
                            }
                            if ($seat_status == 1) {
                                $ck = "<input type='checkbox' name='cl$k$l' id='cl$k$l' value='$seat_name' checked='checked'  disabled='disabled' />";
                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            if (($available == 1 || $available == 2) && $seat_status == 0) {
                                //$x=explode("#",$available_type);
                                $style = "style='background-color: #E4E4E4; width:20px'";
                                $id = "cl$k$l";
                                $ck = '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $seat_name . '" checked="checked" onClick="unchkk(\'' . $seat_name . '\',' . $s . ',\'' . $id . '\')"/>';

                                if ($res_seats == '')
                                    $res_seats = $seat_name;
                                else
                                    $res_seats = $res_seats . "," . $seat_name;
                            }
                            echo "<td style='background-color: #E4E4E4;'>$seat_name$ck";

                            echo "</td>";
                        }
                        $seat_name = '';
                    }
                    echo "</tr>";
                }
                echo '</table><tr>
    <td align="center">';
                echo '<table width="600" border="0" id="chkd' . $s . '" style="font-size:12px; display:none;">
        <tr>
          <td height="27" align="center">&nbsp;</td>
            <td align="right">New Quota Seats are : </td>
            <td style="max-width:10px;" id="gb' . $s . '" align="left"></td>
          </tr>
          <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="230" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ',1)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
            </select></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="uqa' . $s . '" >Select Agent Name TO Give  the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="uqi' . $s . '" >Select Branch Name to Update the Quota </span>   </td>
            <td> <span id="uqii' . $s . '"></span></td>
          </tr>
         <tr>
            <td colspan="3" align="center"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" />
            <input type="button" class="btn btn-primary" name="gbupdt' . $s . '" id="gbupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',1)"></td>
          </tr>
        </table>

<table width="593" border="0" id="unchkd' . $s . '" style="font-size:12px;  display:none;">
        <tr>
            <td width="137" height="31" align="right">&nbsp;</td>
            <td width="182" align="left">Quota Removing Seats are : </td>
            <td width="180" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
  </tr>
  
  <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="270" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to Release the Seats :</span></td>
            <td width="200"><select name="res_atype' . $s . '" id="res_atype' . $s . '" onChange="agentType(' . $s . ',2)">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
              <option value="0">Open to all</option>
            </select></td>
          </tr>
     <tr>
            <td >&nbsp;</td>
            <td ><span style="font-size:12px; color:#000;display:none;" id="rsuqa' . $s . '" >Select Agent Name TO Remove the Quota:</span>
     <span style="font-size:12px;color:#000;display:none;" id="rsuqi' . $s . '" >Select Branch Name to Remove the Quota </span>   </td>
            <td> <span id="rsuqii' . $s . '"></span></td>
          </tr>
         <tr>
		 <td height="34"></td>
            <td align="right"><input type="button" class="btn btn-primary" name="rlupdt' . $s . '" id="rlupdt' . $s . '" value="Save Changes" onClick="quotaUpdate(\'' . $sernum . '\',' . $travel_id . ',' . $s . ',2)" /></td>
            <td colspan="1" align="left"><input type="hidden" id="res_seats' . $s . '" name="res_seats' . $s . '" value="' . $res_seats . '" /></td>
          </tr>
        </table>
</td>
  </tr>
  <tr>
    <td align="center">
    <span id="updtspan' . $s . '"  style="font-size:12; font-weight:normal;"></span></td>
  </tr>
  </td>
  </tr>
</table>';
            }//close else if(seatersleeper) 
        }
    }

    function geAgentName($id) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select("id,name");
        // $this->db->distinct();
        $this->db->order_by("name", "asc");
        $this->db->where("agent_type", $id);
        $this->db->where("operator_id", $travel_id);
        $query = $this->db->get("agents_operator");
        $data = array();
        //echo $query->num_rows();
        if ($query->num_rows() > 0) {
            //$data['all']='All';
            $data[''] = '--select--';
            foreach ($query->result() as $rows) {
                $data[$rows->id] = $rows->name;
            }
            return $data;
        } else {
            $data['0'] = '--select--';
            return $data;
        }
    }

    function layout_Updateallseats1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_name');
        $sernum = $this->input->post('service_num');
        $block_type = $this->input->post('block_type');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $ip = $_SERVER['REMOTE_ADDR'];
        $tim = date('Y-m-d H:i:s');

        if ($block_type == "block") {
            $available_type = $this->input->post('agent_id');
            $available = 1;
            while ($from_date <= $to_date) {

                //storing in quota_update_history table                    
                $this->db->set('t2.available_type', $available_type);
                $this->db->set('t2.available', $available);

                $this->db->set('t2.show_avail_seat', 'no');
                $this->db->set('t2.show_quota', 'no');

                $array3 = array('t2.service_num' => $sernum, 't2.travel_id' => $travel_id, 't2.journey_date' => $from_date);
                $this->db->where($array3);

                $query1 = $this->db->update('layout_list as t2');

                $this->db->query("insert into grabandreleasehistory(service_num,travel_id,seat_name,available,available_type,ip,tim,updated_by_id,updated_by) values('$sernum','$travel_id','all','$available','$available_type','$ip','$tim','$user_id','$name')");

                $date1 = strtotime("+1 day", strtotime($from_date));
                $from_date = date("Y-m-d", $date1);
            }
        } else if ($block_type == "release") {
            $available_type = 0;
            $available = 0;
            while ($from_date <= $to_date) {
                //storing in quota_update_history table
                $this->db->set('t2.available_type', $available_type);
                $this->db->set('t2.available', $available);

                $array3 = array('t2.service_num' => $sernum, 't2.travel_id' => $travel_id, 't2.journey_date' => $from_date);
                $this->db->where($array3);

                $query2 = $this->db->update('layout_list as t2');

                $this->db->query("insert into grabandreleasehistory(service_num,travel_id,seat_name,available,available_type,ip,tim,updated_by_id,updated_by) values('$sernum','$travel_id','all','$available','$available_type','$ip','$tim','$user_id','$name')");

                $date1 = strtotime("+1 day", strtotime($from_date));
                $from_date = date("Y-m-d", $date1);
            }
        }
        if ($query1)
            echo 1;
        else if ($query2)
            echo 2;
        else
            echo 0;
    }

    function updateGrabRelease($sernum, $seats, $travel_id, $agent_type, $agent_id, $date, $c) {
        //echo $available."#".$available_type;
        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_name');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $ip = $_SERVER['REMOTE_ADDR'];
        $tim = date('Y-m-d H:i:s');
        if ($c == 1) {
            while ($from_date <= $to_date) {
                $st = explode(",", $seats);

                for ($i = 0; $i < count($st); $i++) {
                    //storing in quota_update_history table
                    //$this->db->set('t1.available',$agent_type);
                    //$this->db->set('t1.available_type',$agent_id); 
                    $this->db->set('t2.available_type', $agent_id);
                    $this->db->set('t2.available', $agent_type);

                    if ($agent_type == 1) {
                        $this->db->set('t2.show_avail_seat', 'no');
                        $this->db->set('t2.show_quota', 'no');
                    } else if ($agent_type == 2) {
                        $this->db->set('t2.show_avail_seat', 'no');
                        $this->db->set('t2.show_quota', 'yes');
                    }

                    //$array3=array('t1.service_num'=>$sernum,'t1.travel_id'=>$travel_id,'t1.seat_name'=>$st[$i],'t2.service_num'=>$sernum,'t2.travel_id'=>$travel_id,'t2.seat_name'=>$st[$i],'t2.journey_date'=>$date);
                    $array3 = array('t2.service_num' => $sernum, 't2.travel_id' => $travel_id, 't2.seat_name' => $st[$i], 't2.journey_date' => $from_date);
                    $this->db->where($array3);
                    //$query=$this->db->update('master_layouts as t1,layout_list as t2');
                    $query = $this->db->update('layout_list as t2');
                    //print_r($query->result()); 
                }
                $this->db->query("insert into grabandreleasehistory(service_num,travel_id,seat_name,available,available_type,ip,tim,updated_by_id,updated_by) values('$sernum','$travel_id','$seats','$agent_type','$agent_id','$ip','$tim','$user_id','$name')");
                //for
                $date1 = strtotime("+1 day", strtotime($from_date));
                $from_date = date("Y-m-d", $date1);
            }
        } else if ($c == 2) {
            while ($from_date <= $to_date) {
                $st = explode(",", $seats);

                for ($i = 0; $i < count($st); $i++) {
                    //storing in quota_update_history table
                    //echo $agent_id."#".$agent_type."#".$st[$i]."#".$sernum."#".$travel_id."#".$date; 
                    //$this->db->set('t1.available',$agent_type);
                    //$this->db->set('t1.available_type',$agent_id); 
                    $this->db->set('t2.available_type', $agent_id);

                    $this->db->set('t2.available', $agent_type);
                    if ($agent_type == 2) {
                        $this->db->set('t2.show_avail_seat', 'no');
                        $this->db->set('t2.show_quota', 'yes');
                    }
                    // $array3=array('t1.service_num'=>$sernum,'t1.travel_id'=>$travel_id,'t1.seat_name'=>$st[$i],'t2.service_num'=>$sernum,'t2.travel_id'=>$travel_id,'t2.seat_name'=>$st[$i],'t2.journey_date'=>$date);
                    $array3 = array('t2.service_num' => $sernum, 't2.travel_id' => $travel_id, 't2.seat_name' => $st[$i], 't2.journey_date' => $from_date);
                    $this->db->where($array3);
                    // $query2=$this->db->update('master_layouts as t1,layout_list as t2');
                    $query2 = $this->db->update('layout_list as t2');
                    //print_r($query->result());

                    $stmt = "select distinct available_type from layout_list where journey_date='$from_date' and service_num='$sernum' and travel_id='$travel_id' and available='2'";
                    //echo $stmt;
                    $sss = $this->db->query($stmt);
                    if ($sss->num_rows() > 0) {
                        foreach ($sss->result() as $rows) {
                            $available_type = $rows->available_type;
                            $stmt1 = "update layout_list set show_avail_seat='no',show_quota='no' where travel_id='$travel_id' and available_type<>'$available_type' and available='2' and service_num='$sernum' and journey_date='$from_date'";
                            $sql1 = $this->db->query($stmt1) or die(mysql_error());
                        }
                    } else {
                        $stmt1 = "update layout_list set show_avail_seat='no',show_quota='no' where travel_id='$travel_id' and available='2' and service_num='$sernum' and journey_date='$from_date'";
                        $sql1 = $this->db->query($stmt1) or die(mysql_error());
                    }
                }
                $this->db->query("insert into grabandreleasehistory(service_num,travel_id,seat_name,available,available_type,ip,tim,updated_by_id,updated_by) values('$sernum','$travel_id','$seats','$agent_type','$agent_id','$ip','$tim','$user_id','$name')");

                $date1 = strtotime("+1 day", strtotime($from_date));
                $from_date = date("Y-m-d", $date1);
            }
        }

        if ($query)
            echo 1;
        else if ($query2 && $sql1)
            echo 2;
        else
            echo 0;
    }

    public function checkticket1() {
        $ticket = $this->input->post('ticket');
        $sql = $this->db->query("select count(*) from master_booking where tkt_no='$ticket' or pnr='$ticket' or pmobile='$ticket' or receiptno='$ticket'");

        if ($sql->num_rows() > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function ticket_status1() {
        $ticket = $this->input->get('ticket');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $sql = $this->db->query("select * from master_booking where travel_id='$travel_id' and (tkt_no='$ticket' or pnr='$ticket' or pmobile='$ticket' or receiptno='$ticket') order by id desc");

        if ($sql->num_rows() != '') {
            foreach ($sql->result() as $res) {
                $bp = $res->board_point;
                $lm = $res->land_mark;
                $pname = $res->pname;
                $pmobile = $res->pmobile;
                $alter_ph = $res->alter_ph;
                $padd = $res->padd;
                $pemail = $res->pemail;
                $tkt_no = $res->tkt_no;
                $pnr = $res->pnr;
                $jdate = $res->jdate;
                $source = $res->source;
                $dest = $res->dest;
                $seats = $res->seats;
                $pass = $res->pass;
                $gender = $res->gender;
                $age = $res->age;
                $start_time = $res->start_time;
                $bus_type = $res->bus_type;
                $tkt_fare = $res->tkt_fare;
                $status = $res->status;
                $service_no = $res->service_no;
                $travel_id = $res->travel_id;
                $book_pay_type = $res->book_pay_type;
                $agent_id = $res->agent_id;
                $travels = $res->travels;
            }
            $sql1 = $this->db->query("select * from master_booking where (tkt_no='$ticket' or pnr='$ticket' or pmobile='$ticket') and (status='cancelled' || status='service cancelled') and travel_id='$travel_id' order by id desc");
            if ($sql1->num_rows() != '') {
                foreach ($sql1->result() as $res1) {
                    $st = $res1->status;
                    $ref = $res1->refamt;
                    $cdate = $res1->cdate;
                }
            }

            $sql2 = $this->db->query("select distinct name from agents_operator where id='$agent_id'");
            foreach ($sql2->result() as $res2) {
                $agent_name = $res2->name;
            }
            if ($sql1->num_rows() > 0) {
                $sts = $st;
            } else {
                $sts = 'confirmed';
            }

            echo '
			<link rel="stylesheet" href="' . base_url('css/app-css.v1.css') . '">
			<table  align="center" border="0" cellpadding="2" style="border:#666666 solid 1px;" cellspacing="0" width="758" id="fareprint" >
    <tbody>
    <tr>
    <td height="30" colspan="6" style="background-color:#6C6A6A; color:#FFFFFF">Ticket Information - <strong>' . $travels . '</strong></td>
    </tr>
    <tr>
      <td height="30" colspan="6" align="center" style="font-size:24px">Booked By : ' . $agent_name . '</td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="3" align="left" height="25"><strong>&nbsp;Passenger Name : ' . $pname . '</strong></td>
    <td colspan="3" align="left" height="25"><strong>&nbsp;Ticket No : ' . $tkt_no . ' </strong></td>
    </tr>
    <tr align="center" valign="top">
      <td colspan="3" align="left" height="25">&nbsp;<strong>Passenger Mobile : ' . $pmobile . '</strong></td>
      <td colspan="3" align="left" height="25">&nbsp;<strong>PNR : ' . $pnr . '</strong></td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6" height="25"><strong></strong></td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Service Number</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $service_no . '</td>
    <td align="left" height="20" width="18%">&nbsp;Journey Date</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $jdate . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Source</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $source . '</td>
    <td align="left" height="25" width="18%">&nbsp;Destination</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $dest . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Seat Number</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $seats . '</td>
    <td align="left" height="25" width="18%">&nbsp;No.Of Passengers</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $pass . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Start Time</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $start_time . '</td>
    <td align="left" height="25" width="18%">&nbsp;Bus Type</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $bus_type . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Boarding Point</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $bp . '</td>
    <td align="left" height="25" width="18%">&nbsp;Ticket Fare</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Land Mark</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $lm . '</td>
    <td align="left" height="25" width="18%">&nbsp;Total Fare</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">Rs. ' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;</td>
    <td align="center" height="25" width="3%">&nbsp;</td>
    <td align="left" height="25" width="29%">&nbsp;</td>
    <td align="left" height="25" width="18%">&nbsp;Status</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%" style="color:#C30"><strong>' . strtoupper($sts) . '</strong></td>
    </tr>
    ';

            if ($sts == 'cancelled' || $sts == 'service cancelled') {
                echo '
                <tr><th height="15" colspan="11" align="left" style="background-color:#848484; color: #ffffff">Cancellation Details </tr>
                  <tr valign="top">
                    <td height="5" width="18%">&nbsp;</td>
   </tr>

                   <tr><td align="left" height="25" width="18%">&nbsp;Refund Amount</td>
                    <td align="center" height="25" width="3%">:</td>
                    <td align="left" height="25" width="29%">Rs. ' . $ref . '</td>
                    <td align="left" height="25" width="18%">&nbsp;Cancelled date</td>
                    <td align="center" height="25" width="3%">:</td>
                    <td align="left" height="25" width="29%">' . $cdate . '</td>
                 </tr>
                 ';
            }
            echo '</tr>';
            echo '
    </tbody>
</table>
<br/>
<!--table align="center" border="0" cellpadding="2" style="font-size:12px;" cellspacing="0" width="700">
    <tbody>    
    <tr align="center" valign="top">
      <td height="25" ><input value="Print" name="Submit" onClick="javascript:window.print(fareprint);" type="button" class="newsearchbtn" /></td>
    </tr>
    </tbody>
    </table-->
<br/>
';
        }
    }

    function ticket_search1() {
        $ticket = $this->input->get('ticket');
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $tickets = explode("!", $ticket);
        $tkt_no = $tickets[0];
        $pnr = $tickets[1];
        $status1 = $tickets[2];

        $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and pnr='$pnr'");

        if ($sql->num_rows() != '') {
            foreach ($sql->result() as $res) {
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
                $paid = $res->paid;
                $save = $res->save;
                $tkt_fare = $res->tkt_fare;
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
            }
            $seats1 = explode(',', $seats);
            $pname1 = explode(',', $pname);
            $age1 = explode(',', $age);
            $gender1 = explode(',', $gender);
            $board_point1 = explode('-', $board_point);

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

            $sql2 = $this->db->query("SELECT name FROM agents_operator where operator_id='$travel_id' and id='$agent_id'");
            foreach ($sql2->result() as $row2) {
                $name = $row2->name;
            }

            echo '<html>
                  <head>
                  <title>:: ::</title>
                  </head>
			<style type="text/css">
			a {
				text-decoration:none;
				color:#0002CC;
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
table,th,tr,td
{
    font-size:14px;
	font-family:calibri;
}
			</style>
                        <script src="' . base_url('js/app-js.v1.js') . '" type="text/javascript"></script>
			<script type="text/javascript">
			$(function() {
  				//print(pt); //works
			});
			
			function print(elem)
		    {
        		Popup($(elem).html());
		    }

		    function Popup(data) 
		    {
        		var mywindow = window.open("", "my div", "height=400,width=600");
		        mywindow.document.write("<html><head>");
				mywindow.document.write("<style type=\"text/css\">");
				mywindow.document.write("table,th,tr,td{font-size:15px;font-family:calibri;}");
				mywindow.document.write("</style>");
				mywindow.document.write("<title>My Ticket</title>");        		
		        mywindow.document.write("</head><body >");
        		mywindow.document.write(data);
		        mywindow.document.write("</body></html>");

        		mywindow.print();
		        mywindow.close();

        		return true;
		    }						
			
                    function confirmticket()
                    {                                                
                        var ticket = "' . $tkt_no . '!' . $pnr . '!confirmed";
                            
                        var r = window.confirm("Are you sure you want to confirm the ticket");
                        if(r == true)
                        {
                            $.post("' . base_url('booking/confirm_ticket') . '",
                            {
                                ticket:ticket
                            },function(res)
                            {   //alert(res);
                                if(res == 1)
                                {
                                    window.location="' . base_url('booking/ticket_search') . '?ticket="+ticket;
                                }
                                else
                                {
                                    alert("Ticket Not Confirmed");
                                }
                            });                            
                        }
                    }
                    
                    function releaseticket()
                    {                                                
                        var ticket = "' . $tkt_no . '!' . $pnr . '!released";
                            
                        var r = window.confirm("Are you sure you want to Release the Seats");
                        if(r == true)
                        {
                            $.post("' . base_url('booking/release_seats') . '",
                            {
                                ticket:ticket
                            },function(res)
                            {   //alert(res);
                                if(res == 1)
                                {
                                    alert("seats released successfully,please refresh the layout");
                                    window.close();
                                }
                                else
                                {
                                    alert("Seats Not Released");
                                }
                            });                            
                        }
                    }
			</script>
                 </head>
                 <body>
			<div style="width:980px">';
            $ho = $this->session->userdata('bktravels_head_office');
            if ($status1 == "confirmed") {
                echo '<a href="javascript:void()" onClick="javascript:print(\'#pt\');">Print Ticket</a> ';
                if ($ho == "yes") {
                    echo '| 
			<a href="' . base_url('booking/get_canc_details?ticket=' . $ticket) . '">Cancel Ticket</a> | 
			<!--a href="shift_passenger?ticket=' . $ticket . '">Shift Passenger</a> | 
			<a href="update_ticket?ticket=' . $ticket . '">Update Ticket</a> | 
			<a href="ticket_history?ticket=' . $ticket . '">Show History</a-->';
                }
            }
            echo '</div>
			<div id="pt">
			
<p style="color: #d0d0d0;
  font-size: 130pt;
  -webkit-transform: rotate(-35deg);
  -moz-transform: rotate(-35deg);
  width: 90%;
  z-index: -1;
  position:absolute;
  text-align:center;
  margin-top:325px;
  opacity:0.6;
filter:alpha(opacity=60);">';
            if ($status1 == "confirmed") {
                echo "Confirmed";
            } else if ($status1 == "cancelled") {
                echo "Cancelled";
            } else if ($status1 == "released") {
                echo "Released";
            } else {
                echo "Pending";
            }
            echo '</p>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
  <tbody> 
  <tr>
    <td align="center">
      <table width="100%" border="0" cellspacing="1" cellpadding="1" >
        <tr>
          <td height="30" colspan="2" align="center"><img src="http://ticketengine.in/operator_logo/' . $travel_id . '.png"  alt="' . $travels . '" width="180" height="80" /></td>
        </tr>
        <tr>
          <td height="30" colspan="2">Ticket No : <strong>' . $pnr . '</strong>(' . $tkt_no . ') </td>
          </tr>
        <tr>
          <td height="30" colspan="2">Ticket Details for <strong>' . $pname1[0] . '</strong> from <strong>' . $source . '</strong> to <strong>' . $dest . '</strong> on service <strong>' . $service_no . '</strong> </td>
          </tr>
        <tr>
          <td height="30" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style=" border:#CCCCCC solid 1px;border-collapse: collapse;">
            <tr>
              <td width="16%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Seat Numbers </td>
              <td width="84%" height="30" style="border-bottom:#CCCCCC solid 1px">(' . $pass . ' Seats)<br />
                ';
            for ($i = 0; $i < $pass; $i++) {
                echo $seats1[$i] . ' (' . $pname1[$i] . ') (' . $age1[$i] . ') (' . $gender1[$i] . ')<br />';
            }
            echo'</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Journey Date </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $jdate . '</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Dep Time </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point1[1] . ' Report atleast 15 minutes prior to the departure time at this boarding point.</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;">Total Fare</td>
              <td height="30">' . $tkt_fare . '</td>
            </tr>
          </table></td>
          <td height="30" valign="top" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="border:#CCCCCC solid 1px">
            <tr>
              <td width="13%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Boarding @ </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point . '<br />' . $land_mark . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Booked On </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $time . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;">Booked By </td>
              <td width="87%" height="30">' . $name . '</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="30" colspan="2"><strong>Customer Service</strong></td>
          </tr>
        <tr>
          <td height="30" colspan="2">' . $ph . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">' . $op_email . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="30" colspan="2">
		  <b>You need to produce the hard copy of this ticket or a Mobile Ticket at the time of Journey.</b><br />
          <b>Terms and Conditions:</b><br/>
		  <ul>
<li style="text-align: justify;">The arrival and departure times mentioned on the ticket are only tentative timings. Busses may be delayed due to some unavoidable reasons like traffic jams etc.; However the bus will not leave the source before the time that is mentioned on the ticket</li>
<li style="text-align: justify;">
<p style=" text-align: left;">Next to ladies seat should be ladies only if in case gents are there should adjusted with different seat*&nbsp;</p>
</li>
</ul>
<p style="color: #1d1d1d; line-height: 18px; margin: 0px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>a.) A copy of the ticket. (A print out of the ticket or the print out of the ticket e-mail.)</strong></p>
<ul>
<li style="text-align: justify;">property of the passengers and accident.</li>
</ul>
<ul>
<li style="text-align: justify;">The company shall not be responsible for any delay or inconvenience during the journey due to breakdown of the vehicle or other reasons beyond the control of the company.</li>
</ul>
<ul>
<li>The tickets booked through ' . $travels . ' are cancellable with respect to cancellation policy.</li>
</ul>
<ul>
<li>The cancellation refund will be transfered to your respective bank accounts.</li>
</ul>		  </td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="center"><strong>For Complaints and Suggestions Please Contact us @ ' . $ph . ',' . $op_email . '</strong></td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="left">
		  <table width="505" border="0" cellpadding="0" cellspacing="0">              
              <tr>
                <td style="padding:5px 0px 5px 10px;  color:#CC3300; text-decoration:underline;">Cancellation Policy </td>
              </tr>';
            $canc_terms1 = explode('@', $canc_terms);
            for ($i = 0; $i < count($canc_terms1); $i++) {
                echo '<tr>
                <td style="padding:5px 0px 5px 10px;  color:#000000">';
                $canc_terms2 = explode('#', $canc_terms1[$i]);
                echo $canc_terms2[0] . " To " . $canc_terms2[1] . " Hours " . $canc_terms2[2] . "% shall be deducted";
                echo '</td>
                </tr>';
            }
            echo'</table>		  </td>
        </tr>
        <tr>
        	<td height="30" colspan="2" align="center">';
            if ($status1 == "pending") {
                echo '<input type="hidden" name="tkt_no" id="tkt_no" value="' . $tkt_no . '" />
                    <input type="hidden" name="pnr" id="pnr" value="' . $pnr . '" />
                        <input type="hidden" name="status" id="status" value="' . $status1 . '" />
                      <input name="confirm" type="button" id="confirm" value="Confirm Ticket" class="newsearchbtn" onClick="confirmticket();">
					  <input name="release" type="button" id="release" value="Release Seats" class="newsearchbtn" onClick="releaseticket();" style="margin-left:50px;">';
            }
            echo'</td>
        	</tr>
      </table>
      </td>
  </tr>    
  </tbody>
</table>	

</div>
</body>
</html>';
        }
    }

    function get_canc_details1() {
        $ticket = $this->input->get('ticket');
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $tickets = explode("!", $ticket);
        $tkt_no = $tickets[0];
        $pnr = $tickets[1];

        $can1 = $this->db->query("select distinct status from master_booking where tkt_no='$tkt_no' and pnr='$pnr' and status='cancelled'");
        foreach ($can1->result() as $can) {
            $stat = $can->status;
        }

        if ($stat != "cancelled") {
            $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and pnr='$pnr' and status='confirmed'");
            if ($sql->num_rows() != 0) {
                return $sql->result();
            }
        }
    }

    function canc_ticket1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $name = $this->session->userdata('bktravels_name');
        $agent_type_name = $this->session->userdata('bktravels_agent_type_name');

        $ticket = $this->input->get('ticket');
        $cc = $this->input->get('cc');
        $ca = $this->input->get('ca');
        $ra = $this->input->get('ra');
        $paid = $this->input->get('paid');
        $canc_time = $this->input->get('canc_time');

        $tickets = explode("!", $ticket);
        $tkt_no = $tickets[0];
        $pnr = $tickets[1];

        $can1 = $this->db->query("select distinct status from master_booking where tkt_no='$tkt_no' and status='cancelled'")or die(mysql_error());

        foreach ($can1->result() as $can) {
            $stat = $can->status;
        }

        if ($stat != "cancelled") {
            $query1 = $this->db->query("select distinct sender_id,operator_title from registered_operators where travel_id='$travel_id' ")or die(mysql_error());

            foreach ($query1->result() as $res2) {
                $sender_id = $res2->sender_id;
                $operator_title = $res2->operator_title;
            }

            $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and status='Confirmed'");

            if ($sql->num_rows() != 0) {
                foreach ($sql->result() as $res) {
                    $service_no = $res->service_no;
                    $board_point = $res->board_point;
                    $bpid = $res->bpid;
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
                    $pname = $res->pname;
                    $pemail = $res->pemail;
                    $pmobile = $res->pmobile;
                    $age = $res->age;
                    $refno = $res->refno;
                    $status = "cancelled";
                    $pass = $res->pass;
                    $cseat = $res->cseat;
                    $ccharge = $cc;
                    $camt = ($paid * $cc) / 100;
                    if ($paid == "") {
                        $camt = ($tkt_fare * $cc) / 100;
                    }
                    $refamt = $paid - $camt;
                    $travel_id = $res->travel_id;
                    $ip = $_SERVER->REMOTE_ADDR;
                    $time = $res->time;
                    $cdate = date('Y-m-d');
                    $ctime = date('Y-m-d H:i:s');
                    $id_type = $res->id_type;
                    $id_num = $res->id_num;
                    $padd = $res->padd;
                    $alter_ph = $res->alter_ph;
                    $fid = $res->fid;
                    $tid = $res->tid;
                    $operator_agent_type = $res->operator_agent_type;
                    $agent_id1 = $res->agent_id;
                    $book_pay_type = $res->book_pay_type;
                    $book_pay_agent = $res->book_pay_agent;
                }
                //checking for agent balance.
                $query = $this->db->query("select * from agents_operator where id='$agent_id1' and operator_id='$travel_id' ")or die(mysql_error());
                foreach ($query->result() as $res1) {
                    $bal = $res1->balance;
                    $bal1 = $bal + $refamt;
                }

                //updating agent balance
                $this->db->query("update agents_operator set balance='$bal1' where id='$agent_id1' and  operator_id='$travel_id' ")or die(mysql_error());

                $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,pname,pemail,pmobile,age,refno,status,pass,cseat,ccharge,camt,refamt,travel_id,ip,time,cdate,ctime,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,book_pay_type,book_pay_agent,cancelled_by) values('$tkt_no','$pnr','$service_no','$board_point','$bpid','$land_mark','$source','$dest','$travels','$bus_type','$bdate','$jdate','$seats','$gender','$start_time','$arr_time','$paid','$save','$tkt_fare','$pname','$pemail','$pmobile','$age','$refno','$status','$pass','$seats','$ccharge','$camt','$refamt','$travel_id','$ip','$time','$cdate','$ctime','$id_type','$id_num','$padd','$alter_ph','$fid','$tid','$operator_agent_type','$agent_id1','$book_pay_type','$book_pay_agent','$agent_id')")or die(mysql_error());

                $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdate'")or die(mysql_error());

                foreach ($sql3->result() as $row3) {
                    $as1 = $row3->available_seats;
                }
                $as = $as1 + $pass;

                $this->db->query("update buses_list set available_seats='$as' where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdate'")or die(mysql_error());

                $ss1 = explode(',', $seats);
                for ($k = 0; $k < $pass; $k++) {
                    $sea = $ss1[$k];

                    $this->db->query("update layout_list set seat_status='0',is_ladies='0',status1='' where journey_date='$jdate' and service_num='$service_no' and travel_id='$travel_id' and seat_name='$sea'")or die(mysql_error());
                }

                $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,can_fare,refamt,net_amt,bal,dat,ip,agent_id,travel_id,status) values('$tkt_no','$pnr','$pname','$source','$dest','$ctime','Credit','$tkt_fare','$save','$camt','$refamt','$paid','$bal1','$cdate','$ip','$agent_id','$travel_id','cancelled')")or die(mysql_error());

                $text = "Your ticket has been successfully cancelled having ticket no " . $tkt_no . "-" . $operator_title;
                //echo $text;
                $ch = curl_init();
                $user = "pridhvi@msn.com:activa1525@";
                $receipientno = $pmobile;
                //$msgtxt="this is test message , test";
                curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$sender_id&receipientno=$receipientno&msgtxt=$text");
                $buffer = curl_exec($ch);

                curl_close($ch);
            }
        }
        $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and pnr='$pnr' and status='cancelled'");
        return $sql->result();
    }

    function confirm_ticket1() {
        $ticket = $this->input->post('ticket');

        $tickets = explode('!', $ticket);
        $tkt_no = $tickets[0];
        $pnr = $tickets[1];

        $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and pnr='$pnr' and status='pending'");
        foreach ($sql->result() as $row) {
            $status = $row->status;
            $onward_date = $row->jdate;
            $onward_service_num = $row->service_no;
            $onward_travel_id = $row->travel_id;
            $onward_travels = $row->travels;
            $onward_source_name = $row->source;
            $onward_destination_name = $row->dest;
            $onward_bp = $row->land_mark;
            $pmobile = $row->pmobile;
            $pemail = $row->pemail;
            $onward_seats = $row->seats;
            $pname = $row->pname;
            $onward_genders = $row->gender;
            $onward_genders1 = explode(",", $onward_genders);
            $onward_pass = $row->pass;
            $onward_source_id = $row->fid;
            $onward_destination_id = $row->tid;
            $paytyp = $row->book_pay_type;
            $paid = $row->paid;
            $save = $row->save;
            $tkt_fare = $row->tkt_fare;
            $base_fare = $row->base_fare;
            $start_time = $row->start_time;
            $land_mark = $row->land_mark;
            $bus_type = $row->bus_type;
            $tkt_fare = $row->tkt_fare;

            if ($status == "pending") {
                $ss1 = explode(',', $onward_seats);

                for ($k = 0; $k < count($ss1); $k++) {
                    $sea = $ss1[$k];
                    $pgen = $onward_genders1[$k];

                    if ($pgen == 'M') {
                        $pgen1 = 0;
                    } else {
                        $pgen1 = 1;
                    }

                    $sql = $this->db->query("update layout_list set seat_status='1',is_ladies='$pgen1',status1='confirmed' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'")or die(mysql_error());
                }
                $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'")or die(mysql_error());
                foreach ($sql3->result() as $row3) {
                    $as1 = $row3->available_seats;
                }

                $as = $as1 - $onward_pass;
                $this->db->query("update buses_list set available_seats='$as' where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'")or die(mysql_error());
                $sql1 = $this->db->query("update master_booking set status='confirmed' where tkt_no='$tkt_no' and pnr='$pnr' and travel_id='$onward_travel_id' and service_no='$onward_service_num' and jdate='$onward_date'")or die(mysql_error());
                $this->db->query("update master_pass_reports set status='confirmed' where tktno='$tkt_no' and pnr='$pnr' and travel_id='$onward_travel_id' and status='pending'")or die(mysql_error());
                //echo "update layout_list set seat_status='1',is_ladies='$pgen1',status1='confirmed' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'";
                $travel_id = $this->session->userdata('bktravels_travel_id');
                $agent_id = $this->session->userdata('bktravels_user_id');
                $agent_type = $this->session->userdata('bktravels_agent_type');

                /**                 * ********** based on selection of payment details in agent close ** */
                //checking for agent balance.
                if ($paytyp == 'byagent' || $paytyp == 'byphoneagent' || $paytyp == 'byemployee') {
                    $query = $this->db->query("select * from agents_operator where id='$agent_id' and  operator_id='$travel_id' ")or die(mysql_error());
                } else {
                    $query = $this->db->query("select * from agents_operator where id='$agent_id' and agent_type='$agent_type' and  operator_id='$travel_id' ")or die(mysql_error());
                }
                foreach ($query->result() as $res) {
                    $bal = $res->balance;
                    $margin = $res->margin;
                    $pay_type = $res->pay_type;
                    $comm_type = $res->comm_type;
                }

                if ($pay_type == 'postpaid') {
                    if ($comm_type == "percent") {
                        $save = ($base_fare * $margin) / 100;
                    } else {
                        $save = $margin * $onward_pass;
                    }
                    $fare1 = $base_fare - $save;
                    $paid = $fare1;
                    $bal1 = $bal - $fare1;
                } else {
                    $bal1 = $bal - $tkt_fare;
                    $paid = $tkt_fare;
                    $save = '0';
                }

                if ($travel_id == $onward_travel_id) {
                    $sql7 = $this->db->query("update agents_operator set balance='$bal1' where id='$agent_id' and operator_id='$travel_id' ")or die(mysql_error());
                } else {
                    $sql7 = $this->db->query("update agents_operator set balance1='$bal1' where id='$agent_id' and operator_id='$travel_id' ")or die(mysql_error());
                }

                $sql5 = $this->db->query("SELECT sender_id,contact_no FROM registered_operators where travel_id='$travel_id'");
                foreach ($sql5->result() as $row5) {
                    $senid = $row5->sender_id;
                    $contact_no = $row5->contact_no;
                }

                $user = "pridhvi@msn.com:activa1525@";
                $senderID = $senid;

                $text = "TKT No: " . $tkt_no . "->" . $onward_travels . "-" . $onward_source_name . "-" . $onward_destination_name . "->" . $onward_service_num . " , DOJ: " . $onward_date . " , Seats: " . $onward_seats . " , At-" . $onward_bp . " , Ph: " . $contact_no . "";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$pmobile&msgtxt=$text");
                $buffer = curl_exec($ch);
                $x = explode('=', $buffer);
                $y = $x[1];
                $z = explode(',', $y);
                $stat = $z[0];

                if ($stat == 0) {
                    $msg = "sent";
                } else {
                    $msg = "notsent";
                }

                curl_close($ch);

                /* if ($this->session->userdata('travel_id') == $onward_travel_id) {
                  $img = base_url("images/logo.png");
                  } else { */
                //$img = "http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png"
                //}

                $subject = "Ticket Confirmation";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
                $headers .= 'From: ' . $onward_travels . ' <' . $op_email . '>' . "\r\n";
                $headers .= 'Reply-To: ' . $op_email . '' . "\r\n";

                $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: ' . $onward_travels . ' ::</title>
</head>

<body>
<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
  <tbody>
	<tr align="left" valign="top">
	  <td colspan="6" style="color:#c42124;padding-left:5px;font-size:13px;">
	  <img src="http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png" />
	  	</td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;
	  
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">
	  <strong>Onward Journey Details</strong>
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;	  
	  </td>
    </tr>     
    <tr align="center" valign="top">
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Passenger Name :  ' . $pname . '</strong></td>
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Ticket No : ' . $tkt_no . ' </strong></td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Travel Provider</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;"> ' . $onward_travels . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Journey Date</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_date . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Source</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_source_name . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Destination</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_destination_name . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Seat Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_seats . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;"> Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $pname . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Start Time</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $start_time . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">No.Of Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_pass . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Land Mark</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $land_mark . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Bus Type</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $bus_type . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Status</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Confirmed</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Total Fare</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Rs. ' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Boarding Point</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_bp . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Service Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_service_num . '</td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
	</tbody>
    </table>
</body>
</html>
';
                mail($pemail, $subject, $message, $headers);
            }
        }
        if ($sql1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function release_seats1() {
        $ticket = $this->input->post('ticket');

        $tickets = explode('!', $ticket);
        $tkt_no = $tickets[0];
        $pnr = $tickets[1];

        $sql = $this->db->query("select * from master_booking where tkt_no='$tkt_no' and pnr='$pnr' and status='pending'") or die(mysql_error());
        foreach ($sql->result() as $row) {
            $seats = $row->seats;
            $journey_date = $row->jdate;
            $service_num = $row->service_no;
            $travel_id = $row->travel_id;
        }
        $seats1 = explode(',', $seats);

        for ($k = 0; $k < count($seats1); $k++) {
            $seat = $seats1[$k];

            $sql1 = $this->db->query("update layout_list set seat_status='0',is_ladies='0',status1='' where journey_date='$journey_date' and service_num='$service_num' and travel_id='$travel_id' and seat_name='$seat'")or die(mysql_error());
            //echo "update layout_list set seat_status='0',is_ladies='0',status1='' where journey_date='$journey_date' and service_num='$service_num' and travel_id='$travel_id' and seat_name='$seat'";
        }

        $sql2 = $this->db->query("update master_booking set status='released' where tkt_no='$tkt_no' and pnr='$pnr' and travel_id='$travel_id' and service_no='$service_num' and jdate='$journey_date'") or die(mysql_error());
        $this->db->query("update master_pass_reports set status='confirmed' where tktno='$tkt_no' and pnr='$pnr' and travel_id='$travel_id' and status='released'")or die(mysql_error());

        if ($sql1 && $sql2) {
            echo 1;
        } else {
            echo 0;
        }
    }
	
	function junoresponse1($mtxnid) {
        $sql = $this->db->query("select * from master_booking where pnr='$mtxnid' and status='pending'");
        foreach ($sql->result() as $row) {
            $tkt_no = $row->tkt_no;
            $status = $row->status;
            $onward_date = $row->jdate;
            $onward_service_num = $row->service_no;
            $onward_travel_id = $row->travel_id;
            $onward_travels = $row->travels;
            $onward_source_name = $row->source;
            $onward_destination_name = $row->dest;
            $onward_bp = $row->land_mark;
            $pmobile = $row->pmobile;
            $pemail = $row->pemail;
            $onward_seats = $row->seats;
            $pname = $row->pname;
            $onward_genders = $row->gender;
            $onward_genders1 = explode(",", $onward_genders);
            $onward_pass = $row->pass;
            $onward_source_id = $row->fid;
            $onward_destination_id = $row->tid;
            $paytyp = $row->book_pay_type;
            $paid = $row->paid;
            $save = $row->save;
            $tkt_fare = $row->tkt_fare;
            $base_fare = $row->base_fare;
            $start_time = $row->start_time;
            $land_mark = $row->land_mark;
            $bus_type = $row->bus_type; 
            $agent_id = $row->agent_id;
            $operator_agent_type = $row->operator_agent_type;

            if ($status == "pending") {
                $ss1 = explode(',', $onward_seats);

                for ($k = 0; $k < count($ss1); $k++) {
                    $sea = $ss1[$k];
                    $pgen = $onward_genders1[$k];

                    if ($pgen == 'M') {
                        $pgen1 = 0;
                    } else {
                        $pgen1 = 1;
                    }

                    $sql = $this->db->query("update layout_list set seat_status='1',is_ladies='$pgen1',status1='confirmed' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'")or die(mysql_error());
                }
                $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'")or die(mysql_error());
                foreach ($sql3->result() as $row3) {
                    $as1 = $row3->available_seats;
                }

                $as = $as1 - $onward_pass;
                $this->db->query("update buses_list set available_seats='$as' where travel_id='$onward_travel_id' and from_id='$onward_source_id' and to_id='$onward_destination_id' and service_num='$onward_service_num' and journey_date='$onward_date'")or die(mysql_error());
                $sql1 = $this->db->query("update master_booking set status='confirmed' where pnr='$mtxnid' and travel_id='$onward_travel_id' and service_no='$onward_service_num' and jdate='$onward_date'")or die(mysql_error());
                $this->db->query("update master_pass_reports set status='confirmed' where pnr='$mtxnid' and travel_id='$onward_travel_id' and status='pending'")or die(mysql_error());
                //echo "update layout_list set seat_status='1',is_ladies='$pgen1',status1='confirmed' where journey_date='$onward_date' and service_num='$onward_service_num' and travel_id='$onward_travel_id' and seat_name='$sea'";                                

                /**                 * ********** based on selection of payment details in agent close ** */
                //checking for agent balance.
                if ($paytyp == 'byagent' || $paytyp == 'byphoneagent' || $paytyp == 'byemployee') {
                    $query = $this->db->query("select * from agents_operator where id='$agent_id' and  operator_id='$onward_travel_id' ")or die(mysql_error());
                } else {
                    $query = $this->db->query("select * from agents_operator where id='$agent_id' and agent_type='$operator_agent_type' and  operator_id='$onward_travel_id' ")or die(mysql_error());
                }
                foreach ($query->result() as $res) {
                    $bal = $res->balance;
                    $margin = $res->margin;
                    $pay_type = $res->pay_type;
                    $comm_type = $res->comm_type;
                }

                if ($pay_type == 'postpaid') {
                    if ($comm_type == "percent") {
                        $save = ($base_fare * $margin) / 100;
                    } else {
                        $save = $margin * $onward_pass;
                    }
                    $fare1 = $base_fare - $save;
                    $paid = $fare1;
                    $bal1 = $bal - $fare1;
                } else {
                    $bal1 = $bal - $tkt_fare;
                    $paid = $tkt_fare;
                    $save = '0';
                }

                /*if ($travel_id == $onward_travel_id) {
                    $sql7 = $this->db->query("update agents_operator set balance='$bal1' where id='$agent_id' and operator_id='$travel_id' ")or die(mysql_error());
                } else {*/
                    $this->db->query("update agents_operator set balance1='$bal1' where id='$agent_id' and operator_id='$onward_travel_id' ")or die(mysql_error());
                //}

                $sql5 = $this->db->query("SELECT sender_id,contact_no FROM registered_operators where travel_id='$onward_travel_id'");
                foreach ($sql5->result() as $row5) {
                    $senid = $row5->sender_id;
                    $contact_no = $row5->contact_no;
                }

                $user = "pridhvi@msn.com:activa1525@";
                $senderID = $senid;

                $text = "TKT No: " . $tkt_no . "->" . $onward_travels . "-" . $onward_source_name . "-" . $onward_destination_name . "->" . $onward_service_num . " , DOJ: " . $onward_date . " , Seats: " . $onward_seats . " , At-" . $onward_bp . " , Ph: " . $contact_no . "";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$pmobile&msgtxt=$text");
                $buffer = curl_exec($ch);
                $x = explode('=', $buffer);
                $y = $x[1];
                $z = explode(',', $y);
                $stat = $z[0];

                if ($stat == 0) {
                    $msg = "sent";
                } else {
                    $msg = "notsent";
                }

                curl_close($ch);

                /* if ($this->session->userdata('travel_id') == $onward_travel_id) {
                  $img = base_url("images/logo.png");
                  } else { */
                //$img = "http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png"
                //}

                $subject = "Ticket Confirmation";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
                $headers .= 'From: ' . $onward_travels . ' <' . $pemail . '>' . "\r\n";
                $headers .= 'Reply-To: ' . $pemail . '' . "\r\n";

                $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: ' . $onward_travels . ' ::</title>
</head>

<body>
<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
  <tbody>
	<tr align="left" valign="top">
	  <td colspan="6" style="color:#c42124;padding-left:5px;font-size:13px;">
	  <img src="http://ticketengine.in/operator_logo/' . $onward_travel_id . '.png" />
	  	</td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;
	  
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">
	  <strong>Onward Journey Details</strong>
	  </td>
    </tr>
	<tr align="left" valign="top">
	  <td colspan="6"style="color:#c42124;padding-left:5px;font-size:13px;">&nbsp;	  
	  </td>
    </tr>     
    <tr align="center" valign="top">
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Passenger Name :  ' . $pname . '</strong></td>
    <td colspan="3" style="color:#c42124;padding-left:5px;font-size:13px;" align="left"><strong>Ticket No : ' . $tkt_no . ' </strong></td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Travel Provider</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;"> ' . $onward_travels . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Journey Date</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_date . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Source</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_source_name . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Destination</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_destination_name . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Seat Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_seats . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;"> Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $pname . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Start Time</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $start_time . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">No.Of Passengers</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_pass . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Land Mark</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $land_mark . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Bus Type</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $bus_type . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Status</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Confirmed</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Total Fare</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">Rs. ' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Boarding Point</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_bp . '</td>
    <td width="18%" style="color:#c42124;padding-left:5px;font-size:13px;">Service Number</td>
    <td align="center" width="3%"><strong>:</strong></td>
    <td width="29%" style="color:#c42124;padding-left:5px;font-size:13px;">' . $onward_service_num . '</td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6"><strong></strong></td>
    </tr>
	</tbody>
    </table>
</body>
</html>
';
                mail($pemail, $subject, $message, $headers);
            }
        }
        if ($sql1) {
            $data['onward_tktno'] = $tkt_no;
            return $data;
        } else {
            echo 0;
        }
    }
    
    function juno_confirmed_ticket_db() {
        $onward_tktno = $this->input->get('onward_tktno');        
        $onward_way = $this->input->get('onward_way');        

        if ($onward_way == "O") {
            $sql = $this->db->query("select * from master_booking where tkt_no='$onward_tktno'");

            if ($sql->num_rows() > 0) {
                foreach ($sql->result() as $res) {
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
                    $paid = $res->paid;
                    $save = $res->save;
                    $tkt_fare = $res->tkt_fare;
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

                    $seats1 = explode(',', $seats);
                    $pname1 = explode(',', $pname);
                    $age1 = explode(',', $age);
                    $gender1 = explode(',', $gender);
                    $board_point1 = explode('-', $board_point);

                    $sql5 = $this->db->query("SELECT distinct op_url,op_email,other_contact,canc_terms FROM registered_operators where travel_id='$travel_id'");
                    foreach ($sql5->result() as $row5) {
                        $op_url = $row5->op_url;
                        $op_email = $row5->op_email;
                        $ph = $row5->other_contact;
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
                        } else {
                            foreach ($sql5->result() as $row5) {
                                $canc_terms = $row5->canc_terms;
                            }
                        }
                    }

                    $sql2 = $this->db->query("SELECT name FROM agents_operator where operator_id='$travel_id' and id='$agent_id'");
                    foreach ($sql2->result() as $row2) {
                        $name = $row2->name;
                    }
                    echo '<html>
                  <head>
                  <title>:: ::</title>
                  </head>
			<style type="text/css">
			a {
				text-decoration:none;
				color:#0002CC;
			}	
.btn btn-primary {

    background: #CC3300 none repeat scroll 0% 0%;

    color: #FFF;

    font-size: 15px;

    padding: 3px 25px;

    text-align: center;

    cursor: pointer;

    border: medium none #CC3300;

}
table,th,tr,td
{
    font-size:14px;
	font-family:calibri;
}
			</style>
                        <script src="' . base_url('js/app-js.v1.js') . '" type="text/javascript"></script>
			<script type="text/javascript">
			$(function() {
  				//print(pt); //works
			});
			
			function print(elem)
		    {
        		Popup($(elem).html());
		    }

		    function Popup(data) 
		    {
        		var mywindow = window.open("", "my div", "height=400,width=600");
		        mywindow.document.write("<html><head>");
				mywindow.document.write("<style type=\"text/css\">");
				mywindow.document.write("table,th,tr,td{font-size:15px;font-family:calibri;}");
				mywindow.document.write("</style>");
				mywindow.document.write("<title>My Ticket</title>");        		
		        mywindow.document.write("</head><body >");
        		mywindow.document.write(data);
		        mywindow.document.write("</body></html>");

        		mywindow.print();
		        mywindow.close();

        		return true;
		    }			                    
			</script>
                 </head>
                 <body>			
			<div align="center">';                    
                    if ($status == "confirmed") {
                        echo '<a href="javascript:void()" onClick="javascript:print(\'#pt\');">PRINT TICKET</a> ';
                    }
                    echo '</div><br />
			<div id="pt">
			
<p style="color: #d0d0d0;
  font-size: 130pt;
  -webkit-transform: rotate(-35deg);
  -moz-transform: rotate(-35deg);
  width: 90%;
  z-index: -1;
  position:absolute;
  text-align:center;
  margin-top:325px;
  opacity:0.6;
filter:alpha(opacity=60);">';
                    if ($status == "confirmed") {
                        echo "Confirmed";
                    } else if ($status == "pending") {
                        echo "pending";
                    }
                    echo '</p>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
  <tbody> 
  <tr>
    <td align="center">
      <table width="100%" border="0" cellspacing="1" cellpadding="1" >
        <tr>
          <td height="30" colspan="2" align="center"><img src="http://ticketengine.in/operator_logo/' . $travel_id . '.png"  alt="' . $travels . '" width="180" height="80" /></td>
        </tr>
        <tr>
          <td height="30" colspan="2">Ticket No : <strong>' . $pnr . '</strong>(' . $onward_tktno . ') </td>
          </tr>
        <tr>
          <td height="30" colspan="2">Ticket Details for <strong>' . $pname1[0] . '</strong> from <strong>' . $source . '</strong> to <strong>' . $dest . '</strong> on service <strong>' . $service_no . '</strong> </td>
          </tr>
        <tr>
          <td height="30" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style=" border:#CCCCCC solid 1px;border-collapse: collapse;">
            <tr>
              <td width="16%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Seat Numbers </td>
              <td width="84%" height="30" style="border-bottom:#CCCCCC solid 1px">(' . $pass . ' Seats)<br />
                ';
                    for ($i = 0; $i < $pass; $i++) {
                        echo $seats1[$i] . ' (' . $pname1[$i] . ') (' . $age1[$i] . ') (' . $gender1[$i] . ')<br />';
                    }
                    echo'</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Journey Date </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $jdate . '</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Dep Time </td>
              <td height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point1[1] . ' Report atleast 15 minutes prior to the departure time at this boarding point.</td>
            </tr>
            <tr>
              <td width="16%" height="30" style="border-right:#CCCCCC solid 1px;">Total Fare</td>
              <td height="30">' . $tkt_fare . '</td>
            </tr>
          </table></td>
          <td height="30" valign="top" width="50%"><table width="100%" border="0" cellspacing="1" cellpadding="1" style="border:#CCCCCC solid 1px">
            <tr>
              <td width="13%" height="30" valign="top" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Boarding @ </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $board_point . '<br />' . $land_mark . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;border-bottom:#CCCCCC solid 1px">Booked On </td>
              <td width="87%" height="30" style="border-bottom:#CCCCCC solid 1px">' . $time . '</td>
            </tr>
            <tr>
              <td width="13%" height="30" style="border-right:#CCCCCC solid 1px;">Booked By </td>
              <td width="87%" height="30">' . $name . '</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="30" colspan="2"><strong>Customer Service</strong></td>
          </tr>
        <tr>
          <td height="30" colspan="2">' . $ph . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">' . $op_email . '</td>
        </tr>
        <tr>
          <td height="30" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="30" colspan="2">
		  <b>You need to produce the hard copy of this ticket or a Mobile Ticket at the time of Journey.</b><br />
          <b>Terms and Conditions:</b><br/>
		  <ul>
<li style="text-align: justify;">The arrival and departure times mentioned on the ticket are only tentative timings. Busses may be delayed due to some unavoidable reasons like traffic jams etc.; However the bus will not leave the source before the time that is mentioned on the ticket</li>
<li style="text-align: justify;">
<p style=" text-align: left;">Next to ladies seat should be ladies only if in case gents are there should adjusted with different seat*&nbsp;</p>
</li>
</ul>
<p style="color: #1d1d1d; line-height: 18px; margin: 0px;" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>a.) A copy of the ticket. (A print out of the ticket or the print out of the ticket e-mail.)</strong></p>
<ul>
<li style="text-align: justify;">property of the passengers and accident.</li>
</ul>
<ul>
<li style="text-align: justify;">The company shall not be responsible for any delay or inconvenience during the journey due to breakdown of the vehicle or other reasons beyond the control of the company.</li>
</ul>
<ul>
<li>The tickets booked through ' . $travels . ' are cancellable with respect to cancellation policy.</li>
</ul>
<ul>
<li>The cancellation refund will be transfered to your respective bank accounts.</li>
</ul>		  </td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="center"><strong>For Complaints and Suggestions Please Contact us @ ' . $ph . ',' . $op_email . '</strong></td>
        </tr>
        <tr>
          <td height="30" colspan="2" align="left">
		  <table width="505" border="0" cellpadding="0" cellspacing="0">              
              <tr>
                <td style="padding:5px 0px 5px 10px;  color:#CC3300; text-decoration:underline;">Cancellation Policy </td>
              </tr>';
                    $canc_terms1 = explode('@', $canc_terms);
                    for ($i = 0; $i < count($canc_terms1); $i++) {
                        echo '<tr>
                <td style="padding:5px 0px 5px 10px;  color:#000000">';
                        $canc_terms2 = explode('#', $canc_terms1[$i]);
                        echo $canc_terms2[0] . " To " . $canc_terms2[1] . " Hours " . $canc_terms2[2] . "% shall be deducted";
                        echo '</td>
                </tr>';
                    }
                    echo'</table>		  </td>
        </tr>        
      </table>
      </td>
  </tr>    
  </tbody>
</table>	

</div><br />
<div align="center">';                    
                    if ($status == "confirmed") {
                        echo '<a href="javascript:void()" onClick="javascript:print(\'#pt\');">PRINT TICKET</a> ';
                    }
                    echo '</div>
</body>
</html>';
                }
            }
        }
    }
	
	function juno_release_seats1($mtxnid) {
        $sql = $this->db->query("select distinct seats,jdate,service_no,travel_id,tkt_no from master_booking where pnr='$mtxnid' and status='pending'") or die(mysql_error());
        foreach ($sql->result() as $row) {
            $seats = $row->seats;
            $journey_date = $row->jdate;
            $service_num = $row->service_no;
            $travel_id = $row->travel_id;
			$tkt_no = $row->tkt_no;
        }
        $seats1 = explode(',', $seats);

        for ($k = 0; $k < count($seats1); $k++) {
            $seat = $seats1[$k];

            $sql1 = $this->db->query("update layout_list set seat_status='0',is_ladies='0',status1='' where journey_date='$journey_date' and service_num='$service_num' and travel_id='$travel_id' and seat_name='$seat'")or die(mysql_error());
            //echo "update layout_list set seat_status='0',is_ladies='0',status1='' where journey_date='$journey_date' and service_num='$service_num' and travel_id='$travel_id' and seat_name='$seat'";
        }

            $sql2 = $this->db->query("update master_booking set status='released' where tkt_no='$tkt_no' and pnr='$mtxnid' and travel_id='$travel_id' and service_no='$service_num' and jdate='$journey_date'") or die(mysql_error());
        $this->db->query("update master_pass_reports set status='released' where tktno='$tkt_no' and pnr='$mtxnid' and travel_id='$travel_id' and status='pending'")or die(mysql_error());

        /*if ($sql1 && $sql2) {
            echo 1;
        } else {
            echo 0;
        }*/
    }

}
