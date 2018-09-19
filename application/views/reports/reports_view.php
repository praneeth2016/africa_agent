<script type="text/javascript">
    $(function ()
    {
        $("#date_from2").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false,"autoclose": true
        });

        $("#date_to2").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false,"autoclose": true
        });
    });
</script>
<script>
    function Report()
    {
        var service = $('#service').val();
        //alert(service);
        var from = $('#date_from2').val();
        var to = $('#date_to2').val();
        var ag_name = $('#agent').val();
        var ag = $('#select').val();
        var rtype = $('#select2').val();
        var name = $('#name').val();
        var output = $("input[name='output']:checked").val();
        if (service == 0)
        {
            alert("Kindly Select Service Number");
            $('#service').focus();
            return false;
        }
        if (output == 'screen')
        {
            $('#dr').hide();
            //alert("screen");
            //window.open('GetReport?from=' + from + '&to=' + to + '&ag=' + ag + '&ag_name=' + ag_name + '&rtype=' + rtype + '&service=' + service);
           window.open("GetReport?from=" + from + '&to=' + to + '&ag=' + ag + '&ag_name=' + ag_name + '&rtype=' + rtype + '&service=' + service, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400"); 
        }
        else if (output == 'csv')
        {
            document.location.href = "getDownload?output1=" + output + "&date_from=" + from + "&date_to=" + to + "&ag=" + ag + "&ag_name=" + ag_name;
        }
        else if (output == 'xls')
        {
            document.location.href = "getDownload?output1=" + output + "&date_from=" + from + "&date_to=" + to + "&ag=" + ag + "&ag_name=" + ag_name;
        }

    }


    function agentType()
    {

        var ag_type = $('#select').val();
        var agentname = $('#date_to2').val();

        $.post('ShowAgent', {agenttype: ag_type}, function (res) {
            //alert(res);
            if (ag_type == 'all' || ag_type == '4' || ag_type == 'tg' || ag_type == 'tr' || ag_type == "te")
            {
                $('#uq').hide();
                $('#agname').html("");
            }
            else
            {
                $('#uq').show();
                $('#agname').html(res);
            }
        });
    }
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Detail Report<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="650" border="0" align="center" cellpadding="0" cellspacing="8">
						<tr>
							<td>&nbsp;</td>
							<td height="30">Service</td>
							<td align="center"><strong>:</strong></td>
							<td colspan="4"><?php
								$js = 'id="service" class="size"';
								echo form_dropdown('from', $services, '', $js);
								?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td height="30">From</td>
							<td align="center"><strong>:</strong></td>
							<td><input type="text" size='12' name="date_from2" id="date_from2" style="background-image:url(<?php echo base_url('images/calendar.gif') ?>); background-repeat:no-repeat; background-position:left; background-color:#FFFFFF;" class="inputfield" value='<?php echo(Date("Y-m-d")); ?>' /></td>
							<td>To</td>
							<td align="center"><strong>:</strong></td>
							<td><input type="text" size='12' name="date_to2" id="date_to2" style="background-image:url(<?php echo base_url('images/calendar.gif') ?>); background-repeat:no-repeat; background-position:left; background-color:#FFFFFF;" value='<?php echo(Date("Y-m-d")); ?>' class="inputfield" /></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td height="30">Agent Type </td>
							<td align="center"><strong>:</strong></td>
							<td><select name="select2" id="select" onchange="agentType()" class="inputfield">
									<option value="all">All</option>
									<option value="3">API</option>
									<option value="1">Branch</option>
									<option value="prepaid">Prepaid</option>
									<option value="postpaid">Postpaid</option>
									<option value="4">Website</option>
									<!--<option value="tg">Ticketgoose</option>
					<option value="tr">Travelyarri</option>-->
									<option value="te">Ticket Engine</option>
								</select></td>
							<td style="display:none;" id="uq" colspan="2">Agent Name<strong style="float:right">:</strong> </td>
							<td><span id="agname"></span></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td height="30">Based On</td>
							<td align="center"><strong>:</strong></td>
							<td colspan="4"><select name="select2" id="select2" class="inputfield">
									<option value="jdate">Journey Date</option>
									<option value="bdate">Booking Date</option>
								</select></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td height="30">Output</td>
							<td colspan="5" align="left"><input type="radio" name="output" value="screen" id="radio" checked="checked" onclick="showTypeData(this.value)" />
								Onscreen </td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td height="30">&nbsp;</td>
							<td colspan="5" align="left"><input  type="button" class="btn btn-primary" name="submit" id="submit" value="Submit" onclick="Report()" /></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>