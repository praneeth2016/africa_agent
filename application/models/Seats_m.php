<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Seats_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getSericeNumbers() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->distinct();
        $this->db->select('*');
        $array = array('travel_id' => $travel_id, 'status' => 1);
        $this->db->where($array);
        $query = $this->db->get('master_buses');
        $data = array();
        $data['0'] = "--select--";
        //$data['1']="All Services";
        foreach ($query->result() as $rows) {
            $data[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $data;
    }

    public function getSericeNumbers1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');

        $sql = $this->db->query("select distinct service_num,service_name  from  master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
        return $sql;
    }

    public function getroute1($service_num) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->distinct();
        $this->db->where('travel_id', $travel_id);
        $this->db->where('service_num', $service_num);
        $query = $this->db->get('master_buses');
        echo '<select id="service_route" class="inputfield">
	      <option value="">-- Select Service Route --</option>';

        foreach ($query->result() as $rows) {
            echo '<option value="' . $rows->from_id . '-' . $rows->to_id . '">' . $rows->service_route . '</option>';
        }
        echo '</select>';
    }

    public function getRoutesFromDb1($travel_id, $service_num, $service_name, $service_route, $city_id) {
        $city_id1 = explode("-", $city_id);
        $this->db->distinct();
        $this->db->where('travel_id', $travel_id);
        $this->db->where('service_num', $service_num);
        $this->db->where('from_id', $city_id1[0]);
        $this->db->where('to_id', $city_id1[1]);
        $query = $this->db->get('master_buses');
        return $query;
    }

    public function getroute2($service_num, $city_id) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $city_id1 = explode('-', $city_id);
        $this->db->distinct();
        $this->db->where('travel_id', $travel_id);
        $this->db->where('from_id', $city_id1[0]);
        $this->db->where('to_id', $city_id1[1]);
        $this->db->where('service_num', $service_num);
        $query = $this->db->get('master_buses');
        echo '<select id="service_route2" class="inputfield">
	      <option value="">-- Select Service Route --</option>
		<option value="all">All</option>';

        foreach ($query->result() as $rows) {
            echo '<option value="' . $rows->from_id . '-' . $rows->to_id . '">' . $rows->service_route . '</option>';
        }
        echo '</select>';
    }

    public function getfaresFromDb($travel_id, $service_num, $service_route, $current_date, $from_id, $to_id) {
        $service_route1 = explode(" To ", $service_route);
        $this->db->distinct();
        $this->db->where('travel_id', $travel_id);
        $this->db->where('service_num', $service_num);
        $this->db->where('from_id', $from_id);
        $this->db->where('to_id', $to_id);
        $this->db->where('journey_date', $current_date);
        $query = $this->db->get('buses_list');

        return $query;
    }

    function getRoutesFromDb($svc) {
        // $svc=  $this->input->post('svc');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->distinct();
        $this->db->where('service_num', $svc);
        $this->db->where('travel_id', $travel_id);
        $query = $this->db->get('master_buses');

        return $query;
    }

    public function addnewfare1() {
        $bus_type = $this->input->post('bus_type');
        $service_num = $this->input->post('service_num');
        $travel_id = $this->input->post('travel_id');
        $lower_seat_no = $this->input->post('lower_seat_no');
        $upper_seat_no = $this->input->post('upper_seat_no');
        $fdate = $this->input->post('fdate');
        $tdate = $this->input->post('tdate');
        $price_mode = $this->input->post('price_mode');
        $from_id = $this->input->post('from_id');
        $from_name = $this->input->post('from_name');
        $to_id = $this->input->post('to_id');
        $to_name = $this->input->post('to_name');
        $service_route2 = $this->input->post('service_route2');
        $city_id = $this->input->post('city_id');

        if ($service_route2 == "all") {
            $stmt = "select * from master_buses where service_num='$service_num' and travel_id='$travel_id' and status='1'";
        } else {
            $stmt = "select * from master_buses where service_num='$service_num' and travel_id='$travel_id' and status='1' and from_id='$from_id' and to_id='$to_id'";
        }
        //echo $stmt;
        $sql = $this->db->query($stmt);

        if ($price_mode == "permanently") { //Permanent Updation                       
            if ($service_route2 == "all") { //Permanent Update Total Routes
                $this->db->query("delete from master_price where service_num='$service_num' and travel_id='$travel_id' and journey_date is not NULL");
                foreach ($sql->result() as $row) {
                    $from_id = $row->from_id;
                    $from_name = $row->from_name;
                    $to_id = $row->to_id;
                    $to_name = $row->to_name;
                    $service_route = $row->service_route;
                    $service_name = $row->service_name;
                    $seat_fare = $row->seat_fare;
                    $lberth_fare = $row->lberth_fare;
                    $uberth_fare = $row->uberth_fare;

                    $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is NULL";
                    $sql1 = $this->db->query($stmt1);
                    if ($sql1->num_rows() > 0) {
                        if ($bus_type == "seater") {
                            $stmt2 = "update master_price set seat_fare_changed='$lower_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is null";
                        } else {
                            $stmt2 = "update master_price set lberth_fare_changed='$lower_seat_no',uberth_fare_changed='$upper_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is null";
                        }
                    } else {
                        if ($bus_type == "seater") {
                            $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,seat_fare_changed) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lower_seat_no')";
                        } else {
                            $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,lberth_fare,uberth_fare,lberth_fare_changed,uberth_fare_changed) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$lberth_fare','$uberth_fare','$lower_seat_no','$upper_seat_no')";
                        }
                    }
                    $query = $this->db->query($stmt2);
                }
            } else { //Permanent Update Particular Route
                $this->db->query("delete from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is not NULL");
                foreach ($sql->result() as $row) {
                    $service_route = $row->service_route;
                    $service_name = $row->service_name;
                    $seat_fare = $row->seat_fare;
                    $lberth_fare = $row->lberth_fare;
                    $uberth_fare = $row->uberth_fare;
                }
                $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is NULL";
                $sql1 = $this->db->query($stmt1);
                if ($sql1->num_rows() > 0) {
                    if ($bus_type == "seater") {
                        $stmt2 = "update master_price set seat_fare_changed='$lower_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is null";
                    } else {
                        $stmt2 = "update master_price set lberth_fare_changed='$lower_seat_no',uberth_fare_changed='$upper_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date is null";
                    }
                } else {
                    if ($bus_type == "seater") {
                        $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,seat_fare_changed) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lower_seat_no')";
                    } else {
                        $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,lberth_fare,uberth_fare,lberth_fare_changed,uberth_fare_changed) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$lberth_fare','$uberth_fare','$lower_seat_no','$upper_seat_no')";
                    }
                }
                $query = $this->db->query($stmt2);
            }
        } else { //Temporary Updation            
            if ($service_route2 == "all") { //Temporary Update Total Routes
                while ($fdate <= $tdate) {
                    $stmt = "select * from master_buses where service_num='$service_num' and travel_id='$travel_id' and status='1'";
                    $sql = $this->db->query($stmt);
                    foreach ($sql->result() as $row) {
                        $from_id = $row->from_id;
                        $from_name = $row->from_name;
                        $to_id = $row->to_id;
                        $to_name = $row->to_name;
                        $service_route = $row->service_route;
                        $service_name = $row->service_name;
                        $seat_fare = $row->seat_fare;
                        $lberth_fare = $row->lberth_fare;
                        $uberth_fare = $row->uberth_fare;

                        $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                        $sql1 = $this->db->query($stmt1);
                        if ($sql1->num_rows() > 0) {
                            if ($bus_type == "seater") {
                                $stmt2 = "update master_price set seat_fare_changed='$lower_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                            } else {
                                $stmt2 = "update master_price set lberth_fare_changed='$lower_seat_no',uberth_fare_changed='$upper_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                            }
                        } else {
                            if ($bus_type == "seater") {
                                $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,seat_fare_changed,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lower_seat_no','$fdate')";
                            } else {
                                $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,lberth_fare,uberth_fare,lberth_fare_changed,uberth_fare_changed,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$lberth_fare','$uberth_fare','$lower_seat_no','$upper_seat_no','$fdate')";
                            }
                        }
                        $query = $this->db->query($stmt2);
                    }
                    $date1 = strtotime("+1 day", strtotime($fdate));
                    $fdate = date("Y-m-d", $date1);
                }
            } else {//Temporary Update Particular Route
                while ($fdate <= $tdate) {
                    $stmt = "select * from master_buses where service_num='$service_num' and travel_id='$travel_id' and status='1' and from_id='$from_id' and to_id='$to_id'";
                    $sql = $this->db->query($stmt);
                    foreach ($sql->result() as $row) {
                        $service_route = $row->service_route;
                        $service_name = $row->service_name;
                        $seat_fare = $row->seat_fare;
                        $lberth_fare = $row->lberth_fare;
                        $uberth_fare = $row->uberth_fare;
                    }
                    $stmt1 = "select * from master_price where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                    $sql1 = $this->db->query($stmt1);
                    if ($sql1->num_rows() > 0) {
                        if ($bus_type == "seater") {
                            $stmt2 = "update master_price set seat_fare_changed='$lower_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                        } else {
                            $stmt2 = "update master_price set lberth_fare_changed='$lower_seat_no',uberth_fare_changed='$upper_seat_no' where service_num='$service_num' and travel_id='$travel_id' and from_id='$from_id' and to_id='$to_id' and journey_date='$fdate'";
                        }
                    } else {
                        if ($bus_type == "seater") {
                            $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,seat_fare_changed,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$seat_fare','$lower_seat_no','$fdate')";
                        } else {
                            $stmt2 = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,lberth_fare,uberth_fare,lberth_fare_changed,uberth_fare_changed,journey_date) values('$service_num','$travel_id','$from_id','$from_name','$to_id','$to_name','$service_route','$service_name','$lberth_fare','$uberth_fare','$lower_seat_no','$upper_seat_no','$fdate')";
                        }
                    }
                    $query = $this->db->query($stmt2);
                    $date1 = strtotime("+1 day", strtotime($fdate));
                    $fdate = date("Y-m-d", $date1);
                }
            }
        }
        if ($query) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function updateFareDb() {
        $btype = $this->input->post('btype');
        $srvnum = $this->input->post('serno');
        $travid = $this->input->post('travelid');
        $fid = $this->input->post('fid');
        $tid = $this->input->post('tid');
        $fdate = $this->input->post('fdate');
        $tdate = $this->input->post('tdate');
        $lbfare = $this->input->post('lbfare');
        $ubfare = $this->input->post('ubfare');
        $sfare = $this->input->post('sfare');
        //echo $sfare;
        $fid1 = explode("/", $fid);
        $tid1 = explode("/", $tid);
        $sfare1 = explode("/", $sfare);
        $lbfare1 = explode("/", $lbfare);
        $ubfare1 = explode("/", $ubfare);
        $ip = $this->input->ip_address();
        $time = date('Y-m-d H:m:s', time());
        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_name');
        //$fdate='2014-09-12';
        //$tdate='2014-09-15';
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
        //print_r($fid1);

        for ($i = 0; $i < count($dt); $i++) {
            for ($j = 0; $j < count($fid1); $j++) {
                $stmt = "select * from master_price where from_id='$fid1[$j]' and to_id='$tid1[$j]' and service_num='$srvnum' and travel_id='$travid' and journey_date='$dt[$i]'";
                $ssql = $this->db->query($stmt);

                if ($ssql->num_rows() > 0) {
                    $stmt = "update master_price set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]',seat_fare_changed='',lberth_fare_changed='',uberth_fare_changed='' where service_num='$srvnum' and from_id='$fid1[$j]' and to_id='$tid1[$j]' and travel_id='$travid' and journey_date='$dt[$i]'";
                    $this->db->query($stmt);
                } else {
                    $stmt = "select * from master_buses where from_id='$fid1[$j]' and to_id='$tid1[$j]' and service_num='$srvnum' and travel_id='$travid'";
                    $sql = $this->db->query($stmt);
                    foreach ($sql->result as $row) {
                        $from_name = $row->from_name;
                        $to_name = $row->to_name;
                        $service_route = $row->service_route;
                        $service_name = $row->service_name;
                    }
                    //updating in master_price 
                    $stmt = "insert into master_price(service_num,travel_id,from_id,from_name,to_id,to_name,service_route,service_name,seat_fare,lberth_fare,uberth_fare,journey_date) 
            values('$srvnum','$travid','$fid1[$j]','$from_name','$tid1[$j]','$to_name','$service_route','$service_name','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$dt[$i]')";
                    $data1 = $this->db->query($stmt);
                }

                //insert into master_change_pricing table
                $stmt = "insert into master_change_pricing(travel_id,service_num,from_id,to_id,new_seat_fare,new_lberth_fare,new_uberth_fare,change_time,ip_address,journey_date,updated_by_id,updated_by)values('$travid','$srvnum','$fid1[$j]','$tid1[$j]','$sfare1[$j]','$lbfare1[$j]','$ubfare1[$j]','$time','$ip','$dt[$i]','$user_id','$name')";
                $data = $this->db->query($stmt);

                //updating in buses_list table
                $stmt = "update buses_list set seat_fare='$sfare1[$j]',lberth_fare='$lbfare1[$j]' ,uberth_fare='$ubfare1[$j]' where service_num='$srvnum' and from_id='$fid1[$j]' and to_id='$tid1[$j]' and travel_id='$travid' and journey_date='$dt[$i]'";
                $da = $this->db->query($stmt);
            }
        }
        if ($data && $da) {

            //$user = "pridhvi@msn.com:activa1525@";
            $phone = $this->db->query("SELECT `contact_no`, `sender_id` FROM `registered_operators` where travel_id='$travid'");
            foreach ($phone->result() as $row) {
                $receipientno = $row->contact_no;
               $dialCode = $row->dialCode;
            }

			$text = "Price changed for service No. " . $srvnum . " For DOJ " . $fdate . " - " . $tdate;
			$resp = $this->Seats_m->msg91sms($receipientno,$text,$dialCode);
			
           
          /*$ch = curl_init();
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

            curl_close($ch);  */

            echo 1;
        } else {
            echo 0;
        }  
    }

    function getServicesList($key) {
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
        // return $query2->result();
    }

    function getServicesListDetails() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $srvno = $this->input->post('service');
        $this->db->distinct();
        $this->db->select('*');
        $this->db->from('master_buses m');
        $this->db->join('master_layouts l', 'l.service_num = m.service_num');
        $this->db->where('m.travel_id', $travel_id);
        $this->db->where('m.service_num', $srvno);
        $this->db->where('l.service_num', $srvno);
        $this->db->group_by('m.service_num');
        $query2 = $this->db->get();
        $i = 1;
        if ($query2->num_rows() > 0) {
            echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">
                  
                  <tr>
                    <td height="106" valign="top"><table width="108%" border="0" cellpadding="0" cellspacing="0" style="border-top:#f2f2f2 solid 5px;">
                       
                               
                                <tr>
                                  <td class="space" >&nbsp;</td>
                                  <td class="space" >&nbsp;</td>
                                  <td class="space" >&nbsp;</td>
                                  <td class="space" >&nbsp;</td>
                                  <td class="space" >&nbsp;</td>
                                  <td class="space" >&nbsp;</td>
                                  
                                </tr>
                                <tr style="font-weight:bold;">
                                  <td height="48" class="space" >SNo.</td>
                                   <td height="48" class="space" >Service Name<td><span class="space">Service Number</span>
                                  <td height="48" class="space" >Bus Type</td>
                                   <td height="48" class="space" >View </td>
                                   <td height="48" class="space" >Action</td>
                                  
                                </tr>
                              </thead>
                              <tbody>';
            foreach ($query2->result() as $row) {

                $travid = $row->travel_id;
                if ($row->status != 0 || $row->status != '') {
                    //values for Active or Deactive

                    $key1 = 'Deactive';
                    $key2 = 'Active';
                    //$st='<input type="button" class="btn btn-primary" name="act'.$travid.$i.'" id="act'.$travid.$i.'" value="Click To Deactive" onClick="deActivateBus(\''.$srvno.'\','.$travid.','.$i.','.$row->status.','.$row->from_id.','.$row->to_id.')">';
                    $st = '<a class="btn btn-primary" onClick="deActivateBus(\'' . $key1 . '\',\'' . $srvno . '\',' . $travid . ',' . $i . ',' . $row->status . ',' . $row->from_id . ',' . $row->to_id . ')">Cancel Service</a>';
                    echo ' <tr >
    <td height="30" class="space" >' . $i . '</td> 
    <td  class="space">' . $row->service_name . '</td>       
    <td  class="space">' . $srvno . '</td>
   <td class="space" >' . $row->model . '</td>
    <td class="space"><a class="btn btn-primary" href="' . base_url() . 'Seats/ViewHistory?srvno=' . $srvno . '" target="_blank">View</a></td>    
    <td  class="space" >' . $st . '</td>
    </tr>
    <tr  style="display:none;"  >
 <td  colspan="7"  align="center"  ></td>
  </tr>
  <tr id="tr' . $i . '"  style="display:none;"  >
 <td height="6"  colspan="7" align="center" id="dp' . $i . '"  ></td>
  </tr>    
';
                }
            }
            echo '<input type="hidden" id="hdd" value="' . $i . '" >';
            echo ' </table></td>
                  </tr>
                  <tr>
                    <td height="5"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>                  
                </table>';
        } else {
            echo 0;
        }
    }

    function getServicesListDetails_quata() {

        $travel_id = $this->session->userdata('bktravels_travel_id');
        $srvno = $this->input->post('service');

        $this->db->select('*');
        $this->db->where('status', 1);
        $this->db->where("travel_id", $travel_id);
        $this->db->where("service_num", $srvno);
        $this->db->group_by("service_num");
        $query2 = $this->db->get("master_buses");
        $s = 1;

        if ($query2->num_rows() > 0) {
            echo ' <table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><table width="99%" border="0"  align="center">
			<tr>
				<td>&nbsp;</td>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
			</tr>
			<tr style="font-weight:bold;">
				<td width="154" height="30" class="space">Service Number</td>
				<td width="261"  class="space">Service Name</td>
				<td width="289"  class="space">BusType</td>
				<td width="275"  class="space">Departure</td>
				<td width="94" class="space" >Update Quota</td>
				<td width="104"  class="space">Show Quota</td>
			</tr>
			';
            foreach ($query2->result() as $row) {
                $travel_id = $row->travel_id;
                //$class = ($s%2 == 0)? 'bg': 'bg1';

                echo "
			<tr>
				<td  class='space' height='30'>" . $row->service_num . "</td>
				<td  class='space' height='30'>" . $row->service_name . "</td>
				<td  class='space' height='30'>" . $row->model . "</td>
				<td  class='space' height='30'>" . date('h:i A', strtotime($row->start_time)) . "</td>
				<td  class='space' height='30'><input type='button' class='btn btn-primary'name='uq" . $s . "' id='uq" . $s . "' value='Update Quota'  onClick='showLayout(\"" . $row->service_num . "\"," . $travel_id . "," . $s . ")'/></td>
				<td  class='space' height='30'><input type='button' class='btn btn-primary' value='View Quota' onClick='viewLayoutQuota(\"" . $row->service_num . "\"," . $travel_id . "," . $s . ")'/>
					<input type='hidden' name='grab_seats' id='grab_seats' value='' />
				</td>
			</tr>
			<tr>
				<td colspan='6' style='font-size:14px; display:none' id='trr" . $s . "' align='center'></td>
			</tr>
			";
            }
            echo '
		</table>
		<input type="hidden" id="hf" value="' . $s . '">
	</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>';
        } else {
            echo 0;
        }
    }

    function display_LayoutOfQuota($sernum, $travel_id) {
        $this->db->select('layout_id,seat_type');
        $this->db->where('service_num', $sernum);
        $this->db->where('travel_id', $travel_id);
        $sql = $this->db->get('master_layouts');
        foreach ($sql->result() as $row) {
            $layout_id = $row->layout_id;
            $seat_type = $row->seat_type;
            $lid = explode("#", $layout_id);
        }
        echo '<table width="100%" border="0" align="center" cellpadding="10px" style="border:#f2f2f2 solid 1px;">
  <tr >
    <td align="left" style="border-bottom:#f2f2f2 solid 2px;font-weight:bold; font-size:12px;">&nbsp;</td>
    <td align="center" style="border-bottom:#f2f2f2 solid 2px;font-weight:bold; font-size:12px;">Layout</td>
    <td align="center" style="border-bottom:#f2f2f2 solid 2px;font-weight:bold; font-size:12px;">Reserved Seats</td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
    <td align="center"><table border="0" style="font-size:12px;">';
        if ($lid[1] == 'seater') {
            //getting max of row and col from mas_layouts
            $this->db->select_max('row', 'mrow');
            $this->db->select_max('col', 'mcol');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $sq11 = $this->db->get('master_layouts');
            $seat_name = '';
            foreach ($sq11->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }
            echo "<table border='1' cellpadding='0' align='center' >";
            for ($i = 1; $i <= $mcol; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $mrow; $j++) {
                    $this->db->select('*');
                    $this->db->where('row', $j);
                    $this->db->where('col', $i);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $sql3 = $this->db->get('master_layouts');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $available_type = $row2->available_type;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }

            echo '</table></td>';

            echo '<td align="center"><table border="0" style="font-size:12px;">';

            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $inhouse = $this->db->get("master_layouts");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $agent = $this->db->get("master_layouts");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats2 = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        } else if ($lid[1] == 'sleeper') {
            //getting max of row and col from mas_layouts
            //UpperDeck
            $this->db->select_max('row', 'mrow');
            $this->db->select_max('col', 'mcol');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('seat_type', 'U');
            $sq1 = $this->db->get('master_layouts');
            foreach ($sq1->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }


            echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcol; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mrow; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('seat_type', 'U');
                    $sql3 = $this->db->get('master_layouts');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td style='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo "</table><br/>";
            // Lower Deck
            $this->db->select_max('row', 'mroww');
            $this->db->select_max('col', 'mcoll');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('seat_type', 'L');
            $sq1l = $this->db->get('master_layouts');
            foreach ($sq1l->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('seat_type', 'L');
                    $sql3 = $this->db->get('master_layouts');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td style='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo '</table></td>';

            echo '<td align="center"><table border="0" style="font-size:12px;" width="350">';
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $inhouse = $this->db->get("master_layouts");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline;" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $agent = $this->db->get("master_layouts");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats2 = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        } else if ($lid[1] == 'seatersleeper') {

            //getting max of row and col from mas_layouts
            //UpperDeck
            $this->db->select_max('row', 'mrow');
            $this->db->select_max('col', 'mcol');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where("(seat_type='U' OR seat_type='U')");
            $sqll = $this->db->get('master_layouts');
            foreach ($sqll->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcol; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mrow; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where("(seat_type='U' OR seat_type='U')");
                    $sql3 = $this->db->get('master_layouts');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                        $available_type = $row2->available_type;
                        $seat_type = $row2->seat_type;
                    }
                    if ($seat_type == 'U')
                        $st = "(B)";
                    else if ($seat_type == 'U')
                        $st = "(S)";

                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            } //outer for
            echo "</table><br/>";
            // Lower Deck

            $this->db->select_max('row', 'mroww');
            $this->db->select_max('col', 'mcoll');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
            $sq1l = $this->db->get('master_layouts');
            foreach ($sq1l->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
                    $sql3 = $this->db->get('master_layouts');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                        $available_type = $row2->available_type;
                        $seat_type = $row2->seat_type;
                    }
                    if ($seat_type == 'L:b')
                        $st = "(B)";
                    else if ($seat_type == 'L:s')
                        $st = "(S)";
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo '</table></td>';

            echo '<td align="center"><table border="0" style="font-size:12px;">';
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $inhouse = $this->db->get("master_layouts");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $agent = $this->db->get("master_layouts");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats2 = $this->db->get("master_layouts");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        }//if(seatersleeper)
    }

    function getLayoutForQuotaDb($sernum, $travel_id, $s) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        //query for getting seat_type
        $seat_name = '';
        $res_seats = '';
        $query = $this->db->query("select layout_id,seat_type from master_layouts where service_num='$sernum' and travel_id='$travel_id'  ");
        foreach ($query->result() as $r) {
            $layout_id = $r->layout_id;
            $seat_type = $r->seat_type;
            $lid = explode("#", $layout_id);
        }
        echo '<table width="100%" border="0" align="center" style="border:#f2f2f2 solid 0px;">
          ';
        echo'
     <tr>
      <td align="center">';
        if ($lid[1] == 'seater') {
            //getting max of row and col from master_layouts
            $sq = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$sernum' and travel_id='$travel_id' ") or die(mysql_error());
            foreach ($sq->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }
            echo "<input type='hidden' name='mrow' id='mrow' value='$mrow' />
		<input type='hidden' name='mcol' id='mcol' value='$mcol' />";

            echo "<table border='0' cellpadding='10' cellspacing='4' align='center' >";

            for ($i = 1; $i <= $mcol; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql3 = $this->db->query("select * from master_layouts where row='$j' and col='$i' and service_num='$sernum' and travel_id='$travel_id' ") or die(mysql_error());
                    $sql3->result();
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
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
                            $style = "style='background-color: #E4E4E4; width:20px'";
                            $id = "c$i$j";
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
            <td width="230" align="center"><span id="updtspan' . $s . '" class="label">Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select class="inputfield" name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ')">
              <option value="">--select--</option>
              <option value="1">Branch</option>
              <option value="2">Agent</option>
            </select></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td ><span style="color:#000;display:none;" id="uqa' . $s . '" class="label">Select Agent Name TO Give  the Quota:</span>
     <span style="color:#000;display:none;" id="uqi' . $s . '" class="label">Select Branch Name to Update the Quota </span>   </td>
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
            <td width="162" align="left">Quota Removing Seats are : </td>
            <td width="280" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
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
            $sq2 = $this->db->query("select max(row) as mrow,max(col) as mcol from master_layouts where service_num='$sernum' and travel_id='$travel_id' and seat_type='U' ");
            foreach ($sq2->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
            echo "<table border='0' cellpadding='10' cellspacing='4'>";
            for ($k = 1; $k <= $mcol; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mrow; $l++) {
                    $sq3 = $this->db->query("select * from master_layouts where row='$l' and col='$k' and service_num='$sernum' and travel_id='$travel_id' and seat_type='U' ");
                    foreach ($sq3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
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
            $sq4 = $this->db->query("select max(row) as mroww,max(col) as mcoll from master_layouts where service_num='$sernum' and travel_id='$travel_id' and seat_type='L' ") or die(mysql_error());
            foreach ($sq4->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='0' cellpadding='10' cellspacing='4'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $sql3 = $this->db->query("select * from master_layouts where row='$l' and col='$k' and service_num='$sernum' and travel_id='$travel_id' and seat_type='L' ") or die(mysql_error());
                    $sql3->result();
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $seat_status = $row2->seat_status;
                    }

                    if ($seat_name == '' || $seat_name == 'GY') {
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
            echo '<table width="100%" border="0" id="chkd' . $s . '" style="font-size:12px; display:none;">
        <tr>
          <td height="27" align="center">&nbsp;</td>
            <td align="right">New Quota Seats are : </td>
            <td style="max-width:10px;" id="gb' . $s . '" align="left"></td>
          </tr>
          <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="230" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ')">
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
            <td width="162" align="left">Quota Removing Seats are : </td>
            <td width="280" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
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
            $sqll = $this->db->get('master_layouts');

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
                    $sql3 = $this->db->get('master_layouts');

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


                    if ($seat_name == '' || $seat_name == 'GY') {
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
                            $style = "style='background-color: #f2f2f2; width:20px'";
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
            $sql3 = $this->db->get('master_layouts');
            foreach ($sql3->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }

            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='0' cellpadding='10' cellspacing='4'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
                    $sql3 = $this->db->get('master_layouts');
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
                    if ($seat_name == '' || $seat_name == 'GY') {
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
            echo '<table width="100%" border="0" id="chkd' . $s . '" style="font-size:12px; display:none;">
        <tr>
          <td height="27" align="center">&nbsp;</td>
            <td align="right">New Quota Seats are : </td>
            <td style="max-width:10px;" id="gb' . $s . '" align="left"></td>
          </tr>
          <tr>
            <td width="131" height="31" align="center">&nbsp;</td>
            <td width="230" align="center"><span id="updtspan' . $s . '" >Kindly Select Agent Type to give the Quota :</span></td>
            <td width="200"><select name="atype' . $s . '" id="atype' . $s . '" onChange="agentType(' . $s . ')">
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
            <td width="162" align="left">Quota Removing Seats are : </td>
            <td width="280" align="left" style="max-width:10px;" id="rl' . $s . '"></td>
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

    public function geAgentName($id) {
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

    function updateQuota($sernum, $travel_id, $seats, $agent_id, $agent_type, $c) {

        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_name');

        if ($c == 1) {
            $st = explode(",", $seats);
            $ip = $this->input->ip_address();
            for ($i = 0; $i < count($st); $i++) {
                //storing in quota_update_history table
                $data_insert = array(
                    'service_num' => $sernum,
                    'travel_id' => $travel_id,
                    'seat_name' => $st[$i],
                    'available' => $agent_type,
                    'available_type' => $agent_id,
                    'ip' => $ip,
                    'updated_by_id' => $user_id,
                    'updated_by' => $name
                );
                $this->db->insert('quota_update_history', $data_insert);
                $q = $this->db->query("update master_layouts set available='$agent_type', available_type ='$agent_id' where travel_id='$travel_id' and service_num='$sernum' and seat_name='$st[$i]'");
                $q1 = $this->db->query("update layout_list set available='$agent_type', available_type ='$agent_id' where travel_id='$travel_id' and service_num='$sernum' and seat_name='$st[$i]'");
            }//for
            if ($agent_type == 1) {
                $data = array(
                    'show_avail_seat' => 'no',
                    'show_quota' => 'no'
                );
                $this->db->where('travel_id', $travel_id);
                $this->db->where('available', '2');
                $this->db->update('layout_list', $data);
            } else if ($agent_type == 2) {
                $data = array(
                    'show_avail_seat' => 'no',
                    'show_quota' => 'yes'
                );
                $this->db->where('travel_id', $travel_id);
                $this->db->where('available_type', $agent_id);
                $this->db->update('layout_list', $data);
            }
        } else if ($c == 2) {
            $st = explode(",", $seats);
            for ($i = 0; $i < count($st); $i++) {
                //storing in quota_update_history table
                $data_insert = array(
                    'service_num' => $sernum,
                    'travel_id' => $travel_id,
                    'seat_name' => $st[$i],
                    'available' => 0,
                    'available_type' => 0,
                    'ip' => $ip,
                    'updated_by_id' => $user_id,
                    'updated_by' => $name
                );
                $this->db->insert('quota_update_history', $data_insert);
                //storing in quota_update_history table                
                $q2 = $this->db->query("update master_layouts set available='0', available_type ='0' where travel_id='$travel_id' and service_num='$sernum' and seat_name='$st[$i]'");
                $q3 = $this->db->query("update layout_list set available='0', available_type ='0',show_avail_seat='no',show_quota='no' where travel_id='$travel_id' and service_num='$sernum' and seat_name='$st[$i]'");
            }//for
        }

        if ($q && $q1) {
            echo 1;
        } else if ($q2 && $q3) {
            echo 2;
        } else {
            echo 3;
        }
    }

    function detail_breakdown() {
        $srvno = $this->input->get('srvno');
        $this->db->select('*');
        $this->db->from('breakdown_history as b');
        $this->db->where('b.service_num', $srvno);
        $this->db->where('b.status', 'Deactive');
        $this->db->join('master_buses as bl', 'bl.service_num=b.service_num');
        $query = $this->db->get();
        return $query->result();
    }

    function deActivateBusDb($key, $sernum, $travid, $fdate1, $tdate1, $status, $cnt, $s, $fromid, $toid, $chkedRadio) {
        $user_id = $this->session->userdata('bktravels_user_id');
        $name = $this->session->userdata('bktravels_name');

        $sql = $this->db->query("select distinct sender_id,operator_title,op_url from registered_operators where travel_id='$travid'") or die(mysql_error());
        foreach ($sql->result() as $row) {
            $senderID = $row->sender_id;
            $operator_title = $row->operator_title;
            $op_url = $row->op_url;
        }
        $fdate = date('Y-m-d', strtotime($fdate1));
        $tdate = date('Y-m-d', strtotime($tdate1));
        $curdate = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $ip = $this->input->ip_address();
        if (trim($key) == 'Deactive') {
            $query = $this->db->query("select * from breakdown_history where service_num='$sernum' and travel_id='$travid' and from_id='$fromid' and to_id='$toid' and breakdown_date between '$fdate' and '$tdate' and current_date='$curdate' and status='Deactive'");
            if ($query->num_rows() == 0) {
                while ($fdate <= $tdate) {
                    if ($chkedRadio == 'cancelled') {
                        $sql = $this->db->query("update buses_list set status='2' where service_num='$sernum' and travel_id='$travid' and journey_date='$fdate'")or die(mysql_error());
                        $sql2 = $this->db->query("update layout_list set status='2' where service_num='$sernum' and travel_id='$travid' and journey_date='$fdate'")or die(mysql_error());
                        //inserting cancelled service tickets in master table 

                        $query5 = $this->db->query("select * from master_booking where service_no='$sernum' and  jdate='$fdate' and travel_id='$travid' and LOWER(status)='confirmed'");
                        foreach ($query5->result() as $val) {
                            if ($val->paid == '' || $val->paid == 0) {
                                $paid = $val->tkt_fare;
                            } else {
                                $paid = $val->paid;
                            }
                            $agentid = $val->agent_id;
                            $tktno = $val->tkt_no;
                            $queryy = $this->db->query("select * from master_booking where tkt_no='$tktno' and service_no='$sernum' and  jdate='$fdate' and travel_id='$travid' and LOWER(status)='cancelled'");
                            //echo "select * from master_booking where tkt_no='$tktno' and service_no='$sernum' and  jdate='$fdate' and travel_id='$travid' and LOWER(status)='cancelled'";
                            if ($queryy->num_rows() <= 0) {
                                $data1 = array('tkt_no' => $val->tkt_no, 'pnr' => $val->pnr, 'service_no' => $val->service_no, 'board_point' => $val->board_point, 'bpid' => $val->bpid, 'land_mark' => $val->land_mark, 'source' => $val->source, 'dest' => $val->dest, 'travels' => $val->travels, 'bus_type' => $val->bus_type, 'bdate' => $val->bdate, 'jdate' => $val->jdate, 'seats' => $val->seats, 'gender' => $val->gender, 'start_time' => $val->start_time, 'arr_time' => $val->arr_time, 'paid' => $val->paid, 'save' => $val->save, 'tkt_fare' => $val->tkt_fare, 'base_fare' => $val->base_fare, 'service_tax_amount' => $val->service_tax_amount, 'discount_amount' => $val->discount_amount, 'convenience_charge' => $val->convenience_charge, 'promo_code' => $val->promo_code, 'pname' => $val->pname, 'pemail' => $val->pemail, 'pmobile' => $val->pmobile, 'age' => $val->age, 'refno' => $val->refno, 'status' => 'cancelled', 'pass' => $val->pass, 'cseat' => $val->cseat, 'ccharge' => '0', 'camt' => '0', 'refamt' => $paid, 'travel_id' => $val->travel_id, 'mail_stat' => $val->mail_stat, 'sms_stat' => $val->sms_stat, 'ip' => $ip, 'time' => $val->time, 'cdate' => $curdate, 'ctime' => $now, 'id_type' => $val->id_type, 'id_num' => $val->id_num, 'padd' => $val->padd, 'alter_ph' => $val->alter_ph, 'fid' => $val->fid, 'tid' => $val->tid, 'operator_agent_type' => $val->operator_agent_type, 'agent_id' => $val->agent_id, 'is_buscancel' => 'yes', 'book_pay_type' => $val->book_pay_type, 'book_pay_agent' => $val->book_pay_agent);
                                $this->db->insert('master_booking', $data1);
                                //$sql = $this->db->query("insert into master_booking() values()");
                                $query = $this->db->query("select * from agents_operator where id='$agentid' and operator_id='$travid' ")or die(mysql_error());
                                foreach ($query->result() as $res) {
                                    $bal = $res->balance;
                                }
                                $ball = $bal + $paid;
                                $this->db->query("update agents_operator set balance='$ball' where id='$agentid' and operator_id='$travid' ")or die(mysql_error());
                                $to = $val->pemail;
                                $subject = "Service Cancelled - " . $operator_title;
                                $message = '<html><head></head><body><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25">Dear Customer, </td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td height="25">The below service is cancelled. Sorry for the inconvenice </td>
  </tr>
  <tr>
    <td height="25"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Ticket Number </td>
        <td align="center">' . $val->tkt_no . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">PNR Number </td>
        <td align="center">' . $val->pnr . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Service Number </td>
        <td align="center">' . $val->service_no . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Source</td>
        <td align="center">' . $val->source . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Destination</td>
        <td align="center">' . $val->dest . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Journey Date</td>
        <td align="center">' . $val->jdate . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td height="25" align="center">Seats</td>
        <td align="center">' . $val->seats . '</td>
        <td align="center">&nbsp;</td>
        <td height="25" align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table></body></html>';
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                $headers .= 'From: <info@ticketengine.in>' . "\r\n";
                                mail($to, $subject, $message, $headers);
                                
								$user = "pridhvi@msn.com:activa1525@";
                                $receipientno = $val->pmobile;
								$dialCode = $val->dialCode;
                                $text = "BUS CANCELLED for tck No. " . $val->tkt_no . " booked in " . $val->travels . " with DOJ " . $val->jdate . "";
                                $resp = $this->Seats_m->msg91sms($receipientno,$text,$dialCode);
								/*
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
                                curl_close($ch);  */
                            }
                        }
                    }
                    //checking wether the record is already there or not
                    $query12 = $this->db->query("select * from breakdown_history where service_num='$sernum' and travel_id='$travid' and to_id='$toid'  and breakdown_date='$fdate' and status='Active'");
                    // echo $query->num_rows();
                    if ($query12->num_rows() == 0) {
                        $data = array(
                            'service_num' => $sernum,
                            'from_id' => $fromid,
                            'to_id' => $toid,
                            'current_date' => $curdate,
                            'breakdown_date' => $fdate,
                            'travel_id' => $travid,
                            'status' => $key,
                            'is_cancelled_or_alternative' => $chkedRadio,
                            'updated_by_id' => $user_id,
                            'updated_by' => $name
                        );
                        $st = $this->db->insert('breakdown_history', $data);
                    }//if($query12->num_rows()==0)
                    else {
                        $st = $this->db->query("update breakdown_history set status='$key',is_cancelled_or_alternative='$chkedRadio' where service_num='$sernum' and travel_id='$travid' and to_id='$toid' and breakdown_date='$fdate' and status='Active'") or die(mysql_error());
                    }//else
                    //increamenting the date
                    $dat = strtotime("+1 day", strtotime($fdate));
                    $fdate = date("Y-m-d", $dat);
                }//while
                if ($chkedRadio == 'cancelled') {
                    $m = $this->db->query("select distinct email from api_support where status='1'");
                    foreach ($m->result() as $row) {
                        $to = $row->email;
                        $subject = "Service Cancelled - " . $operator_title;
                        $message = '<html><head></head><body><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25">Dear API Client, </td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td height="25">The <strong>' . $sernum . '(' . $operator_title . ')</strong> service is cancelled from ' . $fdate1 . ' to ' . $tdate1 . '. </td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
  </tr>
  <tr>
    <td height="25"><p>Thanks &amp; Regards,</p>
    <p>TICKET ENGINE  </p>
    <p>7799099995 </p></td>
  </tr>
</table>
</body></html>';
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        $headers .= 'From: <support@ticketengine.in>' . "\r\n";

                        mail($to, $subject, $message, $headers);
                    }

                    if ($sql && $sql2 && $st) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                }//if cancel
                else {
                    if ($st)
                        echo 1;
                    else
                        echo 0;
                }
            }//if deactive
            else {
                echo 2;
            }
        } else if (trim($key) == 'Active') {
            $fdate = date('Y-m-d', strtotime($fdate1));
            $tdate = date('Y-m-d', strtotime($tdate1));
            $curdate = date('Y-m-d');
            $now = date('H:i:s');

            while ($fdate <= $tdate) {
                //checking in Db Whether the record is there or not
                $array4 = array('service_num' => $sernum, 'travel_id' => $travid, 'breakdown_date' => $fdate, 'from_id' => $fromid, 'to_id' => $toid, 'status' => Deactive);
                $this->db->select('*');
                $this->db->where($array4);
                $sql1 = $this->db->get('breakdown_history');
                $rowcount = $sql1->num_rows();
                if ($rowcount != 0) {
                    $this->db->set('status', 'Active');
                    $array5 = array('service_num' => $sernum, 'travel_id' => $travid, 'breakdown_date' => $fdate, 'from_id' => $fromid, 'to_id' => $toid, 'status' => Deactive);
                    $this->db->where($array5);
                    $sql2 = $this->db->update('breakdown_history');
                    //updating status as active  in buses_list,layout_list
                    $this->db->set('t1.status', 1);
                    $this->db->set('t2.status', 1);
                    //$this->db->set('t2.is_ladies', 0);
                    //$this->db->set('t2.seat_status', 0);
                    $array3 = array('t1.service_num' => $sernum, 't1.travel_id' => $travid, 't1.journey_date' => $fdate, 't2.service_num' => $sernum, 't2.travel_id' => $travid, 't2.journey_date' => $fdate);
                    $this->db->where($array3);
                    $sql = $this->db->update('buses_list as t1,layout_list as t2');

                    $stmt = "select * from master_booking where service_no='$sernum' and  jdate='$fdate' and travel_id='$travid' and LOWER(status)='confirmed'";
                    $query = $this->db->query($stmt);
                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $row) {
                            $agentid = $row->agent_id;
                            $tktno = $row->tkt_no;

                            if ($row->paid == '' || $row->paid == 0)
                                $paid = $row->tkt_fare;
                            else {
                                $paid = $row->paid;
                            }

                            $stmt1 = "update master_booking set status='reactivated' where service_no='$sernum' and  jdate='$fdate' and travel_id='$travid' and LOWER(status)='cancelled' and is_buscancel='yes'";
                            $this->db->query($stmt1);

                            $query = $this->db->query("select * from agents_operator where id='$agentid' and operator_id='$travid' ")or die(mysql_error());
                            foreach ($query->result() as $res) {
                                $bal = $res->balance;
                            }
                            $ball = $bal - $paid;
                            $this->db->query("update agents_operator set balance='$ball' where id='$agentid' and operator_id='$travid' ")or die(mysql_error());
                        }
                    }
                }
                $dat = strtotime("+1 day", strtotime($fdate));
                $fdate = date("Y-m-d", $dat);
            }//while
            if ($sql2)
                echo 1;
            else
                echo 0;
        }
    }

    function ListOfService($date, $serno) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select('*');
        $this->db->from('buses_list as b');
        $this->db->where('b.travel_id', $travel_id);
        $this->db->where('b.journey_date', $date);
        $this->db->where('b.status', 1);
        $this->db->join('master_buses as mb', 'mb.service_num = b.service_num');
        $this->db->where('mb.service_num', $serno);
        $this->db->group_by('b.service_num');
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            echo '<table width="80%" border="0"  align="center">
	<tr>
		<th height="21">Service Number</td>
		<th height="21" >Service Name</td>
		<th height="21" >BusType</td>
		<th height="21" >Quota</td>
		<th height="21" >Status</td>
	</tr>
	';
            $s = 1;

            foreach ($result->result() as $row) {
                //$class = ($s%2 == 0)? 'bg': 'bg1';
                echo'
	<tr>
		<td height="30">' . $row->service_num . '</td>
		<td height="30">' . $row->service_name . '</td>
		<td height="30">' . $row->bus_type . '</td>
		<td height="30"><input type="button" class="btn btn-primary" name="uq' . $s . '" id="uq' . $s . '" value="Grab and Release" onClick="showLayout(\'' . $row->service_num . '\',' . $travel_id . ',' . $s . ',\'' . $date . '\')" /></td>
		<td height="30"><input type="button" class="btn btn-primary" name="vq' . $s . '" id="vq' . $s . '" value="View Updated Quota"  onClick="showUpdatedLayout(\'' . $row->service_num . '\',' . $travel_id . ',' . $s . ',\'' . $date . '\')" />
			<input type="hidden" value="' . $date . '" name="dt' . $s . '" id="dt' . $s . '" />
		</td>
	</tr>
	<tr>
		<td colspan="6" style="font-size:14px; display:none" id="trr' . $s . '" aligin="center"></td>
	</tr>
	';
                $s++;
            }
            echo "
	<input type='hidden' id='hf' value='" . $s . "'/>
</table>";
        } else {
            echo 0;
        }
    }

    function getLayoutOfGrabRelease($sernum, $travel_id, $s, $date) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        //query for getting seat_type
        $seat_name = '';
        $res_seats = '';
        $query = $this->db->query("select layout_id,seat_type from layout_list where service_num='$sernum' and travel_id='$travel_id' and journey_date='$date' ");
        foreach ($query->result() as $r) {
            $layout_id = $r->layout_id;
            $seat_type = $r->seat_type;
            $lid = explode("#", $layout_id);
        }
        echo '<script type="text/javascript">
    $(function()
    {       
	$("#from_date").datepicker({dateFormat: "yy-mm-dd", numberOfMonths: 1, showButtonPanel: false, minDate: 0,"autoclose": true});
	$("#to_date").datepicker({dateFormat: "yy-mm-dd", numberOfMonths: 1, showButtonPanel: false, minDate: 0,"autoclose": true});
    });
</script>
';
        echo '<table width="100%" border="0" align="center" style="margin-top:15px;">
          <tr>
          <td height="40" align="center"><span style="padding-right:25px;">From Date : <input type="text" name="from_date" id="from_date" value="' . $date . '" size="12" readonly="" /></span>
              To Date : <input type="text" name="to_date" id="to_date" value="' . $date . '" size="12" readonly="" /></td>
          </tr>';
        echo'
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
                    if ($seat_name == '' || $seat_name == 'GY') {
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
                    if ($seat_name == '' || $seat_name == 'GY') {
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

                    if ($seat_name == '' || $seat_name == 'GY') {
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


                    if ($seat_name == '' || $seat_name == 'GY') {
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
                    if ($seat_name == '' || $seat_name == 'GY') {
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

    function display_LayoutOfGrabRelease($sernum, $travel_id, $date) {
        $this->db->select('layout_id,seat_name,seat_type,');
        $this->db->where('service_num', $sernum);
        $this->db->where('travel_id', $travel_id);
        $this->db->where('journey_date', $date);
        $this->db->where('status', 1);
        $sql = $this->db->get('layout_list');
        foreach ($sql->result() as $row) {
            $layout_id = $row->layout_id;
            $seat_type = $row->seat_type;
            $seat_name = $row->seat_name;
            $lid = explode("#", $layout_id);
        }
        echo '<table width="100%" border="0" align="center" cellpadding="0" style="margin-top:15px;">
  <tr >
    <td align="left">&nbsp;</td>
    <td align="center">Layout</td>
    <td align="center">&nbsp;</td>
	<td align="left">Reserved Seats</td>
	
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
    <td align="center"><table border="0" style="font-size:12px;">';
        if ($lid[1] == 'seater') {
            //getting max of row and col from mas_layouts
            $this->db->select_max('row', 'mrow');
            $this->db->select_max('col', 'mcol');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $sq11 = $this->db->get('layout_list');
            $seat_name = '';
            foreach ($sq11->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }
            echo "<table border='1' cellpadding='0' cellspacing='3' align='center' >";
            for ($i = 1; $i <= $mcol; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $mrow; $j++) {
                    $this->db->select('*');
                    $this->db->where('row', $j);
                    $this->db->where('col', $i);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $sql3 = $this->db->get('layout_list');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $seat_type = $row2->seat_type;
                        $available = $row2->available;
                        $available_type = $row2->available_type;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }

            echo '</table></td>';

            echo '<td align="center"><table border="0" style="font-size:12px;">';

            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $inhouse = $this->db->get("layout_list");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $get_seats = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $agent = $this->db->get("layout_list");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $get_seats2 = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        } else if ($lid[1] == 'sleeper') {
            //getting max of row and col from mas_layouts
            //UpperDeck
            $this->db->select_max('row', 'mrow');
            $this->db->select_max('col', 'mcol');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('seat_type', 'U');
            $this->db->where('journey_date', $date);
            $this->db->where('journey_date', $date);
            $sq1 = $this->db->get('layout_list');
            foreach ($sq1->result() as $row1) {
                $mrow = $row1->mrow;
                $mcol = $row1->mcol;
            }


            echo "<span style='font-size:14px; font-weight:bold;'>UpperDeck</span> <br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcol; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mrow; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('seat_type', 'U');
                    $this->db->where('journey_date', $date);
                    $sql3 = $this->db->get('layout_list');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo "</table><br/>";
            // Lower Deck
            $this->db->select_max('row', 'mroww');
            $this->db->select_max('col', 'mcoll');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('seat_type', 'L');
            $this->db->where('journey_date', $date);
            $sq1l = $this->db->get('layout_list');
            foreach ($sq1l->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='1' cellpadding='0'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('seat_type', 'L');
                    $this->db->where('journey_date', $date);
                    $sql3 = $this->db->get('layout_list');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo '</table></td>';

            echo '<td align="center" valign="top"><table border="0" width="100%" cellpadding="0" cellspacing="0">';
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $inhouse = $this->db->get("layout_list");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $get_seats = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $agent = $this->db->get("layout_list");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $get_seats2 = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        } else if ($lid[1] == 'seatersleeper') {

            //getting max of row and col from mas_layouts
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
            echo "<table border='1' cellspacing='3' cellpadding='0'>";
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
                        $available_type = $row2->available_type;
                        $seat_type = $row2->seat_type;
                    }
                    if ($seat_type == 'U')
                        $st = "(B)";
                    else if ($seat_type == 'U')
                        $st = "(S)";

                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            } //outer for
            echo "</table><br/>";
            // Lower Deck

            $this->db->select_max('row', 'mroww');
            $this->db->select_max('col', 'mcoll');
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
            $sq1l = $this->db->get('layout_list');
            $this->db->where('journey_date', $date);
            foreach ($sq1l->result() as $roww) {
                $mroww = $roww->mroww;
                $mcoll = $roww->mcoll;
            }
            echo "<span style='font-size:14px; font-weight:bold;'>LowerDeck</span><br/>";
            echo "<table border='1' cellspacing='3' cellpadding='0'>";
            for ($k = 1; $k <= $mcoll; $k++) {
                echo "<tr>";
                for ($l = 1; $l <= $mroww; $l++) {
                    $this->db->select('*');
                    $this->db->where('row', $l);
                    $this->db->where('col', $k);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where("(seat_type='L:b' OR seat_type='L:s')");
                    $this->db->where('journey_date', $date);
                    $sql3 = $this->db->get('layout_list');
                    foreach ($sql3->result() as $row2) {
                        $seat_name = $row2->seat_name;
                        $available = $row2->available;
                        $available_type = $row2->available_type;
                        $seat_type = $row2->seat_type;
                    }
                    if ($seat_type == 'L:b')
                        $st = "(B)";
                    else if ($seat_type == 'L:s')
                        $st = "(S)";
                    if ($seat_name == '') {
                        echo "<td style='border:none;' align='center'>&nbsp;</td>";
                    } else {
                        if ($available == 1 || $available == 2) {
                            echo "<td class='grublockseats'>$seat_name</td>";
                            $seat_name = '';
                        } else {
                            echo "<td class='grureleaseseats'>$seat_name</td>";
                            $seat_name = '';
                        }
                    }
                }
                echo "</tr>";
            }
            echo '</table></td>';

            echo '<td align="center" valign="top"><table border="0" cellspacing="0" cellpadding="0" width="100%">';
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 1);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $inhouse = $this->db->get("layout_list");

            //if seats reserved to branches
            if ($inhouse->num_rows() > 0) {
                echo '<tr><td valign="top" colspan="2" style="font-weight:normal;text-decoration:underline" align="center">For Branch </td></tr>';
                echo '<tr>';
                //getting available type
                foreach ($inhouse->result() as $rows) {
                    $inhouse_res = '';
                    $available_type = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 1);
                    $this->db->where('available_type', $available_type);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('journey_date', $date);
                    $this->db->where('travel_id', $travel_id);
                    $get_seats = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats->result() as $rows2) {
                        $seats = $rows2->seat_name;
                        if ($inhouse_res == '')
                            $inhouse_res = $seats;
                        else
                            $inhouse_res = $inhouse_res . ", " . $seats;
                    }
                    //getting branch name
                    if ($available_type == 'all')
                        $aname = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch = $this->db->get("agents_operator");
                        foreach ($get_branch->result() as $rows3) {
                            $aname = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname . ':</td><td width="152">' . $inhouse_res . '</td></tr>';
                }//for each
            }//if
            //agent code
            $this->db->distinct();
            $this->db->select('available_type');
            $this->db->where('available', 2);
            $this->db->where('service_num', $sernum);
            $this->db->where('travel_id', $travel_id);
            $this->db->where('journey_date', $date);
            $agent = $this->db->get("layout_list");
            if ($agent->num_rows() > 0) {
                echo '<tr>
  <td height="22" colspan="2"  align="center" style="font-weight:normal; text-decoration:underline">For Agents </td>
  </tr>
<tr>';
                //getting available type
                foreach ($agent->result() as $rows) {
                    $inhouse_res2 = '';
                    $available_type2 = $rows->available_type;
                    $this->db->select('seat_name');
                    $this->db->where('available', 2);
                    $this->db->where('available_type', $available_type2);
                    $this->db->where('service_num', $sernum);
                    $this->db->where('travel_id', $travel_id);
                    $this->db->where('journey_date', $date);
                    $get_seats2 = $this->db->get("layout_list");
                    //getting seats numbers
                    foreach ($get_seats2->result() as $rows2) {
                        $seats2 = $rows2->seat_name;
                        if ($inhouse_res2 == '')
                            $inhouse_res2 = $seats2;
                        else
                            $inhouse_res2 = $inhouse_res2 . ", " . $seats2;
                    }
                    //getting branch name
                    if ($available_type2 == 'all')
                        $aname2 = "ALL";
                    else {
                        $this->db->select('name');
                        $this->db->where('id', $available_type2);
                        $this->db->where('operator_id', $travel_id);
                        $get_branch2 = $this->db->get("agents_operator");
                        foreach ($get_branch2->result() as $rows3) {
                            $aname2 = $rows3->name;
                        }
                    }

                    echo '<td width="104" height="26" style="font-weight:normal;">' . $aname2 . ':</td><td width="152">' . $inhouse_res2 . '</td></tr>';
                }//for each
            }//if
            echo '</table></td>
                        </tr>
                </table>';
        }//if(seatersleeper)
    }

    function updateGrabRelease($sernum, $seats, $travel_id, $agent_type, $agent_id, $date, $c) {

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
                    $this->db->set('t2.available_type', $agent_id);

                    $this->db->set('t2.available', $agent_type);
                    if ($agent_type == 2) {
                        $this->db->set('t2.show_avail_seat', 'no');
                        $this->db->set('t2.show_quota', 'yes');
                    }
                    $array3 = array('t2.service_num' => $sernum, 't2.travel_id' => $travel_id, 't2.seat_name' => $st[$i], 't2.journey_date' => $from_date);
                    $this->db->where($array3);
                    $query2 = $this->db->update('layout_list as t2');
                    $stmt = "select distinct available_type from layout_list where journey_date='$from_date' and service_num='$sernum' and travel_id='$travel_id' and available='2'";
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
	
	function msg91sms($mobileNumber,$message,$dialCode) {
		$to = '+'.$dialCode.$mobileNumber;
		$twilio_number = "+18508426010";
	    $this->load->library('twilio');
		$response = $this->twilio->sms($twilio_number, $to,$message);
		if($response->IsError){
		echo 0;
		}
		else{
		echo 1;
		}
	}


}
