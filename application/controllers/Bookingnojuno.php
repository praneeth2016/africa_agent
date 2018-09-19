<?php

/**
 * Description of booking
 * @author SVPRASADK
 */
class Booking extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('cookie');
        $this->load->helper('url');
        $this->load->model('booking_m');
    }

    function index() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $data['from_cities'] = $this->booking_m->from_cities();
            $result['key'] = "1";
            $this->load->view('sidebar', $result);
            $this->load->view('booking', $data);
        }
    }

    function to_cities() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $to_cities = $this->booking_m->to_city();
            return $to_cities;
        }
    }

    function api_layout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $travel_id = $this->session->userdata('bktravels_travel_id');
            $source_id = $this->input->post('source_id');
            $destination_id = $this->input->post('destination_id');
            $onward_date = $this->input->post('onward_date');

            $service_num = $this->booking_m->get_service_num($source_id, $destination_id, $onward_date, $travel_id);
            $this->booking_m->agent_api_seats($service_num, $source_id, $destination_id, $onward_date, $travel_id);
        }
    }

    function ServiceList() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $response = $this->booking_m->ServiceList1();
            return $response;
        }
    }

    function block_release() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->release_block();
        }
    }

    function ServiceLayout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $response = $this->booking_m->ServiceLayout1();
            return $response;
        }
    }

    public function showPassDetail() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $view = $this->booking_m->SeatPassDetails_view();
        }
    }

    public function seat_options() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $seat_options = $this->booking_m->seat_options1();
            return $seat_options;
        }
    }

    function landmark() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $view_lm = $this->booking_m->show_landmark();
            return $view_lm;
        }
    }

    function paytype() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $view_paytype = $this->booking_m->show_paytype();
            return $view_paytype;
        }
    }

    function gender() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $gender = $this->booking_m->gender_check();
            return $gender;
        }
    }

    function block() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $block = $this->booking_m->blocked();
            return $block;
        }
    }

    function dep() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->boarding_points_mouseover();
        }
    }

    function drop() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->dropping_points_mouseover();
        }
    }

    function service() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->service_mouseover();
        }
    }

    function seatDetails() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {

            $data = $this->booking_m->booked();
            //echo $data."data";

            if ($data === 0) {
                echo "<script>javascript:alert('Balance Limit Exceeded !! Kindly Top Up !!-----------');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('booking') . "'</script>";
            } else if ($data === 2) {
                echo "<script>javascript:alert('Previous Dates Are Not Allowed for Booking');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('booking') . "'</script>";
            } else if ($data == 'no') {
                echo "<script>javascript:alert('Some of the tickets already booked');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('booking') . "'</script>";
            } else {
                $onward_tktno = $data['onward_tktno'];
                $onward_way = $data['onward_way'];
                $onward_date = $data['onward_date'];
                $onward_service_num = $data['onward_service_num'];
                $return_tktno = $data['return_tktno'];
                $return_service_num = $data['return_service_num'];
                $return_date = $data['return_date'];
                $return_way = $data['return_way'];
                $j = $data['j'];
                $cnt = $data['cnt'];
                $key = $data['key'];
                $fare = $this->input->post('fare');
                $travel_id = $data['travel_id'];
                $pay_type = $data['pay_type'];
                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Ticket Engine ::</title>
</head>

<body onLoad="document.confirmed_ticket.submit()">
<form name="confirmed_ticket" method="get" action="' . base_url('booking/confirmed_ticket') . '">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <input name="onward_tktno" type="hidden" value="' . $onward_tktno . '" />
    <input name="onward_date" type="hidden" value="' . $onward_date . '" />
    <input name="onward_service_num" type="hidden" value="' . $onward_service_num . '" />
    <input name="onward_way" type="hidden" value="' . $onward_way . '" />
    <input name="return_tktno" type="hidden" value="' . $return_tktno . '" />
    <input name="return_date" type="hidden" value="' . $return_date . '" />
    <input name="return_service_num" type="hidden" value="' . $return_service_num . '" />
    <input name="return_way" type="hidden" value="' . $return_way . '" /> 
	 <input name="travel_id" type="hidden" value="' . $travel_id . '" /> 
	  <input name="j" type="hidden" value="' . $j . '" /> 
	 <input name="cnt" type="hidden" value="' . $cnt . '" />  
	  <input name="fare" type="hidden" value="' . $fare . '" /> 
	   <input name="key" type="hidden" value="' . $key . '" /> 
               <input name="pay_type" type="hidden" value="' . $pay_type . '" />
    </td>
  </tr>
</table>
</form>
</body>
</html>
';
            }
        }
    }

    function confirmed_ticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->confirmed_ticket_db();
        }
    }

    function layout_change_price() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $service = $this->input->get('service');
            $from_id = $this->input->get('from_id');
            $to_id = $this->input->get('to_id');
            $dtt = $this->input->get('dtt');
            $data['data'] = array("$service", "$from_id", "$to_id", "$dtt");
            $this->load->view('sidebar.php');
            $this->load->view('layout_change_price', $data);
        }
    }

    function layout_change_price1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->layout_change_price2();
        }
    }

    function layout_updatePrice() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->layout_updatePrice1();
        }
    }

    function layout_grab_release() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $token = $this->input->get('token');
            $service = $this->input->get('service');
            $dtt = $this->input->get('dtt');
            $data['data'] = array("$token", "$service", "$dtt");
            $this->load->view('sidebar.php');
            $this->load->view('layout_grab_release', $data);
        }
    }

    function layout_grab_release1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->layout_grab_release2();
        }
    }

    function SelectAgentType() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $s = $this->input->post('s');
            $id = $this->input->post('id');
            //echo $id;
            $agent = $this->booking_m->geAgentName($id);
            $agentid = 'id="ag' . $s . '" class="inputfield"';
            $agent_name = 'ag' . $s;
            if ($agent[0] == '--select--')
                echo form_dropdown($agent_name, $agent, "", $agentid) . 'No Agents are Created !';
            else
                echo form_dropdown($agent_name, $agent, "", $agentid);
        }
    }

    function SaveGrabRelease() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $sernum = $this->input->post('service_num');
            $travel_id = $this->input->post('travel_id');
            $seats = $this->input->post('seat_names');
            $agent_type = $this->input->post('agent_type');
            $agent_id = $this->input->post('agent_id');
            $date = $this->input->post('date');
            $c = $this->input->post('c');
            // $ex1=  explode("/", $exdate);
            //$date=$ex1[2]."-".$ex1[1]."-".$ex1[0];
            if ($agent_type == 0) {
                $agent_id = 0;
            } else {
                $agent_id = $agent_id;
            }
            $this->booking_m->updateGrabRelease($sernum, $seats, $travel_id, $agent_type, $agent_id, $date, $c);
        }
    }

    function layout_Updateallseats() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $this->booking_m->layout_Updateallseats1();
        }
    }

    function checkticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $checkticket = $this->booking_m->checkticket1();
            return $checkticket;
        }
    }

    function ticket_status() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $ticket_status = $this->booking_m->ticket_status1();
            //return $ticket_status;
        }
    }

    function ticket_search() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $ticket_search = $this->booking_m->ticket_search1();
            return $ticket_search;
        }
    }

    function get_canc_details() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $data['sql'] = $this->booking_m->get_canc_details1();
            //print_r($data);
            $this->load->view('get_canc_details', $data);
        }
    }

    function canc_ticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $data['sql'] = $this->booking_m->canc_ticket1();
            //print_r($data);
            $this->load->view('canc_ticket', $data);
        }
    }

    function confirm_ticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $data = $this->booking_m->confirm_ticket1();
            return $data;
        }
    }

    function release_seats() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(get_cookie('website_url'));
        } else {
            $data = $this->booking_m->release_seats1();
            return $data;
        }
    }

}
