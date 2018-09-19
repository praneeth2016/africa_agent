<!DOCTYPE html>
<html lang="en">
<head>
 <script>
	function validateLogin() {
		var Username = $('#username').val();
		var Password = $('#password').val();
		var travel_id = $('#travel_id').val();
		
		if (Username == "") {
			alert('Provide User Name');
			$('#username').focus();
			return false;
		} else if (Password == "") {
			alert('Provide Password');
			$('#password').focus();
			return false;
		} else {
			$.post('<?php echo base_url('Login/validateLogin1'); ?>',{Username:Username,Password:Password,travel_id:travel_id}, function (res) {
		//	alert(res);
				if (res == 1) {
					$('#login').submit();
				} else {
					alert('Invalid Username or Password');
					return false;
				}
			}
			);
		}
	}
  </script>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Ticketengine.in</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/styles.css'); ?>" />

</head>
<body class="lgn-bg-5">
<div class="container">
	<form class="form-signin" action="<?php echo base_url('booking'); ?>" name="login" id="login" class="padder"  method="post">
        <h2 class="form-signin-heading" style="font-size: 21px;text-align: center;">TicketEngine</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="User Name" required autofocus></br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
		<!--input type="hidden" name="travel_id" id="travel_id" value=""-->
        <div class="checkbox">
           
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="button" onClick="validateLogin();">Sign in</button>
    </form>
</div> <!-- /container -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>
</body>
</html>