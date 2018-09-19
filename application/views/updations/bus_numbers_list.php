<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="icon-table"></i>Bus Number List<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<div class="row-fluid">
						<div class="span12">
							<section class="panel">
								<header class="panel-heading"><span class="label bg-danger pull-right"><a href="<?php echo base_url('Updations/add_bus_numbers'); ?>" >Add Bus Number</a></span> &nbsp; </header>
								<div class="pull-out">
								<script>
								$(document).ready(function(){
								$('#myTable').DataTable();
								});
								</script>
									<table class="table table-striped m-b-none text-small" id="myTable">
										<thead>
											<tr>
												<th>S No</th>
												<th>Bus Number</th>									
												<th>Action</th>									
											</tr>
										</thead>
										<tbody>
										<?php $i = 1 ;
										foreach($list as $row){	       						
										$id = $row->id;
											echo '<tr>
												<td>'.$i.'</td>
												<td>'.$row->bus_number.'</td>									
												<td>'.anchor("updations/edit_bus_numbers?id=".$id, "Edit", "EditAgent").'</td>
												</tr>';	
											$i++;								
										}
										
											?>
											
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