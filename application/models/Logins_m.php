<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Logins_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_agentslist_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select('*');
        $this->db->where('operator_id', $travel_id);
        $this->db->where('agent_type', 1);
        $query = $this->db->get("agents_operator");
        return $query->result();
    }

    function get_agentdata_db() {
        $uid = $this->input->get('uid');
        $query = $this->db->query("SELECT * FROM agents_operator WHERE id='$uid' ");
        return $query->result();
    }

    public function get_agentslist_post_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select('*');
        $this->db->where('operator_id', $travel_id);
        $this->db->where('agent_type', 2);
        $this->db->where('pay_type', 'postpaid');
        $query = $this->db->get("agents_operator");
        return $query->result();
    }

    public function get_agentslist_pre_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select('*');
        $this->db->where('operator_id', $travel_id);
        $this->db->where('agent_type', 2);
        $this->db->where('pay_type', 'prepaid');
        $query = $this->db->get("agents_operator");
        return $query->result();
    }

    public function check_user() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $username = $this->input->post('username');
        $stmt = "select uname from agents_operator where uname='$username'";
        $res = $this->db->query($stmt);
        if ($res->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function add_agent2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $name = $this->input->post('name');
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $contact = $this->input->post('contact');
        $landline = $this->input->post('landline');
        $address = $this->input->post('address');
        $location = $this->input->post('location');
        $status = 1;
        $bal_limit = '-' . $this->input->post('bal_limit');
        $margin = $this->input->post('margin');
        $comm_type = $this->input->post('comm_type');
        $key = $this->input->post('key');
        $by_cash = $this->input->post('by_cash');
        $by_phone = $this->input->post('by_phone');
        $by_agent = $this->input->post('by_agent');
        $by_phone_agent = $this->input->post('by_phone_agent');
        $by_employee = $this->input->post('by_employee');
        $ticket_booking = $this->input->post('ticket_booking');
        $is_hover = $this->input->post('is_hover');
        $agent_charge = $this->input->post('agent_charge');
        $price_edit = $this->input->post('price_edit');
        $changeprice = $this->input->post('changeprice');
        $grabrelease = $this->input->post('grabrelease');
        $individual_seatfare = $this->input->post('individual_seatfare');
        $quotaupdation = $this->input->post('quotaupdation');
        $cancelservice = $this->input->post('cancelservice');
        $ticket_cancellation = $this->input->post('ticket_cancellation');
        $branchcancellation = $this->input->post('branchcancellation');
        $ticket_modify = $this->input->post('ticket_modify');
        $ticket_reschedule = $this->input->post('ticket_reschedule');
        $discount = $this->input->post('discount');
        $vehicle_assignment = $this->input->post('vehicle_assignment');
        $boardingchart = $this->input->post('boardingchart');
        $detailreports = $this->input->post('detailreports');
        $rtalist = $this->input->post('rtalist');
        $branchlogins = $this->input->post('branchlogins');
        $postpaidlogins = $this->input->post('postpaidlogins');
        $prepaidlogins = $this->input->post('prepaidlogins');
        $createbus = $this->input->post('createbus');
        $activedeactive = $this->input->post('activedeactive');
        $modifybus = $this->input->post('modifybus');
        $spservice = $this->input->post('spservice');
        $date = date("Y-m-d");
        $ip = $this->input->ip_address();
        $balance = 0;
        if ($key == 'branch') {
            $pay_type = 'postpaid';
            $agent_type = '1';
            $agent_type_name = 'inhouse';
        } else if ($key == 'postpaid') {
            $pay_type = 'postpaid';
            $agent_type = '2';
            $agent_type_name = 'external';
        } else if ($key == 'prepaid') {
            $pay_type = 'prepaid';
            $agent_type = '2';
            $agent_type_name = 'external';
        }

        $stmt = "insert into agents_operator(name,        
				uname,
		        email,        
        		password,       
		        mobile,        		
		        address,        
        		city,
				operator_id,
				agent_type,
				agent_type_name,
				date,
				status,
				ip,
				pay_type,
				bal_limit,
				margin,
				comm_type,				
				by_cash, 
				by_phone, 
				by_agent, 
				by_phone_agent,	
				by_employee,			 
				ticket_booking,
	            is_hover,
    	        agent_charge,
        	    price_edit,
            	changeprice,
	            grabrelease,
    	        individual_seatfare,
        	    quotaupdation,
            	cancelservice,           
	            ticket_cancellation, 			
				branchcancellation,
        	    ticket_modify,
            	ticket_reschedule,
	            discount,
    	        vehicle_assignment,
        	    boardingchart,
            	detailreports,
	            rtalist,
    	        branchlogins,           
        	    postpaidlogins,
				prepaidlogins,
	            createbus,
    	        activedeactive,
        	    modifybus,
            	spservice) values('$name','$username','$email','$password','$contact','$address','$location','$travel_id','$agent_type','$agent_type_name','$date','$status','$ip','$pay_type','$bal_limit','$margin','$comm_type','$by_cash','$by_phone','$by_agent','$by_phone_agent','$by_employee','$ticket_booking','$is_hover','$agent_charge','$price_edit','$changeprice','$grabrelease','$individual_seatfare','$quotaupdation','$cancelservice','$ticket_cancellation','$branchcancellation','$ticket_modify','$ticket_reschedule','$discount','$vehicle_assignment','$boardingchart','$detailreports','$rtalist','$branchlogins','$postpaidlogins','$prepaidlogins','$createbus','$activedeactive','$modifybus','$spservice')";
        $query = $this->db->query($stmt);
        if ($query) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function edit_agent1() {
        $uid = $this->input->get('uid');
        $stmt = "select * from agents_operator where id='$uid'";
        $query = $this->db->query($stmt);
        return $query;
    }

    function edit_agent3() {
        $uid = $this->input->post('uid');
        $name = $this->input->post('name');
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $contact = $this->input->post('contact');
        $address = $this->input->post('address');
        $location = $this->input->post('location');
        $status = $this->input->post('status');
        $bal_limit = '-' . $this->input->post('bal_limit');
        $margin = $this->input->post('margin');
        $comm_type = $this->input->post('comm_type');
        $key = $this->input->post('key');
        $by_cash = $this->input->post('by_cash');
        $by_phone = $this->input->post('by_phone');
        $by_agent = $this->input->post('by_agent');
        $by_phone_agent = $this->input->post('by_phone_agent');
        $by_employee = $this->input->post('by_employee');
        $ticket_booking = $this->input->post('ticket_booking');
        $is_hover = $this->input->post('is_hover');
        $agent_charge = $this->input->post('agent_charge');
        $price_edit = $this->input->post('price_edit');
        $changeprice = $this->input->post('changeprice');
        $grabrelease = $this->input->post('grabrelease');
        $individual_seatfare = $this->input->post('individual_seatfare');
        $quotaupdation = $this->input->post('quotaupdation');
        $cancelservice = $this->input->post('cancelservice');
        $ticket_cancellation = $this->input->post('ticket_cancellation');
        $branchcancellation = $this->input->post('branchcancellation');
        $ticket_modify = $this->input->post('ticket_modify');
        $ticket_reschedule = $this->input->post('ticket_reschedule');
        $discount = $this->input->post('discount');
        $vehicle_assignment = $this->input->post('vehicle_assignment');
        $boardingchart = $this->input->post('boardingchart');
        $detailreports = $this->input->post('detailreports');
        $rtalist = $this->input->post('rtalist');
        $branchlogins = $this->input->post('branchlogins');
        $postpaidlogins = $this->input->post('postpaidlogins');
        $prepaidlogins = $this->input->post('prepaidlogins');
        $createbus = $this->input->post('createbus');
        $activedeactive = $this->input->post('activedeactive');
        $modifybus = $this->input->post('modifybus');
        $spservice = $this->input->post('spservice');
        $date = date("Y-m-d");
        $ip = $this->input->ip_address();

        if ($key == 'branch') {
            $pay_type = 'postpaid';
            $agent_type = '1';
            $agent_type_name = 'inhouse';
        } else if ($key == 'postpaid') {
            $pay_type = 'postpaid';
            $agent_type = '2';
            $agent_type_name = 'external';
        } else if ($key == 'prepaid') {
            $pay_type = 'prepaid';
            $agent_type = '2';
            $agent_type_name = 'external';
        }
        $stmt = "update agents_operator set name='$name',uname='$username',email='$email',password='$password',mobile='$contact',address='$address',city='$location',date='$date',status='$status',ip='$ip',bal_limit='$bal_limit',margin='$margin',comm_type='$comm_type',by_cash='$by_cash',by_phone='$by_phone',by_agent='$by_agent',by_phone_agent='$by_phone_agent',by_employee='$by_employee',ticket_booking='$ticket_booking',is_hover='$is_hover',agent_charge='$agent_charge',price_edit='$price_edit',changeprice='$changeprice',grabrelease='$grabrelease',individual_seatfare='$individual_seatfare',quotaupdation='$quotaupdation',cancelservice='$cancelservice',ticket_cancellation='$ticket_cancellation',branchcancellation='$branchcancellation',ticket_modify='$ticket_modify',ticket_reschedule='$ticket_reschedule',discount='$discount',vehicle_assignment='$vehicle_assignment',boardingchart='$boardingchart',detailreports='$detailreports',rtalist='$rtalist',branchlogins='$branchlogins',postpaidlogins='$postpaidlogins',prepaidlogins='$prepaidlogins',createbus='$createbus',activedeactive='$activedeactive',modifybus='$modifybus',spservice='$spservice' where id='$uid'";
        //echo "$stmt";
        $query = $this->db->query($stmt);
        if ($query) {
            echo 1;
        } else {
            echo 0;
        }
    }

}
