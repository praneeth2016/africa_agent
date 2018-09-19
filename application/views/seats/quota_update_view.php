<script>
 function getServiceDetails()
        {
            var service=$('#service').val();
          
            
            if(service == 0)
            {
                alert("Please Provide Service Number");
                $("#service").focus();
                return false;
            }
            else
            {
            $.post("GetServiceReport_quata",{service:service},function(res){
        // alert(res);
         if(res==0)
        
          $('#tbl').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
          else
          $('#tbl').html(res);  
          });
                     
            }
        }
function showLayout(sernum,travel_id,s)
{
var cnt=$('#hf').val();
$.post("getLayoutForQuota",{sernum:sernum,travel_id:travel_id,s:s},function(res){
//alert(res);   
$('#trr'+s).html(res);
$('#uqi'+s).hide();
$('#uqii'+s).show();
for(var i=1;i<=cnt;i++)
{
$('#trr'+i).hide();
}
$('#trr'+s).show();
});

}
function agentType(s)
{
var id=$('#atype'+s).val();
$.post("SelectAgentType",{id:id,s:s},function(res){
 if(id==1)
{
$('#uqi'+s).show();
$('#uqa'+s).hide();
$('#uqii'+s).html(res);
$('#uqii'+s).show();
}
else if(id==2)
{
$('#uqa'+s).show();
$('#uqi'+s).hide();
$('#uqii'+s).html(res);
$('#uqii'+s).show();
}
else
{
$('#uqa'+s).hide();
$('#uqi'+s).hide();
$('#uqii'+s).hide();
}
});
}
function  chkk(seatname,s,idd){
 //$('#chkd'+s).show(); 
 if($('#unchkd'+s).is(':visible')){
     alert('Grab and Release cannot perform at a time!');
   $("#"+idd).attr('checked', false);
   return false;
 } else{ 
 
 if($('#chkd'+s).is(':visible')){
   $( "#chkd"+s).show();  
 }else{
   $( "#chkd"+s).show();
 }
  var gg2='';
 var gg=$( "#gb"+s).html();

 // if check box is checked 
 if($("#"+idd).is(":checked")){
  if(gg=='' || gg=='&nbsp;')
     gg2=seatname;
     else
  gg2=gg+","+seatname;
 $( "#gb"+s).html(gg2);

 }else{//check box not chcked
      //alert("dfsf");
     var test=","+seatname;
     if(gg.indexOf(test)!="-1")
         test=","+seatname;
         else
           test=seatname;
             
  var result = gg.replace(test,'');  
  $( "#gb"+s).html(result);  

  
 }
  var ggg=$( "#gb"+s).html();
   if(ggg=='' || ggg=='&nbsp;')
     $( "#chkd"+s).hide();
 $( "#unchkd"+s).hide();
 }
 
}
function  unchkk(seatname,s,idd){
   if($('#chkd'+s).is(':visible')){
   alert('Grab and Release cannot perform at a time!');
   $("#"+idd).attr('checked', true);
   return false;
 } else{ 
 //$('#unchkd'+s).show();
 
 if($('#unchkd'+s).is(':visible')){
   $( "#unchkd"+s).show();  
 }else{
   $( "#unchkd"+s).show();
 }
   var gg2='';
 var gg=$( "#rl"+s).html();
 
 // if check box is checked 
 if($("#"+idd).is(":checked")){
    var test=","+seatname;
     if(gg.indexOf(test)!="-1")
         test=","+seatname;
         else
           test=seatname;
            
  var result = gg.replace(test,'');  
  $( "#rl"+s).html(result);
  
 }else{//check box nt chcked
    if(gg=='' || gg=='&nbsp;')
          
     gg2=seatname;
     
     else
  gg2=gg+","+seatname;
 $( "#rl"+s).html(gg2); 
  
 }
 var ggg=$( "#rl"+s).html();
   if(ggg=='' || ggg=='&nbsp;')
     $( "#unchkd"+s).hide();
 $( "#chkd"+s).hide();
 
}//else
}

function quotaUpdate(sernum, travel_id, s, c)
    {

        var seats = '';
        if (c == 1)//grab
            seats = $("#gb" + s).html();
        else if (c == 2)//release
            seats = $("#rl" + s).html();

        var agent_type = $('#atype' + s).val();
        var agent_id = $('#ag' + s).val();
        var ga = $('#ag' + s).val();

        if ((agent_type == '' || agent_type == 0) && c == 1)
        {
            alert('please select Agent Type!');
            return false;
        }
        if ((agent_id == '' || agent_id == 0) && c == 1)
        {
            alert('Kindly Select Agent Name and update the quota!');
            return false;
        }
        else
        {
            var r = confirm("Are sure,you want Update Quota!");
            if (r == true)
            {
                if (c == 1)//grab
                    $('#gbupdt' + s).html('Please wait...');
                else if (c == 2)
                    $('#rlupdt' + s).html('Please wait...');
//alert(arr);
                $.post("UpdateAndValidate", {service_num: sernum, seat_names: seats, travel_id: travel_id, agent_type: agent_type, agent_id: agent_id, c: c}, function (res) {
//alert(res);  
                    if (res == 1)//for grabbing
                    {
                        alert('Seats are Grabbed successfully!');
                        $("#chkd" + s).hide();
                        $("#gb" + s).html('');//making span value as null
                        $('#gbupdt' + s).html('Save Changes');
                        viewLayoutQuota(sernum, travel_id, s);

                    }
                    else if (res == 2) { // for release
                        alert('Seats are Released successfully!');
                        $("#unchkd" + s).hide();
                        $("#rl" + s).html('');  //making span value as null 
                        $('#rlupdt' + s).html('Save Changes');
                        viewLayoutQuota(sernum, travel_id, s);
                    }
                    else
                    {
                        alert('There was a problem occured, Kindly contact 7799099995');
                    }
                });
            }
            else
            {
                return false;
            }
        }
    }
function viewLayoutQuota(sernum,travel_id,s)
{
$('#trr'+s).show();
$('#trr'+s).html('please wait..');
var cnt=$('#hf').val();
$.post("DisplayLayoutForQuota",{sernum:sernum,travel_id:travel_id},function(res){
$('#trr'+s).html(res);
for(var i=1;i<=cnt;i++)
{
$('#trr'+i).hide();
}
$('#trr'+s).show();
});   
}
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Quota Updation<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td><table border="0" cellpadding="0" cellspacing="0" align="center">
							<tr>
							  <td width="120" height="35" align="right" class="size">Service No / Name </td>
							  <td width="26" align="center"><strong>:</strong></td>
							  <td width="111"><?php
								$js = 'id="service" class="inputfield"';
								echo form_dropdown('from', $services, '', $js);           
							?></td>
							  <td width="46">&nbsp;</td>
							  <td height="35" colspan="4" align="center" class="size"><input  type="button" class="btn btn-primary" name="search" id="search" value="Submit" onClick="getServiceDetails()" /></td>
							</tr>
						  </table></td>
					  </tr>
					  <tr>
						<td id="tbl">&nbsp;</td>
					  </tr>
					  <tr>
						<td id="tbl1">&nbsp;</td>
					  </tr>
					</table>
					<br />
					<br />
		</div>
			</div>
		</main>
	</section>
</div>