<?php 
	$agentId = $this->session->userdata('bktravels_user_id');
	$travel_id = $this->session->userdata('bktravels_travel_id');
	$agent_charge = $this->session->userdata('bktravels_agent_charge');
	$agent_type = $this->session->userdata('bktravels_agent_type');
	$ho = $this->session->userdata('bktravels_head_office');
	$changeprice = $this->session->userdata('bktravels_changeprice');
	$boardingchart = $this->session->userdata('bktravels_boardingchart');
	$vehicle_assignment = $this->session->userdata('bktravels_vehicle_assignment');
	$grabrelease = $this->session->userdata('bktravels_grabrelease');	
	$ticket_modify = $this->session->userdata('bktravels_ticket_modify');
	
	$by_cash = $this->session->userdata('bktravels_by_cash');
	$by_phone = $this->session->userdata('bktravels_by_phone');
	$by_agent = $this->session->userdata('bktravels_by_agent');
	$by_phone_agent = $this->session->userdata('bktravels_by_phone_agent');
	$by_employee = $this->session->userdata('bktravels_by_employee');
?>

<script>
function ticketstatus()
    {
        var ticket = $("#ticket").val();
        if (ticket == 0)
        {
            alert('Kindly Provide Ticket Number/PNR/Mobile Number/Receipt Number');
            $('#ticket').focus()
        }
        else
        {
            $.post('<?php echo site_url('Booking/checkticket'); ?>',
                    {
                        ticket: ticket
                    }, function (res)
            {
                if (res == 1)
                {
                    //window.open('Booking/ticket_status?ticket=' + ticket);
                    window.open('Booking/ticket_status?ticket=' + ticket, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
                }
                else
                {
                    alert("Kindly provide valid number");
                }
            });
        }
    }
app.controller("teAgentloginCtrl", function($scope,$http,$filter,$window) {	
	$scope.agentId = '<?php echo $agentId ?>';
	$scope.agentName = '<?php echo $this->session->userdata('bktravels_name'); ?>';
	$scope.selectedOnward = selectedOnward;	
	$scope.operatorId = '<?php echo $travel_id ?>';
	$scope.head_office_te_ses = '<?php echo $ho ?>';
	$scope. agId = '<?php echo $agentId ?>';
	$scope.agent_type_te_ses = '<?php echo $agent_type ?>';
	$scope.agent_logged_in = '<?php echo $this->session->userdata('bktravels_logged_in') ?>';
	$scope.busrow = true;
	$scope.busrow1 = false;
	$scope.url = "http://localhost:8080/TestSpring/";
	//$scope.url = "http://ticketengine.in:8080/TestSpring_intl_live/";
	//$scope.url = "http://ticketengine.in:8080/TestSpring_demo/";
	//if date is empty setting date array as default dates from current date
	$scope.datearray = [];
	if($scope.j_date == '' || $scope.j_date== undefined)
	{
		for(var m=0;m<7;m++)
		{
			var dateOut_emp = new Date();
			dateOut_emp.setDate(dateOut_emp.getDate() + m);
			$scope.date_emp = $filter('date')(new Date(dateOut_emp),'yyyy-MM-dd');
			$scope.datearray.push($scope.date_emp);
		}			
	}
	
    /************************************** method for getting From Cities *********************/
	$http.get($scope.url+"getCities/"+$scope.operatorId).then(function(response) {			
        $scope.myData = response.data.cities;
		//console.log($scope.myData[0].city['cityid']);
		//console.log($scope.myData);		
	});
	/************************************** method for getting To Cities *********************/
   $scope.getToCities = function(fromCityId){
	  // alert("performValidRequest()" + fromCityId +"   operatorId  "+ $scope.operatorId);
		  
	   $http.get($scope.url+"getToCities/"+$scope.operatorId+"/"+fromCityId).then(function(response) {		   
        $scope.getToCity = response.data.cities;
		//console.log($scope.getToCity);
		});
	};
	/************************************** method for getting service list *********************/
	$scope.getServices = function(fromId,toId,jdate){
		
		//alert("agent_logged_in  "+$scope.agent_logged_in);
		$scope.idSelectedVote = null;
		$scope.lgnd = false;
		$scope.from_id=fromId;
		$scope.to_id = toId;
		$scope.selectedOnward = jdate;	
		$scope.j_date = jdate;			
		$scope.datearray = [];
		var dateOut1 = new Date();
		$scope.j_date2 = $filter('date')(new Date($scope.j_date),'yyyy-MM-dd');
		
		var cdate = new Date();
		var curdate = new Date();
		var curdate1 = new Date();
		var curdate2 = new Date();
		
		var cdate_nw = $filter('date')(new Date(cdate),'yyyy-MM-dd');
		var cdate_1 = $filter('date')(new Date(curdate.setDate(curdate.getDate() + 1)),'yyyy-MM-dd');
		var cdate_2 = $filter('date')(new Date(curdate1.setDate(curdate1.getDate() + 2)),'yyyy-MM-dd');
		var cdate_3 = $filter('date')(new Date(curdate2.setDate(curdate2.getDate() + 3)),'yyyy-MM-dd');
		
		//console.log(cdate_1,"---",cdate_2,"----",cdate_3);
		var k = '';
		var l = '';
		if($scope.j_date2 == cdate_nw)
		{	k=0;l=6;}
		else if($scope.j_date2 == cdate_1)
		{	k=1;l=5;}
		else if($scope.j_date2 == cdate_2)
		{	k=2;l=4;}
		else if($scope.j_date2 == cdate_3)
		{	k=3;l=3;}
		else
		{	k=3;l=3;}
		//console.log(k,"---",l);
		for(var i=k;i>0;i--)
		{
			var dateOut = new Date($scope.j_date);
			dateOut.setDate(dateOut.getDate() - i);
			$scope.j_date1 = $filter('date')(new Date(dateOut),'yyyy-MM-dd');	
			//alert("hii  "+$scope.j_date1);
			$scope.datearray.push($scope.j_date1);
		}
		$scope.datearray.push($scope.j_date2);
		for(var j=1;j<=l;j++)
		{	
			var dateOut2 = new Date($scope.j_date);
			dateOut2.setDate(dateOut2.getDate() + j);
			$scope.j_date12 = $filter('date')(new Date(dateOut2),'yyyy-MM-dd');	
			$scope.datearray.push($scope.j_date12);
		}
		if(fromId == undefined || fromId == "" || toId == undefined || toId == "")
		{
			alert('Please provide values for From City, To City!');
		}
		else if(jdate == undefined || jdate == "")
		{
			alert('Please Select Journey Date!');
		}
		else
		{	
			console.log($scope.url+"getServices/"+$scope.operatorId+"/"+fromId+"/"+toId+"/"+$scope.j_date);	
			$scope.loadingbus = true;
			$scope.busrow = true;
			$scope.busrow1 = false;	
			$http.get($scope.url+"getServices/"+$scope.operatorId+"/"+fromId+"/"+toId+"/"+$scope.j_date).then(function(response) {
			$scope.loadingbus = false;	
			$scope.busrow = false;
			$scope.busrow1 = true;
			$scope.servicesList = response.data.service_details;
			//console.log($scope.servicesList);
				if($scope.servicesList == null)
				{
					alert('There are NO Service!!');
				}
				else{
					//$state.go('app.servicelist', {}, {reload: true});
				}
					
			});
		}			
	};
	$scope.searchbusDate = function(dtkey)
	{
		$scope.getServices($scope.from_id,$scope.to_id,dtkey);
	}
	$scope.getIdtypes = function(){	
		$http.get($scope.url+"idTypes/").then(function(response) {			
			$scope.idTypes = response.data;
			console.log($scope.idTypes);
			//console.log($scope.myData);		
		});
	}
	$scope.getLayout = function(fromId,toId,jdate,servno,currency){
		$scope.getIdtypes();		
		$scope.fareVar = 0;
		$scope.totFareVar = 0;
		$scope.service_tax_amountVar = 0;
		$scope.convenience_chargeVar = 0;
		$scope.cgst_amountVar = 0;
		$scope.sgst_amountVar = 0;
		$scope.tcs_amountVar = 0;
		//service number for entire operations
		$scope.idSelectedVote = servno;
		$scope.lgnd = true;
		$scope.servno = servno;	
		$scope.currency = currency;	
		$scope.seatArr = {};
		$scope.loading=true;
		$scope.layoutloading = false;		
		//alert("performValidRequest()" + fromId +"   fromId  " + toId +"   toId  " + $scope.operatorId +"   operatorId  " + jdate +"   jdate  "+ servno +"   servno ");
		$http.get($scope.url+"getSeating/"+$scope.operatorId+"/"+fromId+"/"+toId+"/"+jdate+"/"+servno+"/"+$scope.agentId).then(function(response) {
			console.log($scope.url+"getSeating/"+$scope.operatorId+"/"+fromId+"/"+toId+"/"+jdate+"/"+servno+"/"+$scope.agentId);
		$scope.loading=false;
		$scope.layoutloading = true;
        $scope.seatArr = response.data.service_details;
		$scope.seatArrBlockRelease = angular.copy($scope.seatArr);
		//console.log($scope.seatArr);
		$scope.layServiceNum = $scope.seatArr[0].service_num;
		$scope.layJdate = $scope.seatArr[0].travel_date;	
		//console.log($scope.seatArr[0].coach_layout.seat_details);	
		$scope.seatsList=[];		
		$scope.idSelectedSeat = null;
		$scope.seatShow = false;
		$scope.seats = null;
		//console.log($scope.seatArr[0].bus_type);
		$scope.slMcol = $scope.seatArr[0].m_col;
		var m_col = $scope.seatArr[0].m_col;
		var m_row = $scope.seatArr[0].m_row;
		//console.log(m_col+"  "+m_row);
		$scope.rows = []
		$scope.cols = [];
		/*for(var colarr=m_col;colarr>=1;colarr--){				
			$scope.cols.push(colarr);
		}*/
		for(var colarr=1;colarr<=m_col;colarr++){				
			$scope.cols.push(colarr);	
			//console.log($scope.rows);
		}
		//console.log($scope.cols);
		for(var rowarr=1;rowarr<=m_row;rowarr++){				
			$scope.rows.push(rowarr);	
			//console.log($scope.rows);
		}
		/*if($scope.seatArr[0].bus_type == 'seater' || $scope.seatArr[0].bus_type == 'seatersleeper' || $scope.seatArr[0].bus_type == 'sleeper')
		{
			$scope.lowerShow = true;
			$scope.upperShow = false;
			$scope.lowerShowSL = true;
			$scope.upperShowSL = false;
			var seatsDetails = $scope.seatArr[0].coach_layout.seat_details;
			var slots = [];
			
			for(s in seatsDetails){
				var rowId = seatsDetails[s].seat.row_id;
				var colId = seatsDetails[s].seat.col_id;
				if(slots["col"+colId] == undefined){
					slots["col"+colId] = [];
				}
				slots["col"+colId]["row"+rowId] = true;
				
			}
			//console.log(slots);
			$scope.emptySeats = [];
			for(var col=1;col<=m_col;col++){
				for(var row=1;row<=m_row;row++){
					//console.log(slots["col"+col]["row"+row]);
					if(slots["col"+col] === undefined){
						slots["col"+col] = [];
					}
					if(slots["col"+col]["row"+row] === undefined){
						$scope.emptySeats[col+"-"+row] = true;
					}
				}
			}
		}	*/
		
		//$state.go('app.servicelayout', {}, {reload: true});		
		});		
		
		/****************************************   for getting boarding and dropping points *************/
		$scope.getBoarding();
		//calling function getting seat count details
		$scope.seatcountDetails();
	};
	//function getting seat count details
	$scope.seatcountDetails = function()
	{		
		//console.log($scope.url+"seatCountDetails/"+$scope.servno+"/"+$scope.operatorId+"/"+$scope.agentId+"/"+$scope.j_date)
		$http.get($scope.url+"seatCountDetails/"+$scope.servno+"/"+$scope.operatorId+"/"+$scope.agentId+"/"+$scope.j_date+"/"+$scope.from_id+"/"+$scope.to_id).then(function(response) {
			$scope.seatcountDetail = response.data;
			console.log($scope.seatcountDetail);				
			});
	}
	/****************************************    Seat selection code **************************************************/
	$scope.isSelected = function(ind){
		//alert("$scope.seatsList");
	return inArray(ind,$scope.seatsList).status;
	//console.log(inArray(ind,$scope.seatsList).status);
    }	
	$scope.getSeat = function(val,fare,service_tax,convenience_charge,cgst_amount,sgst_amount,tcs_amount)
	{	
		//$scope.seatFare = fare;
		//alert("$scope.seatsList  "+fare);
		//console.log(val,fare,service_tax,convenience_charge,cgst_amount,sgst_amount,tcs_amount);		
		var a = 0;		
		if($scope.seatsList.length!=0)
		{			
			angular.forEach($scope.seatsList,function(value,index){					
				if(value===val)
				{
					a=1;									
					$scope.seatsList.splice(index, 1);
					$scope.fareVar = (($scope.fareVar)-(fare));
					$scope.service_tax_amountVar = (($scope.service_tax_amountVar)-(service_tax));
					$scope.convenience_chargeVar = (($scope.convenience_chargeVar)-(convenience_charge));
					$scope.cgst_amountVar = (($scope.cgst_amountVar)-(cgst_amount));
					$scope.sgst_amountVar = (($scope.sgst_amountVar)-(sgst_amount));
					$scope.tcs_amountVar = (($scope.tcs_amountVar)-(tcs_amount));
					
				}					
			})			
		}
		if(a==0)
		{			
			$scope.seatsList.push(val);				
			if($scope.fareVar == 0){$scope.fareVar = fare;}else{$scope.fareVar = (($scope.fareVar)+(fare));}
			if($scope.convenience_chargeVar == 0){$scope.convenience_chargeVar = convenience_charge;}else{$scope.convenience_chargeVar = (($scope.convenience_chargeVar)+(convenience_charge));}
			if($scope.cgst_amountVar == 0){$scope.cgst_amountVar = cgst_amount;}else{$scope.cgst_amountVar = (($scope.cgst_amountVar)+(cgst_amount));}
			if($scope.sgst_amountVar == 0){$scope.sgst_amountVar = sgst_amount;}else{$scope.sgst_amountVar = (($scope.sgst_amountVar)+(sgst_amount));}
			if($scope.tcs_amountVar == 0){$scope.tcs_amountVar = tcs_amount;}else{$scope.tcs_amountVar = (($scope.tcs_amountVar)+(tcs_amount));}
			if($scope.service_tax_amountVar == 0){$scope.service_tax_amountVar = service_tax;}else{$scope.service_tax_amountVar = (($scope.service_tax_amountVar)+(service_tax));}
		}		
		$scope.totFareVar = $scope.fareVar+$scope.service_tax_amountVar+$scope.convenience_chargeVar;	
		//console.log($scope.convenience_chargeVar,$scope.cgst_amountVar,$scope.sgst_amountVar,$scope.tcs_amountVar,"tottt");
		//console.log($scope.totFareVar);
		$scope.seatCount=$scope.seatsList.length;
		$scope.seats = $scope.seatsList;
		//console.log($scope.seatsList,"-----$scope.seatsList");
		angular.forEach($scope.seatsList,function(value,index){
                //alert(index);
				$scope.idSelectedSeat = value;
            })
	};
	$scope.getAgTotalAmt = function (agcharge)
	{
		$scope.agentcharge1 = agcharge;
		if($scope.fareVar == 0 || $scope.fareVar == undefined){
			alert("Please Select atlest one seat!");
			$scope.agentcharge = 0;
		}
		else{
			$scope.totFareVar = ($scope.fareVar)+($scope.agentcharge1);
		}		
	}
	$scope.getBoarding = function(){
		//$scope.serv_no = servno;
		//alert("performValidRequest()" + $scope.from_id +"   fromId  " + $scope.to_id +"   toId  " + operatorId +"   operatorId  "+ servno +"   servno ");
		$http.get($scope.url+"getBoardDropPoints/"+$scope.operatorId+"/"+$scope.from_id+"/"+$scope.to_id+"/"+$scope.servno).then(function(response) {
        $scope.boardDrop = response.data.boarding_points;
		console.log($scope.boardDrop,"   Boarding points");		
		});				
	};  
	/*$scope.getDropping  = function(board){
		//alert(board);
		$scope.bpid=board;
		$state.go('app.dropingpoints', {}, {reload: true});
	};*/
	$scope.getLandmark = function(id)
	{
		//alert("getLandmark"+ id);
		angular.forEach($scope.boardDrop,function(value,index){
			//console.log(value);
			if(value.boarding_point.bpid == id && value.boarding_point.type== 'board')
			{
				$scope.lanmark = value.boarding_point.landmark;
			}			
		})
		
		//console.log($scope.lanmark);
	}
	$scope.getLandmark_dep = function(id)
	{
		//alert("getLandmark"+ id);
		angular.forEach($scope.boardDrop,function(value,index){                
			if(value.boarding_point.bpid == id && value.boarding_point.type== 'drop')
			{
				$scope.lanmark_dep = value.boarding_point.landmark;
			}			
		})
		//console.log($scope.lanmark_dep);
		
	}
	//getting paytype agent 
	$scope.pay_type = function(paytyp)
	{
		//alert(paytyp);
		
		 if (paytyp == "byagent" || paytyp == "byphoneagent" || paytyp == "byemployee")
        {
            $.post('<?php echo base_url('booking/paytype'); ?>', {travel_id: $scope.operatorId, paytyp: paytyp}, function (res)
            {
                //alert(res);
                $("#pay").html(res);
            });
        }
        if (paytyp == "byphone")
        {
            $.post('<?php echo base_url('booking/paytype'); ?>', {travel_id: $scope.operatorId, paytyp: paytyp, journey_date: $scope.j_date}, function (res)
            {
                //alert(res);
                $("#pay").html(res);
            });
        }
        else
        {
            $("#pay").html('');
        }
	}
	
	//confirming the ticket
	$scope.getBooking = function(saveTicket)
	{
		var countryData = $("#pmobile").intlTelInput("getSelectedCountryData");
        //alert(countryData.iso2+'---'+countryData.dialCode);
		var ph = saveTicket.pmobile;
		var altph = saveTicket.palt;
		
		var agentcharge = $("#agentcharge").val();
		var pay_agent = $("#pay_agent").val();
		var receiptno = $("#receiptno").val();
		
		
		if(agentcharge == undefined || agentcharge == '' || agentcharge == null )
		{
			var agentcharge =0;
		}
		if(pay_agent == undefined || pay_agent == '' || pay_agent == null)
		{
			var pay_agent =0;
		}
		if(receiptno == undefined || receiptno == '' || receiptno == null)
		{
			var receiptno =0;
		}
		if(saveTicket.pemail == undefined )
		{
			saveTicket.pemail = null;
		}
		if(saveTicket.palt == undefined )
		{
			saveTicket.palt = null;
		}		
		var paytype = $("#paytype").val();
		
		var strFilter = /^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i;
        var strFilter1 = /^[-+]?\d*\.?\d*$/;
		if(saveTicket.pname == undefined || saveTicket.pname == null || saveTicket.pname == '')
		{
			alert("Please Enter Primary Passenger Name");
		}
		/*else if(saveTicket.pemail == undefined || saveTicket.pemail == null || saveTicket.pemail == '')
		{
			alert("Please Enter Primary Passenger Email");
		}*/
		else if(saveTicket.pmobile == undefined || saveTicket.pmobile == null || saveTicket.pmobile == '')
		{
			
			alert("Please Enter Mobile Number");
		}
		else if(!strFilter1.test(ph) || ph.length < 9 || ph.length > 11 )
		{
			
			alert("Please Enter Valid Mobile Number");
		}
		
		/*else if(!strFilter1.test(altph) || altph.length < 10 || altph.length > 11 || saveTicket.palt == undefined || saveTicket.palt == null || saveTicket.palt == '')
		{
			alert("Please Enter Alternative Number");
		}*/
		else if(saveTicket.boardpoint == undefined || saveTicket.boardpoint == null || saveTicket.boardpoint == '')
		{
			alert("Please Select Boarding Point");
		}
		else if(saveTicket.droppoint == undefined || saveTicket.droppoint == null || saveTicket.droppoint == '')
		{
			alert("Please Select Dropping Point");
		}
		else if(paytype == undefined || paytype == null || paytype == '')
		{
			alert("Please Select Payment Mode");
		}
		else if((paytype == "byagent" || paytype == "byphoneagent" || paytype == "byemployee") && (pay_agent == undefined || pay_agent == null || pay_agent == '' || pay_agent == 0))
		{
			alert("Please Select Agent");
		}		
		else
		{
			//alert("saveTicket"+saveTicket);
			//console.log(saveTicket,"----");	
			/*$scope.convenience_chargeVar = 0;
			$scope.cgst_amountVar = 0;
			$scope.sgst_amountVar = 0;
			$scope.tcs_amountVar = 0;*/
			
			$scope.genderList =[];
			$scope.nameList=[];
			$scope.ageList=[];
			angular.forEach($scope.seats,function(value,index){         
				//$scope.genderList.push(saveTicket.gender[value]);
				$scope.genderList.push($("#gender"+value).val());
				$scope.nameList.push(saveTicket.pname);
				$scope.ageList.push(0);
				
			})
				//alert(idtype);
			//console.log($scope.genderList,"----",$scope.nameList,"----",$scope.ageList);	
			$('#bookticket').prop('disabled', true);	
			$http.post($scope.url+"seatBlocking/"+$scope.operatorId+"/"+$scope.from_id+"/"+$scope.to_id+"/"+$scope.j_date+"/"+$scope.servno+"/"+$scope.seats+"/"+$scope.genderList+"/"+$scope.agentId).then(function(response) {
					
				$scope.pnrStatus = response.data;
				//console.log($scope.pnrStatus.response['code']);
				if($scope.pnrStatus.response['code'] == 200)
				{
					//alert($scope.pnrStatus.response['message']);
					$scope.pnr=$scope.pnrStatus.pnrDetails['pnr'];
					//seat confirmation method
					//$http.post($scope.url+"seatBookingWeb/"+$scope.operatorId+"/"+$scope.from_id+"/"
					//+$scope.to_id+"/"+$scope.j_date+"/"+$scope.servno+"/"+$scope.seats+"/"+$scope.genderList+"/"+$scope.nameList+"/"+$scope.ageList
					//+"/"+$scope.fareVar+"/"+saveTicket.boardpoint+"/"+saveTicket.droppoint+"/"+saveTicket.pmobile+
					//"/"+saveTicket.palt+"/"+saveTicket.pemail+"/"+$scope.pnr+"/"+$scope.agentId+"/"+$scope.seatCount+"/"+paytype+"/"+pay_agent+"/"+agentcharge+"/"+receiptno).then(function(response) {
						
					
					$http.post($scope.url+"seatBookingWeb/"+$scope.operatorId+"/"+$scope.from_id+"/"
					+$scope.to_id+"/"+$scope.j_date+"/"+$scope.servno+"/"+$scope.seats+"/"+$scope.genderList+"/"+$scope.nameList+"/"+$scope.ageList
					+"/"+$scope.totFareVar+"/"+saveTicket.boardpoint+"/"+saveTicket.droppoint+"/"+saveTicket.pmobile+
					"/"+saveTicket.palt+"/"+saveTicket.pemail+"/"+$scope.pnr+"/"+$scope.agentId+"/"+$scope.seatCount+"/"+paytype+"/"+pay_agent+"/"
					+agentcharge+"/"+receiptno+"/"+countryData.iso2+"/"+countryData.dialCode+"/"+saveTicket.idtype+"/"+saveTicket.idnum).then(function(response) {
						
						$scope.confirmTicket = response.data;
						console.log($scope.confirmTicket);
						if($scope.confirmTicket.response['code'] == 200)
						{
							
							$('#bookticket').prop('disabled', false);	
							//window.location = '<?php echo base_url("booking/sendSms?onward_tktno=" + onward_tktno); ?>';
							alert("Ticket Confirmed Successfully !");
							var onward_tktno = $scope.confirmTicket.ticket_details['ticket_number'];
							$.post('<?php echo base_url("booking/sendSms") ?>', {onward_tktno: onward_tktno}, function (res) {
							//alert(res);
								//window.open("booking/ticket_search?ticket=" + res, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, width=800, height=600");
								
							});
							//console.log(onward_tktno);						
							window.open("booking/confirmed_ticket_json?onward_tktno=" + onward_tktno, "_blank");
							$window.location.reload();
							
							/*alert($scope.confirmTicket.ticket_details['ticket_number'] +' ;Passenger Name : '
									+ $scope.confirmTicket.ticket_details['passenger_name']+' ;Journey Date : '
									+ $scope.confirmTicket.ticket_details['journey_date']+' ;Service Number: '
									+ $scope.confirmTicket.ticket_details['service_number']+' ;From City : '
									+ $scope.confirmTicket.ticket_details['origin']+' ;To City : '
									+ $scope.confirmTicket.ticket_details['destination']+' ;Seats : '
									+ $scope.confirmTicket.ticket_details['seat_numbers']);*/
							
							//console.log($scope.confirmTicket.ticket_details['ticket_number'])
							
						}
						else 
						{
							alert($scope.confirmTicket.response['message']);							
						}
					});
					
				}
				else
				{
					alert($scope.pnrStatus.response['message']);
				}						
			});
			
		}	
	}
	$scope.layout_change_price = function(service, from_id, to_id, dtt)		
    {
		//alert("layout_change_price");
        window.open("booking/layout_change_price?service=" + service + "&from_id=" + from_id + "&to_id=" + to_id + "&dtt=" + dtt, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
    }
	$scope.Report = function(service, dtt)
    {
        window.open("Reports/GetPassReport?service=" + service + "&dtt=" + dtt, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
    }
	$scope.layout_grab_release = function(service, dtt)
    {
        window.open("booking/layout_grab_release?service=" + service + "&dtt=" + dtt, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
    }
	/************************************** method for grabAndReleaseSeat popup *********************/
	//$scope.layout_grab_release_popup = function
	$scope.statusOptions = [{id : "A",name : 'Active',disabled:"false"}, {id : "I",name : 'InActive',disabled:"false"}];
	$scope.reset=function()
	{
		
	}	
	$scope.cancel = function() {
		$scope.reset();
		$("#cityForm").modal('hide');		
	};
	$scope.layout_grab_release_popup = function() {	
        //alert("hi");	
		$scope.reset();	
		$scope.blckRelLay = false;
		$scope.updateblck = false;
		$scope.seatsListBcolkRelease.length=0;
		$scope.updateQuota = {
			blockreleasefrmdt: '<?php echo date('Y-m-d'); ?>',
			blockreleasetodte: '<?php echo date('Y-m-d'); ?>'
		}		
		$("#cityForm").modal({backdrop : 'static',keyboard : true,show : true});
		
	};
	$scope.getLayoutBlckRel = function(seatsRelType)
	{
		$scope.seatsListBcolkRelease.length=0;
		if(seatsRelType == 'individual')
		{
			$scope.blckRelLay = true;
		}
		else{
			$scope.blckRelLay = false;
		}
	}
	$scope.getAgentsList = function(agentType)
	{
		//alert(agentType);
		if(agentType == 'opentoall')
		{
			$scope.agentsListBlckRelease = [{
				id : 0,
				name : 'Open to All'
			}]
			//$scope.agentsListBlckRelease=$scope.agentsListBlckRelease1;
			//$scope.agentsListBlckRelease.name='Open to All';
		}
		else{
			$http.get($scope.url+"paytype/"+$scope.operatorId+"/"+agentType).then(function(response) {
				console.log(response.data.agents,"   response.data");
				$scope.agentsListBlckRelease = response.data.agents;			
			});
		}
		
	}
	$scope.updateQuotaData = function(invalid)
	{
		if(invalid)return;		
		if(new Date($scope.updateQuota.blockreleasefrmdt) > new Date($scope.updateQuota.blockreleasetodte)){
		  alert('End Date should be greater than or equal to start date');		  
		}
		else{
			$scope.updateQuota.operatorId = $scope.operatorId;
			$scope.updateQuota.servno = $scope.servno;
			$scope.updateQuota.updatedBy = $scope.agentId;
			$scope.updateQuota.updatedByName = $scope.agentName;
			$scope.updateQuota.seatsBlockRelease = $scope.seatsBcolkRelease;
			//console.log($scope.updateQuota,"-------------updateQuota");	
			$scope.updateblck = true;
			$http({	method : 'POST',url : $scope.url+ 'updateBlockRelase',data : $scope.updateQuota}).then(
				function successCallback(response) {
					$scope.updateblck = false;
					$scope.blockReleaseStatus = response.data;
					$scope.cancel();
					//console.log($scope.blockReleaseStatus,"---$scope.blockReleaseStatus");
					if($scope.blockReleaseStatus == 200)
					{
						alert('Updated Successfully !!');						
						$scope.getLayout($scope.from_id,$scope.to_id,$scope.j_date,$scope.servno);												
					}
					else{
						alert('Problem Occurred!!');
					}	
				}, function errorCallback(response) {
			});		
		}	
	}
	$scope.$watch('updateQuota.blockreleasetodte', function() {       
	   if($scope.updateQuota.blockreleasefrmdt>$scope.updateQuota.blockreleasetodte)
	   { alert('End Date should be greater than or equal to start date');  
		//$scope.updateQuota.blockreleasetodte = '';
		}
	});	
	$scope.seatsListBcolkRelease=[];
	$scope.isSelectedBcolkRelease = function(ind){
	return inArray(ind,$scope.seatsListBcolkRelease).status;
    }
	$scope.getSeatBlockRelease = function(val)
	{	
		var a = 0;		
		if($scope.seatsListBcolkRelease.length!=0)
		{			
			angular.forEach($scope.seatsListBcolkRelease,function(value,index){					
				if(value===val)
				{
					a=1;									
					$scope.seatsListBcolkRelease.splice(index, 1);					
				}					
			})			
		}
		if(a==0)
		{		
			$scope.seatsListBcolkRelease.push(val);			
		}		
		$scope.seatsBcolkRelease=$scope.seatsListBcolkRelease;		
	};
	/************************************** method for grabAndReleaseSeat popup END*********************/
    $scope.layout_assign = function(service, dtt)
    {
        window.open("Updations/vihicle_assignment", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
    }
	$scope.getModifyTicket = function()
	{
		window.open("Updations/change_tkt_status", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
	}
	$scope.getSeatDetails = function(seatDt)
	{
		//alert(seatDt);
		$.post('<?php echo base_url("booking/seat_options") ?>', {seat_name: seatDt, service_num: $scope.servno, journey_date: $scope.j_date}, function (res) {
		//alert(res);
            //window.open("booking/ticket_search?ticket=" + res, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, width=800, height=600");
            if (res != 0)
            {
                window.open("<?php echo base_url('booking/ticket_search?ticket='); ?>" + res, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
            }
        });
	}
	$scope.showpass = function (sno)
    {
       // alert(sno);//alert(serno);alert(jdate);alert(seat_status);alert(ishover);
        //if (ishover == '1' && seat_status == '1')
        //{
            // alert("if");
           // $("#dep1" + sno).toggle();
		   var serno = $scope.servno;
		   var jdate = $scope.j_date;
		   $.post('<?php echo base_url("booking/showPassDetail") ?>', {sno: sno, serno: serno, jdate: jdate}, function (res) {
                $("#"+sno).html(res);
                //alert(res);
            });
           
        //}
    }
	//block release function
	$scope.bloclRelease = function(blk_sno,blk_type)
	{			
		$http.get($scope.url+"grabAndReleaseSeat/"+$scope.operatorId+"/"+$scope.servno+"/"+$scope.j_date+"/"+blk_sno+"/"+$scope.agentId+"/"+blk_type).then(function(response) {
		
		$scope.grabAndReleaseSeatRes = response.data;
		//console.log($scope.grabAndReleaseSeatRes);
			if($scope.grabAndReleaseSeatRes.response['code'] == 200)
			{
				alert('Updated Successfully!!');
				$scope.getLayout($scope.from_id,$scope.to_id,$scope.j_date,$scope.servno);			
			}
			else{
				alert('Problem Occurred!!');
			}				
		});
	}
	//Stop Booking 
	/*$scope.stopBooking = function()
	{
		alert("Stop Booking");
		$http.get($scope.url+"stopBooking/"+$scope.operatorId+"/"+$scope.servno+"/"+$scope.j_date).then(function(response) {		
		$scope.stopBookingRes = response.data;
		console.log($scope.stopBookingRes);
			if($scope.stopBookingRes.response['code'] == 200)
			{
				alert('Updated Successfully!!');
				$scope.getLayout($scope.from_id,$scope.to_id,$scope.j_date,$scope.servno);			
			}
			else{
				alert('Problem Occurred!!');
			}				
		});
	}*/
});
function inArray(needle, ary) {
    if (ary instanceof Object) {
        for (var i in ary) {
            if (ary[i] === needle) {
                return {
                    status: true,
                    index: i
                };
            }
        }
    }
    return {
        status: false
    };
}
app.directive('datepicker', ['$parse', function ($parse) {
	var directiveDefinitionObject = {
		restrict: 'A',
		link: function postLink(scope, iElement, iAttrs) {
			iElement.datepicker({
				//dateFormat: 'dd-mm-yy',
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				onSelect: function (dateText, inst) {
					scope.$apply(function (scope) {
						$parse(iAttrs.ngModel).assign(scope, dateText);
					});
				}
			});
		}
	};
return directiveDefinitionObject;
}]);

</script>
<script>
function getPrint(h_sno,h_serno,h_jdate)
{
	//alert("print123  "+h_sno+"  "+h_serno+"  "+h_jdate);
	$.post('<?php echo base_url("booking/seat_options") ?>', {seat_name: h_sno, service_num: h_serno, journey_date: h_jdate}, function (res) {
		//alert(res);
            //window.open("booking/ticket_search?ticket=" + res, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, width=800, height=600");
            if (res != 0)
            {
                window.open("<?php echo base_url('booking/ticket_search?ticket='); ?>" + res, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
            }
        });
}
</script>

<div class="content-wrapper">
    <section class="main-content-bg">
      <main class="container-fluid">
	<div class="row">
    	<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 main-left pdg-rgt-15-to-991">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                	<ul class="bus-optns">
                    	<li>
                        	<label>From</label>
                            <select name="fromCity" id="fromCity"  ng-model="fromCity" ng-change="getToCities(fromCity)" >
								<option  value="">Select From City</option>
								<option ng-repeat="option in myData" value="{{option.city['cityid']}}">{{option.city['cityname']}}</option>
							</select>
                        </li>
                    	<li>
                        	<label>&nbsp;&nbsp;To&nbsp;&nbsp;</label>
                            <select name="toCity" id="toCity"  ng-model="toCity">
								<option  value="">Select To City</option>
								<option ng-repeat="option1 in getToCity" value="{{option1.city['cityid']}}">{{option1.city['cityname']}}</option>
							</select>
                        </li>
                    	<li>
                        	<label>DOJ</label>
                            <input type="text" class="srch-bus" name="onward_date" id="onward_date" ng-model="selectedOnward" datepicker ng-required="true" readonly=""/>
                        </li>
                    	<li>
                            <button ng-click="getServices(fromCity,toCity,selectedOnward)">Search</button>
                        </li>
                    </ul>
					<ul class="date-chng-optn">
						<li ng-repeat="dt in datearray">
							<span ng-if="selectedOnward == dt">
								<button style="background:#005575;color:#fff" ng-click="searchbusDate(dt)">{{dt | date:'MMM d'}}</button>
							</span>
							<span ng-if="selectedOnward !== dt">
								<button ng-click="searchbusDate(dt)">{{dt | date:'MMM d'}}</button>
							</span>
						</li>
					</ul>
                </div>
				
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bus-slctn-optns-bg">
					 <div class="bus-slctn-optns" ng-show="busrow">
							<ul>
								<li><label>Bus Number</label></li>
								<li><label>Bus Type</label></li>
								<li><label>Departure  Time</label></li>
								<li><label>Arrival Time</label></li>
								<li><label>Seats</label></li>
								<li><button>Fare</button></li>
							</ul>
					</div>
					<div ng-show="loadingbus" class="bus-slctn-optns" style="text-align:center"><button class="btn btn-success">Loading.....<i class="fa fa-spinner fa-spin"></i></button></div>
                    <div class="bus-slctn-optns" ng-show="busrow1" ng-class="{selectedbuslist: slist.service_number === idSelectedVote}" ng-repeat="slist in servicesList" ng-click="getLayout(slist.from_id,slist.to_id,slist.journey_date,slist.service_number,slist.currency)">
                    	<ul>
                        	<li class="srv-no"><label>{{slist.service_number}}</label></li>
                        	<li class="bus-mdl"><label>{{slist.bus_model}}</label></li>
                        	<li class="dep-time"><label>{{slist.dep_time}}</label></li>
                        	<li class="darr-time"><label>{{slist.arrival_time}}</label></li>
                        	<li class="dep-seats"><label>{{slist.available_seats}} seats</label></li>
                        	<li class="fare-st" ng-if="slist.bus_type == 'seater'"><button>{{slist.currency}} {{slist.seat_fare}}</button></li>
							<li class="fare-st" ng-if="slist.bus_type == 'sleeper'"><button>{{slist.currency}} {{slist.lb_fare}} / {{slist.ub_fare}}</button></li>
							<li class="fare-st" ng-if="slist.bus_type == 'seatersleeper'"><button>{{slist.currency}} {{slist.seat_fare}} / {{slist.lb_fare}} / {{slist.ub_fare}}</button></li>
                        </ul>
                    </div>                                      
                </div>
            </div>
            <!--<div id="bus-view-horizontal" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                	<div class="bus-bg">
                        <div class="bus-seat-sltn-2">
                            <ul>
                                <li class="sleeper"></li>
                                <li class="sleeper"></li>
                                <li class="sleeper"></li>
                                <li class="sleeper"></li>
                                <li class="sleeper"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="bus-seat-optns">
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                        <li><a href="#">asfdasfsdf</a></li>
                    </ul>
                </div>
            </div>-->
            <div id="bus-view-vertical" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" ng-show="lgnd" >
			
				<div ng-show="loading" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center" style="padding-top: 100px;">
				<button class="btn btn-success">Loading.....<i class="fa fa-spinner fa-spin"></i></button></div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center" ng-show="layoutloading">                	                      
					<span ng-repeat="sarr in seatArr">	
						<div ng-if="sarr.bus_type == 'sleeper' && slMcol != '1' ">
							<div class="bus-bg-vertical-str-slpr no-bg">  
								<div class="bus-seat-sltn-2">
								<p>Upper</p>
								<span ng-repeat="item in cols">
									<ul>									
									<span ng-repeat="itemc in rows">
										<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
										<div ng-if="item == sarrc.seat['col_id'] && itemc ==  sarrc.seat['row_id'] && sarrc.seat['number']!='GY'">
											<span ng-if="sarrc.seat['type'] == 'U'">
												<span ng-if="sarrc.seat['status'] == 'avail' ">	
												<!-----------------------         Head Office              ------------->
													<span ng-if="head_office_te_ses == 'yes'">							
														<span ng-if="sarrc.seat['available_for'] == 1 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
														<span ng-if="sarrc.seat['available_for'] == 2 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>							
														<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
													</span>
												<!-----------------------        Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
														<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
															<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
														</a>							
													</span>
												<!-----------------------      not  Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
														<span ng-if="sarrc.seat['show_quota']=='yes'">
															<span ng-if="sarrc.seat['available_type'] == agId">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] != agId">
																<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>
														<span ng-if="sarrc.seat['show_quota']!='yes'">
															<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>														
													</span>
													<!-----------------------        seat Avail end             ------------->
												</span>
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
													<span ng-if="agent_type_te_ses == '1'">
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"    >
															<span id="{{sarrc.seat['number']}}"></span>
															<li class="sleeper  ladies-bkd-clr" ng-click="getSeatDetails(sarrc.seat['number'])">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
													</span>	
												</span>						
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
													<span ng-if="agent_type_te_ses == '1'">
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
															<span id="{{sarrc.seat['number']}}"></span>
															<li  ng-click="getSeatDetails(sarrc.seat['number'])"class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
													</span>
												</span>
												<span ng-if="sarrc.seat['status'] == 'block'">
													<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
														<span id="{{sarrc.seat['number']}}"></span>
														<li class="sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
													</a>
												</span>
												<span ng-if="sarrc.seat['status'] == 'pend'">
												<a class="tooltip1" ng-mouseover="showpass(sarrc.seat['number'])"   >
													<span id="{{sarrc.seat['number']}}"></span>
													<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
												</a>
												</span>
											</span>
											</div>
											<span ng-if="sarrc.seat['number']=='GY'"><li style="width: 25px;height: 45px;border: 1px #fafafa solid;"></li></span>										
										
										</span>
										</span>								
									</ul>
									</span>	
								</div>
							</div>
							<div class="bus-bg-vertical-str-slpr no-bg">  
								<div class="bus-seat-sltn-2">
								<p> Lower </p>
								<span ng-repeat="item in cols">								
									<ul>
									<span ng-repeat="itemc in rows">									
										<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
											<div ng-if="item == sarrc.seat['col_id'] && itemc ==  sarrc.seat['row_id']">
											<span ng-if="sarrc.seat['type'] == 'L'">									
												<!--a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['fare'],sarrc.seat['service_tax_amount'])"  >
													<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
												</a-->
												<span ng-if="sarrc.seat['status'] == 'avail' ">	
												<!-----------------------         Head Office              ------------->
													<span ng-if="head_office_te_ses == 'yes'">							
														<span ng-if="sarrc.seat['available_for'] == 1 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
														<span ng-if="sarrc.seat['available_for'] == 2 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>							
														<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
															<a class="tooltip2">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
													</span>
												<!-----------------------        Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
														<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
															<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
														</a>							
													</span>
												<!-----------------------      not  Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
														<span ng-if="sarrc.seat['show_quota']=='yes'">
															<span ng-if="sarrc.seat['available_type'] == agId">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] != agId">
																<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>
														<span ng-if="sarrc.seat['show_quota']!='yes'">
															<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>														
													</span>
													<!-----------------------        seat Avail end             ------------->
												</span>	
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
													<span ng-if="agent_type_te_ses == '1'">
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"    >
															<span id="{{sarrc.seat['number']}}"></span>
															<li class="sleeper  ladies-bkd-clr" ng-click="getSeatDetails(sarrc.seat['number'])">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
													</span>	
												</span>						
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
													<span ng-if="agent_type_te_ses == '1'">
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
															<span id="{{sarrc.seat['number']}}"></span>
															<li  ng-click="getSeatDetails(sarrc.seat['number'])"class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
													</span>
												</span>
												<span ng-if="sarrc.seat['status'] == 'block'">
													<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
														<span id="{{sarrc.seat['number']}}"></span>
														<li class="sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
													</a>
												</span>
												<span ng-if="sarrc.seat['status'] == 'pend'">
												<a class="tooltip1" ng-mouseover="showpass(sarrc.seat['number'])"   >
													<span id="{{sarrc.seat['number']}}"></span>
													<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
												</a>
												</span>
											</span>
											</div>
										</span>
										<span ng-if="emptySeats[item+'-'+itemc]"><li style="width: 28px;height: 52px;border: 1px #fafafa solid;"></li></span>
										</span>
									</ul>
									</span>
								</div>
							</div>								
						</div>
						<!-----------------------     sleeper 2+1 end  ------------->
						<div ng-if="sarr.bus_type == 'sleeper' && slMcol == '1' ">
							<div class="bus-bg-vertical-str-slpr no-bg pdg-8">  
								<div class="bus-seat-sltn-2 horizontal-sleeper-rw">
									<ul>
									Upper
										<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
											<span ng-if="sarrc.seat['type'] == 'U'">
												<!--a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['fare'],sarrc.seat['service_tax_amount'])"  >
													<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
												</a-->
											
												<span ng-if="sarrc.seat['status'] == 'avail' ">	
												<!-----------------------         Head Office              ------------->
													<span ng-if="head_office_te_ses == 'yes'">							
														<span ng-if="sarrc.seat['available_for'] == 1 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
														<span ng-if="sarrc.seat['available_for'] == 2 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>							
														<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
													</span>
												<!-----------------------        Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
														<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
															<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
														</a>							
													</span>
												<!-----------------------      not  Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
														<span ng-if="sarrc.seat['show_quota']=='yes'">
															<span ng-if="sarrc.seat['available_type'] == agId">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] != agId">
																<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>
														<span ng-if="sarrc.seat['show_quota']!='yes'">
															<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>														
													</span>
													<!-----------------------        seat Avail end             ------------->
												</span>
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
													<span ng-if="agent_type_te_ses == '1'">
														<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper  ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
													</span>	
												</span>													
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
													<span ng-if="agent_type_te_ses == '1'">
														
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
													</span>
												</span>
												<span ng-if="sarrc.seat['status'] == 'block'">
													<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
														<span id="{{sarrc.seat['number']}}"></span>
														<li class="horizontal-sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
													</a>
												</span>
												<span ng-if="sarrc.seat['status'] == 'pend'">
													<span ng-if="agent_type_te_ses == '1'">														
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
													</span>											
												</span>
											</span>
										</span>	
									</ul>
								</div>
							</div>
							<div class="bus-bg-vertical-str-slpr bus-bg-vertical-horizontal-str-slpr pdg-8">  
								<div class="bus-seat-sltn-2 horizontal-sleeper-rw">									
									<ul>
									Lower
										<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
											<span ng-if="sarrc.seat['type'] == 'L'">									
												<!--a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['fare'],sarrc.seat['service_tax_amount'])"  >
													<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
												</a-->
												<span ng-if="sarrc.seat['status'] == 'avail' ">	
												<!-----------------------         Head Office              ------------->
													<span ng-if="head_office_te_ses == 'yes'">							
														<span ng-if="sarrc.seat['available_for'] == 1 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
														<span ng-if="sarrc.seat['available_for'] == 2 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>							
														<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
															<a class="tooltip3">
																<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
															</a>
														</span>
													</span>
												<!-----------------------        Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
														<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
															<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
														</a>							
													</span>
												<!-----------------------      not  Show aiail seat              ------------->
													<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
														<span ng-if="sarrc.seat['show_quota']=='yes'">
															<span ng-if="sarrc.seat['available_type'] == agId">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] != agId">
																<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>
														<span ng-if="sarrc.seat['show_quota']!='yes'">
															<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="horizontal-sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>
														</span>														
													</span>
													<!-----------------------        seat Avail end             ------------->
												</span>	
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
													<span ng-if="agent_type_te_ses == '1'">
														<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper  ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
													</span>	
												</span>													
												<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
													<span ng-if="agent_type_te_ses == '1'">
														
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
													</span>
												</span>
												<span ng-if="sarrc.seat['status'] == 'block'">
													<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
														<span id="{{sarrc.seat['number']}}"></span>
														<li class="horizontal-sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
													</a>
												</span>
												<span ng-if="sarrc.seat['status'] == 'pend'">
													<span ng-if="agent_type_te_ses == '1'">														
														<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
															<span id="{{sarrc.seat['number']}}"></span>
															<li ng-click="getSeatDetails(sarrc.seat['number'])" class="horizontal-sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
														</a>
													</span>
													<span ng-if="agent_type_te_ses != '1'">
														<li class="horizontal-sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
													</span>											
												</span>
											</span>	
										</span>
									</ul>
								</div>
							</div>								
						</div>
						<!-------------------  SeaterSleeper Code start-------------------->
						<div ng-if="sarr.bus_type == 'seatersleeper'">
							<div class="bus-bg-vertical-str-slpr no-bg">  	
								<div class="bus-seat-sltn-2">	
									<p>Upper</p>
									<span ng-repeat="item in cols">
										<ul>
											<span ng-repeat="itemc in rows">
												<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
													<div ng-if="item == sarrc.seat['col_id'] && itemc ==  sarrc.seat['row_id']">
														<span ng-if="sarrc.seat['type'] == 'U'">
															<span ng-if="sarrc.seat['status'] == 'avail' ">	
													<!-----------------------         Head Office              ------------->
																<span ng-if="head_office_te_ses == 'yes'">							
																	<span ng-if="sarrc.seat['available_for'] == 1 ">
																		<a class="tooltip2">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li  class="sleeper  branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])" >{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																	<span ng-if="sarrc.seat['available_for'] == 2 ">
																		<a class="tooltip2">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li class="sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>							
																	<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
																		<a class="tooltip2">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																			<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																</span>
													<!-----------------------        Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
																	<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																		<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																	</a>							
																</span>
													<!-----------------------      not  Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
																	<span ng-if="sarrc.seat['show_quota']=='yes'">
																		<span ng-if="sarrc.seat['available_type'] == agId">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] != agId">
																			<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>
																	<span ng-if="sarrc.seat['show_quota']!='yes'">
																		<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																			<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>														
																</span>
																	<!-----------------------        seat Avail end             ------------->
															</span>	
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
																<span ng-if="agent_type_te_ses == '1'">
																	<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper  ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																</span>	
															</span>						
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
																<span ng-if="agent_type_te_ses == '1'">														
																	<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																</span>
															</span>
															<span ng-if="sarrc.seat['status'] == 'block'">
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li class="sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['status'] == 'pend'">
															<span ng-if="agent_type_te_ses == '1'">														
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
															</span>	
															</span>	
														</span>
													</div>
												</span>
												<span ng-if="emptySeats[item+'-'+itemc]"><li style="width: 25px;height: 35px;border: 1px #fafafa solid;"></li></span>
											</span>
										</ul>
									</span>
								</div>
							</div>
							<div class="bus-bg-vertical-str-slpr bus-bg-vertical-horizontal-str-slpr no-bg">  
								<div class="bus-seat-sltn-2">	
									<p>Lower</p>
									<span ng-repeat="item in cols">
										<ul>
											<span ng-repeat="itemc in rows">
												<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
													<div ng-if="item == sarrc.seat['col_id'] && itemc ==  sarrc.seat['row_id']">
														<span ng-if="sarrc.seat['type'] == 'L:b'">
															<span ng-if="sarrc.seat['status'] == 'avail' ">	
													<!-----------------------         Head Office              ------------->
																<span ng-if="head_office_te_ses == 'yes'">							
																	<span ng-if="sarrc.seat['available_for'] == 1 ">
																		<a class="tooltip2" >
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li class="sleeper branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																	<span ng-if="sarrc.seat['available_for'] == 2 ">
																		<a class="tooltip2">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li class="sleeper agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>							
																	<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
																		<a class="tooltip2">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																			<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																</span>
													<!-----------------------        Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
																	<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																		<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																	</a>							
																</span>
													<!-----------------------      not  Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
																	<span ng-if="sarrc.seat['show_quota']=='yes'">
																		<span ng-if="sarrc.seat['available_type'] == agId">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] != agId">
																			<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>
																	<span ng-if="sarrc.seat['show_quota']!='yes'">
																		<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																			<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>														
																</span>
														<!-----------------------        seat Avail end             ------------->
															</span>	
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
																<span ng-if="agent_type_te_ses == '1'">
																	<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper  ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="sleeper ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																</span>	
															</span>						
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
																<span ng-if="agent_type_te_ses == '1'">														
																	<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="sleeper" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																</span>
															</span>
															<span ng-if="sarrc.seat['status'] == 'block'">
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li class="sleeper" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['status'] == 'pend'">
															<span ng-if="agent_type_te_ses == '1'">														
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li ng-click="getSeatDetails(sarrc.seat['number'])" class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="sleeper" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
															</span>	
															</span>
														</span>
														<span ng-if="sarrc.seat['type'] == 'L:s'">															
															<span ng-if="sarrc.seat['status'] == 'avail' ">	
													<!-----------------------         Head Office              ------------->
																<span ng-if="head_office_te_ses == 'yes'">							
																	<span ng-if="sarrc.seat['available_for'] == 1 ">
																		<a class="tooltip3" >
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li class="seater branch-clr" class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																	<span ng-if="sarrc.seat['available_for'] == 2 ">
																		<a class="tooltip3">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																			<li class="seater agent-clr" class="sleeper" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
																		</a>
																	</span>							
																	<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
																		<a class="tooltip3">
																			<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																			<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'"ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																</span>
													<!-----------------------        Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
																	<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																		<li class="seater" ng-class="isSelected(sarrc.seat['number'],sarrc.seat['service_tax_amount']) && 'selected'">{{sarrc.seat['number']}}</li>
																	</a>							
																</span>
													<!-----------------------      not  Show aiail seat              ------------->
																<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
																	<span ng-if="sarrc.seat['show_quota']=='yes'">
																		<span ng-if="sarrc.seat['available_type'] == agId">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] != agId">
																			<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>
																	<span ng-if="sarrc.seat['show_quota']!='yes'">
																		<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																			<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																				<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																			</a>
																		</span>
																		<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																			<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																		</span>
																	</span>														
																</span>
														<!-----------------------        seat Avail end             ------------->
															</span>	
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
																<span ng-if="agent_type_te_ses == '1'">
																	<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater  ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="seater ladies-bkd-clr" >{{sarrc.seat['number']}}</li>
																</span>
															</span>						
															<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
																<span ng-if="agent_type_te_ses == '1'">													
																	<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																		<span id="{{sarrc.seat['number']}}"></span>
																		<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="agent_type_te_ses != '1'">
																	<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																</span>
															</span>
															<span ng-if="sarrc.seat['status'] == 'block'">
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li class="seater" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="sarrc.seat['status'] == 'pend'">																									
															<span ng-if="agent_type_te_ses == '1'">														
																<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="seater" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
															</span>	
															</span>
														</span>
													</div>
												</span>	
												<span ng-if="emptySeats[item+'-'+itemc]"><li style="width: 25px;height: 35.5px;border: 1px #fafafa solid;"></li></span>
											</span>
										</ul>
									</span>
								</div>
							</div>
						</div>
						<!-------------------  SeaterSleeper Code end-------------------->
						<!-------------------  seater Code start-------------------->
						<div ng-if="sarr.bus_type == 'seater'">
							<div class="bus-bg-vertical-str-slpr">  	
								<div class="bus-seat-sltn-2">		
									<span ng-repeat="item in cols">
										<ul>
											<span ng-repeat="itemc in rows">
												<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
													<div ng-if="item == sarrc.seat['col_id'] && itemc ==  sarrc.seat['row_id']">
														<span ng-if="sarrc.seat['number'] == 'GY'"><li style="width: 28px;height: 23px;border: 0px #fafafa solid;margin-bottom: 5px;color: #fff;">T</li></span>
														<span ng-if="sarrc.seat['number'] != 'GY'">
														<span ng-if="sarrc.seat['status'] == 'avail' ">	
														<!-----------------------         Head Office              ------------->
															<span ng-if="head_office_te_ses == 'yes'">							
																<span ng-if="sarrc.seat['available_for'] == 1 ">
																	<a class="tooltip3">
																		<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																		<li class="seater branch-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
																	</a>
																</span>
																<span ng-if="sarrc.seat['available_for'] == 2 ">
																	<a class="tooltip3">
																		<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'R')">Release({{sarrc.seat['number']}})</button></span>-->
																		<li class="seater agent-clr" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
																	</a>
																</span>							
																<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 ">
																	<a class="tooltip3">
																		<!--<span><button class="tooltip-btn-prnt" ng-click="bloclRelease(sarrc.seat['number'],'B')">Block({{sarrc.seat['number']}})</button></span>-->
																		<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'" ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >{{sarrc.seat['number']}}</li>
																	</a>
																</span>
															</span>
														<!-----------------------        Show aiail seat              ------------->
															<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']=='yes'">
																<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																	<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																</a>							
															</span>
														<!-----------------------      not  Show aiail seat              ------------->
															<span ng-if="head_office_te_ses != 'yes' && sarrc.seat['show_avail_seat']!='yes'">
																<span ng-if="sarrc.seat['show_quota']=='yes'">
																	<span ng-if="sarrc.seat['available_type'] == agId">
																		<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																			<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																	<span ng-if="sarrc.seat['available_type'] != agId">
																		<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																	</span>
																</span>
																<span ng-if="sarrc.seat['show_quota']!='yes'">
																	<span ng-if="sarrc.seat['available_type'] =='' && sarrc.seat['available_type'] == 0 ">
																		<a ng-click="getSeat(sarrc.seat['number'],sarrc.seat['base_fare'],sarrc.seat['service_tax_amount'],sarrc.seat['convenience_charge1'],sarrc.seat['cgst_amount'],sarrc.seat['sgst_amount'],sarrc.seat['tcs_amount'])"  >
																			<li class="seater" ng-class="isSelected(sarrc.seat['number']) && 'selected'">{{sarrc.seat['number']}}</li>
																		</a>
																	</span>
																	<span ng-if="sarrc.seat['available_type'] !='' && sarrc.seat['available_type'] != 0 ">
																		<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																	</span>
																</span>														
															</span>
															<!-----------------------        seat Avail end             ------------->
														</span>	
														<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == true">
															<span ng-if="agent_type_te_ses == '1'">
																<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])" >
																	<span id="{{sarrc.seat['number']}}"></span>
																	<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater ladies-bkd-clr">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="seater ladies-bkd-clr">{{sarrc.seat['number']}}</li>
															</span>
														
														</span>						
														<span ng-if="sarrc.seat['status'] == 'confirm' && sarrc.seat['is_ladies_seat'] == false">
															<span ng-if="agent_type_te_ses == '1'">
																<a  class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])"  >
																<span id="{{sarrc.seat['number']}}"></span>
																	<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
																</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="seater" style="background-color: #C0C0C0;">{{sarrc.seat['number']}}</li>
															</span>	
														</span>
														<span ng-if="sarrc.seat['status'] == 'block'">
															<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																<span id="{{sarrc.seat['number']}}"></span>
																<li class="seater" style="background-color: #53CFCF;">{{sarrc.seat['number']}}</li>
															</a>
														</span>
														<span ng-if="sarrc.seat['status'] == 'pend'">
															<span ng-if="agent_type_te_ses == '1'">														
															<a class="tooltip1"  ng-mouseover="showpass(sarrc.seat['number'])">
																<span id="{{sarrc.seat['number']}}"></span>
																<li ng-click="getSeatDetails(sarrc.seat['number'])" class="seater" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
															</a>
															</span>
															<span ng-if="agent_type_te_ses != '1'">
																<li class="seater" style="background-color: #E6E68A;">{{sarrc.seat['number']}}</li>
															</span>	
														</span>	
														</span>
													</div>
												</span>
												<!--<span ng-if="emptySeats[item+'-'+itemc]"><li style="width: 28px;height: 23px;border: 1px #fafafa solid;"></li></span>-->
											</span>
										</ul>
									</span>
								</div>
							</div>
						</div>
						<!-------------------  seater Code end-------------------->
					</span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="full-row">
						<div class="dropdown-a1 text-center">
						  <span class="glyphicon glyphicon-th-large" aria-hidden=true></span><br /><p>Legend</p>
						  <div class="dropdown-content-a1">
							<ul class="legand-box">
								<li class="nme">Available</li>
								<li class="nme1">Unavailable</li>
								<li class="nme2">Ladies</li>
								<li class="nme3">Selected</li>
								<li class="nme4">Branch</li>
								<li class="nme5">Agent</li>
								<li class="nme6">Pending</li>
								<li class="nme7">Blocked</li>
							</ul>
						  </div>
						</div>
						
						<ul class="bus-seat-optns">

						<?php 
						if ($grabrelease == 'yes') {
							echo '
							
							<li><a title="Block Seats" ng-click="layout_grab_release_popup(servno,j_date)">
								<span class="fa fa-table" aria-hidden=true></span>
								<p>Block</p>
								</a>
							</li>
							
							
							';
						}
						if ($changeprice == 'yes') {
							echo'<li><a title="Price Change" ng-click="layout_change_price(servno,from_id,to_id,j_date)">
									<span class="fa fa-inr" aria-hidden=true></span>
									<p>Price</p>
									</a>
								</li>';
						}					
						if ($boardingchart == 'yes') {	
							echo '<li><a title="Boarding Chart" ng-click="Report(servno,j_date)">
									<span class="fa fa-ticket" aria-hidden=true></span>
									<p>Chart</p>
									</a>
								</li>';
						}
						if ($vehicle_assignment == 'yes') {
							echo '<li><a title="Vehicle Assign" ng-click="layout_assign()">
									<span class="fa fa-users" aria-hidden=true></span>
									<p>Assign</p>
									</a>
								  </li>';
						}
						/*if ($grabrelease == 'yes') {
							echo '<li><a ng-click="layout_grab_release(servno,j_date)">
									<span class="glyphicon glyphicon-arrow-down" aria-hidden=true></span>
									<p>Block</p>
									</a>
								  </li>';
						} */ 
						if ($ticket_modify == 'yes') {                           
                            echo '<li><a title="Modify Ticket" ng-click="getModifyTicket()">
									<span class="fa fa-pencil-square" aria-hidden=true></span>
									<p>Modify<br />Ticket</p>
									</a>
								  </li>';                            
                        }
						/*if ($ho == 'yes') {                           
                            echo '<li><a ng-click="stopBooking()">
									<span class="glyphicon glyphicon-arrow-down" aria-hidden=true></span>
									<p>Stop Booking</p>
									</a>
								  </li>';                            
                        }*/
						?>						
                    </ul>
					</div>
					
					<form class="form-horizontal" name="alertNameForm" ng-submit="getBooking(saveTicket)">
					<div class="full-row">
                        <ul class="psgr-dtls">
                            <li>
                                <label>Name<span class="star">*</span></label>
                                <input name="pname" id="pname" ng-model="saveTicket.pname" type="text" required />
                            </li>                            
                            <li>
                                <label>Mobile No.<span class="star">*</span></label>
                                <!--input name="pmobile" id="pmobile" ng-model="saveTicket.pmobile" type="text" required /-->	
								<input id="pmobile" name="pmobile" ng-model="saveTicket.pmobile" required type="tel" style="width: 99%;border: 1px solid #cccccc;padding-right: 0px;padding-left: 36px;margin-left: 0;">								
								
                            </li>
							<li>
                                <label>ID Type<span class="star">*</span></label>                                
								<select name="idtype" id="idtype" required="required" style="width: 63%;border: 1px solid #cccccc;padding: 3px 5px;"
								ng-model="saveTicket.idtype" >
									<option value="" >ID Type</option>
									<option ng-repeat="blist in idTypes" value="{{blist.id}}" >{{blist.name}}</option>
								</select>
                            </li>
							<li>
                                <label>ID Number<span class="star">*</span></label>
                                <input name="idnum" id="idnum" required="required" ng-model="saveTicket.idnum" type="text" />
                            </li>
							<li>
                                <label>Email ID</label>
                                <input name="pemail" id="pemail" ng-model="saveTicket.pemail" type="text" />
                            </li>
                            <li>
                                <label>Emergency Ph.</label>
                                <input name="palt" id="palt" ng-model="saveTicket.palt" type="text" />
                            </li>
                        </ul>
					</div>
				</div>
            </div>
        </div>
    	<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 main-right">
		<div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?php if ($ho == "yes") { ?>
            	<div class="row dsply-blck-1 hidden-xs">
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pdg-rgt-0 pdg-rgt-15-to-767">
                    	<div class="ttl">
                        	<p>Total</p>
                            <h1>{{seatcountDetail.total_seats}}</h1>
                        </div>
                    	<div class="bkd">
                        	<p>Booked</p>
                            <h1><?php if ($ho == "yes") { ?>{{seatcountDetail.booked_seats}}<?php } else{?><?php }?></h1>
                        </div>
                    	<!--div class="qut">
                        	<p>Blocked</p>
                            <h1><?php if ($ho == "yes") { ?>{{seatcountDetail.quota_seats}}<?php } else{?><?php }?></h1>
                        </div-->
                    	<div class="avl">
                        	<p>Available</p>
                            <h1><?php if ($ho == "yes") { ?>{{seatcountDetail.available_seats}}<?php } else{?><?php }?></h1>
                        </div>
                    </div>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    	<ul class="dtls-view">
                        	<li>API Online  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.api_seats}}<?php } else{?>0<?php }?></li>
                        	<li>Branch  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.branch_seats}}<?php } else{?>0<?php }?></li>
                        	<li>Agent  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.agent_seats}}<?php } else{?>0<?php }?></li>
                        	<!--li>Phone  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.phone_seats}}<?php } else{?>0<?php }?></li-->
                        	<!--li>Quota Bkd  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.booked_seats}}<?php } else{?>0<?php }?></li-->
                        	<li>Website  - </li>
                        	<li><?php if ($ho == "yes") { ?>{{seatcountDetail.website_seats}}<?php } else{?>0<?php }?></li>
                        </ul>
                    </div>
                </div>
				<?php } else{?><?php }?>
                <div class="row">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="full-row st-nmbrs" >
                            <div class="seat-no-prnt" ng-repeat="seat in seats">
                                <ul>
                                    <li>{{seat}}</li>
                                    <li><img src="images/right-arrow.png" alt="" /></li>
                                    <li>
										<select name="gender{{seat}}" id="gender{{seat}}" ng-model="saveTicket.gender[seat]" required >
											<option value="M" selected="selected" ng-selected="true">M</option>
											<option value="F">F</option>
										</select>
									</li>
                                </ul>
                            </div>                            
                        </div>
                    </div>    
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">            	
                        <ul class="psgr-pk-drp-dtls">
                            <li>
                            	<select name="boardpoint" id="boardpoint" required="required"  ng-model="saveTicket.boardpoint" ng-change="getLandmark(saveTicket.boardpoint)">
									<option value="" >Boarding</option>
									<option ng-repeat="blist in boardDrop" ng-if="blist.boarding_point['type'] == 'board'" value="{{blist.boarding_point['bpid']}}" >{{blist.boarding_point['pickup_point']}} - {{blist.boarding_point['time']}}</option>
								</select>
                                <p>{{lanmark}}</p>
                            </li>
                            <li>
                            	<select name="droppoint" id="droppoint" required="required"  ng-model="saveTicket.droppoint" ng-change="getLandmark_dep(saveTicket.droppoint)">
									<option value="" >Dropping</option>
									<option ng-repeat="blist in boardDrop" ng-if="blist.boarding_point['type'] == 'drop'" value="{{blist.boarding_point['bpid']}}">{{blist.boarding_point['pickup_point']}} - {{blist.boarding_point['time']}}</option>
								</select>
                                <p>{{lanmark_dep}}</p>
                            </li>
                        </ul>
                    </div>
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">            	
                        <ul class="fnl-tckt-dtls">
                        	<li class="bse-fre">
                            	<label>Base Fare :</label>
                                <h4>{{fareVar}}</h4>
                            </li>
							<?php 
							if ($agent_charge == "yes") {
								echo'
								<li class="agnt-chrg">
									<label>Agent Charge :</label>
									<input name="agentcharge" id="agentcharge" ng-change="getAgTotalAmt(agentcharge)" ng-model="agentcharge" style="width:35px;border: 1px solid #337ab7; height: 20px;" type="number" />
								</li>';
							}
							else
							{
								echo'
								<li class="agnt-chrg">
									<label>Agent Charge :</label>
									<h4>0</h4>
								</li>';
							}
							?>							
                        	<li class="fnl-ttl">
                            	<label>VAT :</label>
                                <h4>{{service_tax_amountVar}}</h4>
                            </li>
                        	<li class="grnd-ttl">
                            	<label>Grand Total :</label>
                            	<span>{{currency}}   </span>
                             <h4><span> {{totFareVar}}</span></h4>
                            </li>
                        </ul>
                        <ul class="cnfm-tckt">
                        	<li><p>Mode : </p>
								<label>
									<select name="paytype" id="paytype" required  ng-model="saveTicket.paytype" ng-change="pay_type(saveTicket.paytype)">
										<option value="" >Select</option>
										<?php
											echo '<option value="bycash" selected="selected" ng-selected="true">By Cash</option>';
										if ($by_phone == "yes") {
											echo '<!--<option value="byphone">By Phone</option>-->';
										}
										if ($by_agent == "yes") {
											echo '<option value="byagent">By Agent</option>';
										}
										if ($by_phone_agent == "yes") {
											echo '<!--option value="byphoneagent">By Phone Agent</option-->';
										}
										if ($by_employee == "yes") {
											echo '<!--option value="byemployee">By Employee</option-->';
										}
										?>
									</select>
								</label>
							</li>						
                        </ul>
						<ul class="mode-cnt-dsply">                        	
                        	<li id="pay"></li>
                        </ul>
						<ul class="cnfm-tckt-btn">                        	
                        	<li><button type="submit" id="bookticket" >Confirm Ticket</button></li>
                        </ul>
                    </div>
                </div>
				</form>
            </div>
        </div>
		</div>
    </div>
	<!-- Menu Add Form  dialog-->
