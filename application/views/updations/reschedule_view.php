<style type="text/css">
.available
{
	cursor:pointer;
	background-color:#006600;
	color:#fff;
	text-align:center;
}
	
.selected
{
	cursor:pointer;
	background-color:#6262FF;
	color:#fff;
	text-align:center;
}

.female
{
    background-color:#FF00FF;
    color:#fff;
    text-align:center;
      
}

.booked
{
    background-color:#F4353A;
    color:#fff;
    text-align:center;
    
}
.size{
    font-size: 13px;
}
.dep
{
	width:120px;
	height:16px;
	text-decoration:underline;
	color:#5a8eca;
	cursor:pointer;
}

.dep1
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:30%;
	z-index:999;
}
.arr
{
	width:120px;
	height:16px;
	text-decoration:underline;
	color:#5a8eca;
	cursor:pointer;
}

.dep1
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:45%;
	z-index:999;
}
.dep2
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:25%;
	z-index:999;
}

.dep3
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:75%;
	z-index:999;
}

.arr1
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:55%;
	z-index:999;
}
.arr2
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:30%;
	z-index:999;
}

.arr3
{	
	border:#666666 solid 1px;
	position:fixed;
	width:auto;
	background:#FFFFE1;
	font-size:10px;
	overflow: hidden;
	text-shadow: none;
	top:28%;
	left:150%;
	z-index:999;
}

</style>
<script type="text/javascript">
function text(journey)
{
    var name=$('#name').val();   
    $('#onward_name0').val(name);
	$('#return_name0').val(name);

}
function showpass(sno, serno, jdate, ishover, seat_status, way)
    {       
        if (ishover == '1' && seat_status == '1')
        {           
            $("#dep1" + sno).toggle();
            $.post('<?php echo base_url("welcome/showPassDetail") ?>', {sno: sno, serno: serno, jdate: jdate}, function(res) {
                $("#" + way + sno).html(res);
               
            });
        }
    }

function hidepass()
{
	$('.dep1').hide();
}

$(document).ready(function()
{
	$("#from").change(function()
	{
    	var from=$("#from").val();
        
		$.post('<?php echo site_url('Updations/toListr');?>',{from:from},function(res)
		{	
        	$("#tid").hide();
	        $("#to_id").html(res);
        }); 
  	});
});

$(document).ready(function () {
                $('#doj').datepicker({
				"autoclose": true
                });
				$('#return').datepicker({
				"autoclose": true
                });
            });

function searchBus()
{
    var source = $("#from").val();
    var destination = $("#to").val();   
    var onward_date = $("#doj").val();
	var return_date = $("#return").val();
	var trip = $("input[name='trip']:checked").val();
	
    if(source == 0)
    {
        alert("Please Provide Source");
		$("#from").focus();
		return false;
    }
    
    if(destination == 0)
    {
        alert("Please Provide Destination");
		$("#to").focus();
		return false;
    }
    
    if(source == destination)
    {
        alert("Source and Destination Name must not be same");
		$("#destination").focus();
		return false;
    }
    
    if(onward_date == "")
    {
        alert("Please Select Onward Journey Date");
		$("#doj").focus();
		return false;
    }
		
	if(return_date == "")
    {
       	alert("Please Select Return Journey Date");
		$("#return").focus();
		return false;
    }	
	   
    else
    {
		$('#buslist').load('<?php echo site_url('Updations/reschedule_busList');?>',{source:source,destination:destination,onward_date:onward_date,return_date:return_date,trip:trip},function(){
		});    			
    }
} 
</script>
<script type="text/javascript">
var i = 0;
var cnt;

