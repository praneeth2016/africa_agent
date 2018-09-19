<script type="text/javascript">

    function get_responce()
    {
        var type = $("#type").val();       
        var service_no = $("#service_no").val();
		var from_id = $("#from_id").val();
        var to_id = $("#to_id").val();
        var dtt = $("#dtt").val();

        if (type != '') {
            $.post('<?php echo base_url('booking/layout_change_price1');?>',
                    {
                        type: type,                       
                        service_no: service_no,
						from_id: from_id,
						to_id: to_id,
                        dtt: dtt
                    }, function (res)
            {

                $("#res1").html(res);
            });

        } else {

            //alert('Kindly select type');
			$("#res1").html('');
        }
    }

	function updateFare()
	{
		var i = $("#hdd").val();
		var type = $("#type").val();       
        var service_num = $("#service_no").val();
        var fdate = $('#fdate').val();
        var tdate = $('#tdate').val();
		var bus_type = $("#bus_type").val();		
		var sfare = "";
        var lbfare = "";
		var ubfare = "";
        var fid = "";
        var tid = "";  
		var success = "";
		var t = '<?php echo date('Y-m-d');?>';
				
		for (var j = 1; j <= i; j++)
       	{			
			if($("#sfare"+j).val() == "")
			{
				alert('Kindly Provide Seat Fare Value');
				$("#sfare"+j).focus();
				return false;
			}
			if($("#sfare"+j).val() <= 0)
			{
				alert('Kindly Provide Seat Fare Value Greater than Zero');
				$("#sfare"+j).focus();
				return false;
			}
			if($("#lbfare"+j).val() == "")
			{
				alert('Kindly Provide Lower Berth Fare');
				$("#lbfare"+j).focus();
				return false;
			}
			if($("#lbfare"+j).val() <= 0)
			{
				alert('Kindly Provide Seat Fare Value Greater than Zero');
				$("#lbfare"+j).focus();
				return false;
			}
			if($("#ubfare"+j).val() == "")
			{
				alert('Kindly Provide Upper Berth Fare Value');
				$("#ubfare"+j).focus();
				return false;
			}
			if($("#ubfare"+j).val() <= 0)
			{
				alert('Kindly Provide Seat Fare Value Greater than Zero');
				$("#ubfare"+j).focus();
				return false;
			}
			
			if (sfare == "")
            {
   	            sfare = $("#sfare" + j).val();
       	    }
           	else
            {
   	            sfare = sfare + "/" + $("#sfare" + j).val();
       	    }
           	if (lbfare == "")
            {
  	            lbfare = $("#lbfare" + j).val();
       	    }
           	else
            {
   	            lbfare = lbfare + "/" + $("#lbfare" + j).val();
       	    }
           	if (ubfare == "")
            {
   	            ubfare = $("#ubfare" + j).val();
       	    }
           	else
            {
   	            ubfare = ubfare + "/" + $("#ubfare" + j).val();
   	        }
		}				
		
		if (typeof sfare == "undefined")
        {
            sfare = 0;
        }
        if (typeof lbfare == "undefined")
        {
            lbfare = 0;
        }
        if (typeof ubfare == "undefined")
        {
            ubfare = 0;
        }
		if (fdate < t || tdate < t)
        {
            alert("Date shoud not less than today date");
        }
        else if (tdate < fdate)
        {
            alert("To date shoud not less than From date");
        }
		else
		{		
			var con = confirm("Are You Sure You Want To Update Fares");
			if (con == true)
            {
                $("#up").val("Please Wait..");
                $("#up").attr("disabled", true);
				
				$.post('<?php echo base_url('booking/layout_updatePrice'); ?>', {fdate: fdate, tdate: tdate, type: type,service_num: service_num,lbfare: lbfare, ubfare: ubfare, sfare: sfare,bus_type: bus_type}, function (res)
                {
                    //alert(res);
                    if (res == 0)
                    {
                        $('#ress').html("<span style='color:red;margin:200px'>Not updated</span>");
                    }
                    else
                    {
                        $('#ress').html("<span style='color:red;margin:200px'> updated</span>");
                    }
                    $("#up").val("Update");
                    $("#up").attr("disabled", false);
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
					<h3 class="panel-title">Change Pricing<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td>Select Type of modify  : 	</td> 
							<td>
								<select name="type" id="type" onchange="get_responce()" class="form-control">
									<option value="">---Select---</option>
									<option value="service">Service Wise</option>
									<option value="route">Route Wise</option>
								</select>       	</td>                
						</tr>
						<tr>
							<td>           	
								<input type='hidden' name='service_no' id='service_no' value='<?php echo $data[0]; ?>'/>
								<input type='hidden' name='from_id' id='from_id' value='<?php echo $data[1]; ?>'/>
								<input type='hidden' name='to_id' id='to_id' value='<?php echo $data[2]; ?>'/>
								<input type='hidden' name='dtt' id='dtt' value='<?php echo $data[3]; ?>'/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
						<td colspan="2">
							<center><div id="res1"></div></center></td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>
