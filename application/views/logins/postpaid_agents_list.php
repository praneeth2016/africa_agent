<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Postpaid List<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<div class="row-fluid">
						<div class="span12">
							<section class="panel">
								<header class="panel-heading"><span class="label bg-danger pull-right"><a href="<?php echo base_url('Logins/add_agent?key=postpaid'); ?>" >Add Postpaid Agent</a></span> &nbsp; </header>
								<div class="pull-out">
								<script>
								$(document).ready(function(){
								$('#myTable').DataTable();
								});
								</script>
									<table class="table table-striped m-b-none text-small" id="myTable">
										<thead>
											<tr>
												<th>Name</th>
												<th>Username</th>
												<!--<th>Password</th>-->
												<th>Mobile</th>
												<th>Balance</th>
												<th>Margin</th>
												<th>Paytype</th>
												<th>Status</th>
												<th>Action</th>									
											</tr>
										</thead>
										<tbody>
										<?php 
										foreach($list as $row){
				   $uid=$row->id;
				   $status= $row->status;
				   $e='Edit';
				   if($status==1)
				   {
					$x='Active';   
				   }
				   else {
					   $status=0;
					 $x='Inactive';  
				   }
										
											echo '<tr>
												<td>'.$row->name.'</td>
												<td>'.$row->uname.'</td>
												<!--<td>'.$row->password.'</td>-->
												<td>'.$row->mobile.'</td>
												<td>'.$row->balance.'</td>
												<td>'.$row->margin.'</td>
												<td>'.$row->pay_type.'</td>
												<td>'.$x.'</td>
												<td>'.anchor("logins/edit_agent?uid=".$uid."&key=postpaid", "Edit", "EditAgent").'</td>
											</tr>';
																	}
											?>
											<!--<td></td>-->
										</tbody>
									</table>
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
</div>