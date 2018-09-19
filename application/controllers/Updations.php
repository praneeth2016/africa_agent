<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Updations extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function Ticket_cancel() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $this->load->view('updations/getcanceldetails_view.php');
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
			
        }
    }

    public function confirmcancel() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
			
            $data = $this->Updations_m->confirm_cancel();
            if ($data == 2) {
                print "<script type=\"text/javascript\">alert('Your Ticket is already Cancelled');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
            } else if ($data == 3) {
                print "<script type=\"text/javascript\">alert('No Cancellation For this Ticket due to Rescheduled,contact operator for clarification!');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
            } else {
                $result['key'] = "3";
                //$this->load->view('sidebar', $result);
				$this->load->view('header.php');
                $this->load->view('updations/confirmcancel_view.php', $data);
                $this->load->view('leftSidebar.php');
				$this->load->view('footer_new');
            }
            //}//$ticket_modify=='yes'
        }
    }

    public function cancelticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Updations_m->ticket_cancel();
            if ($data == 1) {
                print "<script type=\"text/javascript\">alert('Your Ticket is already Cancelled');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
            } else if ($data == 3) {
                print "<script type=\"text/javascript\">alert('No Cancellation For this Ticket due to Rescheduled,contact operator for clarification!');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Ticket_cancel') . "'</script>";
            } else {
                $result['key'] = "3";
                //$this->load->view('sidebar', $result);
				$this->load->view('header.php');
                $this->load->view('updations/cancelticket_view.php', $data);
                $this->load->view('leftSidebar.php');
				$this->load->view('footer_new');
            }
        }
    }

    public function Branch_tkt_cancel() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $this->load->view('updations/branch_cancellation');
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    function branch_confirmcancel() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Updations_m->branch_confirmcancel1();
            //print_r($data);
            if ($data == 2) {
                print "<script type=\"text/javascript\">alert('Your Ticket is already Cancelled');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Branch_tkt_cancel') . "'</script>";
            } else {
                $result['key'] = "3";
                //$this->load->view('sidebar', $result);
				$this->load->view('header.php');
                $this->load->view('updations/branch_confirmcancel', $data);
				$this->load->view('leftSidebar.php');
				$this->load->view('footer_new');
            }
        }
    }

    public function branch_cancelticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Updations_m->branch_cancelticket1();
            if ($data == 1) {
                print "<script type=\"text/javascript\">alert('Your Ticket is already Cancelled');</script>";
                print "<script type=\"text/javascript\">window.location = '" . base_url('Updations/Branch_tkt_cancel') . "'</script>";
            } else {
                $result['key'] = "3";
                //$this->load->view('sidebar', $result);
				$this->load->view('header.php');
                $this->load->view('updations/branch_cancelticket', $data);
				$this->load->view('leftSidebar.php');
				$this->load->view('footer_new');
            }
        }
    }

    public function change_tkt_status() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['from_cities'] = $this->Updations_m->from_cities();
            $this->load->view('updations/ticket_status_view', $data);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function tktStatus() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $tktno = $this->input->post('tktno');
            $view = $this->Updations_m->tktStst($tktno);
            return $view;
        }
    }

    public function modifyTicket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Updations_m->modifyTicketDetailsDb();
        }
    }

    public function tkt_reschedule() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['from_cities'] = $this->Updations_m->from_cities_tr();
            $this->load->view('updations/reschedule_view', $data);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function reschedule_busList() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $source_id = "";
            $destination_id = "";
            $source_name = $this->input->post('source');
            $destination_name = $this->input->post('destination');
            $onward_date1 = $this->input->post('onward_date');
            $return_date1 = $this->input->post('return_date');
            //for Onward Date Format           
            $onward_date = $onward_date1;
            //for Return Date Format
            $return_date = $return_date1;
            if ($source_id == "" || $source_id == 0 || $destination_id == "" || $destination_id == 0) {
                //Getting Source and Destination Id's from Database
                $source_id = $this->Updations_m->get_city_id($source_name);
                $destination_id = $this->Updations_m->get_city_id($destination_name);
            }
            $ct = $this->Updations_m->onward_buses_count($source_id, $destination_id, $onward_date);
            $ct1 = $this->Updations_m->return_buses_count_reschedule($source_id, $destination_id, $return_date);
            $view = $this->Updations_m->reschedule_busListView($source_id, $destination_id, $onward_date, $return_date, $source_name, $destination_name, $ct, $ct1);
            return $view;
        }
    }

    public function discount() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            $this->load->view('sidebar', $result);
            $result['services'] = $this->Updations_m->getServicesList();
            $this->load->view('updations/discount.php', $result);
            $this->load->view('footer');
        }
    }

    public function discount1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Updations_m->discount2();
        }
    }

    public function Cc_policy() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['canc'] = $this->Updations_m->getcanc_detail();
            $this->load->view('updations/cancel_terms.php', $data);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function vihicle_assignment() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $result['city'] = $this->Updations_m->display_Source();
            $this->load->view('updations/service_layout_view.php', $result);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    function ServiceDisplay() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Updations_m->getServices_From_db();
        }
    }

    function storeContact() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Updations_m->store_contact_db();
        }
    }

    public function toListr() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $data = $this->Updations_m->to_citiesr();
            echo $data;
        }
    }

    public function reschedule_serviceLayout() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service_num = $_REQUEST['service_num'];
            $source_id = $_REQUEST['source_id'];
            $destination_id = $_REQUEST['destination_id'];
            $onward_date = $_REQUEST['onward_date'];
            $return_date = $_REQUEST['return_date'];
            $fare = $_REQUEST['fare'];
            $j = $_REQUEST['j'];
            $way = $_REQUEST['way'];

            $view_lay = $this->Updations_m->reschedule_serLayout($service_num, $source_id, $destination_id, $onward_date, $return_date, $fare, $j, $way);
            return $view_lay;
        }
    }

    public function reschedule_ticket() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $this->Updations_m->reschedule_ticket_db();
        }
    }

    public function vihicle_details() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            $this->load->view('sidebar', $result);
            $result['services'] = $this->Updations_m->getServicesList_no_all();
            $this->load->view('updations/vehical_details.php', $result);
            $this->load->view('footer');
        }
    }

    public function vihicle_details1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->vihicle_details2();
            return $res;
        }
    }

    public function get_vihicle_details() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res['values'] = $this->Updations_m->get_vihicle_details1();
            return $res;
            //print_r($res);
        }
    }

    public function Ticket_print() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $this->load->view('updations/ticket_print');
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function Ticket_print1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $checkticket = $this->Updations_m->Ticket_print2();
            return $checkticket;
        }
    }

    public function Ticket_print3() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $ticket_status = $this->Updations_m->Ticket_print4();
            //return $ticket_status;
        }
    }

    public function bus_numbers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['list'] = $this->Updations_m->get_busnumbers_db();
            $this->load->view('updations/bus_numbers_list.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function add_bus_numbers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $this->load->view('updations/addbusnumbers.php');
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function add_bus_numbers1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->add_bus_numbers2();
        }
    }

    public function edit_bus_numbers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['list'] = $this->Updations_m->get_busnumbers_db_ed();
            $this->load->view('updations/editbusnumbers.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function edit_bus_numbers1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->edit_bus_numbers2();
        }
    }

    public function drivers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['list'] = $this->Updations_m->get_drivers_db();
            $this->load->view('updations/drivers_list.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function add_driver() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $this->load->view('updations/add_driver_details.php');
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function add_driver1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->add_driver2();
        }
    }

    public function edit_drivers() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['list'] = $this->Updations_m->get_driver_db_ed();
            $this->load->view('updations/edit_driver_details.php', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function edit_driver1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->edit_driver2();
        }
    }

    public function get_driver_number() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->get_driver_number2();
        }
    }

    public function DelayServiceSMS() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "3";
            //$this->load->view('sidebar', $result);
			$this->load->view('header.php');
            $data['services'] = $this->Updations_m->getServicesList_ds();
            $data['time'] = $this->Updations_m->getDelayTime();
            $this->load->view('updations/delay_services_sms.php', $data);
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function ServiceSendSMS() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $res = $this->Updations_m->ServiceSendSMS1();
            return $res;
        }
    }

}
