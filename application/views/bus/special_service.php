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
            $.post("operator_Special_Service1", {service: service}, function (res) {
                // alert(res);
                if (res == 0)
                    $('#tbl').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
                else
                    $('#tbl').html(res);
            });

        }
    }
    
	function activateBus(svc)
	{
		$.post("getstatusanddate", {svc: svc}, function (res)
        {
			$('#tr').show();
			$('#dp').html(res);
			
			var date = new Date();
                var currentMonth = date.getMonth();
                var currentDate = date.getDate();
                var currentYear = date.getFullYear();
                $('#txtdate').datepicker({
                    // minDate: new Date(currentDate, currentMonth, currentYear),
                    minDate: new Date(currentYear, currentMonth, currentDate),
                    numberOfMonths: 2,
                    minDate: '0',
                            //dateFormat:"dd-mm-yy"
                            dateFormat: "yy-mm-dd",
							 "autoclose": true

                });
                $('#txtdatee').datepicker({
                    // minDate: new Date(currentDate, currentMonth, currentYear),
                    minDate: new Date(currentYear, currentMonth, currentDate),
                    numberOfMonths: 2,
                    minDate: '0',
                            //dateFormat:"dd-mm-yy"
                            dateFormat: "yy-mm-dd",
							 "autoclose": true

                });
		});
	}
    
    function updateStatus(sernum)
    {        
        var fdate = $('#txtdate').val();        
        var tdate = $('#txtdatee').val();       
        if (fdate == '')
        {
            alert('Please Select Date !');
            return false;
        }
        else if ($('#txtdatee').val() == '')
        {
            alert('Please Select end Date !');
            return false;
        }
        else if ($('#txtdatee').val() < fdate)
        {
            alert('Please Select end Date more than start Date !');
            return false;
        }
        else
        {
            var r = confirm("Are sure,you want Activate The service!!");
            if (r == true)
            {
                $('#updt').val('please wait...');
                $('#updt').attr("disabled", true)
                $.post("activatesplservice", {sernum: sernum, fdate: fdate, tdate: tdate}, function (res) {
                    //alert(res);
                    if (res == 1)
                    {
                        $('#updt').val('Update');
                        $('#updt').attr("disabled", false)
                        //$('#txtdate'+s).val('');
                        $('#txt').html('');
                        $('#spnmsg').html('Service Activated From ' + fdate + ' to ' + tdate);
                    }
                    else
                    {
                        $('#updt').val('Update');
                        $('#updt').attr("disabled", false)
                        $('#spnmsg').html('There was a problem Occured!');
                    }
                });
            }
        }        
    }

</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Special Service<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="72%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="15%" height="30" align="center"><span class="size">Special Services</span></td>
							<td width="4%" height="30" align="center"><strong>:</strong></td>
							<td width="46%" height="30" align="center"><?php echo $service;?></td>
							<td height="30"><input  type="button" class="btn btn-primary" name="search" id="search" value="Submit" onclick="getServiceDetails()" />		</td>
						</tr>
						<tr>
							<td height="30" colspan="4" id="tbl">&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>