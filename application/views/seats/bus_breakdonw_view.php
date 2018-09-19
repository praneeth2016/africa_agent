
<script>
    function getServiceDetails()
    {
        var service = $('#service').val();


        if (service == 0)
        {
            alert("Please Provide Service Number");
            $("#service").focus();
            return false;
        }
        else
        {
            $.post("GetServiceReport", {service: service}, function (res) {
                // alert(res);
                if (res == 0)
                    $('#tbl').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
                else
                    $('#tbl').html(res);
            });

        }
    }


    function deActivateBus(key, svc, travid, s, stat, fromid, toid)
    {
        var cnt = $('#hdd').val();
        $.post("deActivateBusDatePickers", {key: key, svc: svc, travid: travid, s: s, status: stat, fromid: fromid, toid: toid}, function (res)
        {
//alert(res);
            for (var i = 1; i <= cnt; i++)
            {
                if (i == s)
                    $('#tr' + i).show();
                $('#tr' + i).hide();
            }
            if (key == 'Deactive')
            {
//alert(key);
                $('#tr' + s).show();
                $('#dp' + s).html(res);
                $('#radio' + s).show();
            }
            else
            {
                $('#tr' + s).show();
                $('#dp' + s).html(res);
                $('#radio' + s).hide();
            }

//datepicker
            var date = new Date();
            var currentMonth = date.getMonth();
            var currentDate = date.getDate();
            var currentYear = date.getFullYear();
            $('#txtdatee' + s).datepicker({
                // minDate: new Date(currentDate, currentMonth, currentYear),
                minDate: new Date(currentYear, currentMonth, currentDate),
                numberOfMonths: 1,
                minDate: '0',
                        dateFormat: "dd-mm-yy",
                "autoclose": true
            });
            $('#txtdateee' + s).datepicker({
                // minDate: new Date(currentDate, currentMonth, currentYear),
                minDate: new Date(currentYear, currentMonth, currentDate),
                numberOfMonths: 1,
                minDate: '0',
                        dateFormat: "dd-mm-yy",
                "autoclose": true

            });

        });

    }
    function getFromTo(i, key)
    {
        var date = $('#txtdatee' + i).val();
        var res = $('#txtdateee' + i).val();
        if (date == '')
        {
            $('#spnmsg' + i).html('Kindly select From date!');
        }
        else if (date > res && res != '')
        {
            $('#spnmsg' + i).html('From date should be less than To date!');
        }
        else if (date != '' && res != '')
        {
            $('#spnmsg' + i).html('This Service Will be ' + key + ' from ' + date + ' to ' + res);
        }
    }
    function onChge(i, key)
    {
        getFromTo(i);
    }

//function for update status as inactive
    function updateStatusAsDeAct(key, sernum, travid, status, s, fromid, toid)
    {
//alert(key+"/"+sernum+"/"+travid+"/"+s+"/"+fromid+"/"+toid);
        var cnt = $('#hdd').val();
        var fdate = $('#txtdatee' + s).val();
        var tdate = $('#txtdateee' + s).val();
        if (key == 'Deactive')
        {
            var chkRadio = $('input[name=ser' + s + ']:checked').val();
        }
        else
        {
            var chkRadio = '0';
        }
//alert(chkRadio);
        if (fdate == '')
        {
            alert('Please Select From Date !');
            $('#txtdatee' + s).focus();
            return false;
        }
        if (tdate == '')
        {
            alert('Please Select To Date !');
            $('#txtdateee' + s).focus();
            return false;
        }
        if (fdate > tdate)
        {
            alert('From Date should be less than To date!');
            return false;
        }
        if (key == 'Deactive')
        {
            //alert(key);
            if ($('input[name=ser' + s + ']:checked').length <= 0)
            {
                alert("Please select the radio button ");
                return false;
            }
        }
        if ($('#updt' + s).val() == 'Update')
        {
            var cnf = confirm("Are you sure, want to " + key + " the bus !");
            if (cnf)
            {
                $('#updt' + s).val('please wait...');
                $.post("deActivateBus", {key: key, sernum: sernum, travid: travid, fdate: fdate, tdate: tdate, status: status, cnt: cnt, s: s, fromid: fromid, toid: toid, chkRadio: chkRadio}, function (res) {
                    //alert(res);
                    $('#tbl1').html(res);
                    if (res == 1)
                    {
                        $('#updt' + s).val('Update');
                        $('#txtdatee' + s).val('');
                        $('#txtdateee' + s).val('');
                        $('#spnmsg' + s).html('');
                        $('#spnmsg' + s).html('Service ' + key + 'd From ' + fdate + ' to ' + tdate);
                        $.post("mailForBusCancelController", {key: key, sernum: sernum, travid: travid, fdate: fdate, tdate: tdate, status: status, cnt: cnt, s: s, fromid: fromid, toid: toid}, function (response) {
                            //alert(response);
                            if (res == 0) {
                            }
                        });
                    }
                    else if (res == 2) {
                        $('#updt' + s).val('Update');
                        alert('bus is already deactivated');
                    }
                    else
                    {
                        $('#updt' + s).val('Update');
                        $('#spnmsg' + s).html('There was a problem Occured!');
                    }
                });
            }
        }
        else {
            return false;
        }
    }


</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Cancel Service<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">	
					<table width="98%" border="0" cellpadding="0" cellspacing="0">
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><table border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td width="136" height="35" align="center">Service No / Name </td>
										<td width="25" align="center"><strong>:</strong></td>
										<td width="98" align="center"><?php
											$js = 'id="service" class="inputfield"';
											echo form_dropdown('from', $services, '', $js);
											?></td>
										<td width="46">&nbsp;</td><td width="56" height="35" colspan="4" align="center" class="size"><input  type="button" class="btn btn-primary" name="search" id="search" value="Submit" onClick="getServiceDetails()" /></td>
									</tr>
								</table></td>
						</tr>
						<tr>
							<td id="tbl">&nbsp;</td>
						</tr>
						<tr>
							<td id="tbl1">&nbsp;</td>
						</tr>
					</table>
				<br />
				</div>
			</div>
		</main>
	</section>
</div>




