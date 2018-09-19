
<script type="text/javascript">
    
   function ticketstatus()
    {
        var ticket = $("#tktno").val();
        if (ticket == 0)
        {
            alert('Kindly Provide Ticket Number');
            $('#ticket').focus()
        }
        else
        {
            $.post('<?php echo site_url('Updations/Ticket_print1'); ?>',
                    {
                        ticket: ticket
                    }, function (res)
            {
                if (res == 1)
                {
                    //window.open('Booking/ticket_status?ticket=' + ticket);
                    window.open('Ticket_print3?ticket=' + ticket, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=200, left=300, width=800, height=400");
                }
                else
                {
                    alert("Invalid Ticket number or Ticket not belogs to you !!");
                }
            });
        }
    }
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Print Ticket<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="59%" border="0" align="center" cellpadding="0" cellspacing="0">

						<tr>
							<td align="center"><table width="74%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="109" height="15" align="right" class="size">&nbsp;</td>
										<td width="41" align="center">&nbsp;</td>
										<td width="162">&nbsp;</td>        
										<td width="219">&nbsp;</td>
									</tr>
									<tr>
										<td height="35" align="left" class="size">Ticket Number</td>
										<td align="center"><strong>:</strong></td>
										<td><input type="text" name="tktno" id="tktno" /></td>        
										<td align="center"><span class="size">
												<input  type="button" class="btn btn-primary" name="search" id="search" value="Ticket Status" onclick="ticketstatus();" />
										</span></td>
									</tr>



									<tr>
										<td height="35" colspan="5" align="center" class="size">&nbsp;</td>
									</tr>
							</table></td>
						</tr>

						<tr>
							<td></td>
						</tr>
					</table>
					<table width="700" border="0" align="center" cellpadding="2" cellspacing="0" style="font-size:14px;">
						<tbody>

							<tr align="center" valign="top">
								<td height="25" id="fare">&nbsp;</td>
							</tr>

						</tbody>
					</table>

					<br />
					<br />
					<br />
			</div>
			</div>
		</main>
	</section>
</div>
