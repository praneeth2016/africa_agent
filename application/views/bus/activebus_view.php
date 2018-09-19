<script>
    function getServiceDetails()
    {
        var service = $('#service').val();
        var key = '<?php echo $key; ?>';
        if (service == 0)
        {
            alert("Please Provide Service Number");
            $("#service").focus();
            return false;
        }
        else
        {
            $.post("servicesListActiveOrDeactive", {service: service, key: key}, function (res) {
                //alert(res);
                if (res == 0)
                    $('#tbl').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
                else
                    $('#tbl').html(res);
            });

        }
    }
    function activateBus(svc, travid, s, stat, fromid, toid)
    {
        var cnt = $('#hdd').val();
        var servtype = $('#sertype' + s).val();
        //alert(servtype);
        $.post("getForwordBookingDays", {svc: svc, travid: travid, s: s, status: stat, fromid: fromid, toid: toid, servtype: servtype}, function (res)
        {//alert(res);
            if (res == 0)
            {
                alert('Add Forward booking days to activate the  bus by using Forward Booking Option!');
            }
            else
            {
                for (var i = 1; i <= cnt; i++)
                {
                    if (i == s)
                        $('#tr' + i).show();
                    $('#tr' + i).hide();
                }
                $('#tr' + s).show();
                $('#dp' + s).html(res);

//datepicker
                var date = new Date();
                var currentMonth = date.getMonth();
                var currentDate = date.getDate();
                var currentYear = date.getFullYear();
                $('#txtdate' + s).datepicker({
                    // minDate: new Date(currentDate, currentMonth, currentYear),
                    minDate: new Date(currentYear, currentMonth, currentDate),
                    numberOfMonths: 2,
                    minDate: '0',
                            //dateFormat:"dd-mm-yy"
                            dateFormat: "yy-mm-dd",
                    "autoclose": true

                });
                $('#txtdatee' + s).datepicker({
                    // minDate: new Date(currentDate, currentMonth, currentYear),
                    minDate: new Date(currentYear, currentMonth, currentDate),
                    numberOfMonths: 2,
                    minDate: '0',
                            //dateFormat:"dd-mm-yy"
                            dateFormat: "yy-mm-dd",
                    "autoclose": true

                });

            }
        });

    }
    function deactivateBus(svc, travid, s, stat, fromid, toid, key)
    {
        var model = $("#model").val();
        var st = key;

        if (key == 'Delete')
        {
            var r = confirm("Are sure,You want Delete The service!!");

        }
        else
        {
            var r = confirm("Are sure,you want DeActive The service!!");

        }
        if (r == true)
        {
            if (key == 'Delete')
            {
                var rq = confirm("Are sure,You want Delete The service!!,Once It deleted,You cann't get Back It's Details!!");
            }
            else
            {
                var rq = confirm("Are sure,Tickets booked in this service will be cancel. !!");
            }
            if (rq == true)
            {
                $('#act' + travid + s).val("Please Wait...");
                $('#act' + travid + s).attr("disabled", true);

                $.post("deActivateBusPermanent", {svc: svc, travid: travid, s: s, status: stat, fromid: fromid, toid: toid, st: key, model: model}, function (res)
                {
                    //alert(res);	

                    if (res == 1)
                    {
                        $('#act' + travid + s).val(st);
                        $('#act' + travid + s).attr("disabled", false);
                        if (key == 'Delete')
                        {
                            alert("Service Deleted !!");
                            window.location = '<?php echo base_url("Bus/active_deactive"); ?>';
                        }
                        else {
                            alert("Service Deactivated !!");
                            window.location = '<?php echo base_url("Bus/active_deactive"); ?>';
                        }

                    }
                    else
                    {
                        $('#act' + travid + s).val(st);
                        $('#act' + travid + s).attr("disabled", false);
                        alert('There was a problem Occured!');
                    }
                });
            }
            /*else
             {
             window.location = '<?php echo base_url("Bus/active_deactive"); ?>';
             }*/
        }
        /*else
         {
         window.location = '<?php echo base_url("Bus/active_deactive"); ?>';
         }*/

    }//deactivateBus()


    function getTodate(fwdb, i)
    {
        var date = $('#txtdate' + i).val();
        $.post("getActivateDates", {sdate: date, fwd: fwdb}, function (res) {
            $('#fwddate').val(res);
            $('#txt' + i).html('This Service Will be active from ' + date + ' to ' + res);
        });

    }
    function getTodateForSpecialService(fwdb, i)
    {
        var date = $('#txtdate' + i).val();
        var datee = $('#txtdatee' + i).val();
        $('#txt' + i).html('This Service Will be active from ' + date + ' to ' + datee);

    }
