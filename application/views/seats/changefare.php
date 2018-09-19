<script type="text/javascript">

$(function() 
{                                              
	$( "#fdate" ).datepicker({ dateFormat: 'yy-mm-dd',numberOfMonths: 1, showButtonPanel: false,"autoclose": true});
});

</script>
<script type="text/javascript">
function getRoutes()
{
	$('#ress').html("");

	var service_num=$('#service_num').val();
    var service_name=$('#service_num option:selected').text();
	var city_id=$('#service_route').val();
    var service_route=$('#service_route option:selected').text();
    
	$('#hid').val('');

	if(service_num=="")
	{
		alert('select service number');

		return false;
	}
	if(city_id=="")
	{
		alert('select service route');

		return false;
	}
	else
	{
		$.post("getRoutes1",{service_num:service_num,service_name:service_name,city_id:city_id,service_route:service_route},function(res)
		{
			//alert(res);
			
			$('#resp').html(res);
		});
    }//else
}//getRoutes()

function getroute()
{
	$('#route').html("");

	var service_num=$('#service_num').val();
    var service_name=$('#service_num option:selected').text();   	

	if(service_num == 0)
	{
		alert('select service number');

		return false;
	}
	else
	{
		$.post("getroute",{service_num:service_num,service_name:service_name},function(res)
		{
			//alert(res);
			
			$('#route').html(res);
		});
    }//else
}//getRoutes()

