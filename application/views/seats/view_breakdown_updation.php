
<div class="clearfix">
	<h4> <i class="icon-table"></i>Cancelled services</h4>
</div>
<div class="row-fluid">
	<div class="span12">
		<section class="panel">
			<header class="panel-heading">&nbsp; </header>
			<div class="pull-out">
				<script>
					$(document).ready(function(){
    				$('#myTable').DataTable();
					});
					</script>
				<table class="table table-striped m-b-none text-small" id="myTable">
					<thead>
						<tr>
							<th>Service No</th>
							<th>From</th>
							<th>To</th>
							<th>Current Date</th>
							<th>Breakdown Date</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($query as $rows){
							
								echo '<tr>
									<td>'.$rows->service_num.'</td>
									<td>'.$rows->from_name.'</td>
									<td>'.$rows->to_name.'</td>
									<td>'.$rows->current_date.'</td>
									<td>'.$rows->breakdown_date.'</td>									
								</tr>';
                                                        }
								?>
					</tbody>
				</table>
			</div>
		</section>
	</div>
</div>
