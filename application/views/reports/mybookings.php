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
        var from = $('#date_from2').val();
        var to = $('#date_to2').val();       
        var rtype = $('#select2').val();      
       
        if (service == 0)
        {
            alert("Kindly Select Service Number");
            $('#service').focus();
            return false;
        }
         window.open("mybookings1?from=" + from + '&to=' + to + '&rtype=' + rtype + '&service=' + service, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400"); 

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
					<h3 class="panel-title">My Bookings<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="650" border="0" align="center" cellpadding="0" cellspacing="8">
						<tr>
							<td>&nbsp;</td>
							<td height="30">Service Num</td>
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