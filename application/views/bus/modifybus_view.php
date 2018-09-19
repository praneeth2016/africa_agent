     
	 <script type="text/javascript">
		
		
		function saveBoard()
		{
			var city_name="";
			var city_id="";
			var board_point="";
			var bpid="";
			var lm="";
			var hhST="";
			var mmST="";
			var ampmST="";
			var ival=$("#nval").val();			
			var sernum=$("#sernum").val();
			
			for(var i=0;i<ival;i++)
			{
				var jval=$("#jval"+i).val();
				for(var j=0;j<jval;j++)
				{	
					if(($("#timehrST"+i+j).val()!="HH") && ($("#timemST"+i+j).val()!="MM") && ($("#tfmST"+i+j).val()!="AMPM"))
					{					
					
						if(city_name=="")
							{	
								city_name=$("#cityname"+i+j).val();
							}
							else
							{
								city_name=city_name+"#"+$("#cityname"+i+j).val();
							} 
																	
						if(city_id=="")
							{
								city_id=$("#cityid"+i+j).val();
							}
							else
							{
								city_id=city_id+"#"+$("#cityid"+i+j).val();
							}
						 
						if(board_point=="")
							{
								board_point=$("#bpname"+i+j).val();
							}
							else
							{
								board_point=board_point+"#"+$("#bpname"+i+j).val();
							}
						if(bpid=="")
							{
								bpid=$("#bpid"+i+j).val();
							}
							else
							{
								bpid=bpid+"#"+$("#bpid"+i+j).val();
							}
						if(lm=="")
							{
								lm=$("#lm"+i+j).val();
							}
							else
							{
								lm=lm+"#"+$("#lm"+i+j).val();
							} 
						if(hhST=="")
							{
								hhST=$("#timehrST"+i+j).val();
							}
							else
							{
								hhST=hhST+"#"+$("#timehrST"+i+j).val();
							}
						if(mmST=="")
							{
								mmST=$("#timemST"+i+j).val();
							}
							else
							{
								mmST=mmST+"#"+$("#timemST"+i+j).val();
							}
						if(ampmST=="")
							{
								ampmST=$("#tfmST"+i+j).val();
							}
							else
							{
								ampmST=ampmST+"#"+$("#tfmST"+i+j).val();
							}
																                                                if ($("#lm" + i + j).val() == "")
                    {
                        alert("Please Provide landmark for " + $("#bpname" + i + j).val());
                        $("#lm" + i + j).focus();
                        return false;
                    }
					}					
					
				}				
			}				
			if(hhST=="" && mmST=="" && ampmST=="")
			{
				alert("Please select atleast one boarding point time");
				return false;
			}
			//alert(sernum);alert(city_name);alert(city_id);alert(board_point);alert(bpid);
			//alert(lm);alert(hhST);alert(mmST);alert(ampmST);
							$.post("modify_saving",{sernum:sernum,city_name:city_name,city_id:city_id,board_point:board_point,bpid:bpid,lm:lm,hhST:hhST,mmST:mmST,ampmST:ampmST},function(res)
			{
				//alert(res);
				if(res==1)
				{
					alert("Boarding points are saved successfully !!");
					window.location='<?php echo base_url("Bus/modify_bus")?>';				
				}
				else
				{
					alert("There was a problem occurred, Try again");
				}
				
    		});
			 			 
		}
		</script>
		<script type="text/javascript">
		
		
		function saveDrop()
		{
			var city_name="";
			var city_id="";
			var drop_point="";
			var dpid="";
			var lm="";
			var hhST="";
			var mmST="";
			var ampmST="";
			var ival=$("#nval").val();
			
			var sernum=$("#sernum").val();
			
			for(var i=0;i<ival;i++)
			{
				var jval=$("#jval"+i).val();
				for(var j=0;j<jval;j++)
				{
					if($("#timehrST"+i+j).val()!="HH" && $("#timemST"+i+j).val()!="MM" && $("#tfmST"+i+j).val()!="AMPM")
					{					
					
						if(city_name=="")
						{
							city_name=$("#cityname"+i+j).val();
						}
						else
						{
							city_name=city_name+"#"+$("#cityname"+i+j).val();
						} 
						if(city_id=="")
						{
							city_id=$("#cityid"+i+j).val();
						}
						else
						{
							city_id=city_id+"#"+$("#cityid"+i+j).val();
						} 
						if(drop_point=="")
						{
							drop_point=$("#dpname"+i+j).val();
						}
						else
						{
							drop_point=drop_point+"#"+$("#dpname"+i+j).val();
						} 
						if(dpid=="")
						{
							dpid=$("#dpid"+i+j).val();
						}
						else
						{
							dpid=dpid+"#"+$("#dpid"+i+j).val();
						}
						if(lm=="")
						{
							lm=$("#lm"+i+j).val();
						}
						else
						{
							lm=lm+"#"+$("#lm"+i+j).val();
						}  
						if(hhST=="")
						{
							hhST=$("#timehrST"+i+j).val();
						}
						else
						{
							hhST=hhST+"#"+$("#timehrST"+i+j).val();
						}
						if(mmST=="")
						{
							mmST=$("#timemST"+i+j).val();
						}
						else
						{
							mmST=mmST+"#"+$("#timemST"+i+j).val();
						}
						if(ampmST=="")
						{
							ampmST=$("#tfmST"+i+j).val();
						}
						else
						{
							ampmST=ampmST+"#"+$("#tfmST"+i+j).val();
						}
                                                if ($("#lm" + i + j).val() == "")
                    {
                        alert("Please Provide landmark for " + $("#dpname" + i + j).val());
                        $("#lm" + i + j).focus();
                        return false;
                    }								
					}					
				}				
			}				
			if(hhST=="" && mmST=="" && ampmST=="")
			{
				alert("Please select atleast one Drop point time");
				return false;
			}
			//alert(sernum);alert(city_name);alert(city_id);alert(drop_point);alert(dpid);
			//alert(lm);
							$.post("modify_dp",{sernum:sernum,city_name:city_name,city_id:city_id,drop_point:drop_point,dpid:dpid,lm:lm,hhST:hhST,mmST:mmST,ampmST:ampmST},function(res)
			{
				//alert(res);
				if(res==1)
				{
					alert("Drop points are saved successfully !!");
					window.location='<?php echo base_url("Bus/modify_bus")?>';
				}
				else
				{
					alert("There was a problem occurred, Try again");
				}
				
    		});
			 			 
		}
		</script>
	 
        <script>
            /* method for dispaly arrival time in text box*/
    function arrtime(id)
	{
        var timehr=$('#timehr'+id).val();
        var timehrj=$('#timehrj'+id).val();
         var timem=$('#timem'+id).val();
        var timemj=$('#timemj'+id).val();
        var stimeval=timehr+":"+timem;
        var jtimeval=timehrj+":"+timemj;
         if(timehr!='' && timehrj!='')
         {
           $.post("arrivalTimeCalCon",{stimeval:stimeval,jtimeval:jtimeval},function(res){
               //alert(res);
               $('#arrtime'+id).val(res);
           });  
         }
     }//close arrtime()
            function changeService()
			{
             var service=$('#ser_list').val();
             if(service==0)
                 {
                     $('#hf').hide();
                     //$('#hid').hide();
                     alert('select any service number');
                     return false;
                 }
            else
			 {   
            $('#hf').show();
            $('#hid').hide();
            $('#hid').empty();

            }
            }
            function SelectType()
			{
							 var service=$('#ser_list').val();
			 				 var board=$('#mod').val();
							 
							if(service=='0')
                             {
                             alert("please provide service name");
                             $('#ser_list').focus();
                             return false;
                             }
                            if(board=='0')
                             {
                             alert("please provide modify type ");
                             $('#mod').focus();
                             return false;
                             }
				else
				{				
                 if(board==1)
                 {
                 
                  $('#hid').show();
                  $.post("DoModify",{svrno:service},function(res){
				  
                  $('#hid').html(res);   
                  });
                  $.post("getCity",{svrno:service},function(res){
                  
                  });
                 }
             else if(board==2)
                 {
                
                  $('#hid').show();
                  $.post("DoModifyDrop",{svrno:service},function(res){
                  $('#hid').html(res);   
                  });
                  $.post("getCity",{svrno:service},function(res){
                  
                  });   
                 }
                 
             else if(board==3)
                 {
                
                  $('#hid').show();                  
                  $.post("ModifyRoutes",{svrno:service},function(res){
                  $('#hid').html(res);    
                  });
                 }
             else if(board==4)
                  {
                
                  $('#hid').show();
                  $.post("ModifyAminity",{svrno:service},function(res){
                  $('#hid').html(res);  
                  var checkval=$('#val').val();
                  var checkval1=$('#val1').val();
                  var checkval2=$('#val2').val();
                  var checkval3=$('#val3').val();
                       if(checkval=='yes')
                      {
                          $("#ck").attr('checked', true);
                      }
                       if(checkval1=='yes')
                      {
                          $("#ck1").attr('checked', true);
                      }
                       if(checkval2=='yes')
                      {
                          $("#ck2").attr('checked', true);
                      }
                       if(checkval3=='yes')
                      {
                          $("#ck3").attr('checked', true);
                      }
                  });
                  }
                  else if(board==5)
                 {
                 
                  $('#hid').show();
                  $.post("seat_layout",{srvno:service},function(res){
                  $('#hid').html(res);    
                  });
                  
                 }
				  else if(board==6)
                 {
                 
                  $('#hid').show();
                  $.post("modify_model",{srvno:service},function(res){
                  $('#hid').html(res);    
                  });
                  
                 }
				  else if(board==7)
                 {
                 
                  $('#hid').show();
                  $.post("service_tax",{srvno:service},function(res){
                  $('#hid').html(res);    
                  });
                  
                 }
                 else
				 {
                 $('#hid').hide();
                 } 
				 }   
            }
            
            
            
            function Modify_type()
			{
               $('#hid').hide();
               $('#hid').empty();
            }
            
           function saveModification(srvno,travid,i){
		  
                var arr=[];
                var chk=0;
                var c;
                var loc,bpid,bp_db,vp;
                
                $(":checkbox").each(function() {
                    if (this.checked) {
                    chk=this.value;
                    arr.push(chk);
                    }
                    });
                   
                   if(chk=='')
                      {
                      alert('please select checkbox to Modify');
                      }
                       else
                      {
                      var r=confirm('Are sure,you want to Modify?');
                      if(r==true)
                       {
                        var cnt=arr.length;
                        
                        
                       for(c=0;c<i;c++)
                        {
                            var k=arr[c];
							if($('#loc'+k).val()=='0')
                             {
                             alert("please provide location name");
                             $('#loc'+k).focus();
                             return false;
                             }
                            if($('#bp'+k).val()=='')
                             {
                             alert("please provide boarding point");
                             $('#bp'+k).focus();
                             return false;
                             }
                             if($('#lm'+k).val()=='')
                             {
                             alert("please provide landmark");
                             $('#lm'+k).focus();
                             return false;
                             }
                             if($('#timehr'+k).val()=='00')
                             {
                             alert("please provide time");
							 $('#timehr'+k).focus();
                             return false;
                             }
							 if($('#tfm'+k).val()=='0')
                             {
                             alert("please time mode" );
							 $('#tfm'+k).focus();
                             return false;
                             }
                             
                             else{
                            if(c==cnt)
                                {
                                    break;
                                }
                                else{
                         if(c==0)
                         {
                          vp=$('#vani' +k).val();  
                          loc=$('#loc' +k).val();
                          bpid=$('#bpid' +k).val();
                          bp_db=$('#bp' +k).val()+"#"+$('#timehr' +k).val()+":"+$('#timem' +k).val()+" "+$('#tfm' +k).val()+"#"+$('#lm' +k).val();
                         }
                         else
						 {
                             vp=vp+"!"+$('#vani' +k).val();
                             loc=loc+"!"+$('#loc' +k).val();
                             bpid=bpid+"!"+$('#bpid' +k).val();
                             bp_db=bp_db+"!"+$('#bp' +k).val()+"#"+$('#timehr' +k).val()+":"+$('#timem' +k).val()+" "+$('#tfm' +k).val()+"#"+$('#lm' +k).val();
						 }
                        }
                        }
                        }
						 
                $.post('modify_saving',{service_no:srvno,travel_id:travid,arr:bp_db,loc:loc,bpid:bpid,vp:vp},function(res){
               // alert(res);
				//$('#fg').html(res); 
                if(res==1)
                    {
                      alert("successfully saved");
                      SelectType();
                    }
                else
				{
                      alert("not saved");
                }    
               });    
               }
                else
                       {
                        return false;
                       }
                     }
               }
              
              function DeleteBP(i){
                     var service=$('#ser_list').val();
                     var bpid=$('#bpid' +i).val();
                     var con=confirm('Are you sure,you want to Delete?');
                     if(con==true)
                         {
                             var r=confirm('The information regarding this boarding/dropping point will be deleted..');
                             if(r==true)
                                 {
                     $('#tr'+i).remove();
                     $.post('deletebpFromDb',{bp:bpid,service_num:service},function(res){
                                      
                           if(res==1)
                             {
                                 alert('deleted');
								 SelectType();
                             }
                           else{
                               alert('not deleted');
                           }
                     });
                                 }
                         }
                         else
                             {
                               return false;
                             }    
              }
             
              
              function addNewBP(srvno,travid,i)
              {
			  //alert(i);
                var s=i+1;
                var k=$('#hidd').val();
                var sn=parseInt(k)+1;
                //alert(sn);
                  $.post("addnewbp1",{srvno:srvno,s:sn},function(res)
                   {
                   // alert(res); 
					  if(i==0)
					  {
					  $('#hid').html(res); 
					  } 
					  else
					  { 
                       $('#tr'+i).after(res);
					  }
					  $('#hidd').val(sn);
                   });
                   $.post("getCity",{srvno:srvno},function(res){
                   });
              }
              function Saveaminities(srvno,travid)
                {
                   var ck,ck1,ck2,ck3;
                   
                   var r=confirm('Are you sure,you want to Modify?');
                      if(r==true)
                           {
                               if($('#ck').is(':checked'))
                               {
                                ck='yes';
                               }
                               else{
                                ck='no';
                               }
                               if($('#ck1').is(':checked'))
                               {
                                ck1='yes';
                               }
                               else{
                                ck1='no';
                               }
                               if($('#ck2').is(':checked'))
                               {
                                ck2='yes';
                               }
                               else{
                                ck2='no';
                               }
                               if($('#ck3').is(':checked'))
                               {
                                ck3='yes';
                               }
                               else{
                                ck3='no';
                               }
                               var tot=ck+"#"+ck1+"#"+ck2+"#"+ck3;
                               
                            $.post('modify_save_aminity',{service_no:srvno,travel_id:travid,tot:tot},function(res){
                             
                                if(res==1)
                                {
                                    alert('successfully added');
                                }
                                else{
                                    alert('not added');
                                }
                            });
                           }
                           else{
                               return false;
                           }
                      }
               function addNewDP(srvno,travid,i)
                {
                    // alert(i);      
                    var s=i+1;
                    var k=$('#hidd').val();
                    var sn=parseInt(k)+1;
                
                   $.post("addnewdp1",{srvno:srvno,s:sn},function(res)
                   {
				   if(i==0)
					  {
					  $('#hid').html(res); 
					  } 
					  else
					  { 
                       $('#tr'+i).after(res);
					  }
					  $('#hidd').val(sn);
                   
                   });
                   
                   $.post("getCity",{srvno:srvno},function(res){
                   });
                 
                }
              
              function saveDP(srvno,travid,i){
                var arr=[];
                var chk=0;
                var c;
                var loc,bpid,dp;
                
                $(":checkbox").each(function() {
                    if (this.checked) {
                    chk=this.value;
                    arr.push(chk);
                    }
                    });
                   
                   if(chk=='')
                      {
                      alert('please select checkbox to Modify');
                      }
                       else
                      {
                      var r=confirm('Are you sure,you want to Modify?');
                      if(r==true)
                       {
                        var cnt=arr.length;
                        
                        
                       for(c=0;c<i;c++)
                        {
                            var k=arr[c];
							 if($('#loc'+k).val()=='0')
                             {
                             alert("please provide location name");
                             $('#loc'+k).focus();
                             return false;
                             }
                            if($('#dp'+k).val()=='')
                             {
                             alert("please provide dropping point");
                             $('#dp'+k).focus();
                             return false;
                             }
                             
                             
                             else{
                            if(c==cnt)
                                {
                                    break;
                                }
                                else{
                         if(c==0)
                         {
                          
                          loc=$('#loc' +k).val();
                          bpid=$('#bpid' +k).val();
                          dp=$('#dp' +k).val();
                         }
                         else{
                             
                         loc=loc+"!"+$('#loc' +k).val();
                         bpid=bpid+"!"+$('#bpid' +k).val();
                         dp=dp+"!"+$('#dp' +k).val();
                         }
                        }
                        }
                        }
                $.post('modify_dp',{service_no:srvno,travel_id:travid,arr:dp,bpid:bpid,loc:loc},function(res){
                //alert(res);  
                if(res==1)
                    {
                      alert("successfully saved");
                      SelectType();
                    }
                else{
                      alert("not saved");
                    }    
                 });    
               }
                else
                       {
                        return false;
                       }
                     }
               }
                function DeleteRoutes(i){
                     var service=$('#ser_list').val();
                     var fromid=$('#fromid'+i).val();
                     var toid=$('#toid'+i).val();
                     var con=confirm('Are you sure,you want to Delete?');
                     if(con==true)
                         {
                          var r=confirm('The information regarding this routes will be deleted');
                          if(r==true)
                              {
                        $('#tr'+i).remove();
                        
                        $.post('deleterouteFromDb',{svrno:service,fromid:fromid,toid:toid},function(res){
                        //alert(res);                 
                        if(res==0)
                             {
                              alert('deleted');
                              SelectType();
                             }
                         else{
                             alert('not deleted');
                         }
                         });
                         }
                         }
                         else
                             {
                               return false;
                             }
                }
                function saveRoutes(srvno,travid,i){
                var arr=[];
                var chk=0;
                var c,seat,lseat,useat,str_time,journey_time,from,to,arr_time,status;
                var bus=$('#bus').val();
                var model=$('#busmodel').val();
                var sertype=$('#sertype').val();
                var seroute=$('#seroute').val();
                var sername=$('#sername').val();
				var tseat=$('#tots').val();
                var lseats=$('#ls').val();
                var useats=$('#us').val();
                var status=$('#status').val();
                $(":checkbox").each(function() {
                    if (this.checked) {
                    chk=this.value;
                    arr.push(chk);
                    }
                    });
                   
                   if(chk=='')
                      {
                      alert('please select checkbox to Modify');
                      }
                   else
                      {
                        var cnt=arr.length;
                       
//                      alert("i:"+i+"cnt:"+cnt);

                       for(c=0;c<i;c++)
                        {
                           
                             var k=arr[c];
                           if($('#seat_fare'+k).val()=='')
                           {
                             	 alert("provide seat fare");
                        	 	 $('#seat_fare'+k).focus();
                        		 return false;
                           }
                          if($('#lowerseat_fare'+k).val()=='')
                            {
                        		  alert("provide lower seat fare");
                       			  $('#lowerseat_fare'+k).focus();
                        		  return false;
                            }
                          if($('#upperseat_fare'+k).val()=='')
                           {
                         		 alert("provide upper seat fare");
                         		 $('#upperseat_fare'+k).focus();
                         		 return false;
                           }
                          if($('#from'+k).val()=='0')
                           {
                         		 alert("provide from city");
						 		 $('#from'+k).focus();
                         		 return false;
                           }
                          if($('#to'+k).val()=='0')
                          {
                         		 alert("provide to city");
						 		 $('#to'+k).focus();
                         		 return false;
                          }
                          if($('#timehr'+k).val()=='00' )
                          {
                          		 alert("provide starting   time");
						 		 $('#timehr'+k).focus();
                          		 return false;
                          }
						  if( $('#arrth'+k).val()=='00')
                          {
                          		 alert("provide  Arrival  time");
						 		 $('#arrth'+k).focus();
                         		 return false;
                          }
                          if($('#tfms'+k).val()=='0')
                             {
                             alert("please provide time mode" );
							 $('#tfms'+k).focus();
                             return false;
                             }
							 if($('#tfma'+k).val()=='0')
                             {
                             alert("please provide time mode" );
							 $('#tfma'+k).focus();
                             return false;
                             }
                          else
						  {
                                     
                            if(c==cnt)
                                {
                                    break;
                                }
                                else
								{
                        		 if(c==0)
                       			  {
                              
                        			  seat=$('#seat_fare' +k).val();
                        			  from=$('#from' +k).val();
                        			  to=$('#to' +k).val();
                         			 //arr_time=$('#arrtime' +k).val();
                         			  lseat=$('#lowerseat_fare' +k).val();
                         			  useat=$('#upperseat_fare' +k).val();
                        			  str_time=$('#timehr' +k).val()+":"+$('#timem' +k).val()+""+$('#tfms' +k).val();
                        			  // journey_time=$('#timehrj' +k).val()+":"+$('#timemj' +k).val();
									  arr_time=$('#arrth' +k).val()+":"+$('#arrtm' +k).val()+""+$('#tfma' +k).val();
                        			 }
                        			else
									 {
                        			   seat=seat+"!"+$('#seat_fare' +k).val();
                        			   from=from+"!"+$('#from' +k).val();
                      				   to=to+"!"+$('#to' +k).val();
                       				   //arr_time=arr_time+"!"+$('#arrtime' +k).val();
                      				   lseat=lseat+"!"+$('#lowerseat_fare' +k).val();
                      				   useat=useat+"!"+$('#upperseat_fare' +k).val();
                       				   str_time=str_time+"!"+$('#timehr' +k).val()+":"+$('#timem' +k).val()+""+$('#tfms' +k).val();
                      				   // journey_time=journey_time+"!"+$('#timemj' +k).val();
								   	  arr_time=arr_time+"!"+$('#arrth' +k).val()+":"+$('#arrtm' +k).val()+""+$('#tfma' +k).val();
                         			 }
                        			}
                        }
                              k++;
                        }
                        
                               var r=confirm('Are you sure,you want to Modify?');
                       if(r==true)
                        {    
                             // alert(seat);
                          $.post('save_routes',{service_no:srvno,travel_id:travid,stime:str_time,seat:seat,lseat:lseat,useat:useat,from:from,to:to,art:arr_time,bus:bus,model:model,tseat:tseat,lseats:lseats,useats:useats,status:status,sertype:sertype,seroute:seroute,sername:sername},function(res){
                   // alert(res);
                          // $("#fg").html(res);
                      if(res==1)
                        {
                                 alert("successfully saved");
                                 SelectType();
                        }
                     elseif(res==0)
					   {
                                  alert("not saved");
                       }    
                      });    
                       }
                       else
					   {
                                  return false;
                       }
                     }
               }
               function addNewRoutes(srvno,travid,i,bus)
                   {                 
				  
                         	  		 var s=i+1;
               				    	 var k=$('#hidd').val();
               						 var sn=parseInt(k)+1;
                
                  			$.post("addNewRoutesDb",{s:sn,bus:bus},function(res)
                            {
							 
                     		 $('#tr'+i).after(res);
					 		
					 		 $('#hidd').val(sn);
                    		
                            });
                   }
               function enabledit(i)
               		{
                    
                if ($('#c'+i).is(':checked'))
                   {
                  $('#seat'+i).attr('disabled',false); 
                  $('#seat'+i).focus();
                   }
                   else
                    {
            $('#seat'+i).attr('disabled',true);   
                    }
               }
               
               function seatUpdate(srvno,travid){
                   var chk=0;
                   var arr=[];
                   var c;
                    $(":checkbox").each(function() {
                      if (this.checked) {
                      chk=this.value;
                      arr.push(chk);
                      }
                    });
                    if(chk=='' || chk==0)
                    {
                    alert('please select checkbox to Update the Seat name');
                    }
                    
                    else
                    {
                    
                    var arrlength=arr.length;
                    var rc,oldsn,newsn;
                    for(c=0;c<arrlength;c++)
                        {
                if($('#seat'+arr[c]).val()=='')  
                    {
                        alert('Seat name should not be empty');
                        $('#seat'+arr[c]).focus();
                        return false;
                    }               
                    rc=arr[c].split('-');
                          if(c==0)
                              {
                          oldsn=rc[1];
                          newsn=$('#seat'+arr[c]).val();

                              }
                          else
                              {
                          oldsn=oldsn+"#"+rc[1];
                          newsn=newsn+"#"+$('#seat'+arr[c]).val();
						

                              }
                        }
						alert(oldsn);
						alert(newsn);
                      var r=confirm("Are you sure,you want to Update Seat?");
                    if(r==true)
                    {
                     
                      
                      $.post("SaveSeatname",{srvno:srvno,travid:travid,sname:oldsn,nseat:newsn},function(res){
                         if(res==1)
                          {
                           alert('Seat name updated');
                           SelectType();
                          }
                      else
                          {
                           alert('Not updated');   
                          }
                      });  
                      }
                    else
                        {
                            return false;
                        }
                    
                }
               }
