<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Updations_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function getcanc_detail() {
        $travid = $this->session->userdata('bktravels_travel_id');
        $this->db->select('canc_terms');
        $this->db->where('travel_id', $travid);
        $query = $this->db->get('registered_operators');
        foreach ($query->result() as $row) {
            $canc = $row->canc_terms;
        }
        return $canc;
    }

    function branch_confirmcancel1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $tktno = trim($_POST['tktno']);
        $percent = trim($_POST['percent']);
        $ndt = trim($_POST['doj']);
        $jdt = date("Y-m-d", strtotime($ndt));
        $check = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt'") or die(mysql_error());
        foreach ($check->result() as $check1) {
            $travel_id_tkt = $check1->travel_id;
        }
        if ($travel_id_tkt == $travel_id) {
            $can1 = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt'  and status='cancelled' ") or die(mysql_error());
            foreach ($can1->result() as $can) {
                $stat = $can->status;
            }
            if ($stat != "cancelled") {
                $sql = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='Confirmed'");
                foreach ($sql->result() as $res) {
                    if ($sql->num_rows() != 0) {
                        $bp = $res->board_point;
                        $lm = $res->land_mark;
                        $data['pname'] = $res->pname;
                        $data['travels'] = $res->travels;
                        $data['tkt_no'] = $res->tkt_no;
                        $data['jdate'] = $res->jdate;
                        $data['email'] = $email;
                        $data['source'] = $res->source;
                        $data['dest'] = $res->dest;
                        $data['seats'] = $res->seats;
                        $data['pass'] = $res->pass;
                        $data['gender'] = $res->gender;
                        $data['start_time'] = $res->start_time;
                        $data['bus_type'] = $res->bus_type;
                        $data['land_mark'] = $lm;
                        $data['tkt_fare'] = $res->tkt_fare;
						$data['base_fare'] = $res->base_fare;
						$data['service_tax_amount'] = $res->service_tax_amount;
						$data['discount_amount'] = $res->discount_amount;
						$data['convenience_charge'] = $res->convenience_charge;
						$data['cgst'] = $res->cgst;
						$data['sgst'] = $res->sgst;
                        $data['paid'] = $res->paid;
                        $data['status'] = $res->status;
                        $data['board_point'] = $bp;
                        $data['service_no'] = $res->service_no;
                        $data['book_pay_type'] = $res->book_pay_type;
                        $data['canc_terms'] = $percent;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ctime = date('Y-m-d H:i:s');
                        $sql2 = $this->db->query("insert into checkcancel(tkt_no,email,date_time,ip,travel_id,agent_id) values('$tktno','$percent','$ctime','$ip','$travel_id','$agent_id') ") or die(mysql_error());

                        $data['error'] = 1;
                    } else {
                        $data['error'] = 0;
                    }
                }
            } else {
                $data = 2;
            }
        } else {
            $data['error'] = 0;
        }
        return $data;
    }

    function branch_cancelticket1() {
        $tktno = $_POST['tktno'];
		$percent = $_POST['percent'];
		$ndt = $_POST['jdate'];
		$jdt = date("Y-m-d", strtotime($ndt));
		$cc = $_POST['cc'];

        $can1 = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='cancelled'") or die(mysql_error());
        foreach ($can1->result() as $can) {
            $stat = $can->status;
        }
        if ($stat != "cancelled") {
            $travel_id = $this->session->userdata('bktravels_travel_id');
            $agent_id = $this->session->userdata('bktravels_user_id');
            $agent_type = $this->session->userdata('bktravels_agent_type');
            $name = $this->session->userdata('bktravels_name');
            $agent_type_name = $this->session->userdata('bktravels_agent_type_name');
            $tktno = $_POST['tktno'];
            $percent = $_POST['percent'];
            $ndt = $_POST['jdate'];
            $jdt = date("Y-m-d", strtotime($ndt));
            $cc = $_POST['cc'];
            //checking for agent balance.
            $query = $this->db->query("select * from agents_operator where id='$agent_id' and agent_type='$agent_type' and  operator_id='$travel_id' and id='$agent_id' ") or die(mysql_error());
            foreach ($query->result() as $res) {
                $bal = $res->balance;
            }
            $sql = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='Confirmed'");
            foreach ($sql->result() as $res) {
                if ($sql->num_rows() != 0) {
                    $tkt_no = $tktno;
                    $pnr = $res->pnr;
                    $service_no = $res->service_no;
                    $board_point = $res->board_point;
                    $bpid = $res->bpid;
                    $land_mark = $res->land_mark;
                    $source = $res->source;
                    $dest = $res->dest;
                    $travels = $res->travels;
                    $bus_type = $res->bus_type;
                    $bdate = $res->bdate;
                    $jdate = $jdt;
                    $seats = $res->seats;
                    $gender = $res->gender;
                    $start_time = $res->start_time;
                    $arr_time = $res->arr_time;
                    $paid = $res->paid;
                    $save = $res->save;
                    $tkt_fare = $res->tkt_fare;
					$base_fare = $res->base_fare;
					$service_tax_amount = $res->service_tax_amount;
					$discount_amount = $res->discount_amount;
					$convenience_charge = $res->convenience_charge;
					$cgst = $res->cgst;
					$sgst = $res->sgst;
					$tcs = $res->tcs;
                    $pname = $res->pname;
                    $pemail = $email;
                    $pmobile = $res->pmobile;
                    $age = $res->age;
                    $refno = $res->refno;
                    $status = "cancelled";
                    $pass = $res->pass;
                    $cseat = $res->cseat;
                    $ccharge = $cc;
                    $camt = ($base_fare * $cc) / 100;
                    $refamt = $paid - $camt;
                    $travel_id = $res->travel_id;
                    $ip = $_SERVER['REMOTE_ADDR'];
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
                    $agent_id = $res->agent_id;
                    $book_pay_type = $res->book_pay_type;
                    $book_pay_agent = $res->book_pay_agent;
					$iso = $res->iso;
					$dialCode = $res->dialCode;

                    //checking for agent balance.
                    $query = $this->db->query("select * from agents_operator where id='$agent_id' and  operator_id='$travel_id' ") or die(mysql_error());
                    foreach ($query->result() as $res1) {
                        $bal = $res1->balance;
                    }
                    $bal1 = $bal + $refamt;
                    //updating agent balance
                    $queryy = $this->db->query("update  agents_operator set balance='$bal1' where id='$agent_id' and   operator_id='$travel_id' ") or die(mysql_error());
                    $data['pname'] = $res->pname;
                    $data['travels'] = $res->travels;
                    $data['tkt_no'] = $res->tkt_no;
                    $data['jdate'] = $res->jdate;
                    $data['email'] = $res->pemail;
                    $data['source'] = $res->source;
                    $data['dest'] = $res->dest;
                    $data['seats'] = $res->seats;
                    $data['pass'] = $res->pass;
                    $data['start_time'] = $res->start_time;
                    $data['bus_type'] = $res->bus_type;
                    $data['land_mark'] = $land_mark;
                    $data['tkt_fare'] = $res->tkt_fare;
                    $data['paid'] = $res->paid;
                    $data['status'] = $status;
                    $data['board_point'] = $board_point;
                    $data['service_no'] = $res->service_no;
                    $data['cc'] = $cc;
                    $data['error'] = 1;

                    $sql1 = $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,base_fare,service_tax_amount,discount_amount,convenience_charge,pname,pemail,pmobile,age,refno,status,pass,cseat,ccharge,camt,refamt,travel_id,ip,time,cdate,ctime,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,book_pay_type,book_pay_agent,cgst,sgst,tcs) values('$tkt_no','$pnr','$service_no','$board_point','$bpid','$land_mark','$source','$dest','$travels','$bus_type','$bdate','$jdate','$seats','$gender','$start_time','$arr_time','$paid','$save','$tkt_fare','$base_fare','$service_tax_amount','$discount_amount','$convenience_charge','$pname','$pemail','$pmobile','$age','$refno','$status','$pass','$seats','$ccharge','$camt','$refamt','$travel_id','$ip','$time','$cdate','$ctime','$id_type','$id_num','$padd','$alter_ph','$fid','$tid','$operator_agent_type','$agent_id','$book_pay_type','$book_pay_agent','$cgst','$sgst','$tcs')") or die(mysql_error());

                    $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdt'") or die(mysql_error());
                    foreach ($sql3->result() as $row3) {
                        $as1 = $row3->available_seats;
                    }
                    $as = $as1 + $pass;
                    $sql4 = $this->db->query("update buses_list set available_seats='$as' where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdt'") or die(mysql_error());

                    $ss1 = explode(',', $seats);
					
					/* acivated from java side for stagwise available booking
                    for ($k = 0; $k < $pass; $k++) {
                        $sea = $ss1[$k];
                        $sql5 = $this->db->query("update layout_list set seat_status='0',is_ladies='0' where journey_date='$jdate' and service_num='$service_no' and travel_id='$travel_id' and seat_name='$sea'") or die(mysql_error());
                    }
					*/
					
					$ch = curl_init();
					//curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/TestSpring/updateCancelTicket/$travel_id/$tktno");
					curl_setopt($ch, CURLOPT_URL, "http://ticketengine.in:8080/TestSpring_intl_live/updateCancelTicket/$travel_id/$tktno");
					
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_GET, 1);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, "operatorId=$travel_id&tktno=$tktno");
					$buffer = curl_exec($ch);
					curl_close($ch);
					
					
                    $sql3 = $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,can_fare,refamt,net_amt,bal,dat,ip,agent_id,travel_id,status) values('$tkt_no','$pnr','$pname','$source','$dest','$ctime','Credit','$tkt_fare','$save','$camt','$refamt','$paid','$bal1','$cdate','$ip','$agent_id','$travel_id','cancelled')") or die(mysql_error());
                    
                    $text = "Your ticket has been successfully cancelled having ticket no " . $tkt_no . "-" . $travels;	
					//$text = urlencode("Your ticket has been successfully cancelled having ticket no " . $tkt_no . " - " . $travels . "");
					//echo $text;
					
					$receipientno = $pmobile;
					$sql_sendId = $this->db->query("select distinct sender_id,operator_title,op_url from registered_operators where travel_id='$travel_id'") or die(mysql_error());
					foreach ($sql_sendId->result() as $row) {
						$sender_id = $row->sender_id;
						//$operator_title = $row->operator_title;
						//$op_url = $row->op_url;
					}	
					//$sender_id = "TRAVEL";
					//msg91 SMS
					$this->Updations_m->msg91sms($receipientno,$text,$iso,$dialCode);
					//$msgtxt="this is test message , test";				
					
					
					/* mvaayooapi/MessageCompose
					$ch = curl_init();
					$user = "pridhvi@msn.com:activa1525@";
					curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$sender_id&receipientno=$receipientno&msgtxt=$text");
					$buffer = curl_exec($ch);
					curl_close($ch);*/
					
					

						return $data;
                } else {
                    $data['error'] = 0;
                }
            }
        } else {
            $data = 1;
        }
        return $data;
    }

    public function confirm_cancel() {
        $agent_id = $this->session->userdata('bktravels_user_id');
        $tktno = trim($_POST['tktno']);
        $email = trim($_POST['email']);
        $ndt = trim($_POST['doj']);
        $jdt = date("Y-m-d", strtotime($ndt));
        $can1 = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and agent_id='$agent_id' and status='cancelled' ") or die(mysql_error());
        foreach ($can1->result() as $can) {
            $stat = $can->status;
        }
        if ($stat != "cancelled") {

            $sql = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='Confirmed' and agent_id='$agent_id'");
            foreach ($sql->result() as $res) {
                $reschedule = $res->reschedule;
            }
            if ($reschedule == "reschedule") {
                $data = 3;
            } else if ($sql->num_rows() != 0) {
                $bp = $res->board_point;
                $lm = $res->land_mark;
                $data['pname'] = $res->pname;
                $data['travels'] = $res->travels;
                $data['tkt_no'] = $res->tkt_no;
                $data['jdate'] = $res->jdate;
                $data['email'] = $email;
                $data['source'] = $res->source;
                $data['dest'] = $res->dest;
                $data['seats'] = $res->seats;
                $data['pass'] = $res->pass;
                $data['gender'] = $res->gender;
                $data['start_time'] = $res->start_time;
                $data['bus_type'] = $res->bus_type;
                $data['land_mark'] = $lm;
                $data['tkt_fare'] = $res->tkt_fare;
				$data['base_fare'] = $res->base_fare;
				$data['service_tax_amount'] = $res->service_tax_amount;
				$data['discount_amount'] = $res->discount_amount;
				$data['convenience_charge'] = $res->convenience_charge;
				$data['cgst'] = $res->cgst;
				$data['sgst'] = $res->sgst;
                $data['paid'] = $res->paid;
                $data['status'] = $res->status;
                $data['board_point'] = $bp;
                $data['service_no'] = $res->service_no;
                $data['book_pay_type'] = $res->book_pay_type;
                $travel_id = $res->travel_id;
                $ip = $_SERVER['REMOTE_ADDR'];
                $ctime = date('Y-m-d H:i:s');

                $sql2 = $this->db->query("insert into checkcancel(tkt_no,email,date_time,ip,travel_id,agent_id) values('$tktno','$email','$ctime','$ip','$travel_id','$agent_id') ") or die(mysql_error());

                $sql1 = $this->db->query("select distinct canc_terms from registered_operators where travel_id='$travel_id'");
                foreach ($sql1->result() as $res1) {
                    $data['canc_terms'] = $res1->canc_terms;
                }
                $data['error'] = 1;
            } else {
                $data['error'] = 0;
            }
        } else {
            $data = 2;
        }
        return $data;
    }

    public function ticket_cancel() {

        $travelid = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $name = $this->session->userdata('bktravels_name');
        $agent_type_name = $this->session->userdata('bktravels_agent_type_name');

        $tktno = $_POST['tktno'];
        $email = $_POST['email'];
        $ndt = $_POST['jdate'];
        $jdt = date("Y-m-d", strtotime($ndt));
        $cc = $_POST['cc'];

        $can1 = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='cancelled' and agent_id='$agent_id'") or die(mysql_error());
        foreach ($can1->result() as $can) {
            $stat = $can->status;
        }
        if ($stat != "cancelled") {
            $query = $this->db->query("select * from agents_operator where id='$agent_id' and agent_type='$agent_type' and  operator_id='$travelid' ") or die(mysql_error());

            $sql = $this->db->query("select * from master_booking where tkt_no='$tktno' and jdate='$jdt' and status='Confirmed'");
            foreach ($sql->result() as $res) {
                $reschedule = $res->reschedule;
            }
            if ($reschedule == "reschedule") {
                $data = 3;
            } else if ($sql->num_rows() != 0) {
                $tkt_no = $tktno;
                $pnr = $res->pnr;
                $service_no = $res->service_no;
                $board_point = $res->board_point;
                $bpid = $res->bpid;
                $land_mark = $res->land_mark;
                $source = $res->source;
                $dest = $res->dest;
                $travels = $res->travels;
                $bus_type = $res->bus_type;
                $bdate = $res->bdate;
                $jdate = $jdt;
                $seats = $res->seats;
                $gender = $res->gender;
                $start_time = $res->start_time;
                $arr_time = $res->arr_time;
                $paid = $res->paid;
                $save = $res->save;
                $tkt_fare = $res->tkt_fare;
				$base_fare = $res->base_fare;
				$service_tax_amount = $res->service_tax_amount;
				$discount_amount = $res->discount_amount;
				$convenience_charge = $res->convenience_charge;
				$cgst = $res->cgst;
				$sgst = $res->sgst;
				$tcs = $res->tcs;
                $pname = $res->pname;
                $pemail = $email;
                $pmobile = $res->pmobile;
                $age = $res->age;
                $refno = $res->refno;
                $status = "cancelled";
                $pass = $res->pass;
                $cseat = $res->cseat;
                $ccharge = $cc;
                $camt = ($base_fare * $cc) / 100;
                $refamt = $paid - $camt;
                $travel_id = $res->travel_id;
                $ip = $_SERVER['REMOTE_ADDR'];
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
                $agent_id = $res->agent_id;
                $book_pay_type = $res->book_pay_type;
                $book_pay_agent = $res->book_pay_agent;
				$iso = $res->iso;
				$dialCode = $res->dialCode;
                //checking for agent balance.
                foreach ($query->result() as $res1) {
                    if ($travel_id == $travelid) {
                        $bal = $res1->balance;
                    } else {
                        $bal = $res1->balance1;
                    }
                }
                $bal1 = $bal + $refamt;
                //updating agent balance
                if ($travel_id == $travelid) {
                    $queryy = $this->db->query("update  agents_operator set balance='$bal1' where id='$agent_id' and agent_type='$agent_type' and  operator_id='$travelid' ") or die(mysql_error());
                } else {
                    $queryy = $this->db->query("update  agents_operator set balance1='$bal1' where id='$agent_id' and agent_type='$agent_type' and  operator_id='$travelid' ") or die(mysql_error());
                }

                $data['pname'] = $res->pname;
                $data['travels'] = $res->travels;
                $data['tkt_no'] = $res->tkt_no;
                $data['jdate'] = $res->jdate;
                $data['email'] = $res->pemail;
                $data['source'] = $res->source;
                $data['dest'] = $res->dest;
                $data['seats'] = $res->seats;
                $data['pass'] = $res->pass;
                $data['start_time'] = $res->start_time;
                $data['bus_type'] = $res->bus_type;
                $data['land_mark'] = $land_mark;
                $data['tkt_fare'] = $res->tkt_fare;
				$data['base_fare'] = $res->base_fare;
				$data['service_tax_amount'] = $res->service_tax_amount;
				$data['discount_amount'] = $res->discount_amount;
				$data['convenience_charge'] = $res->convenience_charge;
				$data['cgst'] = $res->cgst;
				$data['sgst'] = $res->sgst;
                $data['paid'] = $res->paid;
                $data['status'] = $status;
                $data['board_point'] = $board_point;
                $data['service_no'] = $res->service_no;
                $data['cc'] = $cc;
                $data['error'] = 1;


                $sql1 = $this->db->query("insert into master_booking(tkt_no,pnr,service_no,board_point,bpid,land_mark,source,dest,travels,bus_type,bdate,jdate,seats,gender,start_time,arr_time,paid,save,tkt_fare,base_fare,service_tax_amount,discount_amount,convenience_charge,pname,pemail,pmobile,age,refno,status,pass,cseat,ccharge,camt,refamt,travel_id,ip,time,cdate,ctime,id_type,id_num,padd,alter_ph,fid,tid,operator_agent_type,agent_id,book_pay_type,book_pay_agent,cgst,sgst,tcs) values('$tkt_no','$pnr','$service_no','$board_point','$bpid','$land_mark','$source','$dest','$travels','$bus_type','$bdate','$jdate','$seats','$gender','$start_time','$arr_time','$paid','$save','$tkt_fare','$base_fare','$service_tax_amount','$discount_amount','$convenience_charge','$pname','$pemail','$pmobile','$age','$refno','$status','$pass','$seats','$ccharge','$camt','$refamt','$travel_id','$ip','$time','$cdate','$ctime','$id_type','$id_num','$padd','$alter_ph','$fid','$tid','$operator_agent_type','$agent_id','$book_pay_type','$book_pay_agent','$cgst','$sgst','$tcs')") or die(mysql_error());

                $sql3 = $this->db->query("select distinct available_seats from buses_list where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdt'") or die(mysql_error());
                foreach ($sql3->result() as $row3) {
                    $as1 = $row3->available_seats;
                }
                $as = $as1 + $pass;

                $sql4 = $this->db->query("update buses_list set available_seats='$as' where travel_id='$travel_id' and from_id='$fid' and to_id='$tid' and service_num='$service_no' and journey_date='$jdt'") or die(mysql_error());

                $ss1 = explode(',', $seats);
               
			   /*for ($k = 0; $k < $pass; $k++) {
                    $sea = $ss1[$k];

                    $sql5 = $this->db->query("update layout_list set seat_status='0',is_ladies='0' where journey_date='$jdate' and service_num='$service_no' and travel_id='$travel_id' and seat_name='$sea'") or die(mysql_error());
                }*/

					$ch = curl_init();
					//curl_setopt($ch, CURLOPT_URL, "http://ticketengine.in:8080/TestSpring_intl_live/updateCancelTicket/$travel_id/$tktno");
					curl_setopt($ch, CURLOPT_URL, "http://ticketengine.in:8080/TestSpring_intl_live/updateCancelTicket/$travel_id/$tktno");
					
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($ch, CURLOPT_GET, 1);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, "operatorId=$travel_id&tktno=$tktno");
					$buffer = curl_exec($ch);
					curl_close($ch);
				
				
                $sql3 = $this->db->query("insert into master_pass_reports(tktno,pnr,pass_name,source,destination,date,transtype,tkt_fare,comm,can_fare,refamt,net_amt,bal,dat,ip,agent_id,travel_id,status) values('$tkt_no','$pnr','$pname','$source','$dest','$ctime','Credit','$tkt_fare','$save','$camt','$refamt','$paid','$bal1','$cdate','$ip','$agent_id','$travel_id','cancelled')") or die(mysql_error());
                
                $text = "Your ticket has been successfully cancelled having ticket no " . $tkt_no . " - " . $travels;
				//$text = urlencode("Your ticket has been successfully cancelled having ticket no " . $tkt_no . " - " . $travels . "");
                //echo $text;
                
                $receipientno = $pmobile;
				//$sender_id = 'KSRBUS';
				$sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$travel_id'");
				foreach ($sql5->result() as $row5) {
					$sender_id = $row5->sender_id;
				}
				//msg91 SMS
				$this->Updations_m->msg91sms($receipientno,$text,$iso,$dialCode);
				
                //$msgtxt="this is test message , test";
				/*$ch = curl_init();
                $user = "pridhvi@msn.com:activa1525@";
                curl_setopt($ch, CURLOPT_URL, "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$sender_id&receipientno=$receipientno&msgtxt=$text");
                $buffer = curl_exec($ch);

                curl_close($ch);*/

                return $data;
            } else {
                $data['error'] = 0;
            }
        } else {
            $data = 1;
        }
        return $data;
    }

    public function from_cities() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select("*");
        $this->db->order_by("from_name", "asc");
        $this->db->where("travel_id", $travel_id);
        $this->db->where("status", 1);
        $query = $this->db->get("master_buses");
        $ftlist = array();
        $ftlist['0'] = '- - - Select - - -';
        foreach ($query->result() as $rows) {
            $ftlist[$rows->from_id] = $rows->from_name;
        }
        return $ftlist;
    }

    public function from_cities_tr() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $other_services = $this->session->userdata('bktravels_other_services');
        $this->db->select("*");
        $this->db->order_by("from_name", "asc");
        if ($other_services == 'no') {
            $this->db->where("travel_id", $travel_id);
        }
        $this->db->where("status", 1);
        $query = $this->db->get("master_buses");
        $ftlist = array();
        $ftlist['0'] = '- - - Select - - -';
        foreach ($query->result() as $rows) {
            $ftlist[$rows->from_name] = $rows->from_name;
        }
        return $ftlist;
    }

    public function to_citiesr() {
        $from_name = $this->input->post('from');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $other_services = $this->session->userdata('bktravels_other_services');
        if ($other_services == 'no') {
            $sql = $this->db->query("select distinct to_id,to_name from master_buses where from_name='$from_name'  and status='1' and travel_id='$travel_id' order by to_name ") or die(mysql_error());
        } else {
            $sql = $this->db->query("select distinct to_id,to_name from master_buses where from_name='$from_name'  and status='1' order by to_name ") or die(mysql_error());
        }
        echo '<select name="to" id="to">
		<option value="">- - - Select - - -</option>';
        foreach ($sql->result() as $res) {
            $to_id = $res->to_id;
            $to_name = $res->to_name;
            echo '<option value="' . $to_name . '">' . $to_name . '</option>';
        }
        echo '</select>';
    }

    public function tktStst($tktno) {
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $ho = $this->session->userdata('bktravels_head_office');

        if ($ho == 'yes') {
            $sql2 = $this->db->query("select * from master_booking where tkt_no = '$tktno'");
        } else {
            $sql2 = $this->db->query("select * from master_booking where tkt_no = '$tktno' and agent_id='$agent_id' ");
        }
        if ($sql2->num_rows() != '') {
            foreach ($sql2->result() as $res) {
                $bp = $res->board_point;
                $bpid = $res->bpid;
                $lm = $res->land_mark;
                $pname = $res->pname;
                $pmobile = $res->pmobile;
                $alter_ph = $res->alter_ph;
                $padd = $res->padd;
                $pemail = $res->pemail;
                $tkt_no = $res->tkt_no;
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
            }


            if ($ho == 'yes') {
                $sql21 = $this->db->query("select * from master_booking where tkt_no = '$tkt_no' and (status='cancelled' || status='service cancelled')");
            } else {
                $sql21 = $this->db->query("select * from master_booking where tkt_no = '$tkt_no' and (status='cancelled' || status='service cancelled') and agent_id='$agent_id'");
            }
            if ($sql21->num_rows() != '') {
                foreach ($sql21->result() as $res1) {
                    $st = $res1->status;
                    $ref = $res1->refamt;
                    $cdate = $res1->cdate;
                }
            }

            if ($sql21->num_rows() > 0) {
                $sts = $st;
            } else {
                $sts = 'confirmed';
            }
            echo '<br/>
                <div id="fareprint">
 <table  align="center" border="0" cellpadding="2" style="font-size:12px; border:#666666 solid 1px;" cellspacing="0" width="100%"  >
    <tbody>
    <tr>
    <td height="15" colspan="6" style="background-color:#999999; color:#FFFFFF">Ticket Information </td>
    </tr>
    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="3" align="left" height="25"><strong>&nbsp;Passenger Name : ' . $pname . ' </strong></td>
    <td colspan="3" align="left" height="25"><strong>&nbsp;Ticket No : ' . $tkt_no . ' </strong></td>
    </tr>
    <tr align="center" valign="top">
    <td colspan="6" height="25"><strong></strong></td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Travel Provider</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%"> ' . $res->travels . '</td>
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
    <td align="left" height="25" width="18%">&nbsp;Land Mark</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $lm . '</td>
    <td align="left" height="25" width="18%">&nbsp;Ticket Fare</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Status</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $sts . '</td>
    <td align="left" height="25" width="18%">&nbsp;Total Fare</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">Rs. ' . $tkt_fare . '</td>
    </tr>
    <tr valign="top">
    <td align="left" height="25" width="18%">&nbsp;Boarding Point</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $bp . '</td>
    <td align="left" height="25" width="18%">&nbsp;Service Number</td>
    <td align="center" height="25" width="3%">:</td>
    <td align="left" height="25" width="29%">' . $service_no . '</td>
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
</div>
<br/>
<table align="center" border="0" cellpadding="2" style="font-size:12px;" cellspacing="0" width="100%">
    <tbody>

    
    <tr align="center" valign="top">
      <td height="25" ><input value="Print" name="Submit" onClick="javascript:window.print(fareprint);" type="button" class="btn btn-primary" /></td>
    </tr>
    </tbody>
    </table>
<br/>
';

            if ($agent_type == '1') {


                echo '
 <table align="center" border="0" cellpadding="2" style="font-size:12px; border:#666666 solid 1px;" cellspacing="0" width="758">
   <tbody>
    
     <tr>
       <td height="273"><table align="center" border="0" cellpadding="2" style="font-size:12px;" cellspacing="0" width="459">
           <tr>
             <td width="199" height="21" style="padding-left:15px;padding-top:5px" >Boarding Point <font color="#FF0000">* </font></td>
             <td width="6" height="25">:</td>
             <td width="240" height="25"><select id="board" name="board"  style="width:155px">
			 <option value=""> - - - Select - - -</option>';

                $bpp = $this->db->query("select * from boarding_points where service_num='$service_no' and  board_or_drop_type='board' and travel_id='$travel_id'");
                foreach ($bpp->result() as $bp1) {
                    $board = $bp1->board_drop;
                    $board_id = $bp1->bpdp_id;
                    $bplm = explode('#', $board);
                    $onward_bp = $bplm[0] . '-' . (date('h:i A', strtotime($bplm[1])));
                    //echo $board_id.' == '.$bpid;
                    if ($board_id == $bpid) {
                        //echo "if";
                        echo '<option selected="selected" value="' . $board_id . '">' . $onward_bp . '</option>';
                    } else {
                        //echo "else";
                        echo '<option value="' . $board_id . '">' . $onward_bp . '</option>';
                    }
                }
                echo'
			 </select></td>
           </tr>
           <tr>
             <td height="21" style="padding-left:15px">Phone <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25"><input type="text" name="phone" id="phone" value="' . $pmobile . '" /><input type="hidden" name="service_num" id="service_num" value="' . $service_no . '"><input type="hidden" name="jdate" id="jdate" value="' . $jdate . '"><input type="hidden" name="tktno" id="tktno" value="' . $tktno . '"><input type="hidden" name="pass" id="pass" value="' . $pass . '"></td>
           </tr>
           <tr>
             <td height="21" style="padding-left:15px">Alternate Ph.no  <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25"><input type="text" name="altph" id="altph" value="' . $alter_ph . '" /></td>
           </tr>
           <tr>
             <td height="21" style="padding-left:15px">Address  <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25"><input type="text" name="add" id="add" value="' . $padd . '" /></td>
           </tr>
           <tr>
             <td height="21" style="padding-left:15px">Email  <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25"><input type="text" name="email" id="email" value="' . $pemail . '" /></td>
           </tr>
           <tr>
             <td height="21" style="padding-left:15px">Booked Type  <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25"><select id="booktype" name="booktype"  style="width:155px" onChange="btype()">
              ';

                $btype = $this->db->query("select * from agents_operator where id='$agent_id' and  operator_id='$travel_id'");
                foreach ($btype->result() as $btype1) {
                    $by_cash = $btype1->by_cash;
                    $by_phone = $btype1->by_phone;
                    $by_employee = $btype1->by_employee;

                    if ($by_cash == 'yes') {
                        if ($book_pay_type == 'bycash')
                            echo '<option selected="selected" value="bycash">By Cash</option>';
                        else
                            echo '<option  value="bycash">By Cash</option>';
                    }
                    /*if ($by_phone == 'yes') {
                        if ($book_pay_type == 'byphone')
                            echo '<option selected="selected" value="byphone">By Phone</option>';
                        else
                            echo '<option  value="byphone">By Phone</option>';
                    }*/
                }
                echo'
             </select></td>
           </tr>
		    <tr id"eval1" style="display:none;">
             <td height="21" style="padding-left:15px">Employee Name  <font color="#FF0000">* </font></td>
             <td height="25">:</td>
             <td height="25" id="eval"></td>
           </tr>
           <tr>
             <td height="25" colspan="3" style="padding-left:15px">Passenger name <input type="hidden" name="seats" id="seats" value="' . $seats . '" /><font color="#FF0000">* </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gender <font color="#FF0000">* </font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Age <font color="#FF0000">* </font> </td>
           </tr>';
                $pname1 = explode(',', $pname);
                $gender1 = explode(',', $gender);
                $age1 = explode(',', $age);
                for ($i = 0; $i < $pass; $i++) {
                    echo '<tr>
             <td height="25" colspan="3"><input type="text" name="pn' . $i . '" id="pn' . $i . '" value="' . $pname1[$i] . '" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="pgen' . $i . '" name="pgen' . $i . '"  style="width:70px">
                
                ';
                    if ($gender1[$i] == 'M') {
                        if ($gender1[$i] == 'M')
                            echo '<option selected="selected" value="M">Male</option>';
                        else
                            echo '<option  value="M">Male</option>';
                    }
                    else {
                        echo '<option  value="M">Male</option>';
                    }
                    if ($gender1[$i] == 'F') {
                        if ($gender1[$i] == 'F')
                            echo '<option selected="selected" value="F">Female</option>';
                        else
                            echo '<option  value="F">Female</option>';
                    }
                    else {
                        echo '<option  value="F">Female</option>';
                    }


                    echo'
             
               </select>           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    <input type="text" size="3" name="page' . $i . '" id="page' . $i . '" value="' . $age1[$i] . '" /></td>
             </tr>';
                }
                echo '<tr>
             <td height="25" colspan="3" align="center" style="padding-top:10px"><input type="button" class="btn btn-primary" name="modify" id="modify" value="Ticket Modify" onClick="modify()"></td>
           </tr>
       </table> ';
            }

            echo'</td>
     </tr>

    
   </tbody>
   ';


            echo '
 </table>
 <p>&nbsp;</p>';
        } else {
            $view = 0;
        }
    }

    public function modifyTicketDetailsDb() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $board_id = $this->input->post('board_id');
        $board = $this->input->post('board');
        $phone = $this->input->post('phone');
        $altph = $this->input->post('altph');
        $add = $this->input->post('add');
        $email = $this->input->post('email');
        $tktno = $this->input->post('tktno');
        $pass = $this->input->post('pass');
        $seats = $this->input->post('seats');
        $pname = $this->input->post('pname');
        $gender = $this->input->post('gender');
        $age = $this->input->post('age');
        $service_num = $this->input->post('service_num');
        $jdate = $this->input->post('jdate');

        $q1 = $this->db->query("SELECT * FROM `boarding_points` WHERE `service_num`='$service_num' and `travel_id`='$travel_id' AND bpdp_id='$board_id'");

        foreach ($q1->result() as $res) {
            $lm = $res->board_drop;
        }
        $lm1 = explode('#', $lm);
        $lm2 = $lm1[2];
        $sql = $this->db->query("update master_booking set board_point='$board',bpid='$board_id',land_mark='$lm2',pmobile='$phone',alter_ph='$altph',padd='$add',pemail='$email',pname='$pname',gender='$gender',age='$age' where tkt_no='$tktno'") or die(mysql_error());

        $seats1 = explode(',', $seats);
        $gender1 = explode(',', $gender);

        for ($k = 0; $k < count($seats1); $k++) {
            $sea = $seats1[$k];
            $pgen = $gender1[$k];

            if ($pgen == 'M') {
                $pgen1 = 0;
            } else {
                $pgen1 = 1;
            }

            $sql1 = $this->db->query("update layout_list set is_ladies='$pgen1' where journey_date='$jdate' and service_num='$service_num' and travel_id='$travel_id' and seat_name='$sea'") or die(mysql_error());
        }
        if ($sql) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function getServicesList() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select("*");
        $this->db->order_by("from_name", "asc");
        $this->db->where("travel_id", $travel_id);
        $this->db->where("status", 1);
        $query = $this->db->get("master_buses");
        $service = array();
        $service['0'] = '- - - Select - - -';
        $service['all'] = 'All';
        foreach ($query->result() as $rows) {
            $service[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $service;
    }

    public function discount2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $discount_type = $this->input->post('discount_type');
        $discount_for = $this->input->post('discount_for');
        $service_num = $this->input->post('service_num');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $discount = $this->input->post('discount');
        $dtype = $this->input->post('dtype');
        //echo "discount ".$discount." / dtype".$dtype;
        if ($discount_type == "permanent") {
            if ($discount_for == "all") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_no','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num'");
                    $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_num','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                }
            } else if ($discount_for == "web") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_no','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num'");
                    $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_num','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                }
            } else if ($discount_for == "api") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_no','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num'");
                    $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount,discount_type,discount_for) values ('$service_num','$travel_id','$discount','$dtype','$discount_for')") or die(mysql_error());
                }
            }
        } else { //temp
            if ($discount_for == "all") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and discount_date between '$from_date' and '$to_date'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $from_date1 = $from_date;
                        $to_date1 = $to_date;
                        while ($from_date1 <= $to_date1) {
                            $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_no','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());
                            $date1 = strtotime("+1 day", strtotime($from_date1));
                            $from_date1 = date("Y-m-d", $date1);
                        }
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num' and discount_date between '$from_date' and '$to_date'");
                    $from_date1 = $from_date;
                    $to_date1 = $to_date;
                    while ($from_date1 <= $to_date1) {
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_num','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());
                        $date1 = strtotime("+1 day", strtotime($from_date1));
                        $from_date1 = date("Y-m-d", $date1);
                    }
                }
            } else if ($discount_for == "web") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and discount_date between '$from_date' and '$to_date'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $from_date1 = $from_date;
                        $to_date1 = $to_date;
                        while ($from_date1 <= $to_date1) {
                            $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_no','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());
                            $date1 = strtotime("+1 day", strtotime($from_date1));
                            $from_date1 = date("Y-m-d", $date1);
                        }
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num' and discount_date between '$from_date' and '$to_date'");
                    $from_date1 = $from_date;
                    $to_date1 = $to_date;

                    while ($from_date1 <= $to_date1) {
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_num','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());

                        $date1 = strtotime("+1 day", strtotime($from_date1));
                        $from_date1 = date("Y-m-d", $date1);
                    }
                }
            } else if ($discount_for == "api") {
                if ($service_num == "all") {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and discount_date between '$from_date' and '$to_date'");
                    $sql = $this->db->query("select distinct service_num from master_buses where travel_id='$travel_id' and status='1'") or die(mysql_error());
                    foreach ($sql->result() as $res) {
                        $service_no = $res->service_num;
                        $from_date1 = $from_date;
                        $to_date1 = $to_date;

                        while ($from_date1 <= $to_date1) {
                            $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_no','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());

                            $date1 = strtotime("+1 day", strtotime($from_date1));
                            $from_date1 = date("Y-m-d", $date1);
                        }
                    }
                } else {
                    $this->db->query("delete from master_discount where travel_id='$travel_id' and service_num='$service_num' and discount_date between '$from_date' and '$to_date'");
                    $from_date1 = $from_date;
                    $to_date1 = $to_date;

                    while ($from_date1 <= $to_date1) {
                        $sql1 = $this->db->query("insert into master_discount(service_num,travel_id,discount_date,discount,discount_type,discount_for) values ('$service_num','$travel_id','$from_date1','$discount','$dtype','$discount_for')") or die(mysql_error());

                        $date1 = strtotime("+1 day", strtotime($from_date1));
                        $from_date1 = date("Y-m-d", $date1);
                    }
                }
            }
        }

        if ($sql1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function display_Source() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $sql = $this->db->query("select distinct from_name from master_buses where travel_id='$travel_id'");
        $city['0'] = "--select city--";
        $city['all'] = "All";
        foreach ($sql->result() as $value) {
            $city[$value->from_name] = $value->from_name;
        }
        return $city;
    }

    function get_bus_numbers() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $stmt = "select distinct bus_number from bus_numbers where travel_id='$travel_id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    function get_drivers() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $stmt = "SELECT DISTINCT driver_name FROM `drivers_details` where travel_id='$travel_id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    function getServices_From_db() {
        $city = $this->input->post('city');
        $newfDate1 = $this->input->post('date_from');
        $ex1 = explode("/", $newfDate1);
        $date_from = $ex1[0] . "-" . $ex1[1] . "-" . $ex1[2];
        $travid = $this->session->userdata('bktravels_travel_id');
        //for showing service no according to city and date                
        if ($city == "all") {
            $stmt = "select distinct service_no from master_booking where jdate='$date_from' and travel_id='$travid' and (status='confirmed' || status='Confirmed')";
        } else {
            $stmt = "select distinct service_no from master_booking where 
          source='$city' and jdate='$date_from' and travel_id='$travid' and (status='confirmed' || status='Confirmed')";
        }
        $query = $this->db->query($stmt);
        // print_r($query->result());
        if ($query->num_rows() > 0) {
            echo '                  
<table  align="center"   id="tbl" width="100%">
  <tr>
<td><input type="checkbox" id="selectck" name="selectck" onClick="selectAll()"></td>
<!--td width="108" height="21"><b >Locations</b></td-->
<td><b > Service No</b></td>
<td><b >Service Name</b></td>
<td><b >Bus Type</b></td>
<td><b >Bus No.</b></td>
<td><b >Contact Name</b></td>
<td><b >Direct Bus Mobile No</b></td>
<td><b >Pick Up Van Mobile No</b></td>
</tr>';
            $i = 1;
            foreach ($query->result() as $val) {
                $service_no = $val->service_no;
                $bus_num = $this->get_bus_numbers();
                $driver = $this->get_drivers();
                //print_r($query3->result());
                /* $this->db->select("*");
                  $this->db->from("boarding_points");
                  $this->db->where('service_num', $service_no);
                  $this->db->where('is_van', 'no');
                  $query2 = $this->db->get();
                  foreach ($query2->result() as $val1) {
                  $dbuscontact = $val1->contact;
                  $busno = $val1->bus_no;
                  $is_van1 = $val1->is_van;
                  $name = $val1->name;
                  }
                  $this->db->select("*");
                  $this->db->from("boarding_points");
                  $this->db->where('service_num', $service_no);
                  $this->db->where('is_van', 'yes');
                  $query4 = $this->db->get();

                  foreach ($query4->result() as $val4) {
                  $vancontact = $val4->contact;
                  $is_van2 = $val4->is_van;
                  $name = $val4->name;
                  } */
                /* $details = $this->db->query("SELECT * FROM `vehicle_details` WHERE service_number='$service_no' AND travel_id='$travid'");
                  foreach ($details->result() as $row) {
                  $bus_number = $row->bus_number;
                  $driver_name = $row->driver_name;
                  $driver_number = $row->driver_number;
                  } */

                $query2 = $this->db->query("select distinct source from master_booking where jdate='$date_from' and travel_id='$travid' and service_no='$service_no' and (status='confirmed' || status='Confirmed')");
                foreach ($query2->result() as $val2) {
                    $source = $val2->source;
                }
                $query3 = $this->db->query("select * from master_booking where source='$source' and travel_id='$travid' and jdate='$date_from' and service_no='$service_no' and (status='confirmed' || status='Confirmed')");


                foreach ($query3->result() as $value) {
                    $bustype = $value->bus_type;
                    $dest = $value->dest;
                }

                $query5 = $this->db->query("select distinct service_name from master_buses where service_num='$service_no' and travel_id='$travid'");

                foreach ($query5->result() as $value5) {
                    $service_name = $value5->service_name;
                }

                $query6 = $this->db->query("select distinct vehicleassigned from buses_list where service_num='$service_no' and journey_date='$date_from' and travel_id='$travid'");

                foreach ($query6->result() as $value6) {
                    $vehicleassigned = $value6->vehicleassigned;
                }

                //echo $vehicleassigned."<br />";

                $query7 = $this->db->query("select distinct vehicleassigned,service_num from buses_list where journey_date='$date_from' and travel_id='$travid'");
                //echo "select distinct vehicleassigned,service_num from buses_list where journey_date='$date_from' and travel_id='$travid'";
                foreach ($query7->result() as $value7) {
                    $vehicleassigned1 = $value7->vehicleassigned;
                    if ($vehicleassigned1 == "yes") {
                        $services1[] = $value7->service_num;
                    }
                }
                //echo $vehicleassigned."<br />";				
                $services = implode(',', $services1);
                //echo $services."<br />";
                if ($vehicleassigned == "no") {
                    //$class = ($i%2 == 0)? 'bg': 'bg1';
                    echo '<tr class="' . $class . '">
<td>
<input type="checkbox" id="chk' . $i . '" class="chkbox" name="chk' . $i . '" 
    value="' . $i . '" onClick="enabledit(this.value)">
<input type="hidden" id="hd' . $i . '" value="' . $is_van2 . '" />        
</td>
<!--td width="108" >' . $source . '</td-->
<td>' . $service_no . ' 
<input type="hidden" id="service_num' . $i . '" value="' . $service_no . '" />            
</td>
<td>' . $service_name . '</td>
    <td>' . $bustype . '</td>';
                    echo '<td><select id="bno' . $i . '" name="bno' . $i . '" class="form-control">
                        <option value="0">-- Select --</option>';
                    foreach ($bus_num as $bus_num) {

                        echo "<option value=" . $bus_num->bus_number . ">" . $bus_num->bus_number . "</option>";
                    }
                    echo '</select></td>';
                    echo '<td><select id="cn' . $i . '" name="cn' . $i . '" class="form-control" onChange="driver_number(' . $i . ')">
                        <option value="0">-- Select --</option>';
                    foreach ($driver as $driver) {

                        echo "<option value=" . $driver->driver_name . ">" . $driver->driver_name . "</option>";
                    }
                    echo '</select></td>';
                    echo '<td><input type="text" size="13" id="dm' . $i . '" class="tbox form-control" name="dm' . $i . '" maxlength="10" 
             value=""/></td>';
                    echo '<td>
<input type="text" size="13" id="vm' . $i . '" class="tbox1 form-control" name="vm' . $i . '" maxlength="10" value="' . $vancontact . '"
            disabled/></td></tr>';
                } /* else {
                  echo "Vehicle assigned services " . $services;
                  } */
                $i++;
            }

            echo '<tr><td colspan="9" align="center">
   <input type="submit" class="btn btn-primary" id="subb" name="subb" value="SendSMS" onClick="saveContact()"/></td></tr>  
</table>';
        } else {
            echo 0;
        }
    }

    function store_contact_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $services = $this->input->post('services');
        $busno = $this->input->post('busno');
        $cname = $this->input->post('cname');
        $newfDate1 = $this->input->post('date_from');
        $ex11 = explode("/", $newfDate1);
        $jdate = $ex11[0] . "-" . $ex11[1] . "-" . $ex11[2];
        $contact1 = $this->input->post('bcontacts');
        $contact2 = $this->input->post('vcontacts');
        $ex1 = explode("#", $services);
        $ex2 = explode("#", $contact1);
        $ex3 = explode("#", $contact2);
        $ex4 = explode("#", $busno);
        $name = explode("#", $cname);
        //updating the phone numbers and bus number  in boarding_points table
        for ($k = 0; $k < count($ex1); $k++) {
            if ($ex1[$k] != '') {
                //$array1=array('contact'=>$ex2[$k],'bus_no'=>$ex4[$k]); 
                $array2 = array('contact' => $ex3[$k], 'bus_no' => $ex4[$k]);
                $where = array('service_num' => $ex1[$k], 'is_van' => 'no', 'board_or_drop_type' => 'board');
                $where1 = array('service_num' => $ex1[$k], 'is_van' => 'yes', 'board_or_drop_type' => 'board');

                //sending SMS to the customers

                $query3 = $this->db->query("select * from master_booking where service_no='$ex1[$k]' and jdate='$jdate' and  (status='confirmed' or status='Confirmed')");
                //$this->getCustomer_Detail_From_db($ex1[$k],$ex2[$k],$ex3[$k],$jdate);  
                foreach ($query3->result() as $va) {
                    $tkt_no = $va->tkt_no;
                    //echo $tkt_no."<br/>".$ex1[$k];
                    $travels = $va->travels;
                    $pmobile = $va->pmobile;
                    $seatno = $va->seats;
                    $bus_type = $va->bus_type;
                    $pnr = $va->pnr;
                    $source = $va->source;
                    $dest = $va->dest;
                    $bpid = $va->bpid;
                    $travid = $va->travel_id;
                    $vehiclesms = $va->vehiclesms;
                    //echo $query3->num_rows() . "#" . $vehiclesms . "#" . $bpid;
                    $query4 = $this->db->query("select * from master_booking where service_no='$ex1[$k]' and tkt_no='$tkt_no' and (status='cancelled' || status='Cancelled')");
                    $query5 = $this->db->query("select * from  boarding_points where bpdp_id='$bpid' and service_num='$ex1[$k]' and travel_id='$travid'");
                    //echo "select * from  boarding_points where bpdp_id='$bpid' and service_num='$ex1[$k]' and travel_id='$travid'";
                    foreach ($query5->result() as $valu) {
                        $board_drop = $valu->board_drop;
                        $contact = $valu->contact;
                        $bus_no = $valu->bus_no;
                    }
                    //echo $contact."#".$bus_no;
                    $bp1 = explode('#', $board_drop);
                    $cno = $ex4[$k] . "-" . $bus_type;
//echo $bpid."#".$board_drop;                                     

                    if ($query4->num_rows() == 0) {
                        if ($ex2[$k] != '') {
                            $query1 = $this->db->query("update boarding_points set name='$name[$k]',contact='$ex2[$k]',bus_no='$ex4[$k]' where service_num='$ex1[$k]' and (is_van='no' or is_van is null) and board_or_drop_type='board'") or die(mysql_error());
                        }
                        if ($ex3[$k] != '') {
                            $query2 = $this->db->query("update boarding_points set name='$name[$k]',contact='$ex3[$k]',bus_no='$ex4[$k]' where service_num='$ex1[$k]' and is_van='yes' and board_or_drop_type='board'") or die(mysql_error());
                        }
                        $sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$travel_id'");
                        foreach ($sql5->result() as $row5) {
                            $senderID = $row5->sender_id;
                        }
                        $user = "pridhvi@msn.com:activa1525@";
                        $receipientno = $pmobile; //customer mobile number                        
                        if ($ex2[$k] != '') {
                            $text = "Journey: " . $source . "-" . $dest . " CoachNo: " . $cno . " Attendent: " . $name[$k] . " ContNo: " . $ex2[$k] . " RepTime: " . $bp1[1] . " ";
                        } else {
                            $text = "Journey: " . $source . "-" . $dest . " CoachNo: " . $cno . " Attendent: " . $name[$k] . " ContNo: " . $ex3[$k] . " RepTime:" . $bp1[1] . " ";
                        }
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
                            $this->db->query("update master_booking set vehiclesms='yes' where service_no='$ex1[$k]' and tkt_no='$tkt_no' and jdate='$jdate' and travel_id='$travel_id'");
                        } else {
                            $msg = "notsent";
                        }
                        curl_close($ch);
                    }
                }
                if ($ex2[$k] != '') {
                    $query1 = $this->db->query("update boarding_points set name='$name[$k]',contact='$ex2[$k]',bus_no='$ex4[$k]' where service_num='$ex1[$k]' and (is_van='no' or is_van is null) and board_or_drop_type='board'") or die(mysql_error());
                    $this->db->query("insert into vechile_assignment(service_no,bus_no,driver_name,mobile,journey_date,travel_id) values('$ex1[$k]','$ex4[$k]','$name[$k]','$ex2[$k]','$jdate','$travel_id')");
                }
                if ($ex3[$k] != '') {
                    $query2 = $this->db->query("update boarding_points set name='$name[$k]',contact='$ex3[$k]',bus_no='$ex4[$k]' where service_num='$ex1[$k]' and is_van='yes' and board_or_drop_type='board'") or die(mysql_error());
                    $this->db->query("insert into vechile_assignment(service_no,bus_no,driver_name,mobile,journey_date,travel_id) values('$ex1[$k]','$ex4[$k]','$name[$k]','$ex3[$k]','$jdate','$travel_id')");
                }
                $this->db->query("update buses_list set vehicleassigned='yes' where service_num='$ex1[$k]' and travel_id='$travel_id' and journey_date='$jdate'");
            }
        }
        if ($query1 || $query2)
            echo 1;
        else
            echo 0;
    }

    public function get_city_id($name) {
        $this->db->select("city_id");
        $this->db->where("city_name", $name);
        $q = $this->db->get("master_cities");
        foreach ($q->result() as $roww) {
            $id = $roww->city_id;
        }
        return $id;
    }

    public function onward_buses_count($source_id, $destination_id, $onward_date) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $gt = $this->db->query("select count(DISTINCT service_num) as ct from buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id' and status='1'") or die(mysql_error());
        foreach ($gt->result() as $gt1) {
            $ctt = $gt1->ct;
        }
        return $ctt;
    }

    public function return_buses_count_reschedule($source_id, $destination_id, $onward_date) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $gt = $this->db->query("select count( DISTINCT service_num) as ct from buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id'") or die(mysql_error());
        foreach ($gt->result() as $gt1) {
            $ctt = $gt1->ct;
        }
        return $ctt;
    }

    public function reschedule_busListView($source_id, $destination_id, $onward_date, $return_date, $source_name, $destination_name, $ct, $ct1) {
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $cdt = date('Y-m-d');
        $ndt = date("l, d M Y", strtotime($onward_date));
        $ndt1 = date("l, d M Y", strtotime($return_date));

        echo '
<style>
.td
{
	border-right:#CCCCCC solid 1px;
	border-bottom:#CCCCCC solid 1px;
	padding-left:5px;
	font-size:13px;
}	
</style>
</head>
    
<body>
<form name="frmseats" id="frmseats" action="' . base_url('welcome/seatDetails') . '" method="POST">
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
	<input type="hidden" name="ct" id="ct" value="' . $ct . '" />
	<input type="hidden" name="ct1" id="ct1" value="' . $ct1 . '" />
	';
        $sql = $this->db->query("SELECT count(*) as bct FROM buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id'");
        foreach ($sql->result() as $row) {
            $bct = $row->bct;
        }
        $sql1 = $this->db->query("SELECT count(*) as bct FROM buses_list where from_id='$destination_id' and to_id='$source_id' and journey_date='$return_date' and travel_id='$travel_id'");
        foreach ($sql1->result() as $row1) {
            $bct1 = $row1->bct;
        }
        if (($bct == '0' || $bct == '') && ($bct1 == 0 || $bct1 == "")) {
            echo'
	<table cellspacing="1" cellpadding="1" align="center">
  	  <tr>
    	<td>&nbsp;</td>
	    <td>&nbsp;</td>
    	<td>&nbsp;</td>
	  </tr>
	  <tr>
    	<td>&nbsp;</td>
	    <td>No Seats were found for the selected Date</td>
    	<td>&nbsp;</td>
	  </tr>
	  <tr>
    	<td>&nbsp;</td>
	    <td>&nbsp;</td>
    	<td>&nbsp;</td>
	  </tr>
	  <tr>
    	<td>&nbsp;</td>
	    <td>Our search results change on real time, Unfortunately we are unable to find   seat/s for the Route selected by you.</td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
    	<td>&nbsp;</td>
	    <td>&nbsp;</td>
    	<td>&nbsp;</td>
	  </tr>
	</table>
	';
        } else {
            $way = "O";
            echo'		
	</td>
  </tr>
  <tr>
    <td valign="top">				
	<table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top">
		<table width="490" border="0" cellspacing="0" cellpadding="0" align="center" style="font-size:12px; border:#CCCCCC solid 1px;margin-left:10px;">
          <tr>
            <td height="25" colspan="7" class="td">
			<strong>Onward Journey</strong></span> : ' . $source_name . ' to ' . $destination_name . ' on ' . $ndt . '
			</td>
            </tr>
          <tr>
            <td height="25" class="td"><strong>Service No</strong></td>
            <td height="25" class="td"><strong>Bus Type</strong></td>
            <td height="25" class="td"><strong>Start Time</strong></td>
            <td height="25" class="td"><strong>Arrival Time</strong></td>
            <!--td height="25" class="td"><strong>Fare(s)</strong></td-->
            <td height="25" class="td"><strong>Available Seats</strong></td>
            <td height="25" style=" border-bottom:#CCCCCC solid 1px;">&nbsp;</td>
          </tr>
          ';
            $buses = $this->db->query("select distinct t1.travel_id,t1.service_num,t1.status,t1.from_id,t1.to_id,t2.from_name,t2.to_name,t2.model,t2.bus_type,t2.seat_nos,t2.lowerdeck_nos,t2.upperdeck_nos,t1.available_seats,t1.lowerdeck_nos,t1.upperdeck_nos,t2.start_time,t2.journey_time,t2.arr_time,t1.seat_fare,t1.lberth_fare,t1.uberth_fare,t2.service_tax from buses_list t1,master_buses t2 where t1.service_num=t2.service_num and t1.from_id='$source_id' and t1.to_id='$destination_id' and t2.from_id='$source_id' and t2.to_id='$destination_id' and t1.journey_date='$onward_date' and t1.travel_id='$travel_id' and t2.travel_id='$travel_id'") or die(mysql_error());

            $busesct = $this->db->query("SELECT count(*) as ct FROM buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$onward_date' and travel_id='$travel_id'") or die(mysql_error());
            foreach ($busesct->result() as $ct1) {
                $cot1 = $ct1->ct;
            }
            $i = 1;
            foreach ($buses->result() as $busrow) {
                $status = $busrow->status;
                $bus_type = $busrow->bus_type;
                $start_time = date('h:i A', strtotime($busrow->start_time));
                $arr_time = trim($busrow->arr_time);
                $model = $busrow->model;
                $service_num = $busrow->service_num;
                $service_tax = $busrow->service_tax;
                $check = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$onward_date' and (available='$agent_type' and (available_type='$agent_id'))");
                $check2 = $check->num_rows();
                if ($check2 > 0) {
                    $avail = $this->db->query("select count(distinct seat_name) as avai from layout_list where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' and (seat_status='0' || seat_status='2' || seat_status is NULL) and ((available='$agent_type' or available='1') and (available_type='$agent_id' or available_type='all' or available_type='1'))")or die(mysql_error());
                    foreach ($avail->result() as $av) {
                        $seats = $av->avai;
                    }
                } //if
                else {
                    $avail = $this->db->query("select count(distinct seat_name) as avai from layout_list where journey_date='$onward_date' and service_num='$service_num' and travel_id='$travel_id' and (seat_status='0' || seat_status='2' || seat_status is NULL) and (available='0' || available='3')")or die(mysql_error());
                    foreach ($avail->result() as $av) {
                        $seats = $av->avai;
                    }
                }  //else
                if ($status == '1') {
                    if ($bus_type == "seater") {
                        $fare = $busrow->seat_fare;
                    } else if ($bus_type == "sleeper") {
                        $fare = $busrow->lberth_fare . "/" . $busrow->uberth_fare;
                    } else if ($bus_type == "seatersleeper") {
                        $fare = $busrow->seat_fare . "/" . $busrow->lberth_fare . "/" . $busrow->uberth_fare;
                    }
                    $way = "O";
                    $class = ($i % 2 == 0) ? 'disp2' : 'disp1';
                    echo'
          <tr class=" ' . $class . '" style="border-bottom:#999999 solid 1px;">
            <td height="35" valign="middle" class="td">' . $service_num . '</td>
            <td height="35" class="td">' . $busrow->model . '</td>
           <td class="td"><a class="dep" id="dephover" onMouseOver="boarding(\'' . $service_num . '\',' . $i . ',\'' . $travel_id . '\',\'' . $way . '\')" onMouseOut="hidedep()">' . $start_time . '</a><div id="dep1' . $i . '" class="dep1" style="display:none;"></td>
        <td class="td"><a class="arr" id="arrhover" onMouseOver="dropping(\'' . $service_num . '\',' . $i . ',\'' . $travel_id . '\',\'' . $way . '\')" onMouseOut="hidearr()">' . $arr_time . '</a><div id="arr1' . $i . '" class="arr1 " style="display:none;"></td>
            <!--td height="35" class="td">' . $fare . '</td-->
            <td height="35" class="td">' . $seats . '</td>
            <td height="35" class="td" style="border-bottom:#CCCCCC solid 1px;"><input type="button" class="btn btn-primary1" name="book" id="book" onClick="javascript:reschedule_layout(\'' . $service_num . '\',\'' . $source_id . '\',\'' . $destination_id . '\',\'' . $onward_date . '\',\'' . $return_date . '\',\'' . $fare . '\',\'' . $i . '\',\'' . $way . '\')" value="View" />            </td>
          </tr>
          <tr class="' . $class . '">
            <td colspan="7" style="border-bottom:#CCCCCC solid 1px;">
			<span class="lay" id="layO' . $i . '" style="display:none;">Please Wait</span>
			<span id="O' . $i . '"></span>
			</td>
          </tr>
          ';
                    $i++;
                }
            }
            echo'<input type="hidden" id="bus_count" value="' . $i . '">
        </table>
		</td>
        <td>&nbsp;</td>
        <td valign="top">
		<table width="490" border="0" cellspacing="0" cellpadding="0" align="center" style="font-size:12px; border:#CCCCCC solid 1px;margin-left:10px;">
          <tr>
            <td height="25" colspan="7" class="td">
			<strong>Reschedule Journey</strong></span> : ' . $source_name . ' to ' . $destination_name . ' on ' . $ndt1 . '
			</td>
            </tr>
          <tr>
            <td height="25" class="td"><strong>Service No</strong></td>
            <td height="25" class="td"><strong>Bus Type</strong></td>
            <td height="25" class="td"><strong>Start Time</strong></td>
            <td height="25" class="td"><strong>Arrival Time</strong></td>
            <!--td height="25" class="td"><strong>Fare(s)</strong></td-->
            <td height="25" class="td"><strong>Available Seats</strong></td>
            <td height="25" style=" border-bottom:#CCCCCC solid 1px;">&nbsp;</td>
          </tr>
          ';
            $buses = $this->db->query("select distinct t1.travel_id,t1.service_num,t1.status,t1.from_id,t1.to_id,t2.from_name,t2.to_name,t2.model,t2.bus_type,t2.seat_nos,t2.lowerdeck_nos,t2.upperdeck_nos,t1.available_seats,t1.lowerdeck_nos,t1.upperdeck_nos,t2.start_time,t2.journey_time,t2.arr_time,t1.seat_fare,t1.lberth_fare,t1.uberth_fare,t2.service_tax from buses_list t1,master_buses t2 where t1.service_num=t2.service_num and t1.from_id='$source_id' and t1.to_id='$destination_id' and t2.from_id='$source_id' and t2.to_id='$destination_id' and t1.journey_date='$return_date' and t1.travel_id='$travel_id' and t2.travel_id='$travel_id'") or die(mysql_error());

            $busesct = $this->db->query("SELECT count(*) as ct FROM buses_list where from_id='$source_id' and to_id='$destination_id' and journey_date='$return_date' and travel_id='$travel_id'") or die(mysql_error());
            foreach ($busesct->result() as $ct1) {
                $cot1 = $ct1->ct;
            }
            $j = 1;
            foreach ($buses->result() as $busrow) {
                $status = $busrow->status;
                $bus_type = $busrow->bus_type;
                $start_time = date('h:i A', strtotime($busrow->start_time));
                $arr_time = trim($busrow->arr_time);
                $model = $busrow->model;
                $service_num = $busrow->service_num;
                $service_tax = $busrow->service_tax;
                $check = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$return_date' and (available='$agent_type' and (available_type='$agent_id' or available_type='all'))");
                $check2 = $check->num_rows();
                if ($check2 > 0) {
                    $avail = $this->db->query("select count(distinct seat_name) as avai from layout_list where journey_date='$return_date' and service_num='$service_num' and travel_id='$travel_id' and (seat_status='0' || seat_status='2') and ((available='$agent_type' or available='1') and (available_type='$agent_id' or available_type='all' or available_type='1'))")or die(mysql_error());
                    foreach ($avail->result() as $av) {
                        $seats = $av->avai;
                    }
                } //if
                else {
                    $avail = $this->db->query("select count(distinct seat_name) as avai from layout_list where journey_date='$return_date' and service_num='$service_num' and travel_id='$travel_id' and (seat_status='0' || seat_status='2' || seat_status is NULL) and (available='0' || available='3')")or die(mysql_error());
                    foreach ($avail->result() as $av) {
                        $seats = $av->avai;
                    }
                }  //else
                if ($status == '1') {
                    if ($bus_type == "seater") {
                        $fare = $busrow->seat_fare;
                    } else if ($bus_type == "sleeper") {
                        $fare = $busrow->lberth_fare . "/" . $busrow->uberth_fare;
                    } else if ($bus_type == "seatersleeper") {
                        $fare = $busrow->seat_fare . "/" . $busrow->lberth_fare . "/" . $busrow->uberth_fare;
                    }
                    $way = "R";
                    $class = ($j % 2 == 0) ? 'disp2' : 'disp1';
                    echo'
          <tr class=" ' . $class . '" style="border-bottom:#999999 solid 1px;">
            <td height="35" valign="middle" class="td">' . $service_num . '</td>
            <td height="35" class="td">' . $busrow->model . '</td>
            <td class="td"><a class="dep" id="dephover" onMouseOver="boarding(\'' . $service_num . '\',' . $j . ',\'' . $travel_id . '\',\'' . $way . '\')" onMouseOut="hidedep()">' . $start_time . '</a><div id="dep1' . $j . '" class="dep1" style="display:none;"></td>
        <td class="td"><a class="arr" id="arrhover" onMouseOver="dropping(\'' . $service_num . '\',' . $j . ',\'' . $travel_id . '\',\'' . $way . '\')" onMouseOut="hidearr()">' . $arr_time . '</a><div id="arr1' . $j . '" class="arr1 " style="display:none;"></td>
            <!--td height="35" class="td">' . $fare . '</td-->
            <td height="35" class="td">' . $seats . '</td>
            <td height="35" class="td" style="border-bottom:#CCCCCC solid 1px;"><input type="button" class="btn btn-primary1" name="book" id="book" onClick="javascript:reschedule_layout(\'' . $service_num . '\',\'' . $source_id . '\',\'' . $destination_id . '\',\'' . $onward_date . '\',\'' . $return_date . '\',\'' . $fare . '\',\'' . $j . '\',\'' . $way . '\')" value="View" />            </td>
          </tr>
          <tr class="' . $class . '">
            <td colspan="7" style="border-bottom:#CCCCCC solid 1px;">
			<span class="lay" id="layR' . $j . '" style="display:none;">Please Wait</span>
			<span id="R' . $j . '"></span>
			</td>
          </tr>
          ';
                    $j++;
                }
            }
            echo'<input type="hidden" id="bus_count" value="' . $j . '">
        </table>
		</td>
      </tr>
    </table>
	';
        }
        echo'
	</td>	
  </tr>
</table>
&nbsp;
<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td><input name="ret" type="button" class="btn btn-primary" value="Reschedule Ticket" onClick="validate()" style="background-color:#e82227; color:#fff; font-weight:bold; padding:10px; border:none" /></td>
  </tr>
</table>
</form>
';
    }

    public function get_city_name($id) {
        $this->db->select("city_name");
        $this->db->where("city_id", $id);
        $q = $this->db->get("master_cities");

        foreach ($q->result() as $roww) {
            $id = $roww->city_name;
        }
        return $id;
    }

    public function reschedule_serLayout($service_num, $source_id1, $destination_id1, $onward_date, $return_date, $fare1, $j, $way) {

        $travel_id = $this->session->userdata('bktravels_travel_id');
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $sqlpay = $this->db->query("select * from agents_operator where id='$agent_id'") or die(mysql_error());
        foreach ($sqlpay->result() as $pay) {
            $is_hover = $pay->is_hover;
            $is_pay = $pay->is_pay;
        }
        $source_name = $this->Updations_m->get_city_name($source_id1);
        $destination_name = $this->Updations_m->get_city_name($destination_id1);

        if ($way == "O") {
            $ndt = date("l, d M Y", strtotime($onward_date));
            $way1 = $source_name . ' to ' . $destination_name . ' on ' . $ndt;
            $source_id = $source_id1;
            $destination_id = $destination_id1;
            $date = $onward_date;
        }
        if ($way == "R") {
            $ndt = date("l, d M Y", strtotime($return_date));
            $way1 = $source_name . ' to ' . $destination_name . ' on ' . $ndt;
            $source_id = $source_id1;
            $destination_id = $destination_id1;
            $date = $return_date;
        }

        $buses = $this->db->query("select distinct t1.travel_id,t1.service_num,t1.status,t1.from_id,t1.to_id,t2.from_name,t2.to_name,t2.model,t2.bus_type,t2.seat_nos,t2.lowerdeck_nos,t2.upperdeck_nos,t1.available_seats,t1.lowerdeck_nos,t1.upperdeck_nos,t2.start_time,t2.journey_time,t2.arr_time,t1.seat_fare,t1.lberth_fare,t1.uberth_fare,t2.service_tax from buses_list t1,master_buses t2 where t1.service_num='$service_num' and t2.service_num='$service_num' and t1.from_id='$source_id' and t1.to_id='$destination_id' and t2.from_id='$source_id' and t2.to_id='$destination_id' and t1.journey_date='$date' and t1.travel_id='$travel_id' and t2.travel_id='$travel_id'") or die(mysql_error());
        foreach ($buses->result() as $busrow) {
            $bus_type = $busrow->bus_type;
            $start_time = $busrow->start_time;
            $arr_time = trim($busrow->arr_time);
            $model = $busrow->model;
            $source_name = $busrow->from_name;
            $destination_name = $busrow->to_name;
            $travel_id = $busrow->travel_id;
            $service_tax = $busrow->service_tax;
        }
        echo '       
<style>
body
{
	font-size:13px;
	calibri;
}
.tdborder
{
	border:#dcdcdc solid 1px;
	padding-left:2px;
	color:#000000;
}
.td1
{
	padding-left:10px;	
}
.td2
{
	height:20px;
}	

/* tooltip1 */
a.tooltip1 {outline:none; }
a.tooltip1 strong {line-height:20px;}
a.tooltip1:hover {text-decoration:none; cursor:pointer} 
a.tooltip1 span {
    z-index:10;display:none; padding:18px 20px;
    margin-top:-200px; margin-left:-160px;
     line-height:16px;
}
a.tooltip1:hover span{
    display:inline; position:absolute; 
    border:1px solid #000;  color:#000; border-radius:0px;
    background:#fff; cursor:pointer
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
</head>

<body>
<table border="0" cellspacing="1" cellpadding="1" align="center" style="margin-top:10px; margin-bottom:10px">
  <tr>
    <td width="521">&nbsp;</td>
  </tr>
  <tr>
    <td>
	<table border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td width="521" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td valign="top" style="padding-left:10px; font-size:12px;">' . $way1 . '
                  <div id="onward" style="color:#FF0000;"></div></td>
            </tr>
            <tr>
              <td valign="top">';

        if ($way == "O") {
            $journey = "onward_";
        } else if ($way == "R") {
            $journey = "return_";
        }

        /*         * **************** Seater Logic**************** */
        $sql = $this->db->query("select distinct layout_id from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date'") or die(mysql_error());
        foreach ($sql->result() as $res) {
            $layout_id = explode('#', $res->layout_id);
        }
        $layout_type = $layout_id[1];
        //textbox increment value
        $rt = 1;
        if ($layout_type == 'seater') {

            $fare = $fare1;
            $sql1 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date'") or die(mysql_error());
            foreach ($sql1->result() as $ress) {
                $mrow = $ress->mrow;
                $mcol = $ress->mcol;
            }
            echo '
                <table border="0" cellspacing="2" cellpadding="2" style="padding:5px 5px 5px 5px;border:#c1d0e3 solid 1px;">
                  ';
            for ($i = 1; $i <= $mcol; $i++) {
                echo '
                <tr>';
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql2 = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and row='$j' and col='$i'") or die(mysql_error());

                    foreach ($sql2->result() as $resss) {
                        $seat_name = $resss->seat_name;
                        $seat_status = $resss->seat_status;
                        $available = $resss->available;
                        $available_type = $resss->available_type;
                        $blocked_time = $resss->blocked_time;
                        $num = $seat_name;
                        $is_ladies = $resss->is_ladies;
                        $ser_num = $resss->service_num;
                        $jdate = $resss->journey_date;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo '<td height="20">&nbsp;</td>';
                    } else if ($seat_status == 0) {
                        echo '<td id=' . $seat_name . '' . $way . ' width="20" height="20" class="available">' . $num . '';
                        if ($way == "R") {
                            echo'<input type="text" id="rt' . $rt . '" name="rt' . $rt . '" size="1">
					     		 <input type="hidden" id="rsn' . $rt . '" name="rsn' . $rt . '" value="' . $num . '">';
                        }
                        echo'</td>';
                    } else if ($seat_status == 1 && $is_ladies == 1) {
                        echo '<td width="20" height="20" class="female"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else if ($seat_status == 1) {
                        echo '<td width="20" height="20" class="booked"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else {
                        echo '<td width="20" height="20" class="booked">' . $num . '</td>';
                    }
                    $rt++;
                }//inner for loop
                unset($seat_name);
                echo '</tr>';
            }//for looop
            echo '<input type="hidden" id="seaterrt" name="seaterrt" value="' . $rt . '">
                </table>';
        }//seater close

        /*         * **************** Sleeper ************** */ else if ($layout_type == 'sleeper') {
            $fa = explode('/', $fare1);
            $fare = $fa[0];
            $fare0 = $fa[1];

            /*             * ************** Lower Deck ************ */
            $sql12 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and seat_type='L'") or die(mysql_error());
            foreach ($sql12->result() as $ress) {
                $mrow = $ress->mrow;
                $mcol = $ress->mcol;
            }
            //checking does agent have any quota or not
            $check = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and (available='$agent_type' and (available_type='$agent_id' ))");
            $check2 = $check->num_rows();

            echo '
                <table border="0" cellspacing="2" cellpadding="2" style="padding:5px 5px 5px 5px;border:#c1d0e3 solid 1px;">
                  ';
            echo '
                    <tr>
                      <td>Lower</td>
                    </tr>
                  ';

            for ($i = 1; $i <= $mcol; $i++) {
                echo '
                  <tr>';
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql3 = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and row='$j' and col='$i' and seat_type='L'") or die(mysql_error());
                    foreach ($sql3->result() as $result) {
                        $seat_name = $result->seat_name;
                        $seat_status = $result->seat_status;
                        $available = $result->available;
                        $available_type = $result->available_type;
                        $blocked_time = $result->blocked_time;
                        $num = $seat_name;
                        $is_ladies = $result->is_ladies;
                        $ser_num = $result->service_num;
                        $jdate = $result->journey_date;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo '<td height="20">&nbsp;</td>';
                    } else if ($seat_status == 0) {
                        echo '<td id=' . $seat_name . '' . $way . ' width="20" height="20" class="available">' . $num . '';
                        if ($way == "R") {
                            echo'<input type="text" id="rt' . $rt . '" name="rt' . $rt . '" size="1">
					     		 <input type="hidden" id="rsn' . $rt . '" name="rsn' . $rt . '" value="' . $num . '">';
                        }
                        echo'</td>';
                    } else if ($seat_status == 1 && $is_ladies == 1) {
                        echo '<td width="20" height="20" class="female"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else if ($seat_status == 1) {
                        echo '<td width="20" height="20" class="booked"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else {
                        echo '<td width="20" height="20" class="booked">' . $num . '</td>';
                    }
                    $rt++;
                }//inner for loop
                unset($seat_name);
                echo '</tr>';
            }//for loop
            echo '</table><br />';

            /*             * **************** Upper Deck *************** */
            $sql1 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and seat_type='U'") or die(mysql_error());
            foreach ($sql1->result() as $ress) {
                $mrow = $ress->mrow;
                $mcol = $ress->mcol;
            }
            $rt = $rt;
            //checking does agent have any quota or not
            $check = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and (available='$agent_type' and (available_type='$agent_id'))");
            $check2 = $check->num_rows();

            echo '
                <table border="0" cellspacing="2" cellpadding="2" style="padding:5px 5px 5px 5px;border:#c1d0e3 solid 1px;">
                  ';
            echo '
                    <tr>
                      <td>Upper</td>
                    </tr>
                  ';
            for ($i = 1; $i <= $mcol; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql2 = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and row='$j' and col='$i' and seat_type='U'") or die(mysql_error());
                    foreach ($sql2->result() as $resss) {
                        $seat_name = $resss->seat_name;
                        $seat_status = $resss->seat_status;
                        $available = $resss->available;
                        $available_type = $resss->available_type;
                        $blocked_time = $resss->blocked_time;
                        $num = $seat_name;
                        $is_ladies = $resss->is_ladies;
                        $ser_num = $resss->service_num;
                        $jdate = $resss->journey_date;
                    }

                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo '<td height="20">&nbsp;</td>';
                    } else if ($seat_status == 0) {
                        echo '<td id=' . $seat_name . '' . $way . ' width="20" height="20" class="available">' . $num . '';
                        if ($way == "R") {
                            echo'<input type="text" id="rt' . $rt . '" name="rt' . $rt . '" size="1">
					     		 <input type="hidden" id="rsn' . $rt . '" name="rsn' . $rt . '" value="' . $num . '">';
                        }
                        echo'</td>';
                    } else if ($seat_status == 1 && $is_ladies == 1) {
                        echo '<td width="20" height="20" class="female"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else if ($seat_status == 1) {
                        echo '<td width="20" height="20" class="booked"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else {
                        echo '<td width="20" height="20" class="booked">' . $num . '</td>';
                    }
                    $rt++;
                }//inner for loop
                unset($seat_name);
                echo '</tr>';
            }//for loop
            echo '</table><input type="hidden" id="sleeper_rt" name="sleeper_rt" value="' . $rt . '">';
        }//sleeper closed...

        /*         * ************** SeaterSleeper ************************************* */ else if ($layout_type == "seatersleeper") {
            $fa = explode('/', $fare1);
            $faree = $fa[0];
            $fare0 = $fa[1];
            $fare1 = $fa[2];

            /*             * ************* Lower Deck ************ */
            $sql1 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and (seat_type='L:b' OR seat_type='L:s')") or die(mysql_error());
            foreach ($sql1->result() as $ress) {
                $mrow = $ress->mrow;
                $mcol = $ress->mcol;
            }
            echo '<table border="0" cellspacing="2" cellpadding="2" style="padding:5px 5px 5px 5px;border:#c1d0e3 solid 1px;">                 ';
            echo '  <tr>
                      <td>Lower</td>
                    </tr>
                  ';

            for ($i = 1; $i <= $mcol; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql3 = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and row='$j' and col='$i' and (seat_type='L:b' OR seat_type='L:s')") or die(mysql_error());
                    foreach ($sql3->result() as $result) {
                        $seat_name = $result->seat_name;
                        $seat_type = $result->seat_type;
                        $seat_status = $result->seat_status;
                        $available = $result->available;
                        $available_type = $result->available_type;
                        $blocked_time = $result->blocked_time;
                        $num = $seat_name;
                        $is_ladies = $result->is_ladies;
                    }
                    //checking the fare for seat or berth                       
                    if ($seat_type == "L:s") {
                        $fare = $faree;
                    } else if ($seat_type == "L:b") {
                        $fare = $fare0;
                    }

                    $ser_num = $resss->service_num;
                    $jdate = $resss->journey_date;

                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo '<td height="20">&nbsp;</td>';
                    } else if ($seat_status == 0) {
                        echo '<td id=' . $seat_name . '' . $way . ' width="20" height="20" class="available">' . $num . '';
                        if ($way == "R") {
                            echo'<input type="text" id="rt' . $rt . '" name="rt' . $rt . '" size="1">
					     		 <input type="hidden" id="rsn' . $rt . '" name="rsn' . $rt . '" value="' . $num . '">';
                        }
                        echo'</td>';
                    } else if ($seat_status == 1 && $is_ladies == 1) {
                        echo '<td width="20" height="20" class="female"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else if ($seat_status == 1) {
                        echo '<td width="20" height="20" class="booked"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else {
                        echo '<td width="20" height="20" class="booked">' . $num . '</td>';
                    }
                    $rt++;
                }//inner for loop
                unset($seat_name);
                echo '</tr>';
            }//for loop
            echo '</table><br />';

            /*             * ************************Upper Deck **************** */
            $sql1 = $this->db->query("select max(row) as mrow,max(col) as mcol from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and (seat_type='U' OR seat_type='U')") or die(mysql_error());
            foreach ($sql1->result() as $ress) {
                $mrow = $ress->mrow;
                $mcol = $ress->mcol;
            }
            $rt = $rt;
            echo '<table border="0" cellspacing="2" cellpadding="2" style="padding:5px 5px 5px 5px;border:#c1d0e3 solid 1px;">
                   ';
            echo '
                    <tr>
                      <td>Upper</td>
                    </tr>
                  ';

            for ($i = 1; $i <= $mcol; $i++) {
                echo '<tr>';
                for ($j = 1; $j <= $mrow; $j++) {
                    $sql2 = $this->db->query("select * from layout_list where service_num='$service_num' and travel_id='$travel_id' and journey_date='$date' and row='$j' and col='$i' and (seat_type='U' OR seat_type='U')") or die(mysql_error());
                    foreach ($sql2->result() as $resss) {
                        $seat_name = $resss->seat_name;
                        $seat_status = $resss->seat_status;
                        $available = $resss->available;
                        $available_type = $resss->available_type;
                        $blocked_time = $resss->blocked_time;
                        $num = $seat_name;
                        $is_ladies = $resss->is_ladies;
                        $ser_num = $resss->service_num;
                        $jdate = $resss->journey_date;
                    }
                    if ($seat_name == '' || $seat_name == 'GY') {
                        echo '<td height="20">&nbsp;</td>';
                    } else if ($seat_status == 0) {
                        echo '<td id=' . $seat_name . '' . $way . ' width="20" height="20" class="available">' . $num . '';
                        if ($way == "R") {
                            echo'<input type="text" id="rt' . $rt . '" name="rt' . $rt . '" size="1">
					     		 <input type="hidden" id="rsn' . $rt . '" name="rsn' . $rt . '" value="' . $num . '">';
                        }
                        echo'</td>';
                    } else if ($seat_status == 1 && $is_ladies == 1) {
                        echo '<td width="20" height="20" class="female"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else if ($seat_status == 1) {
                        echo '<td width="20" height="20" class="booked"><a class="tooltip1" onMouseOver="showpass(\'' . $seat_name . '\',\'' . $service_num . '\',\'' . $date . '\',\'' . $is_hover . '\',\'' . $seat_status . '\',\'' . $way . '\')">' . $num . '<span id="' . $way . '' . $seat_name . '"></span></a></td>';
                    } else {
                        echo '<td width="20" height="20" class="booked">' . $num . '</td>';
                    }
                    $rt++;
                }//inner for loop
                unset($seat_name);
                echo '</tr>';
            }//for loop
            echo '</table><input type="hidden" id="sleeper_seater_rt" name="sleeper_seater_rt" value="' . $rt . '">';
        }
        echo '</td>
            </tr>
        </table></td>        
      </tr>
      <tr>
        <td><table border="0" cellspacing="1" cellpadding="1" style="margin-left:10px; font-size:10px;">
            <tr>
              <td colspan="15" height="10"></td>
            </tr>
            <tr>
              <td><div style="height:12px; width:12px; background-color:#FF00FF;"></div></td>
              <td>&nbsp;</td>
              <td><strong>:</strong> Ladies </td>
              <td>&nbsp;</td>
              <td><div style="height:12px; width:12px;background-color:#006600;"></div></td>
              <td>&nbsp;</td>
              <td><strong>:</strong>Available </td>
              <td>&nbsp;</td>
              <td><div style="height:12px; width:12px;background-color:#6262FF;"></div></td>
              <td>&nbsp;</td>
              <td><strong>:</strong>Selected </td>
              <td>&nbsp;</td>
              <td><div style="height:12px; width:12px;background-color:#F4353A;"></div></td>
              <td>&nbsp;</td>
              <td><strong>:</strong> Booked </td>
            </tr>
            <tr>
              <td colspan="15" height="10"></td>
            </tr>
        </table></td>
      </tr>                                               
      <tr>
        <td class="td1"><input type="hidden" name="' . $journey . 'pass" id="' . $journey . 'pass" size="7" value="" />
            <input type="hidden" name="' . $journey . 'start_time" id="' . $journey . 'start_time" value="' . $start_time . '" />
            <input type="hidden" name="' . $journey . 'arr_time" id="' . $journey . 'arr_time" value="' . $arr_time . '" />
            <input type="hidden" name="' . $journey . 'source_id" id="' . $journey . 'source_id" value="' . $source_id . '" />
            <input type="hidden" name="' . $journey . 'destination_id" id="' . $journey . 'destination_id" value="' . $destination_id . '" />
            <input type="hidden" name="' . $journey . 'source_name" id="' . $journey . 'source_name" value="' . $source_name . '" />
            <input type="hidden" name="' . $journey . 'destination_name" id="' . $journey . 'destination_name" value="' . $destination_name . '" />
            <input type="hidden" name="' . $journey . 'date" id="' . $journey . 'date" value="' . $date . '" />
            <input type="hidden" name="' . $journey . 'service_num" id="' . $journey . 'service_num" value="' . $service_num . '" />
            <input type="hidden" name="' . $journey . 'bus_type" id="' . $journey . 'bus_type" value="' . $bus_type . '" />
            <input type="hidden" name="' . $journey . 'model" id="' . $journey . 'model" value="' . $model . '" />
            <input type="hidden" name="trip" id="trip" value="R" />
            <input type="hidden" name="' . $journey . 'way" id="' . $journey . 'way" value="' . $way . '" />
            <input type="hidden" name="' . $journey . 'travel_id" id="' . $journey . 'travel_id" value="' . $travel_id . '" /></td>        
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
';
    }

    public function reschedule_ticket_db() {
        $onward_service_num = $this->input->post('onward_service_num');
        $return_service_num = $this->input->post('return_service_num');
        $return_start_time = $this->input->post('return_start_time');
        $return_arr_time = $this->input->post('return_arr_time');
        $return_bus_type = $this->input->post('return_bus_type');
        $return_model = $this->input->post('return_model');
        $onward_date = $this->input->post('onward_date');
        $return_date = $this->input->post('return_date');
        $travel_id = $this->input->post('travel_id');
        $rt1 = $this->input->post('rt1');
        $rsn1 = $this->input->post('rsn1');

        $rt2 = explode(',', $rt1);
        $rsn2 = explode(',', $rsn1);

        if (count($rt2) == 1) {
            $sql = $this->db->query("select * from master_booking where service_no='$onward_service_num' and jdate='$onward_date' and travel_id='$travel_id' and seats = '$rt2[0]'") or die(mysql_error());
        } else {
            $sql = $this->db->query("select * from master_booking where service_no='$onward_service_num' and jdate='$onward_date' and travel_id='$travel_id' and seats like '%$rt2[0]%'") or die(mysql_error());
        }
        foreach ($sql->result() as $row) {
            $status = $row->status;
            $pass = $row->pass;
        }
		//echo $status."   before";
        if ($status != "cancelled") {
            if ($pass == count($rt2)) {
                foreach ($sql->result() as $row) {
                    $tkt_no = $row->tkt_no;
                    $pnr = $row->pnr;
                    $service_no = $row->service_no;
                    $board_point = $row->board_point;
                    $bpid = $row->bpid;
                    $land_mark = $row->land_mark;
                    $dpid = $row->dpid;
                    $drop_point = $row->drop_point;
                    $source = $row->source;
                    $travels = $row->travels;
                    $bus_type = $row->bus_type;
                    $bdate = $row->bdate;
                    $jdate = $row->jdate;
                    $seats = $row->seats;
                    $gender = $row->gender;
                    $start_time = $row->start_time;
                    $arr_time = $row->arr_time;
                    $paid = $row->paid;
                    $save = $row->save;
                    $tkt_fare = $row->tkt_fare;
                    $promo_code = $row->promo_code;
                    $pname = $row->pname;
                    $pemail = $row->pemail;
                    $pmobile = $row->pmobile;
                    $age = $row->age;
                    $refno = $row->refno;
                    $status = $row->status;
                    $cseat = $row->cseat;
                    $ccharge = 0;
                    $camt = 0;
                    $refamt = $row->refamt;
                    $mail_stat = $row->mail_stat;
                    $sms_stat = $row->sms_stat;
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $time = date('Y-m-d H:i:s');
                    $cdate = date('Y-m-d');
                    $ctime = "";
                    $id_type = $row->id_type;
                    $id_num = $row->id_num;
                    $padd = $row->padd;
                    $alter_ph = $row->alter_ph;
                    $fid = $row->fid;
                    $tid = $row->tid;
                    $operator_agent_type = $row->operator_agent_type;
                    $agent_id = $row->agent_id;
                    $is_buscancel = $row->is_buscancel;
                    $pay = $row->pay;
                    $pay_type = $row->pay_type;
                    $authid = $row->authid;
                    $txn_refno = $row->txn_refno;
                    $issuer_refno = $row->issuer_refno;
                    $payment_refno = $row->payment_refno;
                    $bus_model = $row->bus_model;
                    $is_refunded = $row->is_refunded;
                    $book_pay_type = $row->book_pay_type;
                    $book_pay_agent = $row->book_pay_agent;
                    $update_status = $row->update_status;
                    $collection_status = $row->collection_status;
                    $dest = $row->dest;
                }
				
				
				
                //cancellation updation full refund
                $sql2 = $this->db->query("insert into master_booking (tkt_no, pnr, service_no, board_point, bpid, land_mark, dpid, drop_point, source, dest, travels,
				bus_type, bdate, jdate, seats, gender, start_time, arr_time, paid, save, tkt_fare, promo_code, pname, pemail, pmobile, age, refno, status,
				pass, cseat, ccharge, camt, refamt, travel_id, mail_stat, sms_stat, ip, time, cdate, ctime, id_type, id_num, padd, alter_ph, fid, tid,
				operator_agent_type, agent_id, is_buscancel, pay, pay_type, authid, txn_refno, issuer_refno, payment_refno, bus_model, is_refunded,
				book_pay_type, book_pay_agent, update_status, collection_status)
				values('$tkt_no', '$pnr', '$service_no', '$board_point', '$bpid', '$land_mark', '$dpid', '$drop_point', '$source', '$dest', '$travels',
				'$bus_type', '$bdate', '$jdate', '$seats', '$gender', '$start_time', '$arr_time', '$paid', '$save', '$tkt_fare', '$promo_code', '$pname',
				'$pemail', '$pmobile', '$age', '$refno', 'cancelled', '$pass', '$cseat', '$ccharge', '$camt', '$paid', '$travel_id', '$mail_stat',
				'$sms_stat', '$ip', '$time', '$cdate', '$ctime', '$id_type', '$id_num', '$padd', '$alter_ph', '$fid', '$tid', '$operator_agent_type',
				'$agent_id', '$is_buscancel', '$pay', '$pay_type', '$authid', '$txn_refno', '$issuer_refno', '$payment_refno', '$bus_model',
				'$is_refunded', '$book_pay_type', '$book_pay_agent', '$update_status', '$collection_status')") or die(mysql_error());

				//echo $sql2."excuted";
				
                $seats1 = explode(",", $seats);
                $gender1 = explode(",", $gender);
                $seats_gender = "";
				
				
                for ($k = 0; $k < count($rt2); $k++) {
                    $a = $rt2[$k];

                    for ($l = 0; $l < count($seats1); $l++) {
                        if ($a == $seats1[$l]) {
                            if ($seats_gender == "") {
                                $seats_gender = $gender1[$l];
                            } else {
                                $seats_gender = $seats_gender . "#" . $gender1[$l];
                            }
                        } else {
                            
                        }
                    }
                }
				
                $seats_gender1 = explode("#", $seats_gender);
                //layout updation
                for ($j = 0; $j < count($rt2); $j++) {

                    if ($seats_gender1[$j] == "F") {
                        $is_ladies = 1;
                    } else {
                        $is_ladies = 0;
                    }
                   // $this->db->query("update layout_list set seat_status='1',is_ladies='$is_ladies' where seat_name='$rsn2[$j]' and travel_id='$travel_id' and service_num='$return_service_num' and journey_date='$return_date'");

                   // $this->db->query("update layout_list set seat_status='0',is_ladies='0' where seat_name='$rt2[$j]' and travel_id='$travel_id' and service_num='$onward_service_num' and journey_date='$onward_date'");
                }
                //Rescheduled ticket insertion
                $tkt_no = $tkt_no . "R";
                if (strtotime($onward_date) == strtotime($return_date)) {
                    $reschedule = "";
                } else {
                    $reschedule = "reschedule";
                }
                $sql3 = $this->db->query("insert into master_booking (tkt_no, pnr, service_no, board_point, bpid, land_mark, dpid, drop_point, source, dest, travels, bus_type, bdate, jdate, seats, gender, start_time, arr_time, paid, save, tkt_fare, promo_code, pname, pemail, pmobile, age, refno, status, pass, cseat, ccharge, camt, refamt, travel_id, mail_stat, sms_stat, ip, time, cdate, ctime, id_type, id_num, padd, alter_ph, fid, tid, operator_agent_type, agent_id, is_buscancel, pay, pay_type, authid, txn_refno, issuer_refno, payment_refno, bus_model, is_refunded, book_pay_type, book_pay_agent, update_status, collection_status, reschedule)
				values('$tkt_no', '$pnr', '$return_service_num', '$board_point', '$bpid', '$land_mark', '$dpid', '$drop_point', '$source', '$dest', '$travels', '$bus_type', '$bdate', '$return_date', '$rsn1', '$seats_gender', '$start_time', '$arr_time', '$paid', '$save', '$tkt_fare', '$promo_code', '$pname', '$pemail', '$pmobile', '$age', '$refno', '$status', '$pass', '$cseat', '$ccharge', '$camt', '$refamt', '$travel_id', '$mail_stat', '$sms_stat', '$ip', '$time', '', '$ctime', '$id_type', '$id_num', '$padd', '$alter_ph', '$fid', '$tid', '$operator_agent_type', '$agent_id', '$is_buscancel', '$pay', '$pay_type', '$authid', '$txn_refno', '$issuer_refno', '$payment_refno', '$return_model', '$is_refunded', '$book_pay_type', '$book_pay_agent', '$update_status', '$collection_status', '$reschedule')") ;

                $sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$travel_id'");
                foreach ($sql5->result() as $row5) {
                     $senid = $row5->sender_id;
					$ph = $row5->contact_no;
                }               
                $user = "pridhvi@msn.com:activa1525@";
                $receipientno = "$pmobile";
                $senderID = $senid;

                $text = "TKT No: " . $tkt_no . "->" . $travels . "-" . $source . "-" . $dest . "->" . $return_service_num . " , DOJ: " . $return_date . " , Seats: " . $rsn1 . " , At-" . $land_mark . " , Ph: " . $ph . "";

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
                echo 1;
            } else {
                echo 2; //seats mismatch
            }
        } else {
            echo 3;
        }
		
    }

    public function getServicesList_no_all() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $this->db->select("*");
        $this->db->order_by("from_name", "asc");
        $this->db->where("travel_id", $travel_id);
        $this->db->where("status", 1);
        $query = $this->db->get("master_buses");
        $service = array();
        $service['0'] = '- - - Select - - -';
        foreach ($query->result() as $rows) {
            $service[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $service;
    }

    public function vihicle_details2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $service_num = $this->input->post('services');
        $bus_number = $this->input->post('bus_number');
        $driver_name = $this->input->post('driver_name');
        $driver_number = $this->input->post('driver_number');

        $result = $this->db->query("SELECT * FROM `vehicle_details` WHERE service_number='$service_num' AND travel_id='$travel_id' ");
        if ($result->num_rows() > 0) {
            $query = $this->db->query("UPDATE vehicle_details SET bus_number='$bus_number',driver_name='$driver_name',driver_number='$driver_number' WHERE travel_id='$travel_id' AND service_number='$service_num'");
        } else {
            $query = $this->db->query("INSERT INTO vehicle_details (service_number, travel_id, bus_number, driver_name, driver_number) values('$service_num', '$travel_id', '$bus_number', '$driver_name', '$driver_number')") or die(mysql_error());
        }
        if ($query) {
            echo 1;
        }
    }

    public function get_vihicle_details1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $service_num = $this->input->post('services');

        $result = $this->db->query("SELECT * FROM `vehicle_details` WHERE service_number='$service_num' AND travel_id='$travel_id' ");
        foreach ($result->result() as $row) {
            $bus_number = $row->bus_number;
            $driver_name = $row->driver_name;
            $driver_number = $row->driver_number;
        }
        echo $bus_number . '#' . $driver_name . '#' . $driver_number;
    }

    public function Ticket_print2() {
        $ticket = $this->input->post('ticket');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $ho = $this->session->userdata('bktravels_head_office');
        if ($ho == 'yes') {
            $sql = $this->db->query("select count(*) from master_booking where tkt_no='$ticket'");
        } else {
            $sql = $this->db->query("select count(*) from master_booking where tkt_no='$ticket' and agent_id='$agent_id'");
        }

        if ($sql->num_rows() > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function Ticket_print4() {
        $ticket = $this->input->get('ticket');
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $s1 = $this->db->query("select * from master_booking where tkt_no='$ticket' and STATUS='cancelled'");
        if ($s1->num_rows() > 0) {
            echo "ticket already cancelled";
        } else {
            $sql = $this->db->query("select * from master_booking where tkt_no='$ticket'");

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
					$iso = $res->iso;
					$id_type = $res->id_type;
					$id_num = $res->id_num;
					
					
					if($start_time!='')
					{
					$reportTime2 =  date('H:i:s', strtotime("-15 minutes", strtotime($start_time)));
					}
					
					
					
					$sql_query = $this->db->query("select distinct country from master_iso where iso='$iso' ");
                    if ($sql_query->num_rows() > 0) {
                        foreach ($sql_query->result() as $roww) {
                            $country = $roww->country;
                        }
                    }
					
					$sql_query2 = $this->db->query("select distinct id_type from idproof_types where id='$id_type' ");
                    if ($sql_query2->num_rows() > 0) {
                        foreach ($sql_query2->result() as $roww2) {
                            $idtype2 = $roww2->id_type;
                        }
                    }
					
					if($idtype2=='')  $idtype2  = 'Passport';

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
						$canc_terms = $row5->canc_terms;
                    }
					
					$query = $this->db->query("select distinct terms_condition from operator_terms_conditions where operator_id='$travel_id'");
					foreach ($query->result() as $row) {  
						$terms_condition = $row->terms_condition;
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
			<link rel="stylesheet" type="text/css" href="' . base_url('css/ticket_screen.css') . '"  media="screen" />
			
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
				mywindow.document.write("table {border-collapse: collapse;  } th, td { border: 1px solid #ccc;     padding: 10px; text-align: left;   }");
				mywindow.document.write(" tr:nth-child(even) {  background-color: #eee; !important;  }  tr:nth-child(odd) {    background-color: #fff; !important;   } ");
				mywindow.document.write("a {	text-decoration:none; 	color:#0002CC; 		}	");
				mywindow.document.write(".btn btn-primary { background: #CC3300 none repeat scroll 0% 0%; color: #FFF;font-size: 15px;");
				mywindow.document.write(" padding: 3px 25px;    text-align: center;    cursor: pointer;    border: medium none #CC3300;}");
				mywindow.document.write(" table,th,tr,td { font-size:14px;font-family:calibri;}");
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
<table width="100%" style="border:hidden;" >
 <tr style="border:hidden;">
    <td colspan="2" style="border:hidden;"  align="center">
	<img src="http://ticketengine.in/intl_operator_logo/' . $travel_id . '.png"  alt="' . $travels . '"  style="display: block;
    margin-left: auto;   margin-right: auto;}"  width="160" height="50" />
	</td>
  </tr>  
</table>	<br />	<br />				
<table width="100%" style="border:hidden;" >
  <tr>
    <td style="border:hidden;" ><strong>' . strtoupper($source) . '</strong> OFFICE :
    Tel : </td>
    <td style="border:hidden;"><strong>' . strtoupper($dest) . '</strong> OFFICE :
    Tel :</td>
  </tr>
  
</table>
					
<table width="100%" height="214">
  <tr>
    <td colspan="2" valign="top">Name : &nbsp; &nbsp;<strong>' . $pname1[0] . '</strong>
    </td>
    <td width="393" valign="top">Ticket No : &nbsp; &nbsp;<strong>' . $pnr . '</strong>(' . $ticket . ')</td>
  </tr>
  <tr>
    <td width="231">Mobile : &nbsp; &nbsp; '.$pmobile.'</td>
    <td width="197">'.$idtype2.' No:  &nbsp; &nbsp; '.$id_num.'</td>
    <td>Nationality:  &nbsp; &nbsp; '.$country.' </td>
  </tr>
  <tr>
    <td>From  : <strong>' . $source . '</strong></td>
    <td>To: <strong>' . $dest . '</strong></td>
    <td>Seat No: &nbsp; &nbsp;'; 
	
	$st="";
	for ($i = 0; $i < $pass; $i++) {
		   if($i==0)
           $st=$seats1[$i];
		   else $st = $st.','.$seats1[$i];
         }
	echo $st;
	echo '</td>
  </tr>
  <tr>
    <td>Date of Issue : ' . $bdate . '</td>
    <td>Date of travel: ' . $jdate . '</td>
    <td>Amount:  ' . $tkt_fare . '</td>
  </tr>
  <tr>
    <td>Reporting time: &nbsp; &nbsp; '.$reportTime2.'</td>
    <td>Departure time: &nbsp; &nbsp; '.$start_time.'</td>
    <td>Boarding at : &nbsp; &nbsp; ' . $board_point . '<br />' . $land_mark . '</td>
  </tr>
  
   <tr>
    <td colspan="3">Issued by: &nbsp; &nbsp; ' . $name . '</td>
  </tr>
   <tr>
    <td colspan="3"><b>Terms and Conditions:</b><br/>
		 <ul>
		'.$terms_condition.'
		</ul> <br/> <b> Cancellation Policy </b> <br/> ';
		
				
				if($canc_terms=="00#n#100" || $canc_terms=='' || $canc_terms=='0')
				{	
				
				echo '<span style="padding:5px 0px 5px 10px;  color:#000000">100% cancellation charges, no refund will be provided.</span>';
				
				}
				else
				{
					
                    $canc_terms1 = explode('@', $canc_terms);
                    for ($i = 0; $i < count($canc_terms1); $i++) {
						$canc_terms2 = explode('#', $canc_terms1[$i]);
                        echo $canc_terms2[0] . " To " . $canc_terms2[1] . " Hours " . $canc_terms2[2] . "% shall be deducted </br>";
						    }
				}
		
		echo '</td>
  </tr>
    
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

    function get_busnumbers_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $stmt = "SELECT * FROM `bus_numbers` WHERE travel_id='$travel_id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    function get_busnumbers_db_ed() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $id = $this->input->get('id');
        $stmt = "SELECT * FROM `bus_numbers` WHERE travel_id='$travel_id' and id='$id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    public function add_bus_numbers2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $bus_number = $this->input->post('bus_number');
        $query = $this->db->query("INSERT INTO bus_numbers (travel_id, bus_number) values('$travel_id', '$bus_number')") or die(mysql_error());

        if ($query) {
            echo 1;
        }
    }

    public function edit_bus_numbers2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $bus_number = $this->input->post('bus_number');
        $id = $this->input->post('id');
        $query = $this->db->query("UPDATE bus_numbers SET bus_number='$bus_number' WHERE travel_id='$travel_id' AND id='$id'");
        if ($query) {
            echo 1;
        }
    }

    function get_drivers_db() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $stmt = "SELECT * FROM `drivers_details` WHERE travel_id='$travel_id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    public function add_driver2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $driver_name = $this->input->post('driver_name');
        $driver_number = $this->input->post('driver_number');
        $query = $this->db->query("INSERT INTO drivers_details (travel_id, driver_name, driver_number) values('$travel_id', '$driver_name','$driver_number')") or die(mysql_error());

        if ($query) {
            echo 1;
        }
    }

    function get_driver_db_ed() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $id = $this->input->get('id');
        $stmt = "SELECT * FROM `drivers_details` WHERE travel_id='$travel_id' and id='$id'";
        $query = $this->db->query($stmt);
        return $query->result();
    }

    function edit_driver2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $driver_name = $this->input->post('driver_name');
        $driver_number = $this->input->post('driver_number');
        $id = $this->input->post('id');
        $query = $this->db->query("UPDATE drivers_details SET driver_name='$driver_name',driver_number='$driver_number' WHERE travel_id='$travel_id' AND id='$id'");
        if ($query) {
            echo 1;
        }
    }

    function get_driver_number2() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $driver_name = $this->input->post('name');
        $query = $this->db->query("SELECT driver_number FROM `drivers_details` WHERE travel_id='$travel_id' AND driver_name='$driver_name'");
        foreach ($query->result() as $row) {
            $number = $row->driver_number;
        }
        echo $number;
    }

    public function getServicesList_ds() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $other_services = $this->session->userdata('bktravels_services');
        $today_date = date("Y-m-d");
        $sql = $this->db->query("SELECT DISTINCT t1.service_name,t2.service_num FROM master_buses t1, buses_list t2 WHERE t2.status='1' and t1.status='1' and t1.travel_id='$travel_id' AND t2.travel_id='$travel_id' and t1.service_num=t2.service_num and t2.journey_date>='$today_date'") or die(mysql_error());
        $slist = array();
        $slist['0'] = '- - - Select - - -';
        foreach ($sql->result() as $rows) {
            $slist[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $slist;
    }

    public function getDelayTime() {
        $data = array("" => "--- select ---");

        for ($i = 1; $i < 60; $i++) {
            if ($i < 10)
                $data[$i] = "0" . $i;
            else
                $data[$i] = $i;
        }
        return $data;
    }

    public function ServiceSendSMS1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $serviceno = $this->input->post('services');
        $time = $this->input->post('time');
        $timef = $this->input->post('timef');
        $jdate = date("Y-m-d");
        $sql5 = $this->db->query("SELECT  * FROM registered_operators where travel_id='$travel_id'");
        foreach ($sql5->result() as $row5) {
            $operator_title = $row5->operator_title;
        }
        $msg = $time . " " . $timef . "(" . $operator_title . ").";
        //sending SMS to the customers        
        $query3 = $this->db->query("select * from master_booking where service_no='$serviceno' and jdate='$jdate' and  (status='confirmed' or status='Confirmed')");

        foreach ($query3->result() as $va) {
            $tkt_no = $va->tkt_no;
            $travels = $va->travels;
            $pmobile = $va->pmobile;
            $seatno = $va->seats;
            $bus_type = $va->bus_type;
            $pnr = $va->pnr;
            $source = $va->source;
            $dest = $va->dest;
            $bpid = $va->bpid;
            $travid = $va->travel_id;
			$iso = $va->iso;
			$dialCode = $va->dialCode;
            //echo $pmobile;

            $query4 = $this->db->query("select * from master_booking where service_no='$serviceno' and tkt_no='$tkt_no' and (status='cancelled' || status='Cancelled')");
            if ($query4->num_rows() <= 0) {
				$text = "Service No " . $serviceno . " is delay by " . $msg . " ";
				$receipientno = $pmobile;
				$smsresp = $this->Updations_m->msg91sms($receipientno,$text,$iso,$dialCode);
               /* $user = "pridhvi@msn.com:activa1525@";
                $receipientno = $pmobile; //customer mobile number
                $senderID = $row5->sender_id;
                $text = "Service No " . $serviceno . " is delay by " . $msg . " ";
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
                curl_close($ch);  */
            }
        }//foreach

         echo smsresp;
       
    }
	function msg91sms($mobileNumber,$message,$iso2,$dialCode) {
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
