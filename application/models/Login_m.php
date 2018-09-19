<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function validateLogin2() {
        $Username = $this->input->post('Username');
        $Password = $this->input->post('Password');
        //$travel_id = $this->input->post('travel_id');
        //$stmt = "select * from agents_operator where uname='$Username' and password='$Password' and status='1' and operator_id ='$travel_id'";
		$stmt = "select * from agents_operator where uname='$Username' and password='$Password' and status='1'";
        $res1 = $this->db->query($stmt);
        if ($res1->num_rows() > 0) {
            
            foreach ($res1->result() as $rows) {
				$travel_id = $rows->operator_id;
				$stmt = "select operator_title,op_url from registered_operators where travel_id='$travel_id'";
				$res2 = $this->db->query($stmt);
				foreach ($res2->result() as $res3) {
					$operator_title = $res3->operator_title;
					$op_url = $res3->op_url;
				}
                $newdata = array(
                    'bktravels_operator_title' => $operator_title,
                    'bktravels_op_url' => $op_url,
                    'bktravels_user_id' => $rows->id,
                    'bktravels_user_name' => $rows->uname,
                    'bktravels_email_id' => $rows->email,
                    'bktravels_password' => $rows->password,
                    'bktravels_appname' => $rows->appname,
                    'bktravels_name' => $rows->name,
                    'bktravels_address' => $rows->address,
                    'bktravels_location' => $rows->city,
                    'bktravels_contact_no' => $rows->mobile,
                    'bktravels_travel_id' => $rows->operator_id,
                    'bktravels_state' => $rows->state,
                    'bktravels_agent_type' => $rows->agent_type,
                    'bktravels_agent_type_name' => $rows->agent_type_name,
                    'bktravels_status' => $rows->status,
                    'bktravels_balance' => $rows->balance,
                    'bktravels_bal_limit' => $rows->bal_limit,
                    'bktravels_margin' => $rows->margin,
					'bktravels_comm_type' => $rows->comm_type,
                    'bktravels_pay_type' => $rows->pay_type,
                    'bktravels_is_pay' => $rows->is_pay,
                    'bktravels_is_hover' => $rows->is_hover,
                    'bktravels_allow_cancellation' => $rows->allow_cancellation,
                    'bktravels_allow_modification' => $rows->allow_modification,
                    'bktravels_branch' => $rows->branch,
                    'bktravels_branch_address' => $rows->branch_address,
                    'bktravels_payment_reports' => $rows->payment_reports,
                    'bktravels_booking_reports' => $rows->booking_reports,
                    'bktravels_passenger_reports' => $rows->passenger_reports,
                    'bktravels_vehicle_assignment' => $rows->vehicle_assignment,
                    'bktravels_ticket_booking' => $rows->ticket_booking,
                    'bktravels_check_fare' => $rows->check_fare,
                    'bktravels_ticket_status' => $rows->ticket_status,
                    'bktravels_ticket_cancellation' => $rows->ticket_cancellation,
                    'bktravels_ticket_modify' => $rows->ticket_modify,
                    'bktravels_board_passenger_reports' => $rows->board_passenger_reports,
                    'bktravels_ticket_reschedule' => $rows->ticket_reschedule,
                    'bktravels_group_boarding_passenger_reports' => $rows->group_boarding_passenger_reports,
                    'bktravels_by_cash' => $rows->by_cash,
                    'bktravels_by_phone' => $rows->by_phone,
                    'bktravels_by_agent' => $rows->by_agent,
                    'bktravels_by_phone_agent' => $rows->by_phone_agent,
                    'bktravels_by_employee' => $rows->by_employee,
                    'bktravels_head_office' => $rows->head_office,
                    'bktravels_other_services' => $rows->other_services,
                    'bktravels_show_avail_seat' => $rows->show_avail_seat,
                    'bktravels_op_comm' => $rows->op_comm,
                    'bktravels_price_edit' => $rows->price_edit,
                    'bktravels_bus_mgmt' => $rows->bus_mgmt,
                    'bktravels_seat_mgmt' => $rows->seat_mgmt,
                    'bktravels_login_mgmt' => $rows->login_mgmt,
                    'bktravels_api_type' => $rows->api_type,
                    'bktravels_individual_seatfare' => $rows->individual_seatfare,
                    'bktravels_agent_charge' => $rows->agent_charge,
                    'bktravels_logged_in' => 'TRUE',
                    'bktravels_changeprice' => $rows->changeprice,
                    'bktravels_grabrelease' => $rows->grabrelease,
                    'bktravels_quotaupdation' => $rows->quotaupdation,
                    'bktravels_cancelservice' => $rows->cancelservice,
                    'bktravels_updations' => $rows->updations,
                    'bktravels_branchcancellation' => $rows->branchcancellation,
                    'bktravels_discount' => $rows->discount,
                    'bktravels_reports' => $rows->reports,
                    'bktravels_boardingchart' => $rows->boardingchart,
                    'bktravels_detailreports' => $rows->detailreports,
                    'bktravels_rtalist' => $rows->rtalist,
                    'bktravels_branchlogins' => $rows->branchlogins,
                    'bktravels_postpaidlogins' => $rows->postpaidlogins,
                    'bktravels_prepaidlogins' => $rows->prepaidlogins,
                    'bktravels_createbus' => $rows->createbus,
                    'bktravels_activedeactive' => $rows->activedeactive,
                    'bktravels_modifybus' => $rows->modifybus,
                    'bktravels_spservice' => $rows->spservice,
                );
            }
            $this->session->set_userdata($newdata);
            echo 1;
        } else {
            echo 2;
        }
    }
	public function validateLogin22() {
        $Username = $this->input->post('uname');
        $Password = $this->input->post('password');
        $travel_id = $this->input->post('travel_id');        
        $stmt = "select * from agents_operator where uname='$Username' and password='$Password' and status='1' and operator_id ='$travel_id'";
        $res1 = $this->db->query($stmt);
        if ($res1->num_rows() > 0) {
            $stmt = "select operator_title,op_url from registered_operators where travel_id='$travel_id'";
            $res2 = $this->db->query($stmt);
            foreach ($res2->result() as $res3) {
                $operator_title = $res3->operator_title;
                $op_url = $res3->op_url;
            }
            foreach ($res1->result() as $rows) {
                $newdata = array(
                    'bktravels_operator_title' => $operator_title,
                    'bktravels_op_url' => $op_url,
                    'bktravels_user_id' => $rows->id,
                    'bktravels_user_name' => $rows->uname,
                    'bktravels_email_id' => $rows->email,
                    'bktravels_password' => $rows->password,
                    'bktravels_appname' => $rows->appname,
                    'bktravels_name' => $rows->name,
                    'bktravels_address' => $rows->address,
                    'bktravels_location' => $rows->city,
                    'bktravels_contact_no' => $rows->mobile,
                    'bktravels_travel_id' => $rows->operator_id,
                    'bktravels_state' => $rows->state,
                    'bktravels_agent_type' => $rows->agent_type,
                    'bktravels_agent_type_name' => $rows->agent_type_name,
                    'bktravels_status' => $rows->status,
                    'bktravels_balance' => $rows->balance,
                    'bktravels_bal_limit' => $rows->bal_limit,
                    'bktravels_margin' => $rows->margin,
                    'bktravels_pay_type' => $rows->pay_type,
                    'bktravels_is_pay' => $rows->is_pay,
                    'bktravels_is_hover' => $rows->is_hover,
                    'bktravels_allow_cancellation' => $rows->allow_cancellation,
                    'bktravels_allow_modification' => $rows->allow_modification,
                    'bktravels_branch' => $rows->branch,
                    'bktravels_branch_address' => $rows->branch_address,
                    'bktravels_payment_reports' => $rows->payment_reports,
                    'bktravels_booking_reports' => $rows->booking_reports,
                    'bktravels_passenger_reports' => $rows->passenger_reports,
                    'bktravels_vehicle_assignment' => $rows->vehicle_assignment,
                    'bktravels_ticket_booking' => $rows->ticket_booking,
                    'bktravels_check_fare' => $rows->check_fare,
                    'bktravels_ticket_status' => $rows->ticket_status,
                    'bktravels_ticket_cancellation' => $rows->ticket_cancellation,
                    'bktravels_ticket_modify' => $rows->ticket_modify,
                    'bktravels_board_passenger_reports' => $rows->board_passenger_reports,
                    'bktravels_ticket_reschedule' => $rows->ticket_reschedule,
                    'bktravels_group_boarding_passenger_reports' => $rows->group_boarding_passenger_reports,
                    'bktravels_by_cash' => $rows->by_cash,
                    'bktravels_by_phone' => $rows->by_phone,
                    'bktravels_by_agent' => $rows->by_agent,
                    'bktravels_by_phone_agent' => $rows->by_phone_agent,
                    'bktravels_by_employee' => $rows->by_employee,
                    'bktravels_head_office' => $rows->head_office,
                    'bktravels_other_services' => $rows->other_services,
                    'bktravels_show_avail_seat' => $rows->show_avail_seat,
                    'bktravels_op_comm' => $rows->op_comm,
                    'bktravels_price_edit' => $rows->price_edit,
                    'bktravels_bus_mgmt' => $rows->bus_mgmt,
                    'bktravels_seat_mgmt' => $rows->seat_mgmt,
                    'bktravels_login_mgmt' => $rows->login_mgmt,
                    'bktravels_api_type' => $rows->api_type,
                    'bktravels_individual_seatfare' => $rows->individual_seatfare,
                    'bktravels_agent_charge' => $rows->agent_charge,
                    'bktravels_logged_in' => 'TRUE',
                    'bktravels_changeprice' => $rows->changeprice,
                    'bktravels_grabrelease' => $rows->grabrelease,
                    'bktravels_quotaupdation' => $rows->quotaupdation,
                    'bktravels_cancelservice' => $rows->cancelservice,
                    'bktravels_updations' => $rows->updations,
                    'bktravels_branchcancellation' => $rows->branchcancellation,
                    'bktravels_discount' => $rows->discount,
                    'bktravels_reports' => $rows->reports,
                    'bktravels_boardingchart' => $rows->boardingchart,
                    'bktravels_detailreports' => $rows->detailreports,
                    'bktravels_rtalist' => $rows->rtalist,
                    'bktravels_branchlogins' => $rows->branchlogins,
                    'bktravels_postpaidlogins' => $rows->postpaidlogins,
                    'bktravels_prepaidlogins' => $rows->prepaidlogins,
                    'bktravels_createbus' => $rows->createbus,
                    'bktravels_activedeactive' => $rows->activedeactive,
                    'bktravels_modifybus' => $rows->modifybus,
                    'bktravels_spservice' => $rows->spservice,
                );
            }
            $this->session->set_userdata($newdata);
            return 1;
        } else {
            return 2;
        }
    }

    function password_update() {

        $username = $this->session->userdata('bktravels_user_name');
        $password = $this->session->userdata('bktravels_password');

        $oldpassword = $this->input->post('oldpassword');
        $newpassword = $this->input->post('newpassword');
        $conpassword = $this->input->post('conpassword');
        //updating password in database
        $stmt = "UPDATE agents_operator SET password='$newpassword' WHERE uname='$username' and password='$password'";

        $query = $this->db->query($stmt);
        if ($query) {
            $this->session->set_userdata('bktravels_password', $new_password);
            return 1;
        } else {
            return 2;
        }
    }

}
