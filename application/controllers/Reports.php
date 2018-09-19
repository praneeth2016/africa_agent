<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function Boarding_chart() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "4";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Reports_m->getServicesList();
            $this->load->view('reports/boarding_chart', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function GetPassReport() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service = $this->input->get('service');
            $dtt = $this->input->get('dtt');
            $this->Reports_m->displayPassReports($service, $dtt);
        }
    }

	 public function getBoardingChart() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service = $this->input->get('service');
            $dtt = $this->input->get('dtt');
            $this->Reports_m->getBoardingChartModel($service, $dtt);
        }
    }

	
    public function generateDetailReport() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
           $result['key'] = "4";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Reports_m->getServicesList1();
            $this->load->view('reports/reports_view',$data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }       
        
    }
    function ShowAgent() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {            
            $agent_type = $this->input->post('agenttype');            
            $agent = $this->Reports_m->getAgentName($agent_type);
            $agentid = 'id="agent" class="inputfield"';
            $agent_name = 'name="agent"';
            echo form_dropdown($agent_name, $agent, "", $agentid);
        }
    }
    public function GetReport() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service = $this->input->get('service');
            $from = $this->input->get('from');
            $to = $this->input->get('to');
            $ag = $this->input->get('ag');
            $ag_name = $this->input->get('ag_name');
            $rtype = $this->input->get('rtype');
            $data1['query'] = $this->Reports_m->displayReports($from, $to, $ag_name, $ag, $rtype, $service);
            $data1['query1'] = $this->Reports_m->displayCanReports($from, $to, $ag_name, $ag, $rtype, $service);            
            $this->load->view('reports/booking_total_reports.php', $data1);
        }
    }

    public function Rta_list() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "4";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Reports_m->getServicesList();
            $this->load->view('reports/rta_list', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }
    function getViewSelectedData() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $date = $this->input->get('date');
            $ser = $this->input->get('service');
            $this->Reports_m->getViewSelected_PassengerDetail($date, $ser);            
        }
    }
     public function mybookings() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
           $result['key'] = "4";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['services'] = $this->Reports_m->getServicesList1();
            $this->load->view('reports/mybookings',$data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }       
        
    }
    public function mybookings1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $service = $this->input->get('service');
            $from = $this->input->get('from');
            $to = $this->input->get('to');           
            $rtype = $this->input->get('rtype');
            $this->Reports_m->mybookings2($from, $to,$rtype, $service);            
        }
    }
    

}
