<?php 
foreach($list as $row){  						
							$id = $row->id;
							$driver_name = $row->driver_name;
							$driver_number = $row->driver_number;
							}

?>
<script type="text/javascript">	   
    function validation() {       
        var driver_name = $('#driver_name').val();
        var driver_number = $('#driver_number').val();
		var id = $('#id').val();
        if (driver_name == "")
        {
            alert('Kindly provide Driver Name');
			document.getElementById('driver_name').focus();
        }else if (driver_number == "")
        {
            alert('Kindly provide Driver Number');
			document.getElementById('driver_number').focus();
        }		
        else
        {
            var con = confirm("Are You Sure You Want To add  Driver Details");
            if (con == true)
            {               
                $.post("edit_driver1", {driver_name: driver_name, driver_number: driver_number,id:id}, function (res)
                {//alert(res);
                    if (res == 1) {
						alert('Successful');
                        window.location = '<?php echo base_url('Updations/drivers'); ?>';
                    }
                    else {
						alert('Not Updated');
                       window.location = '<?php echo base_url('Updations/drivers'); ?>';
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
					<h3 class="panel-title">Vehicle Details<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">

					<form class="form-horizontal">
					  <div class="form-group">
						<label for="inputEmail3" class="col-sm-4 control-label">Driver Name</label>
						<div class="col-sm-3">
							<input type="text" name="driver_name" id="driver_name"  class="form-control" value="<?php echo $driver_name; ?>" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-4 control-label">Driver Mobile No</label>
						<div class="col-sm-3">
							<input type="text" name="driver_number" id="driver_number" class="form-control" value="<?php echo $driver_number; ?>" />
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-4 control-label">&nbsp;</label>
						<div class="col-sm-3">
							<input type="button" class="btn btn-primary" name="btn" id='btn' value="Submit" onclick="validation()" />
						</div>
					  </div>
					</form>
				</div>
			</div>
		</main>
	</section>
</div>
