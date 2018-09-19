<div class="row-fluid">
   <div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Change Password<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
    <script>
        function cpwd()
        {
            var oldpassword = $("#oldpassword").val();
            var newpassword = $("#newpassword").val();
            var conpassword = $("#conpassword").val();

            if (oldpassword == "")
            {
                alert("Please Provide Old Password");
                $("#oldpassword").focus();
                return false;
            }

            if (newpassword == "")
            {
                alert("Please Provide New Password");
                $("#newpassword").focus();
                return false;
            }

            if (conpassword == "")
            {
                alert("Please Provide Confirm Password");
                $("#conpassword").focus();
                return false;
            }
            if (conpassword != newpassword)
            {
                alert("New Password and Confirm Pasword should be match!");
                $("#conpassword").focus();
                return false;
            }
            else
            {
                $.post("<?php echo base_url('Login/password_update'); ?>", {oldpassword: oldpassword, newpassword: newpassword, conpassword: conpassword}, function (res) {
                    //alert(res);
                    if (res == 1) //success
                    {
                        alert('Password updated successfully');
                    }
                    else
                    {
                        alert('Not updated');
                    }
                });
            }
        }
    </script>
    <!--div class="span6"> </div>
    <div class="span6">
        <form class="form-horizontal">
            <div class="row-fluid">
                <div class="span5">
                    <div class="control-group">
                        <label class="control-label">Old Password</label>
                        <div class="controls">
                            <input type="text" id="oldpassword" name="oldpassword" class="bg-focus">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">New Password</label>
                        <div class="controls">
                            <input type="text" id="newpassword" name="newpassword" class="bg-focus">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Confirm Password</label>
                        <div class="controls">
                            <input type="text" id="conpassword" name="conpassword" class="bg-focus" >
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="button" class="btn btn-primary" onClick="cpwd();" >Change</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div-->
	
	<form class="form-horizontal">
					  <div class="form-group">
						<label for="inputEmail3" class="col-sm-4 control-label">Old Password</label>
						<div class="col-sm-3">
							<input type="text" id="oldpassword" name="oldpassword" class="form-control bg-focus" placeholder="old password"/>
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-4 control-label">New Password</label>
						<div class="col-sm-3">
							<input type="text" id="newpassword" name="newpassword" class="form-control bg-focus" placeholder="New password"/>
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-4 control-label">Confirm Password</label>
						<div class="col-sm-3">
							<input type="text" id="conpassword" name="conpassword" class="form-control bg-focus" placeholder="Confirm password"/>
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-4 control-label">&nbsp;</label>
						<div class="col-sm-3">
							<input type="button" class="btn btn-primary" name="btn" id='btn' value="Update Password" onClick="cpwd();" />
						</div>
					  </div>
					</form>
				</div>
			</div>
		</main>
	</section>
</div>