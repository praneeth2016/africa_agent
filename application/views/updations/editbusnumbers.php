<?php 
foreach($list as $row){  						
							$id = $row->id;
							$bus_number = $row->bus_number;
							}

?>
<script type="text/javascript">
	   
    function validation() {
        
        var bus_number = $('#bus_number').val();
		var id = $('#id').val();               
		var letters = /^[0-9a-zA-Z]+$/;       
        if (bus_number == "")
        {
            alert('Kindly provide Bus Number');
			document.getElementById('bus_number').focus();
        }
		else if (!letters.test(bus_number)) {
                    alert('Enter Bus Number Without Spaces');
                    document.getElementById('bus_number').focus();
                    
        }				
        else
        {
            var con = confirm("Are You Sure You Want To ADD Bus Number");
            if (con == true)
            {               
                $.post("edit_bus_numbers1", {bus_number: bus_number,id:id}, function (res)
                {//alert(res);
                    if (res == 1) {
						alert('Successful');
                        window.location = '<?php echo base_url('Updations/bus_numbers'); ?>';
                    }
                    else {
						alert('Not Updated');
                       window.location = '<?php echo base_url('Updations/bus_numbers'); ?>';
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
					<h3 class="panel-title">Edit Bus Numbers<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="100%" border="0" cellspacing="1" cellpadding="1" style="margin-top:15px">
						<tr>
							<td>&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td>&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td align="center">Bus Number : </td>
							<td><label>
								<input type="text" name="bus_number" id="bus_number" value="<?php echo $bus_number; ?>" />
								<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
								</label></td>
							<td align="center">&nbsp;</td>
						</tr>	
						<tr>
							<td>&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td>&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input  type="button" class="btn btn-primary" name="btn" id='btn' value="Update" onclick="validation()" /></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>