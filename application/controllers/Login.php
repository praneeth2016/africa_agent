<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Login_m');
    }

    public function index() {
        $this->load->view('signin');
    }

    public function validateLogin1() {
        $res = $this->Login_m->validateLogin2();
        return $res;
    }

    public function validateLogin12() {
        $res = $this->Login_m->validateLogin22();
        if ($res == 1) {
            redirect(base_url('Booking'));
        }else{
            redirect("http://".$_SERVER['HTTP_HOST']."/main_controller/AgentLogin");
        }
    }

    public function Home_page() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            //$this->load->view('sidebar');
            //$this->load->view('footer');
			$this->load->view('header.php');
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function password_change() {
        if ($this->session->userdata('bktravels_logged_in') != 'TRUE') {
            redirect(base_url());
        } else {
            //$this->load->view('sidebar');
			$this->load->view('header.php');
            $this->load->view('password_change');
			$this->load->view('leftSidebar.php');
			$this->load->view('footer_new');
        }
    }

    public function password_update() {
        if ($this->session->userdata('bktravels_logged_in') != TRUE) {
            redirect(base_url());
        } else {
            $result = $this->Login_m->password_update();
            echo $result;
        }
    }

    public function Logout() {
        $this->session->set_userdata(array('bktravels_logged_in' => FALSE, 'bktravels_user_id' => '', 'bktravels_travel_id' => ''));
        redirect("http://".$_SERVER['HTTP_HOST']."/");
    }

}