function reschedule_layout(service_num,source_id,destination_id,onward_date,return_date,fare,j,way)
{		
	var ct = $("#ct").val();
	var ct1 = $("#ct1").val();
	
	if(way == "O")
	{
		cnt = ct;
	}
	else if(way == "R")
	{
		cnt = ct1;
	}
	$("#lay"+way+j).show();
	$("#"+way+j).empty();
	for(i = 1;i <= cnt;i++)
	{
		if(i == j)
		{
			//alert(way);
			$("#"+way+j).load('<?php echo site_url('Updations/reschedule_serviceLayout');?>',{service_num:service_num,source_id:source_id,destination_id:destination_id,onward_date:onward_date,return_date:return_date,fare:fare,j:j,way:way},
            function()
            {
                $(".lay").hide(); 
            });
		}
		else
        {                      
            $("#"+way+i).empty();
        }
	}
	$('#sno').val('0');
	$('#sno1').val('0');
}
</script>
<script type="text/javascript">
function validate()
{	
	var onward_way = $('#onward_way').val();
	var return_way = $('#return_way').val();
	var return_start_time = $('#return_start_time').val();
	var return_arr_time = $('#return_arr_time').val();
	var return_bus_type = $('#return_bus_type').val();
	var return_model = $('#return_model').val();    	
	
	var onward_date = $('#onward_date').val();
	var return_date = $('#return_date').val();
	var travel_id = $('#onward_travel_id').val();		   
	
	var strFilter = /^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i; 
    var strFilter1 = /^[-+]?\d*\.?\d*$/;
	
	var onward_gender = "";
	var return_gender = "";
	
	
	var onward_service_num = $('#onward_service_num').val();
	var return_service_num = $('#return_service_num').val();
	
	var seater_cnt = $('#seaterrt').val();
	var sleeper_rt = $('#sleeper_rt').val();
	var sleeper_seater_rt = $('#sleeper_seater_rt').val();
	
	
	var rt1 = "";
	var rsn1 = "";
	var cnt="";
	
	if(return_bus_type == "seater")
	{
		cnt = seater_cnt;
	}
	else if(return_bus_type == "sleeper")
	{
		cnt = sleeper_rt;
	}
	else if(return_bus_type == "seatersleeper")
	{
		cnt = sleeper_seater_rt;
	}
	
	for(var i=1;i<= cnt;i++)
	{
		var rt = $('#rt'+i).val();
		var rsn = $('#rsn'+i).val();
		//alert(rt);
		
		if((typeof(rt) == "undefined") || rt=="")
		{
		}		
		else
		{
			
			if(rt1 == "")
			{
				rt1 = rt;
				rsn1 = rsn;
				
			}
			else
			{
				rt1 = rt1+","+rt;
				rsn1 = rsn1+","+rsn;
			}
			
		}
	}
	
	
	/*alert(rt1);
	alert(rsn1);
	alert(onward_service_num);
	alert(return_service_num);
	alert(return_start_time);
	alert(return_arr_time);
	alert(return_bus_type);
	alert(return_model);
	alert(onward_date);
	alert(return_date);
	alert(travel_id);*/
	
	if(rt1 == "")
	{
		alert("Please Provide Reschedule Seat Numbers");
	}
	else
	{
		var r = confirm("Are You Sure You Want To Reschedule Ticket");
		
		if(r == true)
		{
			$.post('<?php echo site_url('Updations/reschedule_ticket');?>',{onward_service_num:onward_service_num,return_service_num:return_service_num,return_start_time:return_start_time,return_arr_time:return_arr_time,return_bus_type:return_bus_type,return_model:return_model,onward_date:onward_date,return_date:return_date,travel_id:travel_id,rt1:rt1,rsn1:rsn1},function(res)
			{
				alert(res);
				
				console.log(res);
				if(res == 1)
				{
					alert("Ticket has been Rescheduled Successfully");
					window.location='<?php echo base_url('Updations/tkt_reschedule');?>';
				}
				else if(res == 3)
				{
					alert("The Tickets already Rescheduled!!");
					//window.location='<?php echo base_url('Updations/tkt_reschedule');?>';
				}
				else
				{
					alert("Need to Reschedule All the Seats");
				}
			});
		}
	}
}

function boarding(srvno,j,trvlid){
var ac = $("#bus_count").val();
//alert(ac);
    for(var i = 1;i <= ac;i++)
    {
        if(i == j)
        {      
           // alert(ac);
            $("#dep1" +i).toggle();
           
	    $("#dep1" +i).load('<?php echo site_url('welcome/dep');?>',{srvno:srvno,tid:trvlid}, function(res) {
                //alert(res);
            });
        }        
        else
        {
            //alert('else');
	    $("#dep1" +i).hide();
        }
    }
}

function hidedep()
{
	$('.dep1').hide();
        $('.dep2').hide();
        $('.dep3').hide();
}

function dropping(srvno,j,trvlid,way){
var ac = $("#bus_count").val();
//alert(ac);

    for(var i = 1;i <= ac;i++)
    {
        if(i == j)
        {      
           // alert(ac);
            $("#arr1" +i).toggle();
           
	    $("#arr1" +i).load('<?php echo site_url('welcome/drop');?>',{srvno:srvno,tid:trvlid}, function(res) {
                //alert(res);
            });
        }        
        else
        {
            //alert('else');
	    $("#arr1" +i).hide();
        }
    }
}

function hidearr()
{
	$('.arr1').hide();
        $('.arr2').hide();
        $('.arr3').hide();
}
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Ticket Reschedule<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<form name="ind" id="ind" method="post">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
							<tr >
								<td height="30" style="padding-left:10px">&nbsp</td>
							</tr>
							<tr>
								<td><table border="0" cellspacing="1" cellpadding="1" style="border:#CCCCCC solid 1px; font-size:12px;" width="100%">
										<tr>
											<td>&nbsp;</td>
											<td colspan="20">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="21" height="5"></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td>From</td>
											<td><strong>:</strong></td>
											<td><?php

								$js = 'id="from"';

								echo form_dropdown('from', $from_cities, '', $js);           

							?></td>
											<td>&nbsp;</td>
											<td><span class="size">To</span></td>
											<td id="to_id"><strong>:</strong></td>
											<td><select name="select" id='tid'>
													<option value="">- - - Select - - -</option>
												</select></td>
											<td>&nbsp;</td>
											<td><span class="size">Date of Journey</span></td>
											<td><strong>:</strong></td>
											<td><input type="text" name="doj" id="doj" size="8" value="<?php echo date('Y-m-d'); ?>" /></td>
											<td>&nbsp;</td>
											<td>Reschedule Date </td>
											<td><strong>:</strong></td>
											<td><input name="return" id="return" type="text" size="8" value="<?php echo date('Y-m-d'); ?>" /></td>
											<td>&nbsp;</td>
											<td><input  type="button" class="btn btn-primary" name="search" id="search" value="Search Buses" onclick="searchBus();" /></td>
										</tr>
									</table></td>
							</tr>
						</table>
						<br />
					</form>
					<div id="buslist"></div>
					<input type="hidden" name="sno" id="sno" value="0" size="7" />
					<input type="hidden" name="sno1" id="sno1" value="0" size="7" />
			</div>
			</div>
		</main>
	</section>
</div>
