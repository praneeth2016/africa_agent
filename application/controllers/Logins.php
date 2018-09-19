<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Logins extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function add_agent() {
        $data['key'] = $this->input->get('key');
        $result['key'] = "5";
		//$this->load->view('sidebar',$result);
		$this->load->view('header.php');		
        $this->load->view('logins/add_agent', $data);
		$this->load->view('leftSidebar.php');
        $this->load->view("footer_new.php");
    }

    public function Branch_logins() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "5";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');			
            $data['list'] = $this->Logins_m->get_agentslist_db();
            $this->load->view('logins/branch_agents_list', $data);
			$this->load->view('leftSidebar.php');
            $this->load->view("footer_new.php");
        }
    }

    public function Branch_logins_edit() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "5";
           //is->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['agent_data'] = $this->Logins_m->get_agentdata_db();
            $this->load->view('logins/agent_edit', $data);
            $this->load->view('leftSidebar.php');
            $this->load->view("footer_new.php");
        }
    }

    public function Postpaid_logins() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "5";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['list'] = $this->Logins_m->get_agentslist_post_db();
            $this->load->view('logins/postpaid_agents_list', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function prepaid_logins() {
        if ($this->session->userdata('bktravels_logged_in') != 'TRUE') {
            redirect(base_url());
        } else {
            $result['key'] = "5";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
            $data['list'] = $this->Logins_m->get_agentslist_pre_db();
            $this->load->view('logins/prepaid_agents_list', $data);
            $this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function recharge() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result = $this->Logins_m->password_update();
            echo $result;
        }
    }

    public function checkUser() {

        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {

            $data = $this->Logins_m->check_user();
            echo $data;
        }
    }

    public function add_agent1() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result = $this->Logins_m->add_agent2();

            return $result;
        }
    }

    public function edit_agent() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result['key'] = "5";
            //$this->load->view('sidebar',$result);
			$this->load->view('header.php');
			
            $data['result'] = $this->Logins_m->edit_agent1();
            $this->load->view('logins/edit_agent', $data);
			$this->load->view('leftSidebar.php');
            $this->load->view("footer_new.php");
        }
    }
    public function edit_agent2(){
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result = $this->Logins_m->edit_agent3();
            return $result;
        }
    }

}
