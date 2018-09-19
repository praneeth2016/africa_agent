<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Cancellation Terms<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="83%" border="0" cellpadding="0" cellspacing="0">
									  
									  <tr>
										<td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
										 
										 
										<?php  $cnt=1;
						if($canc=='' || $canc=='NULL') 
							  {
								  echo 'No cancellation Terms has added!';
							  }
							  else
							  {
								$y=explode("@",$canc);
								$rcnt=count($y);
								for($j=0;$j<$rcnt;$j++)
								{
								 
								$z=explode("#",$y[$j]);
								$hour=$z['0'];
								$hour1=$z['1'];
								$ct=$z['2'];
								?>
							   
										  <tr>
											<td width="22" height="25">&nbsp;</td>
											<td height="25" align="center">
											<?php
							  echo 'From &nbsp;<strong><span style="color:#990000;"> '.$hour.'</span></strong> &nbsp; Hours &nbsp; To &nbsp;<strong><span style="color:#990000;"> '.$hour1.'</span></strong> &nbsp; Hours &nbsp; Cancellation &nbsp; is &nbsp;<strong><span style="color:#990000;"> '.$ct.'</span></strong> %
							  <input type="hidden" name="timehr'.$cnt.'" id="timehr'.$cnt.'" value="'.$hour.'" />
							  <input type="hidden" name="timehr1'.$cnt.'" id="timehr1'.$cnt.'" value="'.$hour1.'" />
							  <input type="hidden" name="ct'.$cnt.'" id="ct'.$cnt.'" value="'.$ct.'" />';
						?>
						</td></tr>
						<?php
						}
					 
							  } ?> 
										
										</table></td>
									  </tr>
									  <tr>
										<td height="5"></td>
									  </tr>
									  <tr>
										<td>&nbsp;</td>
									  </tr>                  
					</table>
				</div>
			</div>
		</main>
	</section>
</div>