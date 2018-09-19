<script type="text/javascript">
    $(function()
    {
        $("#txtdate").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false, minDate: 0,"autoclose": true});				
    });
</script>
<script>
    $(document).ready(function() {
        $('#subb').click(function() {            
            var service = $('#service').val();
			var txtdate = $('#txtdate').val();
						
            if (service == 0)
            {
                alert("Please Provide Service Number");
                $("#service").focus();
                return false;
            }            
            else
            {
                $("#subb").attr('disabled', true);
                $("#subb").val("Please Wait...");
                $.post("booking/GetServiceList", {serno: service, txtdate:txtdate}, function(res) {
                    //alert(res);
                    if (res == 0)
                    {
                        $('#content').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
                        $("#subb").attr('disabled', false);
                        $("#subb").val("submit");
                    }
                    else
                    {
                        $("#subb").attr('disabled', false);
                        $("#subb").val("submit");
                        $('#content').html(res);
                    }
                });
            }
        });
    });
	
	function get_responce()
    {
        var type = $("#type").val();
        var token = $("#token").val();
        var service_no = $("#service_no").val();		
        var dtt = $("#dtt").val();		
		var text = "";
		
        if (type != '') {
			$("#res1").empty();
			if(type == "all")
			{
				text = "Searching...";
			}
			else
			{
				text = "Please Wait Layout Is Loading...";
			}
			
			$("#load").html(text);
			$("#load").show();			
            $.post('<?php echo base_url('booking/layout_grab_release1'); ?>',
                    {
                        type: type,
                        token: token,
                        service_no: service_no,						
                        dtt: dtt
                    }, function (res)
            {

                $("#load").empty();
				$("#load").hide();
				$("#res1").html(res);
            });

        } else {

            //alert('Kindly select type');
			$("#res1").html('');
        }
    }
	function hideopentoall(){
	var block_type = $('#block_type').val();
	//alert(block_type);
	if(block_type =='block'){
		
		$('#opentoall').hide();
	}else{
		$('#opentoall').show();
	}
	}	
	function Updateallseats() {
		var type = $("#type").val();
        var token = $("#token").val();
        var service_no = $("#service_no").val();		
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
		var block_type = $('#block_type').val();
        var agent_id = $('#agent_id').val();
		
		if (from_date == "")
        {
            alert("Please Select From Date");
            $("#from_date").focus();
            return false;
        }			
		if (to_date == "")
        {
            alert("Please Select To Date");
            $("#to_date").focus();
            return false;
        }
		if(from_date > to_date)
		{
			alert("To Date Must be Greater than From Date");
            $("#to_date").focus();
            return false;
		}
		if (block_type == "")
        {
            alert("Please Select Type");
            $("#block_type").focus();
            return false;
        }
		if(block_type == 'block' && agent_id == ""){
			
            alert("Please Select Agent");
            $("#agent_id").focus();
            return false;
        	
		}		
		
		else
		{
			var r = confirm("Are sure,you want Update Quota!");
			if (r == true)
            {
				$('#allseats').html('Please wait...');
				
				$.post("<?php echo base_url('booking/layout_Updateallseats');?>", {service_num: service_no, block_type:block_type, agent_id: agent_id, from_date:from_date, to_date:to_date}, function(res) {
				//alert(res);
                    if (res == 1)//for grabbing
                    {
                        $('#resallseats').html('Seats are Grabbed successfully!');                        
                        $('#allseats').html('Save Changes');
                    }
                    else if (res == 2) { // for release
                        $('#resallseats').html('Seats are Released successfully!');                        
                        $('#allseats').html('Save Changes');
                    }
                    else
                    {
                        alert('There was a problem occured, Kindly contact 040-4026 6613');
                    }
                });
			}
		}
	}
	
    function showLayout(sernum, travel_id, s, date)
    {
        var cnt = $('#hf').val();
        $("#uq" + s).attr('disabled', true);
        $("#uq" + s).val("Please Wait...");
        $.post("GrabReleaseLayout", {sernum: sernum, travel_id: travel_id, s: s, txtdate: date}, function(res) {
		//alert(res); 
            $("#uq" + s).attr('disabled', false);
            $("#uq" + s).val("Grab and Release");
            $('#trr' + s).html(res);
            $('#uqi' + s).hide();
            $('#uqii' + s).show();
            for (var i = 1; i <= cnt; i++)
            {
                $('#trr' + i).hide();
            }
            $('#trr' + s).show();
        });

    }
    function agentType(s, h)
    {
        if (h == 1) {
            var id = $('#atype' + s).val();
        }
        else if (h == 2) {
            var id = $('#res_atype' + s).val();
        }        
		//alert(s+""+h);
        $.post("<?php echo base_url('booking/SelectAgentType');?>", {id: id, s: s}, function(res) {
            //alert(res);
			if (id == 1)
            {
                if (h == 1) {
                    $('#uqi' + s).show();
                    $('#uqa' + s).hide();
                    $('#uqii' + s).html(res);
                    $('#uqii' + s).show();
                }
                else if (h == 2) {
                    $('#rsuqi' + s).show();
                    $('#rsuqa' + s).hide();
                    $('#rsuqii' + s).html(res);
                    $('#rsuqii' + s).show();
                }
            }
            else if (id == 2)
            {

                if (h == 1) {
                    $('#uqi' + s).show();
                    $('#uqa' + s).hide();
                    $('#uqii' + s).html(res);
                    $('#uqii' + s).show();
                }
                else if (h == 2) {
                    $('#rsuqi' + s).show();
                    $('#rsuqa' + s).hide();
                    $('#rsuqii' + s).html(res);
                    $('#rsuqii' + s).show();
                }
            }
            else if (id == 0) {
                $('#uqa' + s).hide();
                $('#uqi' + s).hide();
                $('#uqii' + s).hide();
                $('#rsuqa' + s).hide();
                $('#rsuqi' + s).hide();
                $('#rsuqii' + s).html("selected seats will be release to all");
                $('#rsuqii' + s).show();
            }
            else
            {
                $('#uqa' + s).hide();
                $('#uqi' + s).hide();
                $('#uqii' + s).hide();
                $('#rsuqa' + s).hide();
                $('#rsuqi' + s).hide();
                $('#rsuqii' + s).hide();
            }
        });
    }

    function  chkk(seatname, s, idd) {
        //$('#chkd'+s).show(); 
        if ($('#unchkd' + s).is(':visible')) {
            alert('Giving new quota and removing the quota cannot be performed at a time!');
            $("#" + idd).attr('checked', false);
            return false;
        } else {

            if ($('#chkd' + s).is(':visible')) {
                $("#chkd" + s).show();
            } else {
                $("#chkd" + s).show();
            }
            var gg2 = '';
            var gg = $("#gb" + s).html();

            // if check box is checked 
            if ($("#" + idd).is(":checked")) {
                if (gg == '' || gg == '&nbsp;')
                    gg2 = seatname;
                else
                    gg2 = gg + "," + seatname;
                $("#gb" + s).html(gg2);

            } else {//check box not chcked
                //alert("dfsf");
                var test = "," + seatname;
                if (gg.indexOf(test) != "-1")
                    test = "," + seatname;
                else
                    test = seatname;

                var result = gg.replace(test, '');
                $("#gb" + s).html(result);


            }
            var ggg = $("#gb" + s).html();
            if (ggg == '' || ggg == '&nbsp;')
                $("#chkd" + s).hide();
            $("#unchkd" + s).hide();
        }

    }
    function  unchkk(seatname, s, idd) {
        if ($('#chkd' + s).is(':visible')) {
            alert('Giving new quota and removing the quota cannot be performed at a time!');
            $("#" + idd).attr('checked', true);
            return false;
        } else {
            //$('#unchkd'+s).show();

            if ($('#unchkd' + s).is(':visible')) {
                $("#unchkd" + s).show();
            } else {
                $("#unchkd" + s).show();
            }
            var gg2 = '';
            var gg = $("#rl" + s).html();

            // if check box is checked 
            if ($("#" + idd).is(":checked")) {
                var test = "," + seatname;
                if (gg.indexOf(test) != "-1")
                    test = "," + seatname;
                else
                    test = seatname;

                var result = gg.replace(test, '');
                $("#rl" + s).html(result);

            } else {//check box nt chcked
                if (gg == '' || gg == '&nbsp;')
                    gg2 = seatname;

                else
                    gg2 = gg + "," + seatname;
                $("#rl" + s).html(gg2);

            }
            var ggg = $("#rl" + s).html();
            if (ggg == '' || ggg == '&nbsp;')
                $("#unchkd" + s).hide();
            $("#chkd" + s).hide();

        }//else
    }

    function quotaUpdate(sernum, travel_id, s, c)
    {
        var seats = '';
        var txtdate = $('#txtdate').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		
		if (from_date == "")
        {
            alert("Please Select From Date");
            $("#from_date").focus();
            return false;
        }
			
		if (to_date == "")
        {
            alert("Please Select To Date");
            $("#to_date").focus();
            return false;
        }
		if(from_date > to_date)
		{
			alert("To Date Must be Greater than From Date");
            $("#to_date").focus();
            return false;
		}
        //var td2=txtdate2.split("/");
        //var txtdate=td2[2]+"-"+td2[1]+"-"+td2[0];
        if (c == 1)//grab
            seats = $("#gb" + s).html();
        else if (c == 2)//release
            seats = $("#rl" + s).html();
        if (c == 1) {
            var agent_type = $('#atype' + s).val();
        }
        else if (c == 2) {
            var agent_type = $('#res_atype' + s).val();
        }
        var agent_id = $('#ag' + s).val();
        var ga = $('#ag' + s).val();
        var status = '';


        if (c == 1) {
            if ((agent_type == '') && c == 1)
            {
                alert('please select Agent Type!');
                return false;
            }
            if ((agent_id == '' || agent_id == 0) && c == 1)
            {
                alert('Kindly Select Agent Name and update the quota!');
                return false;
            }
            status = "success";
        }
        if (c == 2) {
            if ((agent_type == '') && c == 2)
            {
                alert('please select Agent Type!');
                return false;
            }
            if ((agent_id == '' || agent_id == 0) && c == 2)
            {
                alert('Kindly Select Agent Name and update the quota!');
                return false;
            }
            status = "success";
        }
        if (status = "success")
        {
            var r = confirm("Are sure,you want Update Quota!");
            if (r == true)
            {
                if (c == 1)//grab
                    $('#gbupdt' + s).html('Please wait...');
                else if (c == 2)
                    $('#rlupdt' + s).html('Please wait...');
//alert(arr);
                $.post("<?php echo base_url('booking/SaveGrabRelease');?>", {service_num: sernum, seat_names: seats, travel_id: travel_id, agent_type: agent_type, agent_id: agent_id, date: txtdate, c: c,from_date:from_date, to_date:to_date}, function(res) {
//alert(res);
                    if (res == 1)//for grabbing
                    {
                        alert('Seats are Grabbed successfully!');
                        $("#chkd" + s).hide();
                        $("#gb" + s).html('');//making span value as null
                        $('#gbupdt' + s).html('Save Changes');
//viewLayoutQuota(sernum,travel_id,s); 
                        showUpdatedLayout(sernum, travel_id, s, txtdate)
                    }
                    else if (res == 2) { // for release
                        alert('Seats are Released successfully!');
                        $("#unchkd" + s).hide();
                        $("#rl" + s).html('');  //making span value as null 
                        $('#rlupdt' + s).html('Save Changes');
//showLayout(sernum,travel_id,s,txtdate);
//viewLayoutQuota(sernum,travel_id,s);
                        showUpdatedLayout(sernum, travel_id, s, txtdate)
                    }
                    else
                    {
                        alert('There was a problem occured, Kindly contact 040-6613 6613');
                    }
                });
            }
            else
            {
                return false;
            }
        }
    }
    function viewLayoutQuota(sernum, travel_id, s)
    {
        $('#trr' + s).show();
        $('#trr' + s).html('please wait..');
        var cnt = $('#hf').val();
        $.post("booking/DisplayLayoutForQuota", {sernum: sernum, travel_id: travel_id}, function(res) {
            $('#trr' + s).html(res);
            for (var i = 1; i <= cnt; i++)
            {
                $('#trr' + i).hide();
            }
            $('#trr' + s).show();
        });
    }
    function showUpdatedLayout(sernum, travel_id, s, txtdate)
    {

        $('#trr' + s).show();
        $('#trr' + s).html('please wait..');
        var cnt = $('#hf').val();
        $.post("booking/GrabReleaseUpdatedLayout", {service_num: sernum, travel_id: travel_id, journey_date: txtdate}, function(res) {
            //alert(cnt);
            $('#trr' + s).html(res);
            for (var i = 1; i < cnt; i++)
            {
                $('#trr' + i).hide();
            }
            $('#trr' + s).show();
        });
    }

</script>
<div class="clearfix">
			<h4></i>Grab & Release</h4>
		</div>
<table width="55%" border="0" cellspacing="1" cellpadding="1" style="margin-top:15px">
    <tr>
        <td width="35%">Select Block or Release Type : 	</td> 
        <td width="65%">
            <select name="type" id="type" onchange="get_responce()" class="inputfield">
                <option value="">---Select---</option>
                <option value="all">All Seats</option>
                <option value="individual">Individual Seat</option>
            </select>       	</td>                
    </tr>
    <tr>
        <td>	
            <input type="hidden" name="token" id='token' value='<?php echo $data[0]; ?>'/>	
            <input type='hidden' name='service_no' id='service_no' value='<?php echo $data[1]; ?>'/>            
            <input type='hidden' name='dtt' id='dtt' value='<?php echo $data[2]; ?>'/></td>
		<td>&nbsp;</td>
    </tr>
    <tr>
	<td colspan="2">
		<div id="load" style="display:none"></div>
		<div id="res1"></div></td>
    </tr>
</table>
</body>
</html>