//updation
    function updateStatus(sernum, travid, fwd, status, s, fromid, toid)
    {
        var servtype = $('#sertype' + s).val();
        var fdate = $('#txtdate' + s).val();
        if (servtype == "normal" || servtype == "weekly")
            var tdate = $('#fwddate').val();
        else
            var tdate = $('#txtdatee' + s).val();
        //alert(tdate);
        if (fdate == '')
        {
            alert('Please Select Date !');
            return false;
        }
        else if ($('#txtdatee' + s).val() == '')
        {
            alert('Please Select end Date !');
            return false;
        }
        else if ($('#txtdatee' + s).val() < fdate)
        {
            alert('Please Select end Date more than start Date !');
            return false;
        }
        else if ($('#updt' + s).val() == 'Update')
        {
            var r = confirm("Are sure,you want Update The service!!");
            if (r == true)
            {
                $('#updt' + s).val('please wait...');
                $('#updt' + s).attr("disabled", true)
                $.post("activeBusStatus", {sernum: sernum, travid: travid, fwd: fwd, fdate: fdate, tdate: tdate, status: status, s: s, fromid: fromid, toid: toid, servtype: servtype}, function (res) {
                    //alert(res);
                    if (res == 1)
                    {
                        $('#updt' + s).val('Update');
                        $('#updt' + s).attr("disabled", false)
                        //$('#txtdate'+s).val('');
                        $('#txt' + s).html('');
                        $('#spnmsg' + s).html('Service Activated From ' + fdate + ' to ' + tdate);
                    }
                    else
                    {
                        $('#updt' + s).val('Update');
                        $('#updt' + s).attr("disabled", false)
                        $('#spnmsg' + s).html('There was a problem Occured!');
                    }
                });
            }
        }
        else
        {
            window.location = '<?php echo base_url("Bus/active_deactive"); ?>';
        }
    }

</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Active / Deactive Bus<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="83%" border="0" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<td width="8%"  height="96" class="space" ></td>
								<td width="14%"  height="96" class="space" ></td>
								<td width="20%"  height="96" class="space" ><span >Service No / Route : </span></td>
								<td width="26%"  height="96" class="space" ><span class="space" >
										<?php
										$js = 'id="service" class="form-control"';
										echo form_dropdown('from', $services, '', $js);
										?>
									</span></td>
								<td width="10%"  height="96" class="space" ><span class="size">
										<input  type="button" class="btn btn-primary" name="search" id="search" value="Submit" onClick="getServiceDetails()" />
									</span></td>
								<td width="13%"  height="96" class="space" ></td>
								<td width="9%"  height="96" class="space" ></td>
							</tr>
							<tr >
								<center><td  height="2" colspan="7" class="space" id="tbl" ></td></center>
							</tr>
						<thead>
						<tbody>
							<?php
							$i = 1;
							//print_r($query);
							foreach ($query as $row) {
								$srvtype2 = $row->serviceType;
								$srvno = $row->service_num;


								if ($srvtype2 == '' || $srvtype2 == 'normal') {
									$srvtype = "Normal";
								} else {
									$srvtype = "Special";
								}
								$travid = $row->travel_id;
								if ($row->status == 0 || $row->status == '')
									$st = '<input  type="button" class="btn btn-primary" name="act' . $travid . $s . '" id="act' . $travid . $i . '" value="InActive" 
							  onclick="activateBus(\'' . $srvno . '\',' . $travid . ',' . $i . ',' . $row->status . ',' . $row->from_id . ',
								  ' . $row->to_id . ')">';
								else
									$st = 'Activated';

								echo '<tr >
					<td height="30" class="space">' . $i . '</td>
					<td height="30" class="space">' . $srvtype . '</td>
						<input type="hidden"  value="' . $srvtype . '" id="sertype' . $i . '" name="sertype' . $i . '">
					<td height="30" class="space">' . $srvno . '</td>
					<td height="30" class="space">' . $row->from_name . '</td>
					<td height="30" class="space">' . $row->to_name . '</td>
					<td height="30" class="space">' . $row->bus_type . '</td>
					<td height="30" class="space">' . $st . ' </td>
					</tr>
					<tr  style="display:none;" >
				 <td  colspan="6"  align="center" height="30" class="space" ></td>
				  </tr>
				  <tr id="tr' . $i . '"  style="display:none;">
				 <td  colspan="6" id="dp' . $i . '" align="center" height="30" class="space" ></td>
				  </tr>    
				';
								$i++;
							}
							echo '<input type="hidden" id="hdd" value="' . $i . '" >';
							?>
							</tr>

						<tbody>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>