<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bus extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function createBus() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "6";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['cities'] = $this->Bus_m->getAllCity1();
            $data['busmodel'] = $this->Bus_m->busmodel();
            $this->load->view('bus/serviceCreationView.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');	
        }
    }

    public function checkUser() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Bus_m->check_user();
            echo $data;
        }
    }

    public function getServiceLayout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $lay = $this->Bus_m->getLayoutDb();
            echo $lay;
        }
    }

    public function getHaltsAndFares() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $nstops = $this->input->post('halts');
            $busmodel = $this->input->post('busmodel');
            $getroutes = $this->Bus_m->getAllCity();
            $bustype = $this->Bus_m->getbustype();

            if ($nstops > 0) {

                echo '<table width="100%" border="0">
  <tr>
    <td><strong>Source </strong></td>
    <td><strong>Destination</strong></td>
    <td><strong>Start Time </strong></td>
    <td><strong>Arrival Time</strong></td>';
                if ($bustype == "seater") {
                    echo '<td><strong>Seat Fare </strong></td>';
                } else if ($bustype == "sleeper") {
                    echo '<td><strong>Lower Berth Fare </strong></td>
    	<td><strong>Upper Berth Fare </strong></td>';
                } else if ($bustype == "seatersleeper") {
                    echo '<td><strong>Seat Fare </strong></td>
		<td><strong>Lower Berth Fare </strong></td>
    	<td><strong>Upper Berth Fare </strong></td>';
                }
                echo'</tr>';
                for ($i = 1; $i <= $nstops; $i++) {
                    $id = 'id="from' . $i . '"style="width:100px"';
                    $name = 'name="from' . $i . '"';

                    $id2 = 'id="to' . $i . '" style="width:100px"';
                    $name2 = 'name="to' . $i . '"';
                    $sty = 'style="width:100px"';
                    $hours = $this->getHour();

                    $timehrST = 'id="timehrST' . $i . '" style="width:50px"';
                    $timenST = 'name="timehrST' . $i . '" style="width:50px"';

                    $timehrAT = 'id="timehrAT' . $i . '" style="width:50px"';
                    $timenAT = 'name="timehrAT' . $i . '" style="width:50px"';

                    $hours1 = $this->getMinutes();

                    $timemiST = 'id="timemST' . $i . '"style="width:50px"';
                    $timemnST = 'name="timemST' . $i . '"style="width:50px"';

                    $timemiAT = 'id="timemAT' . $i . '"style="width:50px"';
                    $timemnAT = 'name="timemAT' . $i . '"style="width:50px"';

                    $tfidST = 'id="tfmST' . $i . '" ';
                    $tfnameST = 'name="tfm' . $i . '" style="width:50px"';

                    $tfidAT = 'id="tfmAT' . $i . '" ';
                    $tfnameAT = 'name="tfmAT' . $i . '" style="width:50px"';

                    $tfv = array("AMPM" => "-select-", "AM" => "AM", "PM" => "PM");

                    echo'
  <tr>
    <td height="30">' . form_dropdown($name, $getroutes, "", $id) . '</td>
    <td>' . form_dropdown($name2, $getroutes, "", $id2) . '</td>
	
    <td>' . form_dropdown($timenST, $hours, $hr, $timehrST, $sty) . ' ' . form_dropdown($timemnST, $hours1, $hr1, $timemiST) . ' ' . form_dropdown($tfnameST, $tfv, $tf[1], $tfidST) . '</td>
    <td>' . form_dropdown($timenAT, $hours, $hr, $timehrAT, $sty) . ' ' . form_dropdown($timemnAT, $hours1, $hr1, $timemiAT) . ' ' . form_dropdown($tfnameAT, $tfv, $tf[1], $tfidAT) . '</td>';
                    if ($bustype == "seater") {
                        echo '<td align="center"><input type="text" name="sfare' . $i . '" id="sfare' . $i . '" style="width:30px" value=""></td>';
                    } else if ($bustype == "sleeper") {
                        echo '<td align="center"><input type="text" name="lbfare' . $i . '" id="lbfare' . $i . '" style="width:30px" value=""></td>
    	<td align="center"><input type="text" name="ubfare' . $i . '" id="ubfare' . $i . '" style="width:30px" value=""></td>';
                    } else if ($bustype == "seatersleeper") {
                        echo '<td align="center"><input type="text" name="sfare' . $i . '" id="sfare' . $i . '" style="width:30px" value=""></td>
		<td align="center"><input type="text" name="lbfare' . $i . '" id="lbfare' . $i . '" style="width:30px" value=""></td>
    	<td align="center"><input type="text" name="ubfare' . $i . '" id="ubfare' . $i . '" style="width:30px" value=""></td>';
                    }
                    echo'</tr>';
                }//for
                echo '</table>
';
            } else {
                echo 0;
            }
        }
    }

    public function getHour() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = array();
            $data[HH] = "HH";
            for ($i = 0; $i <= 12; $i++) {
                if ($i < 10)
                    $i = "0" . $i;
                $data[$i] = $i;
            }
            return $data;
        }
    }

    public function getHours() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = array();
            for ($i = 0; $i < 24; $i++) {
                if ($i < 10)
                    $i = "0" . $i;
                $data[$i] = $i;
            }
            return $data;
        }
    }

    public function getMinutes() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = array();
            $data[MM] = "MM";
            for ($i = 0; $i <= 60; $i++) {
                if ($i < 10)
                    $i = "0" . $i;
                $data[$i] = $i;
            }
            return $data;
        }
    }

    public function getBoard() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $from = $this->input->get('froms');
            $fid = $this->input->get('fids');
            $snum = $this->input->get('snum');
            $halts = $this->input->get('halts');

            $data = $this->Bus_m->getBoardDb($fid, $from, $snum, $halts);
        }
    }

    public function saveBoard() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('sernum');
            $city_name = $this->input->post('city_name');
            $city_id = $this->input->post('city_id');
            $board_point = $this->input->post('board_point');
            $bpid = $this->input->post('bpid');
            $lm = $this->input->post('lm');
            $hhST = $this->input->post('hhST');
            $mmST = $this->input->post('mmST');
            $ampmST = $this->input->post('ampmST');

            $data = $this->Bus_m->saveBoardDb($sernum, $city_name, $city_id, $board_point, $bpid, $lm, $hhST, $mmST, $ampmST);
        }
    }

    public function getDrop() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $to = $this->input->get('tos');
            $tid = $this->input->get('tids');
            $snum = $this->input->get('snum');
            $halts = $this->input->get('halts');

            $data = $this->Bus_m->getDropDb($tid, $to, $snum, $halts);
        }
    }

    public function saveDrop() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sernum = $this->input->post('sernum');
            $city_name = $this->input->post('city_name');
            $city_id = $this->input->post('city_id');
            $board_point = $this->input->post('drop_point');
            $bpid = $this->input->post('dpid');
            $lm = $this->input->post('lm');
            $hhST = $this->input->post('hhST');
            $mmST = $this->input->post('mmST');
            $ampmST = $this->input->post('ampmST');

            $data = $this->Bus_m->saveDropDb($sernum, $city_name, $city_id, $board_point, $bpid, $lm, $hhST, $mmST, $ampmST);
        }
    }

    public function getBoardOrDropVal() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Bus_m->getBoardOrDropValDb();
        }
    }

    public function saveBusDetails() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Bus_m->saveBusDetailsDb();
        }
    }

    public function active_deactive() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "6";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Bus_m->getServicesListForActiveOrDeactive();
            $data['key'] = 'DeActive';
            $this->load->view('bus/activebus_view.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function servicesListActiveOrDeactive() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->getServicesListActiveOrDeactive();
        }
    }

    function getForwordBookingDays() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $servtype = $this->input->post('servtype');
            $travid = $this->input->post('travid');
            $s = $this->input->post('s');
            $svc = $this->input->post('svc');
            $fromid = $this->input->post('fromid');
            $toid = $this->input->post('toid');
            $stat = $this->input->post('status');
            $data = $this->Bus_m->getForwordBookingDaysFromDb($travid);
            if ($data == 0) {
                echo '0';
            }
            if ($servtype == "normal") {
                echo '<table width="457" border="0" style="border-right:#f2f2f2 solid 1px; border-top:#f2f2f2 solid 1px; border-left:#f2f2f2 solid 1px; border-bottom:#f2f2f2 solid 1px; font-size:14px;color:#333333;" align="center">
  <tr>
    <td width="449">Forward booking days are:&nbsp;<span style="color:#000066;font-size:12px; font-weight:bold;">' . $data . '</span></td>
  </tr>
  <tr>
    <td>select the start date from datepicker:
 <input name="txtdate' . $s . '" type="text" id="txtdate' . $s . '" style="cursor:pointer;border-radius:3px" value="" 
     onChange="getTodate(' . $data . ',' . $s . ')"/></td>
  </tr>
  <tr>
    <td id="txt' . $s . '"></td>
  </tr>
  <tr>
    <td align="center">
    <input type="button" class="btn btn-primary" name="updt' . $s . '" id="updt' . $s . '" value="Update" 
        onClick="updateStatus(\'' . $svc . '\',' . $travid . ',' . $data . ',' . $stat . ',' . $s . ',' . $fromid . ',' . $toid . ')">
       </td>
  </tr>
  <tr>
    <td align="center"><input type="hidden" name="fwddate" id="fwddate" value="" ><span id="spnmsg' . $s . '" style="font-size:12px; font-weight:bold;"></span> </td>
  </tr>
</table>';
            } else if ($servtype == "special") {

                echo ' <table width="457" border="0" style="border-right:#f2f2f2 solid 1px; border-top:#f2f2f2 solid 1px; border-left:#f2f2f2 solid 1px; border-bottom:#f2f2f2 solid 1px; font-size:14px;color:#333333;" align="center">
  <tr>
    <td>select the start date from datepicker:
 <input name="txtdate' . $s . '" type="text" id="txtdate' . $s . '" style="cursor:pointer;border-radius:3px"
     value=""  /></td>
  </tr>
  <tr>
    <td>select the end date from datepicker:
 <input name="txtdatee' . $s . '" type="text" id="txtdatee' . $s . '" style="cursor:pointer;border-radius:3px" 
     value=""  onChange="getTodateForSpecialService(' . $data . ',' . $s . ')"/></td>
  </tr>
  <tr>
    <td id="txt' . $s . '"></td>
  </tr>
  <tr>
    <td align="center">
    <input type="button" class="btn btn-primary" name="updt' . $s . '" id="updt' . $s . '" value="Update" 
        onClick="updateStatus(\'' . $svc . '\',' . $travid . ',' . $data . ',' . $stat . ',' . $s . ',' . $fromid . ',' . $toid . ')">
       </td>
  </tr>
  <tr>
  
    <span id="spnmsg' . $s . '" style="font-size:12px; font-weight:bold;"></span> </td>
  </tr>
</table>';
            } else if ($servtype == "weekly") {
                echo '<table width="457" border="0" style="border-right:#f2f2f2 solid 1px; border-top:#f2f2f2 solid 1px; border-left:#f2f2f2 solid 1px; border-bottom:#f2f2f2 solid 1px; font-size:14px;color:#333333;" align="center">
  <tr>
    <td width="449">Forward booking days are:&nbsp;<span style="color:#000066;font-size:12px; font-weight:bold;">' . $data . '</span></td>
  </tr>
  <tr>
    <td>select the start date from datepicker:
 <input name="txtdate' . $s . '" type="text" id="txtdate' . $s . '" style="cursor:pointer;border-radius:3px" value="" 
     onChange="getTodate(' . $data . ',' . $s . ')"/></td>
  </tr>
  <tr>
    <td id="txt' . $s . '"></td>
  </tr>
  <tr>
    <td align="center">
    <input type="button" class="btn btn-primary" name="updt' . $s . '" id="updt' . $s . '" value="Update" 
        onClick="updateStatus(\'' . $svc . '\',' . $travid . ',' . $data . ',' . $stat . ',' . $s . ',' . $fromid . ',' . $toid . ')">
       </td>
  </tr>
  <tr>
    <td align="center"><input type="hidden" name="fwddate" id="fwddate" value="" ><span id="spnmsg' . $s . '" style="font-size:12px; font-weight:bold;"></span> </td>
  </tr>
</table>';
            }//else if($servtype=="special")
        }
    }

    function activeBusStatus() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $travid = $this->input->post('travid');
            $s = $this->input->post('s');
            $sernum = $this->input->post('sernum');
            $fromid = $this->input->post('fromid');
            $toid = $this->input->post('toid');
            $fdate = $this->input->post('fdate');
            $tdate = $this->input->post('tdate');
            $status = $this->input->post('status');
            $fwd = $this->input->post('fwd');
            $servtype = $this->input->post('servtype');
            $data = $this->Bus_m->activeBusStatusDb($travid, $sernum, $s, $fromid, $toid, $status, $fwd, $fdate, $tdate, $servtype);
            echo $data;
        }
    }

    function getActivateDates() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $sdate = $this->input->post('sdate');
            $fwd = $this->input->post('fwd') - 1;
            $date = new DateTime($sdate);
            $date->modify('+' . $fwd . 'day');
            $max_date = $date->format('Y-m-d');
            echo $max_date;
        }
    }

    public function deActivateBusPermanent() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->deActivateBusPermanentDb();
        }
    }

    public function modify_bus() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "6";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['service'] = $this->Bus_m->getServicesList();
            $this->load->view('bus/modifybus_view.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    function DoModify() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->modifyRequirements();
        }
    }

    function getCity() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $srvno = $this->input->post('srvno');
            $this->db->select('city_name');
            $this->db->where('service_num', $srvno);
            $query = $this->db->get('boarding_points');
            $data = array();
            $data['0'] = "--select--";
            foreach ($query->result() as $rows) {
                $data[$rows->city_name] = $rows->city_name;
            }
            return $data;
        }
    }

    function modify_saving() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->SaveModifytoDb();
        }
    }

    function DoModifyDrop() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->modify_Drop_point();
        }
    }

    function modify_dp() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->SaveDPtoDb();
        }
    }

    function ModifyRoutes() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->modify_routes();
        }
    }

    function save_routes() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->save_routes_db();
        }
    }

    function deleterouteFromDb() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Bus_m->delete_routes();
        }
    }

    function addNewRoutesDb() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $s = $this->input->post('s');
            $bus_type = $this->input->post('bus');
            $hours = $this->Bus_m->getHour();
            $timehr = 'id="timehr' . $s . '" ';
            $timen = 'name="timehr' . $s . '" style="width:50px"';
            $hours1 = $this->Bus_m->getMinutes();
            $timemi = 'id="timem' . $s . '" onChange="arrtime(' . $s . ')"';
            $timemn = 'name="timem' . $s . '" style="width:50px"';
            $cities = $this->Bus_m->getAllCity1();
            $cityid = 'id="from' . $s . '" style="width: 130px;" ';
            $cityn = 'name="from' . $s . '"';
            $tocities = $this->Bus_m->getAllCity1();
            $tocityid = 'id="to' . $s . '" style="width: 130px;" ';
            $tocityn = 'name="to' . $s . '"';
            $hoursa = $this->Bus_m->getHour();
            $arrth = 'id="arrth' . $s . '"';
            $arrh1 = 'name="arrth' . $s . '" style="width:50px"';
            $arrtm = 'id="arrtm' . $s . '"';
            $arrtm1 = 'name="arrtm' . $i . '" style="width:50px"';
            $hoursa1 = $this->Bus_m->getMinutes();
            $tfid = 'id="tfms' . $s . '" ';
            $tfname = 'name="tfms' . $s . '" style="width:50px"';
            $tfid1 = 'id="tfma' . $s . '" ';
            $tfname1 = 'name="tfma' . $s . '" style="width:50px"';
            $tfv = array("0" => "-select-", "AM" => "AM", "PM" => "PM");
            echo'<tr id="tr' . $s . '">
         <td><input type="checkbox" name="ck' . $s . '" id="ck' . $s . '" value="' . $s . '" checked></td>
         <td>' . form_dropdown($cityn, $cities, "", $cityid) . '<input type="hidden" size="15" name="fromid' . $s . '" id="fromid' . $s . '" value="' . $fromid . '"></td>
         <td>' . form_dropdown($tocityn, $tocities, "", $tocityid) . '<input type="hidden" size="15" name="toid' . $s . '" id="toid' . $s . '" value="' . $toid . '"></td>
         <td>' . form_dropdown($timen, $hours, "", $timehr) . '' . form_dropdown($timemn, $hours1, "", $timemi) . '' . form_dropdown($tfname, $tfv, "", $tfid) . '</td>
         <td>' . form_dropdown($arrh1, $hoursa, "", $arrth) . '' . form_dropdown($arrtm1, $hoursa1, "", $arrtm) . '' . form_dropdown($tfname1, $tfv, "", $tfid1) . '</td>
        ';
            if ($bus_type == 'seater') {
                echo '<td><input type="text" size="8" name="seat_fare' . $s . '" id="seat_fare' . $s . '" ></td>';
            } else if ($bus_type == 'sleeper') {
                echo '<td><input type="text" size="8" name="lowerseat_fare' . $s . '" id="lowerseat_fare' . $s . '" ></td>
               <td><input type="text" size="8" name="upperseat_fare' . $s . '" id="upperseat_fare' . $s . '" ></td>';
            } else if ($bus_type == 'seatersleeper') {
                echo '<td><input type="text" size="8" name="seat_fare' . $s . '" id="seat_fare' . $s . '" ></td>
               <td><input type="text" size="8" name="lowerseat_fare' . $s . '" id="lowerseat_fare' . $s . '" ></td>
               <td><input type="text" size="8" name="upperseat_fare' . $s . '" id="upperseat_fare' . $s . '" ></td>';
            }
            echo '<td><span style="cursor:pointer; font-weight:bold; color:#81BEF7; text-decoration:underline;" onClick="DeleteRoutes(' . $s . ')">Delete</span></td>
          </tr>';
        }
    }

    public function operator_Special_Service() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "6";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['service'] = $this->Bus_m->getservic_modify();
            $this->load->view('bus/special_service.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function operator_Special_Service1() {

        $this->Bus_m->get_special_services_db();
    }

    public function getstatusanddate() {
        $this->Bus_m->getstatusanddate1();
    }

    public function activatesplservice() {
        $res = $this->Bus_m->activatesplservice1();
        return $res;
    }

}
