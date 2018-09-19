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
                $.post("GetServiceList", {serno: service, txtdate:txtdate}, function(res) {
                    
                    if (res == 0)
                    {
                        $('#content1').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
                        $("#subb").attr('disabled', false);
                        $("#subb").val("submit");
                    }
                    else
                    {
                        $("#subb").attr('disabled', false);
                        $("#subb").val("submit");
                        $('#content1').html(res);
                    }
                });
            }
        });
    });
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
        //alert(id);

        $.post("SelectAgentType", {id: id, s: s}, function(res) {
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
                $.post("SaveGrabRelease", {service_num: sernum, seat_names: seats, travel_id: travel_id, agent_type: agent_type, agent_id: agent_id, date: txtdate, c: c,from_date:from_date, to_date:to_date}, function(res) {
//alert(res);
                    if (res == 1)//for grabbing
                    {
                        alert('Seats are Grabbed successfully!');
                        $("#chkd" + s).hide();
                        $("#gb" + s).html('');//making span value as null
                        $('#gbupdt' + s).html('Save Changes');
						//viewLayoutQuota(sernum,travel_id,s); 
                        //showUpdatedLayout(sernum, travel_id, s, txtdate)
						window.location = "<?php echo base_url('Seats/Grab_release');?>";						
                    }
                    else if (res == 2) { // for release
                        alert('Seats are Released successfully!');
                        $("#unchkd" + s).hide();
                        $("#rl" + s).html('');  //making span value as null 
                        $('#rlupdt' + s).html('Save Changes');
//showLayout(sernum,travel_id,s,txtdate);
//viewLayoutQuota(sernum,travel_id,s);
                        //showUpdatedLayout(sernum, travel_id, s, txtdate)
						window.location = "<?php echo base_url('Seats/Grab_release');?>";
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
        $.post("DisplayLayoutForQuota", {sernum: sernum, travel_id: travel_id}, function(res) {
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
        $.post("GrabReleaseUpdatedLayout", {service_num: sernum, travel_id: travel_id, journey_date: txtdate}, function(res) {
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
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Grab and Release<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="2%" height="35">&nbsp;</td>
							<td width="22%" height="35">Select Service No/Name</td>
							<td width="17%" height="35"><span class="quick_demos">
								<?php
									$js = 'id="service"';
									echo form_dropdown('from', $services, '', $js);
									?>
								</span></td>
							<td width="8%">  </td>
							<td width="19%">Date &nbsp&nbsp&nbsp : <input type="text" name="txtdate" id="txtdate" value="<?php echo date('Y-m-d');?>" size="12" readonly="" /></td>
							<td width="30%"><input  type="button" class="btn btn-primary" name="subb" id="subb" value="Submit" />
								<input type="hidden" name="service_type" id="service_type" value="0" />
								<input type="hidden" name="grab_seats" id="grab_seats" value="" />		</td>
							<td width="2%" height="35">&nbsp;</td>
						</tr>
						<tr>
							<td height="35">&nbsp;</td>
							<td height="35" colspan="5" id="content1"></td>
							<td height="35">&nbsp;</td>
						</tr>	
					</table>
				</div>
			</div>
		</main>
	</section>
</div>