function updatemodel()
{
	var service_num = $("#ser_list").val();
	var model = $("#model").val();
	var r=confirm("Are you sure,you want to Update Model!");
    
	if(r==true)
    {
    	$.post("updatemodel",{service_num:service_num,model:model},function(res)
		{
        	if(res==1)
            {
            	alert('Model Updated Successfully');
				window.location = "<?php echo base_url('createbus/modify_bus');?>";
            }
            else
            {
            	alert('Not updated');   
            }
        });  
    }
    else
    {
    	return false;
    }
}
function updateTax()
{
	var service_num = $("#ser_list").val();
	var service_tax = $("#service_tax").val();
	 var RE = /^\d*\.?\d*$/;
    if(service_tax=="")
    {
    	alert("Please Provide Service Tax Value");
	$("#service_tax").focus();
    	return false;
	}
	else if(service_tax < 0)
    {
    	alert("Please Provide Service Tax greater than zero");
	    $("#service_tax").focus();
    	return false;
	}
   else if(!RE.test(service_tax))
    {
    	alert("Please Provide Service Tax as Float or Integer ");
        $("#service_tax").focus();
        return false;
    }
	else
	{
		var r=confirm("Are you sure,you want to Update Service Tax!");
	    
		if(r==true)
	    {
    		$.post("updateTax",{service_num:service_num,service_tax:service_tax},function(res)
			{
        		if(res==1)
	            {
    	        	alert('Service Tax Updated Successfully');
					window.location = "<?php echo base_url('createbus/modify_bus');?>";
            	}
	            else
    	        {
        	    	alert('Not updated');   
            	}
	        });  
    	}
	    else
    	{
    		return false;
	    }
	}	
}			   
              </script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Modify Bus<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<!--  <tr>
						<td colspan="6" height="40" class="space" style="border-bottom:#f2f2f2 solid 1px;">Modify Bus </td>
					  </tr>-->
					  <tr>
						<td colspan="6">&nbsp;</td>
					  </tr>
					  <tr>
						<td height="30">Select Service No/Name</td>
						<td height="30" class="space"><?php
				$id_list='id="ser_list" class="inputlarge" onChange="changeService()"';
				echo form_dropdown('service_num',$service,'',$id_list);
				?></td>
						<td height="30">&nbsp;</td>
						<td height="30">Select Modify type</td>
						<td height="30" class="space"><select name="mod" id="mod" class="inputfield" onChange="Modify_type();">
									 <option value="0">--select--</option>
									 <option value="1">Boarding point</option>
									 <option value="2">Dropping point</option>
								 <option value="3">Routes</option>
									 <!--option value="4">Amenities</option>
										 <option value="6">Model</option-->
										  <!--option value="7">Service Tax</option-->
									 <!--option value="5">Seat Names</option-->
						  </select></td>
						<td height="30"><input  type="button" class="btn btn-primary" id="type" value="Submit" onClick="SelectType();" ></td>
					  </tr>
					  <tr>
						<td class="space">&nbsp;</td>
						<td class="space">&nbsp;</td>
						<td class="space">&nbsp;</td>
						<td class="space">&nbsp;</td>
						<td class="space">&nbsp;</td>
						<td id="fg">&nbsp;</td>
					  </tr>
					  <tr>
						<td colspan="6"><div id="hid"></div></td>
					  </tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>