<div class="modal fade" id="cityForm" tabindex="-1" role="dialog" aria-labelledby="myusersLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" ng-click="cancel()"	data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myusersLabel"> Grab & Release Seats - &nbsp&nbsp&nbsp&nbsp Service Num : <b>{{servno}}</b></h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" ng-submit="updateQuotaData(updateQuota1Form.$invalid)" autocomplete="off" name="updateQuota1Form">					
					<div class="form-group">
						<label for="userid" class="col-sm-4 control-label"> Seats<span class="star">*</span>  </label>						
						<div class="col-sm-6">
							<select  ng-model="updateQuota.seats" name="seats" ng-change="getLayoutBlckRel(updateQuota.seats)"  class="form-control val-field" required="required" >
								<option value="">------Select------</option>
								<option value="all">All Seats</option>
								<option value="individual">Individual Seat</option>
							</select> 					
						</div>																	
				    </div>
					<div class="form-group">
						<label for="userid" class="col-sm-4 control-label">Block or Release Type<span class="star">*</span> : </label>						
						<div class="col-sm-6">
							<select  ng-model="updateQuota.blockrelease" name="blockrelease"  class="form-control val-field" required="required" >
								<option value="">------Select------</option>
								<option value="block">Block</option>
								<option value="release">Release</option>
							</select> 					
						</div>																	
				    </div>	
					<div class="form-group">
						<label for="userid" class="col-sm-4 control-label">Agent Type<span class="star">*</span> : </label>						
						<div class="col-sm-6">
							<select  ng-model="updateQuota.agenttype" name="agenttype" ng-change="getAgentsList(updateQuota.agenttype)"  class="form-control val-field" required="required" ng-disabled="updateQuota.blockrelease==undefined" >
								<option value="">------Select------</option>
								<option value="branch">Branch</option>
								<option value="agent">Agent</option>
								<option ng-if="updateQuota.blockrelease=='release'" value="opentoall">Open to All</option>
							</select> 					
						</div>																	
				    </div>
					<div class="form-group">
						<label for="userid" class="col-sm-4 control-label">Name <span class="star">*</span> : </label>						
						<div class="col-sm-6">
							<select  ng-model="updateQuota.agname" name="agname" ng-options="x.id as x.name for x in agentsListBlckRelease" class="form-control val-field" required="required" >
								<option value="">------Select------</option>								
							</select> 					
						</div>																	
				    </div>					
					<div class="form-group">
						<label for="userid" class="col-sm-4 control-label">Date From<span
							class="star">*</span> :
						</label>						
						<div class="col-sm-3">
							<input type="text" class="form-control val-field"  name="date_br_frm" id="date_br_frm" ng-model="updateQuota.blockreleasefrmdt" datepicker ng-required="true" readonly="" />					
						</div>	
						<label for="userid" class="col-sm-2 control-label">Date To<span
							class="star">*</span> :
						</label>						
						<div class="col-sm-3">
							<input type="text" class="form-control val-field"  name="date_br_to" id="date_br_to" ng-model="updateQuota.blockreleasetodte" datepicker ng-required="true" readonly="" />					
						</div>						
				   </div>
				  
					<div class="form-group" ng-show="blckRelLay">
						<label for="userid" class="col-sm-4 control-label">Layout Seats<span
							class="star">*</span> :
						</label>
						<div class="col-sm-8">
						<span ng-repeat="sarr in seatArrBlockRelease">							
							<span ng-repeat="sarrc in sarr.coach_layout['seat_details']">
								<span ng-if="sarrc.seat['number']!='GY'">
									<span ng-if="sarrc.seat['available_for'] != 1 && sarrc.seat['available_for'] != 2 " >
										<span class="pop-up-seat-lyout" ng-class="isSelectedBcolkRelease(sarrc.seat['number']) && 'selectedblck'" ng-click="getSeatBlockRelease(sarrc.seat['number'])">
											{{sarrc.seat['number']}}												
										</span>
									</span>
									<span ng-if="sarrc.seat['available_for'] == 1 || sarrc.seat['available_for'] == 2 ">
										<span class="pop-up-seat-lyout-bgclr" ng-class="isSelectedBcolkRelease(sarrc.seat['number']) && 'selectedblck'" ng-click="getSeatBlockRelease(sarrc.seat['number'])">
											{{sarrc.seat['number']}}												
										</span>
									</span>
								</span>	
							</span>
						</span>	
						</div>						
					</div>
					<div class="form-group">
						<label for="page" class="col-sm-4 control-label">&nbsp;</label>
						<div class="col-sm-6">
							<button type="submit" class="btn btn-success btn-xs" ng-disabled="updateblck"><i class="fa fa-floppy-o" aria-hidden="true"></i> Update </button>
							<button type="button" class="btn btn-warning btn-xs" ng-click="reset()"><i class="fa fa-repeat" aria-hidden="true" ></i> Reset</button>
							<button type="button" class="btn btn-danger btn-xs" ng-click="cancel()" ><i class="fa fa-ban" aria-hidden="true"></i> Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
</main>
</div>


    </section>
</div>

 <!-- Load jQuery from CDN so can run demo immediately -->
 
  <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script-->
  <script src="<?php echo base_url('js/intlTelInput.js'); ?>"></script>
  <script>
    $("#pmobile").intlTelInput({
      // allowDropdown: false,
      // autoHideDialCode: false,
      // autoPlaceholder: "off",
      // dropdownContainer: "body",
      // excludeCountries: ["us"],
      // formatOnDisplay: false,
      // geoIpLookup: function(callback) {
      //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
      //     var countryCode = (resp && resp.country) ? resp.country : "";
      //     callback(countryCode);
      //   });
      // },
      // hiddenInput: "full_number",
      // initialCountry: "auto",
      // localizedCountries: { 'de': 'Deutschland' },
      // nationalMode: false,
      // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
      // placeholderNumberType: "MOBILE",
      // preferredCountries: ['cn', 'jp'],
      // separateDialCode: true,
      utilsScript: "<?php echo base_url('js/utils.js');?>"
    });
  </script>