function updateFare()
{
	var bus_type = $("#bus_type").val();
	var service_num = $("#service_num").val();
	var travel_id = $("#travel_id").val();
	var sfare = $("#sfare").val();
	var lfare = $("#lfare").val();
	var ufare = $("#ufare").val();
	var lower_rows = $("#lower_rows").val();
	var lower_cols = $("#lower_cols").val();
	var upper_rows = $("#upper_rows").val();
	var upper_cols = $("#upper_cols").val();
	var lower_seat_no = "";
	var upper_seat_no = "";
	var fdate = $("#fdate").val();
	var tdate = $("#tdate").val();
	var price_mode = $("#price_mode").val();
	var from_id = $("#from_id").val();
	var from_name = $("#from_name").val();
	var to_id = $("#to_id").val();
	var to_name = $("#to_name").val();
	var service_route2 = $("#service_route2").val();
	var city_id = $("#service_route").val();
	var seats_count = $("#seats_count").val();
	var max_rows = $("#max_rows").val();
	var max_cols = $("#max_cols").val();
	var double_berth = $("#double_berth").val();
	var single_berth = $("#single_berth").val();	
	var i = 1;
	var j = 1;
	//alert(lfare);
	
	
	var t="<?php echo date('Y-m-d'); ?>";
	
	if(price_mode == "")
	{
		alert("Please Select Fare Saving  Mode");
		$("#price_mode").focus();
		return false;
	}
	
	if(service_route2 == "")
	{
		alert("Please Select service route to update fare");
		$("#service_route2").focus();
		return false;
	}
	
	if(bus_type == "seater")
	{
		for(i = 1;i <= lower_cols;i++)
		{
			for(j = 1;j <= lower_rows;j++)
			{
				if(typeof $('#ltxt'+j+'-'+i).val() != "undefined")
				{
					if($('#sfare'+j+'-'+i).val() == "" || $('#sfare'+j+'-'+i).val() == 0)
					{
						lower_seat_no = "";
					}
					else if(sfare != $('#sfare'+j+'-'+i).val())
					{
						if(lower_seat_no == "")
						{
							lower_seat_no = $('#ltxt'+j+'-'+i).val() +"#"+ $('#sfare'+j+'-'+i).val();
						}
						else
						{
							lower_seat_no = lower_seat_no +'@'+ $('#ltxt'+j+'-'+i).val() +"#"+ $('#sfare'+j+'-'+i).val();
						}
					}				
				}
			}
		}	
	}
	if(bus_type == "sleeper" || bus_type == "seatersleeper")
	{		
		if(bus_type == "sleeper" && (seats_count >= 30 || seats_count <= 40))
		{
			if(double_berth == "")
			{
				alert("Please Provide Fare for Double Berth");
				$("#double_berth").focus();
				return false;
			}
			if(single_berth == "")
			{	
				alert("Please Provide Fare for Single Berth");
				$("#single_berth").focus();
				return false;
			}
			if(double_berth <= 0)
			{
				alert("Please Provide Fare greater than zero for Double Berth");
				$("#double_berth").focus();
				return false;
			}
			if(single_berth <= 0)
			{	
				alert("Please Provide Fare greater than zero for Single Berth");
				$("#single_berth").focus();
				return false;
			}
			if(double_berth % 1 != 0)
			{
				alert("Double Berth Fare should be an Integer");
				$("#double_berth").focus();
				return false;
			}
			if(single_berth % 1 != 0)
			{	
				alert("Single Berth Fare should be an Integer");
				$("#single_berth").focus();
				return false;
			}
			else
			{
				for(i = 1;i <= upper_cols;i++)
				{
					for(j = 1;j <= upper_rows;j++)
					{				
						if(typeof $('#utxt'+j+'-'+i).val() != "undefined")
						{						
							if(($('#double_berth').val() == "" || $('#double_berth').val() == 0) && ($('#single_berth').val() == "" || $('#single_berth').val() == 0))
							{
								upper_seat_no = "";
							}
							else
							{
								if(upper_seat_no == "")
								{
									if(max_cols != $('#lcol'+j+'-'+i).val())
									{
										upper_seat_no = $('#utxt'+j+'-'+i).val() +"#"+ $('#double_berth').val();
									}
									else
									{
										upper_seat_no = $('#utxt'+j+'-'+i).val() +"#"+ $('#single_berth').val();
									}	
								}
								else
								{
									if(max_cols != $('#lcol'+j+'-'+i).val())
									{
										upper_seat_no = upper_seat_no +'@'+ $('#utxt'+j+'-'+i).val() +"#"+ $('#double_berth').val();	
									}
									else
									{
										upper_seat_no = upper_seat_no +'@'+ $('#utxt'+j+'-'+i).val() +"#"+ $('#single_berth').val();	
									}	
								}							
							}					
						}	
					}
				}			
				//alert(upper_seat_no);
				
				for(i = 1;i <= lower_cols;i++)
				{
					for(j = 1;j <= lower_rows;j++)
					{
						if(typeof $('#ltxt'+j+'-'+i).val() != "undefined")
						{						
							if(($('#double_berth').val() == "" || $('#double_berth').val() == 0) && ($('#single_berth').val() == "" || $('#single_berth').val() == 0))
							{
								lower_seat_no = "";
							}
							else
							{													
								if(lower_seat_no == "")
								{
									if(max_cols != $('#lcol'+j+'-'+i).val())
									{
										lower_seat_no = $('#ltxt'+j+'-'+i).val() +"#"+ $('#double_berth').val();
									}
									else
									{
									lower_seat_no = $('#ltxt'+j+'-'+i).val() +"#"+ $('#single_berth').val();
									}	
								}
								else
								{
									if(max_cols != $('#lcol'+j+'-'+i).val())
									{
										lower_seat_no = lower_seat_no +'@'+ $('#ltxt'+j+'-'+i).val() +"#"+ $('#double_berth').val();
									}
									else
									{
										lower_seat_no = lower_seat_no +'@'+ $('#ltxt'+j+'-'+i).val() +"#"+ $('#single_berth').val();	
									}	
								}
							}					
						}
					}
				}	
				//alert(lower_seat_no);	
			}	
		}
		else
		{
			for(i = 1;i <= upper_cols;i++)
			{
				for(j = 1;j <= upper_rows;j++)
				{				
					if(typeof $('#utxt'+j+'-'+i).val() != "undefined")
					{
						//alert($('#utxt'+j+'-'+i).val());
						//alert($('#ufare'+j+'-'+i).val());
						if($('#ufare'+j+'-'+i).val() == "" || $('#ufare'+j+'-'+i).val() == 0)
						{
							upper_seat_no = "";
						}
						else if(ufare != $('#ufare'+j+'-'+i).val())
						{
							if(upper_seat_no == "")
							{
								upper_seat_no = $('#utxt'+j+'-'+i).val() +"#"+ $('#ufare'+j+'-'+i).val();
							}
							else
							{
								upper_seat_no = upper_seat_no +'@'+ $('#utxt'+j+'-'+i).val() +"#"+ $('#ufare'+j+'-'+i).val();
							}
						}					
					}	
				}
			}
			
			//alert(upper_seat_no);
			
			for(i = 1;i <= lower_cols;i++)
			{
				for(j = 1;j <= lower_rows;j++)
				{
					if(typeof $('#ltxt'+j+'-'+i).val() != "undefined")
					{
						//alert($('#ltxt'+j+'-'+i).val());
						//alert(lfare);
						//alert($('#lfare'+j+'-'+i).val());
						if($('#lfare'+j+'-'+i).val() == "" || $('#lfare'+j+'-'+i).val() == 0)
						{
							lower_seat_no = "";
						}
						else if(lfare != $('#lfare'+j+'-'+i).val())
						{						
							//alert("elseif");
							if(lower_seat_no == "")
							{
								lower_seat_no = $('#ltxt'+j+'-'+i).val() +"#"+ $('#lfare'+j+'-'+i).val();
							}
							else
							{
								lower_seat_no = lower_seat_no +'@'+ $('#ltxt'+j+'-'+i).val() +"#"+ $('#lfare'+j+'-'+i).val();
							}
						}					
					}
				}
			}
		}
		//alert(lower_seat_no);	
	}
	if(lower_seat_no != "" || upper_seat_no != "")
	{	
		if(fdate < t || tdate < t)
		{
			alert("Date shoud not less than today date");
		}
		else if(tdate < fdate)
		{
			alert("To date shoud not less than From date");
		}
	else
	{	
		var r = confirm("Are You Sure, change price "+price_mode+" for selected route");
		if(r == true)
		{
			//alert("if");
			$.post("<?php echo base_url('Seats/addnewfare');?>",
			{
				bus_type:$("#bus_type").val(),
				service_num:$("#service_num").val(),
				travel_id:$("#travel_id").val(),
				sfare:$("#sfare").val(),
				lfare:$("#lfare").val(),
				ufare:$("#ufare").val(),
				lower_rows:$("#lower_rows").val(),
				lower_cols:$("#lower_cols").val(),
				upper_rows:$("#upper_rows").val(),
				upper_cols:$("#upper_cols").val(),
				lower_seat_no:lower_seat_no,
				upper_seat_no:upper_seat_no,
				fdate:fdate,
				tdate:tdate,
				price_mode:price_mode,
				from_id:from_id,
				from_name:from_name,
				to_id:to_id,
				to_name:to_name,
				service_route2:service_route2,
				city_id:city_id,
				seats_count:seats_count,
				max_rows:max_rows,
				max_cols:max_cols,
				double_berth:double_berth,
				single_berth:single_berth
			},function(res)
			{			
				//alert(res);
				if(res == 1)
				{
					alert("Fare Updated Successfully");
					window.location = "<?php echo base_url('Seats/Ind_seat_fare');?>";
				}	
				else 
				{
					alert("Error");
				}		
			});
		}
	  }
	}
	else
	{
		alert("Base fare and Changed fare are equal,Please change Fare and Update");
		return false;
	}
}
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Individual Seat Fare<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">

				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>
						<td>Service Name</td>
						<td><select name="select" id="service_num" onchange="getroute()" >
								<option value="">-- Select Service --</option>
								<?php
								foreach($result->result() as $res){
								echo  '
			  <option value='.$res->service_num.'>'.$res->service_name.'('.$res->service_num.')</option>';
								}
								?>
							</select></td>
						<td>Service Route</td>
						<td id="route">&nbsp;</td>						
						<td><input  type="button" class="btn btn-primary" name="btn" id='btn' value="Submit" onclick="getRoutes()"/></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>						
					</tr>
					<tr>
						<td height="5" colspan="8"><input type="hidden" name="hid" id='hid' value=''/>
							<input type='hidden' name='fromto' id='fromto' val=''/>
							<input type='hidden' name='sequence' id='sequence' val=''/>
						</td>
					</tr>
					<tr>
						<td  colspan="8" align="center">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="8" id="resp" align="center">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="8" id="ress" align="center">&nbsp;</td>
					</tr>
				</table>
			</div>
			</div>
		</main>
	</section>
</div>