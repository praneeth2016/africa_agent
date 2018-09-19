<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getServicesList() {
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
	
	function getBoardingChartModel($service, $dtt) {
		
		$travelid = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $head_office = $this->session->userdata('bktravels_head_office');
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $other_services = $this->session->userdata('bktravels_other_services');
		//getting travel id
        $stmt = "select travel_id from master_buses where service_num='$service'";
        $sql = $this->db->query($stmt);
        foreach ($sql->result() as $res) {
            $travel_id = $res->travel_id;
        }
		
		 $route = $this->Reports_m->getRouteForService($service,$travel_id);
		 if($route!='')
		 {
			 $route2 = explode('-', $route);
                    
		 }
        //getting service route 
        $stmt = "select distinct service_name from master_buses where service_num='$service' and travel_id='$travel_id'";
        $ser_name = $this->db->query($stmt);
        foreach ($ser_name->result() as $q) {
            $service_name = $q->service_name;
        }
        //getting service details from boarding ponts
		$driverForJdate = false;
        $ser_name1 = $this->db->query("select distinct driver_name,mobile,bus_no from vechile_assignment where service_no='$service' and travel_id='$travel_id' and journey_date='$dtt'") or die(mysql_error());
        foreach ($ser_name1->result() as $q1) {
            $driver_name = $q1->driver_name;
            $contact = $q1->mobile;
            $bus_no = $q1->bus_no;
			$availDriver = true;
        }
		
		if($driverForJdate == false)
		{
			 $ser_name1 = $this->db->query("select distinct driver_name,mobile,bus_no from vechile_assignment where service_no='$service' and travel_id='$travel_id' ") or die(mysql_error());
			foreach ($ser_name1->result() as $q1) {
            $driver_name = $q1->driver_name;
            $contact = $q1->mobile;
            $bus_no = $q1->bus_no;
			$availDriver = true;
        }
			
		}
		
		echo '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Boarding Chart</title>

<script type ="text/javascript" src="' . base_url('js/app-js.v1.js') . '"></script>
            <script type="text/javascript">

    function print(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open("", "Service Wise Report", "height=400,width=600");
        mywindow.document.write("<html><head>");
        mywindow.document.write("<style type=\"text/css\">");
	 mywindow.document.write("table {    border-collapse: collapse; }");
	  mywindow.document.write("td {     border: 1px solid #ccc;  }");
	   mywindow.document.write("th, td {  padding: 10px;  text-align: left;  }");
	    mywindow.document.write("table,th,tr,td { font-size:14px;	font-family:calibri;} ");
	  mywindow.document.write("img {  -webkit-print-color-adjust: exact;  }");
	   
	mywindow.document.write("table,th,tr,td{font-size:13px;font-family:calibri;}");
        mywindow.document.write("</style>");
	mywindow.document.write("<title>Service Wise Report</title>");        
        mywindow.document.write("</head><body >");
        mywindow.document.write(data);
        mywindow.document.write("</body></html>");

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>

<style>
  table {
    border-collapse: collapse;
  }
  td {
    border: 1px solid #ccc;
  }
  th, td {
    padding: 10px;
    text-align: left;
  }
       table,th,tr,td
{
    font-size:14px;
	font-family:calibri;
} 
</style></head>
<body>
<div align="center">
<input value="Print" name="Submit" onclick="javascript:window.print(servicewiseprint);"  type="button" class="btn btn-primary" />
</div>  
<div id="servicewiseprint">
<table width="100%">
<tr><td>
<table width="100%" border="0" style="border:hidden;">
   <tr style="border:hidden;">
    <td colspan="4" style="border:hidden;text-align:center"><img src="http://ticketengine.in/intl_operator_logo/' . $travel_id . '.png"  alt="' . $travels . '"  style="display: block;
    margin-left: auto;   margin-right: auto;}"  width="160" height="50" /> 
	<h3>PASSENGER MANIFEST</h3>
	</td>
    <tr style="border:hidden;">
    <td style="border:hidden;"></td>
    <td style="border:hidden; " >&nbsp;</td>
    <td style="border:hidden; ">&nbsp;</td>
    <td width="26%" style="border:hidden;" valign="buttom"><span class="headings">Branch : '.$route2[0].'</span></td>
  </tr>
  <tr>
    <td style="border:hidden;"><span class="headings">Service No : </span>' . $service. '</td>
    <td style="border:hidden;"><span class="headings">Driver Name :  </span>'.$driver_name.'</td>
    <td style="border:hidden;"><span class="headings">Conductor Name:</span></td>
    <td style="border:hidden;">&nbsp;</td>
  </tr>
 
  <tr>
    <td width="22%" style="border:hidden;" ><span class="headings">From :  '.$route2[0].'</span></td>
    <td width="35%" style="border:hidden;"><span class="headings">To: '.$route2[1].'</span></td>
    <td width="17%" style="border:hidden;"><span class="headings">Date: </span>' . date("l, d M Y", strtotime($dtt)) . '</td>
    <td style="border:hidden;" align="left"></td>
   
  </tr>
 
  
</table><br /><br />
<table width="100%" border="0">
  <tr style="background-color: #eee;">
    <td>SEAT No</td>
    <td>TICKET No</td>
	 <td>PRICE</td>
    <td>PASSENGER NAME</td>
	<td> Mobile </td>
    <td>NATIONALITY</td>
    <td>ID TYPE</td>
    <td>ID No</td>
    <td>ORIGIN</td>
    <td>DESTINATION</td>
  </tr>
  ';
   $query = $this->db->query("select * from layout_list where seat_name!='GY' and journey_date='$dtt' and service_num='$service' and travel_id='$travel_id' order by seat_name") or die(mysql_error());
    foreach ($query->result() as $value) {
		
	$seatName = $value->seat_name;
	
	$query2 = $this->db->query("select * from master_booking where  seats in ('$seatName') and  jdate='$dtt' and service_no='$service' and travel_id='$travel_id'") or die(mysql_error());
     foreach ($query2->result() as $value2) {
     $tktno = $value2->tkt_no;	
	 $status = $value2->status;
	 $pname = $value2->pname;
	 $source = $value2->source;
	 $dest = $value2->dest;
	 $status = $value2->status;	
	 $iso = $value2->iso;
	 $id_type = $value2->id_type;
	 $tkt_fare = $value2->tkt_fare;
	 $bus_type = $value2->bus_type;
	 $lberth_fare = $value2->lberth_fare;
	 $uberth_fare = $value2->uberth_fare;
	 $id_num = $value2->id_num;
	 $iscancelled = false;
	 $pmobile = $value2->pmobile;
	 $dialCode = $value2->dialCode;
	 
	 $mobile = '+'.$dialCode.' '. $pmobile;
		 
	 
	 
	 $nationality = $this->Reports_m->getCountryFromISO($iso);
	 $idtypeTxt = $this->Reports_m->getIDType($id_type);
	//checking whether seat canceled or not
	$query3 = $this->db->query("select * from master_booking where  seats in ('$seatName') and tkt_no = '$tktno' and  jdate='$dtt' and service_no='$service' and travel_id='$travel_id'  and  LOWER(status) = 'cancelled' ") or die(mysql_error());
	foreach ($query3->result() as $value3) {
		$iscancelled = true;
	}
	  if($iscancelled==false){
		echo '<tr> <td>'.$seatName.'</td>
    <td>'. $tktno.'</td>
	<td>'.$tkt_fare.'</td>
    <td>'.$pname.'</td>
	<td>'.$mobile.'</td>
    <td>'.$nationality.'</td>
    <td>'.$idtypeTxt.'</td>
    <td>'.$id_num.'</td>
    <td>'.$source.'</td>
    <td>'.$dest.'</td>
  </tr>';
	  }
		
	}
   
	 
	}
  echo '</table></td></tr></table></div>
  <div align="center">
<input value="Print" name="Submit" onclick="javascript:window.print(servicewiseprint);"  type="button" class="btn btn-primary" />
</div>  </body></html>';
		
	}
	
	public function getCountryFromISO($iso) {
       $sql_query = $this->db->query("select distinct country from master_iso where iso='$iso' ");
                    if ($sql_query->num_rows() > 0) {
                        foreach ($sql_query->result() as $roww) {
                            $country = $roww->country;
                        }
                    }
        return $country;
    }
	
	public function getIDType($id_type) {
       $sql_query2 = $this->db->query("select distinct id_type from idproof_types where id='$id_type' ");
           if ($sql_query2->num_rows() > 0) {
             foreach ($sql_query2->result() as $roww2) {
               $idtype2 = $roww2->id_type;
               }
                    }
        return $idtype2;
    }		

public function getRouteForService($service,$operator_id) {
       $sql_query = $this->db->query("select distinct route_id from master_operator_stages where operator_id='$operator_id' and service_num='$service' ");
          foreach ($sql_query->result() as $roww) {
             $route_id = $roww->route_id;
           }
		   
		   $sql_query2 = $this->db->query("select * from master_routes_international where route_id='$route_id' ");
          foreach ($sql_query2->result() as $roww2) {
             $source_name = $roww2->source_name;
			 $destination_name = $roww2->destination_name;
           }
                
        return $source_name."-".$destination_name;
    }	
	
	
					

    function displayPassReports($service, $dtt) {

        $travelid = $this->session->userdata('bktravels_travel_id');
        $agent_id = $this->session->userdata('bktravels_user_id');
        $head_office = $this->session->userdata('bktravels_head_office');
        $agent_type = $this->session->userdata('bktravels_agent_type');
        $other_services = $this->session->userdata('bktravels_other_services');
        $pass = 0;
        $pass1 = 0;

        //getting travel id
        $stmt = "select travel_id from master_buses where service_num='$service'";
        $sql = $this->db->query($stmt);
        foreach ($sql->result() as $res) {
            $travel_id = $res->travel_id;
        }
        //getting service route 
        $stmt = "select distinct service_name from master_buses where service_num='$service' and travel_id='$travel_id'";
        $ser_name = $this->db->query($stmt);
        foreach ($ser_name->result() as $q) {
            $service_name = $q->service_name;
        }
        //getting service details from boarding ponts
        $ser_name1 = $this->db->query("select distinct driver_name,mobile,bus_no from vechile_assignment where service_no='$service' and travel_id='$travel_id' and journey_date='$dtt'") or die(mysql_error());
        foreach ($ser_name1->result() as $q1) {
            $name = $q1->driver_name;
            $contact = $q1->mobile;
            $bus_no = $q1->bus_no;
        }

        echo'<script type ="text/javascript" src="' . base_url('js/app-js.v1.js') . '"></script>
            <script type="text/javascript">

    function print(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open("", "Service Wise Report", "height=400,width=600");
        mywindow.document.write("<html><head>");
        mywindow.document.write("<style type=\"text/css\">");
	mywindow.document.write("table,th,tr,td{font-size:13px;font-family:calibri;}");
        mywindow.document.write("</style>");
	mywindow.document.write("<title>Service Wise Report</title>");        
        mywindow.document.write("</head><body >");
        mywindow.document.write(data);
        mywindow.document.write("</body></html>");

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>';

        echo '
            <div id="servicewiseprint">
<table width="100%" id="tbl" style="border:#CCCCCC solid 1px;font-size:15px;font-family:calibri;" cellpadding="0" cellspacing="0">
  <tr>
    <th height="30" colspan="14" align="left" style="background-color:#8A0808; color: #FFFFFF">Boarding Chart</th>
  </tr>
  <tr>
    <td height="30" colspan="5">Service Name :  ' . $service_name . '</td>
    <td height="30" colspan="4" >Service No :  ' . $service . '</td>
    <td height="30" colspan="5">DOJ :   ' . date("l, d M Y", strtotime($dtt)) . '</td>
  <tr>
    <td height="30" colspan="5">Veh No : ' . $bus_no . '</td>
    <td height="30" colspan="4">Attendent Name : ' . $name . '</td>
    <td height="30" colspan="5">Attendent Ph : ' . $contact . '</td>
  <tr style="background-color:#dddddd">
    <th width="4%" height="30">S.No</th>
    <th width="9%" height="30">Ticket No.</th>
    <th width="6%" height="30">Booked Date</th>
    <th width="6%" height="30">Booked By</th>
    <th width="7%" height="30">Payment Mode</th>
    <th width="7%" height="30">Total Seats</th>
    <th width="6%" height="30">Seat Nos.</th>
    <th width="6%" height="30">Customer </th>
    <th width="6%" height="30"> Phone </th>
    <th width="6%" height="30">From.</th>
    <th height="30">To</th>
    <th width="8%" height="30">Ticket Amt</th>
    <th width="6%" height="30">Comm.</th>
    <th width="10%" height="30">Net Amt</th>
  </tr>';
        $tkt_amt = 0;
        $tcom = 0;
        $netf = 0;
        $tnet = 0;
        $seats = 0;
        $s = 1;


        $query1 = $this->db->query("select distinct bpdp_id from boarding_points where travel_id='$travel_id' and service_num='$service' order by timing") or die(mysql_error());
        foreach ($query1->result() as $roww) {
            $id = $roww->bpdp_id;
            $getbdp = $this->db->query("select distinct bpid from master_booking where bpid='$id' and jdate='$dtt' and travel_id='$travel_id' and service_no='$service'");
            foreach ($getbdp->result() as $getbdp1) {
                $bpid = $getbdp1->bpid;
                if ($getbdp->num_rows() > 0) {
                    $sql_bp = $this->db->query("select * from boarding_points where bpdp_id='$bpid' and travel_id='$travel_id' and service_num='$service' order by timing") or die(mysql_error());
                    foreach ($sql_bp->result() as $valbp) {
                        $bp1 = $valbp->board_drop;
                        $bp = explode('#', $bp1);
                    }
                    $query_con = $this->db->query("select count(tkt_no) as con from master_booking where bpid='$bpid' and jdate='$dtt' and service_no='$service' and travel_id='$travel_id' and  (status='Confirmed' or status='confirmed')") or die(mysql_error());
                    foreach ($query_con->result() as $res_con) {
                        $row_con = $res_con->con;
                    }
                    $query_can = $this->db->query("select count(tkt_no) as can from master_booking where bpid='$bpid' and jdate='$dtt' and service_no='$service' and travel_id='$travel_id' and (status='Cancelled' or status='cancelled')") or die(mysql_error());
                    foreach ($query_con->result() as $res_con) {
                        $row_can = $res_can->can;
                        $cnt = $row_can;
                    }
                    if ($row_con > 0) {
                        echo '<tr style="background-color:#F1F1F1"><td height="30" colspan="14"><strong>' . $bp[0] . '-' . $bp[2] . '-' . date('h:i A', strtotime($bp[1])) . '</strong></td>
                            </tr>';
                    }
                    $query = $this->db->query("select * from master_booking where bpid='$bpid' and jdate='$dtt' and service_no='$service' and travel_id='$travel_id'") or die(mysql_error());
                    foreach ($query->result() as $value) {
                        $tktno = $value->tkt_no;
                        if ($value->status == 'confirmed') {
                            $sql = $this->db->query("select count(*) as cnt from master_booking where jdate='$dtt' and travel_id='$travel_id' and  tkt_no='$tktno' and (status='Cancelled' or status='cancelled')");
                            foreach ($sql->result() as $res2) {
                                $row_cnt = $res2->cnt;
                            }
                            if ($row_cnt > 0) {
                                
                            } else {
                                $seats = $seats + $value->pass;

                                $netf = $value->tkt_fare - $value->save;

                                if ($value->save == '') {
                                    $comm = 0;
                                } else {
                                    $comm = $value->save;
                                }
                                $tcom = $tcom + $comm;
                                $tkt_amt = $tkt_amt + $value->tkt_fare;
                                $tnet = $tnet + $netf;

                                //getting agent name
                                $id = $value->agent_id;
                                $sql_ag = $this->db->query("select * from agents_operator where id='$id'");
                                foreach ($sql_ag->result() as $res12) {
                                    $api_type = $res12->api_type;
                                }
                                if (trim($api_type) == "op") {
                                    $uname = $res12->name;
                                    $book_pay_type = "API";
                                } else if (trim($api_type) == "te") {
                                    $uname = "Ticket Engine";
                                    $book_pay_type = "API";
                                } else {
                                    $uname = $res12->name;
                                    $book_pay_type = "API";
                                }
                                $book_pay_type = $value->book_pay_type;
                                if ($book_pay_type == "") {
                                    $book_pay_type = "API";
                                }
                                if ($id == "") {
                                    $uname = "Website";
                                    $book_pay_type = "Website";
                                }
                                $names = explode(',', $value->pname);
                                $pnames = implode(', ', $names);
                                $mobiles = explode(',', $value->pmobile);
                                $pmobiles = implode(', ', $mobiles);
                                echo '<tr>
            <td height="30" align="center"> ' . $s . '</td>
            <td height="30" align="center">' . $value->tkt_no . '</td>
            <td height="30" align="center"> ' . $value->time . '</td>
            <td height="30" align="center"> ' . $uname . '</td>
            <td height="30" align="center">' . $book_pay_type . '</td>
            <td height="30" align="center">' . $value->pass . '</td>
            <td height="30" align="center"> ' . $value->seats . '<br/>
              ' . $value->gender . '</td>
            <td height="30" align="center">' . $pnames . '<br/>
              ' . $value->age . '</td>';
                                if ($value->pmobile != $value->alter_ph) {
                                    echo '<td height="30" align="center">' . $value->pmobile . ", " . $value->alter_ph . '</td>';
                                } else {
                                    echo '<td height="30" align="center">' . $value->pmobile . '</td>';
                                }
                                echo '<td height="30" align="center">' . $value->source . '</td>
            <td height="30" align="center">' . $value->dest . '  </td>
            <td height="30" align="center">' . $value->tkt_fare . '</td>
            <td height="30" align="center">' . round($comm, 2) . '</td>
            <td height="30" align="center">' . round($netf, 2) . '</td>
            </tr>';
                                $s++;
                            }//else
                        }
                    }
                }
            }
        }


        echo '<tr>
        <td height="30" colspan="11"  align="right"><b>Grand Totals</b></td>
        <td height="30" align="center"><b>' . round($tkt_amt, 2) . '</b></td>
        <td height="30" align="center"><b>' . round($tcom, 2) . '</b></td>
        <td height="30" align="center"><b>' . round($tnet, 2) . '</b></td>
        </tr>';

        echo '<tr>
        <td height="30" colspan="6"  align="right"><b>Total no. of Seats=' . $seats . '</b></td>
        <td height="30" colspan="8"  align="right" ><b>Total Collection Amount=' . round($tnet, 2) . '</b></td>
        </tr><br/>';

        if ($travelid == $travel_id) {
            //getting available seats from layout list            
            $query4 = $this->db->query("select *  from layout_list where travel_id='$travel_id'  and service_num='$service' and seat_name<>'GY' and journey_date='$dtt' and seat_status='0' order by seat_name ") or die(mysql_error());
            $es = $query4->num_rows();
            echo '<tr ><td height="30" colspan="14">
            <td></tr>';
            echo '<tr ><td height="30" colspan="14">Empty Seats(' . $es . '): &nbsp;';
            foreach ($query4->result() as $value2) {
                $sname = $value2->seat_name;
                echo $sname . ",";
            }
            echo '
            <td></tr>';
            //getting route wise booking
            $query2 = $this->db->query("select distinct from_id,from_name,to_id,to_name from master_buses where travel_id='$travel_id'  and service_num='$service'") or die(mysql_error());
            foreach ($query2->result() as $value) {
                $fname = $value->from_name;
                $tname = $value->to_name;
                $from_id = $value->from_id;
                $to_id = $value->to_id;
                $query3 = $this->db->query("select sum(pass) as pass from master_booking  where fid='$from_id' and tid='$to_id' and travel_id='$travel_id'  and service_no='$service' and jdate='$dtt' and status='confirmed'") or die(mysql_error());
                $query9 = $this->db->query("select sum(pass) as pass from master_booking  where fid='$from_id' and tid='$to_id' and travel_id='$travel_id'  and service_no='$service' and jdate='$dtt' and status='cancelled'") or die(mysql_error());
                foreach ($query3->result() as $value1) {
                    $pas = $value1->pass;
                }
                foreach ($query9->result() as $value9) {
                    $pass9 = $value9->pass;
                    $pass = $pas - $pass9;
                    $pass1 = $pass1 + $pass;
                }
                if ($pass != "") {
                    echo '<tr><td height="30" colspan="14" align="left">' . $fname . "&nbsp;-&nbsp;" . $tname . " =" . $pass . '
            </tr>';
                }
            }
            echo '<tr style="background-color:#F1F1F1"> <td height="30" colspan="14"  align="center"><b>Total No. of Seats=' . $pass1 . '</b></td> 
            </tr>           
           <tr> <td height="30"  colspan="14"></td> 
            </tr>';
            //getting API Bookings
            $sql_ag1 = $this->db->query("select * from agents_operator where operator_id='$travel_id' and agent_type_name='api'");
            $tpaid = 0;
            $tpass = 0;
            foreach ($sql_ag1->result() as $value4) {
                $aname = $value4->name;
                $aid = $value4->id;

                $query5 = $this->db->query("select sum(paid) as tpaid,sum(pass) as tpass from master_booking  where travel_id='$travel_id'  and service_no='$service' and agent_id='$aid' and jdate='$dtt' and status='confirmed'") or die(mysql_error());
                foreach ($query5->result() as $value5) {
                    if ($value5->tpaid == "") {
                        $tpaid1 = 0;
                    } else {
                        $tpaid1 = $value5->tpaid;
                    }
                    if ($value5->tpass == "") {
                        $tpass1 = 0;
                    } else {
                        $tpass1 = $value5->tpass;
                    }
                }

                $query6 = $this->db->query("select sum(paid) as tpaid,sum(pass) as tpass from master_booking  where travel_id='$travel_id'  and service_no='$service' and agent_id='$aid' and jdate='$dtt' and status='cancelled'") or die(mysql_error());
                foreach ($query6->result() as $value6) {
                    if ($value6->tpaid == "") {
                        $tpaid2 = 0;
                    } else {
                        $tpaid2 = $value6->tpaid;
                    }
                    if ($value6->tpass == "") {
                        $tpass2 = 0;
                    } else {
                        $tpass2 = $value6->tpass;
                    }
                }
                $tpass = $tpass1 - $tpass2;
                $tpaid = round($tpaid1 - $tpaid2, 2);
                echo '<tr style="background-color:#F1F1F1"> <td height="30"  colspan="14">By ' . $aname . " = " . $tpass . " & Amount = " . $tpaid . ' </td> 
                     </tr>
                     ';
            }
            $query7 = $this->db->query("select sum(paid) as tepaid,sum(pass) as tepass from master_booking  where travel_id='$travel_id'  and service_no='$service' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') and jdate='$dtt' and status='confirmed'") or die(mysql_error());
            foreach ($query7->result() as $value7) {
                if ($value7->tepaid == "") {
                    $tepaid1 = 0;
                } else {
                    $tepaid1 = $value7->tepaid;
                }
                if ($value7->tepass == "") {
                    $tepass1 = 0;
                } else {
                    $tepass1 = $value7->tepass;
                }
            }
            $query8 = $this->db->query("select sum(paid) as tepaid,sum(pass) as tepass from master_booking  where travel_id='$travel_id'  and service_no='$service' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') and jdate='$dtt' and status='cancelled'")or die(mysql_error());
            foreach ($query8->result() as $value8) {
                if ($value8->tepaid == "") {
                    $tepaid2 = 0;
                } else {
                    $tepaid2 = $value8->tepaid;
                }
                if ($value8->tepass == "") {
                    $tepass2 = 0;
                } else {
                    $tepass2 = $value8->tepass;
                }
            }
            $tepass = $tepass1 - $tepass2;
            $tepaid = round($tepaid1 - $tepaid2, 2);


            echo '<tr style="background-color:#F1F1F1"> <td height="30"  colspan="14">By Ticketengine = ' . $tepass . " & Amount = " . $tepaid . ' </td> 
                     </tr>
                     ';
            echo '</table><br />
</div>
<div align="center"><input value="Print" name="Submit" onclick="javascript:window.print(servicewiseprint);"  type="button" class="btn btn-primary" /></div>';
        }
    }

    function getServicesList1() {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $other_services = $this->session->userdata('bktravels_other_services');
        $sql = $this->db->query("select distinct to_id,to_name,from_id,from_name,service_name from master_buses where travel_id='$travel_id'") or die(mysql_error());
        foreach ($sql->result() as $res) {
            $from_name = $res->from_name;
            $to_name = $res->to_name;
            $from_id = $res->from_id;
            $to_id = $res->to_id;
            $service_name = $res->service_name;
        }
        $this->db->distinct();
        $this->db->select('*');
        $this->db->from('master_buses m');
        if ($other_services == "yes") {
            if ($key == '1') {

                $this->db->where('m.from_id ', $from_id);
                $this->db->or_where('m.to_id ', $to_id);
                $this->db->where('m.travel_id  <>', $travel_id);
                $this->db->where('m.service_name  <>', $service_name);
            } else
                $this->db->where('m.travel_id ', $travel_id);
        }
        else {
            $this->db->where('m.travel_id', $travel_id);
        }
        $query2 = $this->db->get();
        $slist = array();
        $slist['0'] = '- - - - Select - - - -';
        $slist['all'] = 'ALL';
        foreach ($query2->result() as $rows) {
            $slist[$rows->service_num] = $rows->service_name . "(" . $rows->service_num . ")";
        }
        return $slist;
        // return $query2->result();
    }

    function getAgentName($agent_type) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        if ($agent_type == 3)
            $sql = $this->db->query("select name,id from agents_operator where operator_id='$travel_id' and (agent_type='$agent_type' || pay_type='$agent_type') and api_type='op'");
        else if ($agent_type == 'all')
            $sql = $this->db->query("select name,id from agents_operator where operator_id='$travel_id' and (agent_type='$agent_type' || pay_type='$agent_type')");
        else if ($agent_type == 'prepaid' || $agent_type == 'postpaid')
            $sql = $this->db->query("select name,id from agents_operator where operator_id='$travel_id' and agent_type='2' and pay_type='$agent_type'");
        else
            $sql = $this->db->query("select name,id from agents_operator where operator_id='$travel_id' and  agent_type='$agent_type'");
        $data = array();
        $data['all'] = "All";
        foreach ($sql->result() as $rows) {
            $data[$rows->id] = $rows->name;
        }
        return $data;
    }

    function displayReports($from, $to, $agentname, $agents1, $rtype, $service) {


        //echo $from." - ".$to." - ".$agentname." - ".$agents1." - ".$rtype." - ".$service;
        if ($agents1 == 'postpaid' || $agents1 == 'prepaid') {
            $agents = '2';
        } else {
            $agents = $agents1;
        }
        if ($service == 'all') {
            $service_num = "";
            $service_num1 = "";
        } else {
            $service_num = " and service_no='$service'";
            $service_num1 = " and master_booking.service_no='$service'";
        }
        $travel_id = $this->session->userdata('bktravels_travel_id');

        if ($agents == 'all') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') " . $service_num . "");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id' and (status='Confirmed' || status='confirmed') " . $service_num . "");
            }
        } else if ($agents == 'all' && $agentname == 'all') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') " . $service_num . "");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') " . $service_num . "");
            }
        } else if ($agents == '4') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='4' and (agent_id is NULL or agent_id='')  " . $service_num . "");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='4' and (agent_id is NULL or agent_id='') " . $service_num . " ");
            }
        } else if ($agents == 'tg') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and agent_id='125' " . $service_num . " ");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and agent_id='125' " . $service_num . "");
            }
        } else if ($agents == 'tr') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and agent_id='161' " . $service_num . " ");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and agent_id='161' " . $service_num . " ");
            }
        } else if ($agents == 'te') {
            if ($rtype == 'bdate') {
                $query = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') " . $service_num . "");
            } else {
                $query = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='3' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') " . $service_num . "");
            }
        } else {
            if ($agents != 'all' && $agentname != 'all') {
                if ($rtype == 'bdate') {
                    $query1 = $this->db->query("select DISTINCT agent_id from master_booking  INNER JOIN agents_operator
     ON master_booking.agent_id=agents_operator.id where 
           (master_booking.bdate BETWEEN '" . $from . "' AND '" . $to . "') and 
               master_booking.travel_id='$travel_id'  and (master_booking.status='Confirmed' || master_booking.status='confirmed') and 
                   master_booking.operator_agent_type='$agents' and 
                       ((master_booking.agent_id='$agentname' ) )
                       and agents_operator.api_type<>'te' " . $service_num1 . "");
                } else {
                    $query1 = $this->db->query("select DISTINCT agent_id from master_booking  INNER JOIN agents_operator
     ON master_booking.agent_id=agents_operator.id where 
           (master_booking.jdate BETWEEN '" . $from . "' AND '" . $to . "') and 
               master_booking.travel_id='$travel_id'  and (master_booking.status='Confirmed' || master_booking.status='confirmed') and 
                   master_booking.operator_agent_type='$agents' and 
                       ((master_booking.agent_id='$agentname' ) )
                       and agents_operator.api_type<>'te' " . $service_num1 . "");
                }
            } else if ($agents != 'all' && $agentname == 'all') {
                if ($rtype == 'bdate') {
                    $query1 = $this->db->query("select DISTINCT agent_id from master_booking  INNER JOIN agents_operator
     ON master_booking.agent_id=agents_operator.id where 
           (master_booking.bdate BETWEEN '" . $from . "' AND '" . $to . "') and
               master_booking.travel_id='$travel_id'  and (master_booking.status='Confirmed' || master_booking.status='confirmed') and 
                   master_booking.operator_agent_type='$agents' and agents_operator.api_type<>'te' " . $service_num1 . "");
                } else {
                    $query1 = $this->db->query("select DISTINCT agent_id from master_booking  INNER JOIN agents_operator
     ON master_booking.agent_id=agents_operator.id where 
           (master_booking.jdate BETWEEN '" . $from . "' AND '" . $to . "') and
               master_booking.travel_id='$travel_id'  and (master_booking.status='Confirmed' || master_booking.status='confirmed') and 
                   master_booking.operator_agent_type='$agents' and agents_operator.api_type<>'te' " . $service_num1 . "");
                }
            }


            if ($query1->num_rows() > 0) {
                foreach ($query1->result() as $value) {
                    $agentid = $value->agent_id;
                    $query2 = $this->db->query("select * from agents_operator where id='$agentid'");
                    if ($rtype == 'bdate') {
                        $query3 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='$agents' and 
               (agent_id='$agentid' " . $service_num . " ) 
               ");
                    } else {
                        $query3 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and operator_agent_type='$agents' and 
               (agent_id='$agentid' " . $service_num . " ) 
               ");
                    }
                    foreach ($query2->result() as $val) {
                        $name = $val->name;
						$api_type = $val->api_type;

                        //for print data
                        echo '<script>
function printBooking()
  {
    var printButton = document.getElementById("printpagebutton");
       
       printButton.style.visibility = "hidden";
        window.print()
printButton.style.visibility = "visible";

  }

</script>';
                        echo '<table width="100%" id="tbl" style="border:#cccccc solid 2px; border-collapse:collapse;">
		<tr> 
        <td height="30" colspan="9" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Booking List</b></td>
		<td height="30" colspan="9" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        Agent name : <b>' . $name . '</b></td></tr>
		<tr>        
        <th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Booking</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare<br />(A)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST + Comm<br />(B)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare<br />(C = A - B)</th>
        </tr>';
                        
                        $i = 1;
						$total_seats = 0;
			$total_base_fare = 0;
			$total_cgst = 0;
			$total_sgst = 0;
			$total_tcs = 0;
			$total_convenience_charge = 0;
			$total_discount_amount = 0;
			$total_tkt_fare = 0;
			$total_commission = 0;
			$total_igst_commission = 0;
			$total_net_fare = 0;
			
                        foreach ($query3->result() as $value) {
			$tkt_no = $value->tkt_no;
			$pnr = $value->pnr;
			$service_no = $value->service_no;
			$jdate = date('Y-m-d',strtotime($value->jdate));
			$bdate = date('Y-m-d',strtotime($value->bdate));
			$source = $value->source;
			$dest = $value->dest;
			$seats = $value->seats;
			$pass = $value->pass;
			$pname = $value->pname;
			$pmobile = $value->pmobile;
			$base_fare = round(($value->base_fare),2);
			$service_tax_amount = round(($value->service_tax_amount),2);
			$cgst = round(($value->cgst),2);
			$sgst = round(($value->sgst),2);
			$tcs = round(($value->tcs),2);
			$convenience_charge = round(($value->convenience_charge),2);
			$discount_amount = round(($value->discount_amount),2);
			$tkt_fare = round(($value->tkt_fare),2);
			$save = round(($value->save),2);
			$paid = round(($value->paid),2);
			$operator_agent_type = $value->operator_agent_type;
			$agent_id = $value->agent_id;
									
			if($agent_id == '12' || $agent_id == '15' || $agent_id == '125' || $agent_id == '144' || $agent_id == '161' || $agent_id == '204') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = round(($commission * 18 /100),2);
			} else if($operator_agent_type == '4') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = 0;
			} else {				
				$commission = round($save,2);					
			}
			
			if($cgst == 0 || $cgst == "0.0") {
				$cgst = round(($service_tax_amount / 2),2);
				$sgst = round(($service_tax_amount / 2),2);
				$tcs = round(($base_fare / 100),2);
			}
			
			$tcs = 0;			
			
			if($operator_agent_type == 1 || $operator_agent_type == 2) {
				$igst_commission = $commission;
				$tkt_fare = $base_fare + $cgst + $sgst + $tcs + $convenience_charge - $discount_amount;
				//$net_fare = $base_fare + $cgst + $sgst + $convenience_charge - $discount_amount - $commission - $tcs;
				$net_fare = $tkt_fare - $igst_commission;
			} else {
				$igst_commission = $convenience_charge + $commission;
				$tkt_fare = $base_fare + $cgst + $sgst + $tcs + $convenience_charge - $discount_amount;
				//$net_fare = $base_fare + $cgst + $sgst + $tcs - $convenience_charge - $discount_amount - $commission;
				$net_fare = $tkt_fare - $igst_commission;
			}	
			
			$total_seats = $total_seats + $pass;
			$total_base_fare = $total_base_fare + $base_fare;
			$total_cgst = $total_cgst + $cgst;
			$total_sgst = $total_sgst + $sgst;
			$total_tcs = $total_tcs + $tcs;
			$total_convenience_charge = $total_convenience_charge + $convenience_charge;
			$total_discount_amount = $total_discount_amount + $discount_amount;
			$total_tkt_fare = $total_tkt_fare + $tkt_fare;
			$total_commission = $total_commission + $commission;
			$total_igst_commission = $total_igst_commission + $igst_commission;
			$total_net_fare = $total_net_fare + $net_fare;			
			
			echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$i.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no.'<br /> / <br />'.$pnr.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$bdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source.'<br /> - <br />'.$dest.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats.'<br/> '.$pass.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname).'<br/> '.$pmobile.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$igst_commission.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare.'</td>
        </tr>';
            $i++;
		}       
        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_igst_commission.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare.'</b></td>
        </tr>';

        echo '<tr>
        <td height="30" align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Booked Seats = '.$total_seats.'</b></td>
        <td align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Booking amount = '.$total_net_fare.'</b></td>
        </tr>';

        echo "</table><br/>";
                        //cancellation
        echo '<table width="100%" id="tbl" style="border:#cccccc solid 2px;border-collapse:collapse;">
        <tr> 
        <td height="30" colspan="19" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Cancellation List</b></td></tr>';
                        if ($rtype == 'bdate') {
                            $query4 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id' and status='cancelled' and (agent_id='$agentid' " . $service_num . " )");
                        } else {
                            $query4 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id' and status='cancelled' and (agent_id='$agentid' " . $service_num . " )");
                        }


                        echo '<tr>
		<th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Cancel</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare<br />(A)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Cancel Amount<br />(B)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST + Comm<br />(C)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare<br />D = A - (B + C)</th>
        </tr>';                        
                        $j = 1;
						$total_seats2 = 0;
			$total_base_fare2 = 0;
			$total_cgst2 = 0;
			$total_sgst2 = 0;
			$total_tcs2 = 0;
			$total_convenience_charge2 = 0;
			$total_discount_amount2 = 0;
			$total_tkt_fare2 = 0;
			$total_commission2 = 0;
			$total_cancellation_amount2 = 0;
			$total_igst_commission2 = 0;
			$total_net_fare2 = 0;
			
                        foreach ($query4->result() as $value2) {
                            $tkt_no2 = $value2->tkt_no;
			$pnr2 = $value2->pnr;
			$service_no2 = $value2->service_no;
			$jdate2 = date('Y-m-d',strtotime($value2->jdate));
			$cdate2 = date('Y-m-d',strtotime($value2->cdate));
			$source2 = $value2->source;
			$dest2 = $value2->dest;
			$seats2 = $value2->seats;
			$pass2 = $value2->pass;
			$pname2 = $value2->pname;
			$pmobile2 = $value2->pmobile;
			$base_fare2 = round(($value2->base_fare),2);
			$service_tax_amount2 = round(($value2->service_tax_amount),2);
			$cgst2 = round(($value2->cgst),2);
			$sgst2 = round(($value2->sgst),2);
			$tcs2 = round(($value2->tcs),2);
			$convenience_charge2 = round(($value2->convenience_charge),2);
			$discount_amount2 = round(($value2->discount_amount),2);
			$tkt_fare2 = round(($value2->tkt_fare),2);
			$save2 = round(($value2->save),2);
			$paid2 = round(($value2->paid),2);
			$camt2 = round(($value2->camt),2);
			$operator_agent_type2 = $value2->operator_agent_type;
			$agent_id2 = $value2->agent_id;
			
			if($agent_id2 == '12' || $agent_id2 == '15' || $agent_id2 == '125' || $agent_id2 == '144' || $agent_id2 == '161' || $agent_id2 == '204') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = round(($commission2 * 18 /100),2);
			} else if($operator_agent_type2 == '4') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = 0;
			} else {
				$commission2 = round($save2,2);
			}
			
			if($operator_agent_type == '3' || $operator_agent_type == '4') {						
				$cancellation_amount2 = round(($camt2 / 2),2);
			} else {
				$cancellation_amount2 = round($camt2,2);
			}
			
			if($cgst2 == 0 || $cgst2 == "0.0") {
				$cgst2 = round(($service_tax_amount2 / 2),2);
				$sgst2 = round(($service_tax_amount2 / 2),2);
				$tcs2 = round(($base_fare2 / 100),2);
			}
			
			$tcs2 = 0;			
			
			if($operator_agent_type2 == 1 || $operator_agent_type2 == 2) {
				$igst_commission2 = $commission2;
				$tkt_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 + $convenience_charge2 - $discount_amount2;
				$net_fare2 = $tkt_fare2 - $igst_commission2;
			} else {
				$igst_commission2 = $convenience_charge2 + $commission2;
				$tkt_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 + $convenience_charge2 - $discount_amount2;			
				//$net_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 - $convenience_charge2 - $discount_amount2 - $cancellation_amount2 - $commission2;
				$net_fare2 = $tkt_fare2 - $cancellation_amount2 - $igst_commission2;
			}						
			
			$total_seats2 = $total_seats2 + $pass2;
			$total_base_fare2 = $total_base_fare2 + $base_fare2;
			$total_cgst2 = $total_cgst2 + $cgst2;
			$total_sgst2 = $total_sgst2 + $sgst2;
			$total_tcs2 = $total_tcs2 + $tcs2;
			$total_convenience_charge2 = $total_convenience_charge2 + $convenience_charge2;
			$total_discount_amount2 = $total_discount_amount2 + $discount_amount2;
			$total_tkt_fare2 = $total_tkt_fare2 + $tkt_fare2;
			$total_cancellation_amount2 = $total_cancellation_amount2 + $cancellation_amount2;
			$total_commission2 = $total_commission2 + $commission2;
			$total_igst_commission2 = $total_igst_commission2 + $igst_commission2;
			$total_net_fare2 = $total_net_fare2 + $net_fare2;

            echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$j.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no2.'<br /> / <br />'.$pnr2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source2.'<br /> - <br />'.$dest2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats2.'<br/> '.$pass2.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname2).'<br/> '.$pmobile2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cancellation_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$igst_commission2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare2.'</td>
        </tr>';
            $j++;
        }
		$balance = 	$total_net_fare - $total_net_fare2;
                        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cancellation_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_igst_commission2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Cancelled Seats = '.$total_seats2.'</b></td>
        <td align="center" colspan="10" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Cancellation Amount =  '.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="19" style="font-size:14px;border:#cccccc solid 1px;">&nbsp;</td>        
        </tr>
		<tr>
        <td height="30" align="center" colspan="19" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Amount to Pay =  '.$total_net_fare.' - '.$total_net_fare2.' = '.$balance.'</b></td>        
        </tr>
		</table><br /><br />';
                    }
                }
                echo '<table align="center" style="margin: 0px auto;">
        <tr align="center"><td>
        <input type="button" class="btn btn-primary" name="print" id="printpagebutton" value="Print" onClick="printBooking();">
        </td></tr>
        </table>';
            } else {
                echo '<table align="center" style="margin: 0px auto;color:red">
        <tr align="center" style="color:red"><td>
       No Records Found on selected date
        </td></tr>
        </table>';
            }
        }

        return $query->result();
        //print_r($query1->result());
    }

    function displayCanReports($from, $to, $agentname, $agents1, $rtype, $service) {
        if ($service == 'all') {
            $service_num = "";
            $service_num1 = "";
        } else {
            $service_num = " and service_no='$service'";
            $service_num1 = " and master_booking.service_no='$service'";
        }
        if ($agents1 == 'postpaid' || $agents1 == 'prepaid') {
            $agents = '2';
        } else {
            $agents = $agents1;
        }
        $travel_id = $this->session->userdata('bktravels_travel_id');
        if ($agents == 'all') {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           ( bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and status='cancelled' " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           ( jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and status='cancelled' " . $service_num . "");
            }
        } else if ($agents == 'all' && $agentname == 'all') {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           ( bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and status='cancelled' " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           ( jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and status='cancelled' " . $service_num . "");
            }
        } else if ($agents == '4') {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='4' and (agent_id is NULL or agent_id='') " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='4' and (agent_id is NULL or agent_id='') " . $service_num . "");
            }
        } else if ($agents == 'tg') {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and agent_id='125' " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and agent_id='125' " . $service_num . "");
            }
        } else if ($agents == 'tr') {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and agent_id='161' " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and agent_id='161' " . $service_num . "");
            }
        } else if ($agents == "te") {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') " . $service_num . "");
            } else {
                $query1 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and operator_agent_type='3' and (agent_id='12' || agent_id='15' || agent_id='125' || agent_id='144' || agent_id='161' || agent_id='204') " . $service_num . "");
            }
        } else {
            if ($rtype == 'bdate') {
                $query1 = $this->db->query("select * from master_booking INNER JOIN agents_operator
ON master_booking.agent_id=agents_operator.id where 
           ( master_booking.bdate BETWEEN '" . $from . "' AND '" . $to . "') and master_booking.travel_id='$travel_id'  and master_booking.status='cancelled' and 
               (master_booking.agent_id='$agentname' ) " . $service_num1 . "");
            } else {
                $query1 = $this->db->query("select * from master_booking INNER JOIN agents_operator
ON master_booking.agent_id=agents_operator.id where 
           ( master_booking.jdate BETWEEN '" . $from . "' AND '" . $to . "') and master_booking.travel_id='$travel_id'  and master_booking.status='cancelled' and 
               (master_booking.agent_id='$agentname' ) " . $service_num1 . "");
            }
        }
        return $query1->result();
    }

    function getViewSelected_PassengerDetail($date, $ser) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $stmt = "select * from master_booking where travel_id='$travel_id' and jdate='$date' and status='confirmed' and service_no='$ser'";       
        $query = $this->db->query($stmt);        
        foreach ($query->result() as $rows) {
            $tktno = $rows->tkt_no;
            $fromm = $rows->source;
            $too = $rows->dest;
            $svrno = $rows->service_no;
            $jdate1 = $rows->jdate;
        }
        $service_name = $this->db->query("SELECT service_name FROM master_buses WHERE service_num='$ser'");
        foreach($service_name->result() as $sn){
            $sname = $sn->service_name;
        }
        echo '<script>
function Printpass(){
    
    var printButton = document.getElementById("print");
       
       printButton.style.visibility = "hidden";
        window.print();
printButton.style.visibility = "visible";
}
</script><table align="center" style="border:#f2f2f2 solid 1px;" width="100%" class="fixed">
                        <tr>
                        <td><b>Source - Destination: </b>' . $sname . '</td>
                        <td><b>Service No: </b>' . $svrno . '</td>
                        <td><b>Date: </b>' . $jdate1 . '</td>
                        </tr>
                        </table>
                        <table id="tbl1" align="center" border="1" style="border-collapse:collapse;" width="100%">
                       <tr id="tr" style=" background:#f2f2f2;">                        
                        <th style="border:#f2f2f2 solid 1px;">Seat No.</th>
                        <th style="border:#f2f2f2 solid 1px;">Passenger Name</th>
						<th style="border:#f2f2f2 solid 1px;">Passenger Age</th>
                        <th style="border:#f2f2f2 solid 1px;">Contact No.</th>
                        <th style="border:#f2f2f2 solid 1px;">Signature</th>
                       </tr>';       

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $rows1) {
                $tktno = $rows1->tkt_no;
                $sql = $this->db->query("select * from master_booking where tkt_no='$tktno' and (status='cancelled' or status='Cancelled')");
                if ($sql->num_rows() <= 0) {
                    if ($rows1->pass > 1) {
                        $seats = explode(',', $rows1->seats);
                        $pname = explode(',', $rows1->pname);
                        $age = explode(',', $rows1->age);
                        for ($j = 0; $j < $rows1->pass; $j++) {
                            echo "
					<tr align='center'>        
					<td style='border:#f2f2f2 solid 1px;'>" . $seats[$j] . "</td>
<td style='border:#f2f2f2 solid 1px;'> " . $pname[$j] . "</td>
<td style='border:#f2f2f2 solid 1px;'> " . $age[$j] . "</td>
<td style='border:#f2f2f2 solid 1px;'>" . $rows1->pmobile . "</td>
<td style='border:#f2f2f2 solid 1px;'> </td>
</tr>
";                      
                        }
                    } else {
                        echo "
<tr align='center'>        
<td style='border:#f2f2f2 solid 1px;'>" . $rows1->seats . "</td>
<td style='border:#f2f2f2 solid 1px;'>" . $rows1->pname . "</td>
<td style='border:#f2f2f2 solid 1px;'>" . $rows1->age . "</td>
<td style='border:#f2f2f2 solid 1px;'>" . $rows1->pmobile . "</td>
<td style='border:#f2f2f2 solid 1px;'> </td>
</tr>
";
                    }
                   
                }//if($sql->num_rows()<=0)
            }
            echo "</table>";
        } else {
            echo "<div align='center'>No booked seats are available!</div>";
        }

        echo "<table align='center' width='700'>
            <tr align='center'>";
        echo '<td><input type="button" class="btn btn-primary" name="print" id="print" value="Print" class="btn btn-primary" onClick="Printpass()">
            </td>
            </tr>
            </table>';
    }

    public function mybookings2($from, $to, $rtype, $service) {
        $travel_id = $this->session->userdata('bktravels_travel_id');
        $bktravels_user_id = $this->session->userdata('bktravels_user_id');
		$bktravels_api_type = $this->session->userdata('bktravels_api_type');
		$bktravels_margin = $this->session->userdata('bktravels_margin');
		$bktravels_comm_type = $this->session->userdata('bktravels_comm_type');
		
        if ($service == 'all') {
            $service_num = "";
        } else {
            $service_num = " and service_no='$service'";
        }
        if ($rtype == 'bdate') {
            $result1 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and agent_id='$bktravels_user_id' " . $service_num . " ");
            $result2 = $this->db->query("select * from master_booking where 
           (bdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and agent_id='$bktravels_user_id' " . $service_num . "");
        } else {
            $result1 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Confirmed' || status='confirmed') and agent_id='$bktravels_user_id' " . $service_num . " ");
            $result2 = $this->db->query("select * from master_booking where 
           (jdate BETWEEN '" . $from . "' AND '" . $to . "') and travel_id='$travel_id'  and (status='Cancelled' || status='cancelled') and agent_id='$bktravels_user_id' " . $service_num . "");
        }
        //print_r($result1->result());
        //print_r($result2->result());		
        echo '<script>
function printBooking()
  {
    var printButton = document.getElementById("printpagebutton");
       
       printButton.style.visibility = "hidden";
        window.print()
printButton.style.visibility = "visible";

  }

</script>';
        echo '
		<table width="100%" id="tbl" style="border:#cccccc solid 2px; border-collapse:collapse;">
		<tr> 
        <td height="30" colspan="17" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Booking List</b></td></tr>
		<tr>        
        <th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Booking</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare</th>
        </tr>';
		
		$i = 1;
		
		foreach ($result1->result() as $value) {
			$tkt_no = $value->tkt_no;
			$pnr = $value->pnr;
			$service_no = $value->service_no;
			$jdate = date('Y-m-d',strtotime($value->jdate));
			$bdate = date('Y-m-d',strtotime($value->bdate));
			$source = $value->source;
			$dest = $value->dest;
			$seats = $value->seats;
			$pass = $value->pass;
			$pname = $value->pname;
			$pmobile = $value->pmobile;
			$base_fare = round(($value->base_fare),2);
			$service_tax_amount = round(($value->service_tax_amount),2);
			$cgst = round(($value->cgst),2);
			$sgst = round(($value->sgst),2);
			$tcs = round(($value->tcs),2);
			$convenience_charge = round(($value->convenience_charge),2);
			$discount_amount = round(($value->discount_amount),2);
			$tkt_fare = round(($value->tkt_fare),2);
			$save = round(($value->save),2);
			$paid = round(($value->paid),2);
			$operator_agent_type = $value->operator_agent_type;
			$agent_id = $value->agent_id;
			
			if($agent_id == '12' || $agent_id == '15' || $agent_id == '125' || $agent_id == '144' || $agent_id == '161' || $agent_id == '204') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = round(($commission * 18 /100),2);
			} else if($operator_agent_type == '4') {
				$commission = round(($base_fare * 13 / 100),2);
				$convenience_charge = 0;
			} else {
				$commission = round($save,2);
			}
			
			if($cgst == 0 || $cgst == "0.0") {
				$cgst = round(($service_tax_amount / 2),2);
				$sgst = round(($service_tax_amount / 2),2);
				$tcs = round(($base_fare / 100),2);
			}
			
			$tcs = 0;
			
			$tkt_fare = $base_fare + $cgst + $sgst + $tcs - $convenience_charge - $discount_amount;
			$net_fare = $base_fare + $cgst + $sgst + $tcs - $convenience_charge - $discount_amount - $commission;
			
			$total_seats = $total_seats + $pass;
			$total_base_fare = $total_base_fare + $base_fare;
			$total_cgst = $total_cgst + $cgst;
			$total_sgst = $total_sgst + $sgst;
			$total_tcs = $total_tcs + $tcs;
			$total_convenience_charge = $total_convenience_charge + $convenience_charge;
			$total_discount_amount = $total_discount_amount + $discount_amount;
			$total_tkt_fare = $total_tkt_fare + $tkt_fare;
			$total_commission = $total_commission + $commission;
			$total_net_fare = $total_net_fare + $net_fare;			
			
			echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$i.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no.'<br /> / <br />'.$pnr.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$bdate.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source.'<br /> - <br />'.$dest.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats.'<br/> '.$pass.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname).'<br/> '.$pmobile.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare.'</td>
        </tr>';
            $i++;
		}                    
        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare.'</b></td>
        </tr>';

        echo '<tr>
        <td height="30" align="center" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Booked Seats = '.$total_seats.'</b></td>
        <td align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Booking amount = '.$total_net_fare.'</b></td>
        </tr>';

        echo "</table><br/>";
        //cancellation

        echo '<table width="100%" id="tbl" style="border:#cccccc solid 2px;border-collapse:collapse;">
        <tr> 
        <td height="30" colspan="18" align="left" style="background-color:#f2f2f2; color:#000000;margin-left: 140px">
        <b>Cancellation List</b></td></tr>
		<tr>
		<th style="font-size:14px;border:#cccccc solid 1px;">S.No</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Tkt No<br /> / <br />PNR</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Service</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Journey</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Cancel</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Source<br /> - <br />Destination</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Seats</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Passenger</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Base Fare</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">CGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">SGST</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">TCS</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">IGST</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Discount</th>
        <th style="font-size:14px;border:#cccccc solid 1px;">Total Fare<br />(A)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Cancel Amount<br />(B)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Comm<br />(C)</th>
		<th style="font-size:14px;border:#cccccc solid 1px;">Net Fare<br />D = A-(B+C)</th>
        </tr>';
        
        $j = 1;
		
        foreach ($result2->result() as $value2) {
			$tkt_no2 = $value2->tkt_no;
			$pnr2 = $value2->pnr;
			$service_no2 = $value2->service_no;
			$jdate2 = date('Y-m-d',strtotime($value2->jdate));
			$cdate2 = date('Y-m-d',strtotime($value2->cdate));
			$source2 = $value2->source;
			$dest2 = $value2->dest;
			$seats2 = $value2->seats;
			$pass2 = $value2->pass;
			$pname2 = $value2->pname;
			$pmobile2 = $value2->pmobile;
			$base_fare2 = round(($value2->base_fare),2);
			$service_tax_amount2 = round(($value2->service_tax_amount),2);
			$cgst2 = round(($value2->cgst),2);
			$sgst2 = round(($value2->sgst),2);
			$tcs2 = round(($value2->tcs),2);
			$convenience_charge2 = round(($value2->convenience_charge),2);
			$discount_amount2 = round(($value2->discount_amount),2);
			$tkt_fare2 = round(($value2->tkt_fare),2);
			$save2 = round(($value2->save),2);
			$paid2 = round(($value2->paid),2);
			$camt2 = round(($value2->camt),2);
			$operator_agent_type2 = $value2->operator_agent_type;
			$agent_id2 = $value2->agent_id;
			
			if($agent_id2 == '12' || $agent_id2 == '15' || $agent_id2 == '125' || $agent_id2 == '144' || $agent_id2 == '161' || $agent_id2 == '204') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = round(($commission2 * 18 /100),2);
			} else if($operator_agent_type2 == '4') {
				$commission2 = round(($base_fare2 * 13 / 100),2);
				$convenience_charge2 = 0;
			} else {
				$commission2 = round($save2,2);
			}
			
			if($operator_agent_type == '3' || $operator_agent_type == '4') {						
				$cancellation_amount2 = round(($camt2 / 2),2);
			} else {
				$cancellation_amount2 = round($camt2,2);
			}
			
			if($cgst2 == 0 || $cgst2 == "0.0") {
				$cgst2 = round(($service_tax_amount2 / 2),2);
				$sgst2 = round(($service_tax_amount2 / 2),2);
				$tcs2 = round(($base_fare2 / 100),2);
			}
			
			$tcs2 = 0;
			
			$tkt_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 - $convenience_charge2 - $discount_amount2;			
			$net_fare2 = $base_fare2 + $cgst2 + $sgst2 + $tcs2 - $convenience_charge2 - $discount_amount2 - $cancellation_amount2 - $commission2;
			
			$total_seats2 = $total_seats2 + $pass2;
			$total_base_fare2 = $total_base_fare2 + $base_fare2;
			$total_cgst2 = $total_cgst2 + $cgst2;
			$total_sgst2 = $total_sgst2 + $sgst2;
			$total_tcs2 = $total_tcs2 + $tcs2;
			$total_convenience_charge2 = $total_convenience_charge2 + $convenience_charge2;
			$total_discount_amount2 = $total_discount_amount2 + $discount_amount2;
			$total_tkt_fare2 = $total_tkt_fare2 + $tkt_fare2;
			$total_cancellation_amount2 = $total_cancellation_amount2 + $cancellation_amount2;
			$total_commission2 = $total_commission2 + $commission2;
			$total_net_fare2 = $total_net_fare2 + $net_fare2;			

            echo '<tr>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$j.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_no2.'<br /> / <br />'.$pnr2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$service_no2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$jdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cdate2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$source2.'<br /> - <br />'.$dest2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$seats2.'<br/> '.$pass2.' Seats</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.str_replace(",",", ",$pname2).'<br/> '.$pmobile2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$base_fare2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$sgst2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$tcs2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;">'.$convenience_charge2.'</td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$discount_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$tkt_fare2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$cancellation_amount2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$commission2.'</td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"> '.$net_fare2.'</td>
        </tr>';
            $j++;
        }
		$balance = 	$total_net_fare - $total_net_fare2;
        echo '<tr>
        <td height="30" align="right" colspan="8" style="font-size:14px;border:#cccccc solid 1px;"><b>Totals</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_base_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cgst2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_sgst2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tcs2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_convenience_charge2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_discount_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_tkt_fare2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_cancellation_amount2.'</b></td>
		<td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_commission2.'</b></td>
        <td align="center" style="font-size:14px;border:#cccccc solid 1px;"><b>'.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Cancelled Seats = '.$total_seats2.'</b></td>
        <td align="center" colspan="9" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Cancellation Amount =  '.$total_net_fare2.'</b></td>
        </tr>
		<tr>
        <td height="30" align="center" colspan="18" style="font-size:14px;border:#cccccc solid 1px;">&nbsp;</td>        
        </tr>
		<tr>
        <td height="30" align="center" colspan="18" style="font-size:14px;border:#cccccc solid 1px;"><b>Total Amount to Pay =  '.$total_net_fare.' - '.$total_net_fare2.' = '.$balance.'</b></td>        
        </tr>
		</table>';
    }

}
