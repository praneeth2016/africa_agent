<script>
      
    function selectAll(){ //for selecting all checkboxes
        if($('#selectck').is(":checked"))
		{
            $('.chkbox').attr('checked',true); //checknig all cheeckboxes   
            $('.tbox').attr('disabled',false);
        }
        else
		{
           $('.chkbox').attr('checked',false);   //unchecking all select boxes 
           $('.tbox').attr('disabled',true); 
        
        }
     
     }

</script>
<script>
 function enabledit(k)
  {
                    if ($('#chk'+k).is(':checked'))
                    {
                 	  $('#msg'+k).attr('disabled',false); 
					  $('#msg'+k).focus();
                    }
                   else
                    {
           			  $('#msg'+k).attr('disabled',true);  
                    }
    
}
</script>
<script>
function getService()
{
	var services=$('#services').val();
	var date_from=$('#date_from').val();

	if(city=='0')
	{
    	alert("select city");
	    $('#city').focus();
    	return false;
	}
	else
	{
		$.post("ServiceDisplay1",{services:services},function(res)
		{
	  		//alert(res);
    		if(res==0)
         		$('#loadlayout').html("<span style='color:red;margin:200px'>No Service available on selected Date</span>");
			else
  				$('#loadlayout').html(res);  
    
		});
	}
}
</script>

<script>
function ServiceSendSMS()
{
	var services=$('#services').val();
	var time=$('#time').val();
	var timef=$('#timef').val();
   if(services=='0')
	{
    	alert("Select Service no!");
	    $('#services').focus();
    	return false;
	}
	 if(time=='')
	{
    	alert("Select Delay Time!");
	    $('#time').focus();
    	return false;
	}
	 if(timef=='')
	{
    	alert("Select Delay Time in Hours or Minutes!");
	    $('#timef').focus();
    	return false;
	}
		 else {
			var r=confirm("Are You sure ,You want to send the delay service SMS");
			if(r==true){
              $.post('ServiceSendSMS',{time:time,services:services,timef:timef},function(res){
                 //alert(res);
                if(res==0){
                     $('#spamsg2').html("<span style='color:red;margin:200px'>\n\
         message has  not been sent</span>");
                     //$('#spamsg2').html("message has  not been sent");
                      }
                else
                $('#spamsg2').html("<span style='color:red;margin:200px'>\n\
            message has  been sent</span>");

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
					<h3 class="panel-title">Delay SMS<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					 <table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
								  <td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									 
									  <tr>
					 <td valign="top">
						 <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top:#f2f2f2 solid 1px;">
										  <tr>
											<td>&nbsp;</td>
											<td height="28">&nbsp;</td>
											<td height="28">&nbsp;</td>
											<td>&nbsp;</td>
											<td height="28">&nbsp;</td>
											<td height="28">&nbsp;</td>
											<td height="28">&nbsp;</td>
										  </tr>
										  <tr>
											<td>&nbsp;</td>
						 <td height="25"><span class="size">Select Service No:</span></td>
						  <td height="25">
						  
						  <?php   
						  $js = 'id="services"';
							  echo form_dropdown('services',$services,"",$js);
							  ?>		  </td>
						  <td>&nbsp;</td>
						  <td height="25"><span class="size">Delay Time:</span></td>
					   <td height="25">
						<?php  $js = 'id="time""width:50px"';
							  echo form_dropdown('time',$time,"",$js);
							  $timef=array(""=>"--- select ---","hrs"=>"Hours","min"=>"Minutes");
							   $js1 = 'id="timef""width:50px"';
							  echo form_dropdown('timef',$timef,"",$js1);
							  ?></td>
							<td width="139" height="25">
							 <input  type="button" class="btn btn-primary" value="Submit" id="getservice2" name="getservice2" onClick="ServiceSendSMS()" ></td>
										  </tr>
										</table></td>
									  </tr>
									  <tr>
										<td height="2"></td>
									  </tr>
									  <tr>
										<td>&nbsp;</td>
									  </tr>
									  <tr>
										<td>  <span id="loadlayout">
					 </span>
						<span id="spamsg" ></span>
						<span id="spamsg2"></span></td>
									  </tr>                  
									</table>
						 </td>
								</tr>
							  </table>
				</div>
			</div>
		</main>
	</section>
</div>