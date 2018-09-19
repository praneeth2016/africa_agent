<script>
    $(function()
    {
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 2, showButtonPanel: false, minDate: +0,"autoclose": true});
    });
</script>
<script>
    function selectAll() { //for selecting all checkboxes
        if ($('#selectck').is(":checked")) {
            $('.chkbox').attr('checked', true); //checknig all cheeckboxes   
            $('.tbox').attr('disabled', false);
            $('.tbox1').attr('disabled', false);
        }
        else {
            $('.chkbox').attr('checked', false);   //unchecking all select boxes 
            $('.tbox').attr('disabled', true);
            $('.tbox1').attr('disabled', true);
        }

    }

    function enabledit(k) {
        if ($('#chk' + k).is(':checked'))
        {
            $('#vm' + k).attr('disabled', false);
            $('#vm' + k).focus();
            $('#dm' + k).attr('disabled', false);
            $('#dm' + k).focus();
            $('#cn' + k).attr('disabled', false);
            $('#cn' + k).focus();
            $('#bno' + k).attr('disabled', false);
            $('#bno' + k).focus();
        }
        else
        {
            $('#dm' + k).attr('disabled', true);
            $('#vm' + k).attr('disabled', true);
            $('#bno' + k).attr('disabled', true);
            $('#cn' + k).attr('disabled', true);
        }

    }
</script>
<script>
    function getService()
    {
        var city = $('#city').val();
        var date_from = $('#date_from').val();
        if (city == '0') {
            alert("select city");
            $('#city').focus();
            return false;
        }
        else {
            $.post("ServiceDisplay", {city: city, date_from: date_from}, function(res) {
                //alert(res);
                if (res == 0)
                    $('#loadlayout').html("<span style='color:red;margin:200px'>No Service available on selected Date</span>");
                else
                    $('#loadlayout').html(res);
            });
        }
    }
</script>
<script>
    function driver_number(i)
    { 
       var name = $('#cn' + i).val();
        	$.post("get_driver_number", {name: name}, function(res) {                
				$('#dm' + i).val(res);                
            });
		 	
    }
</script>
<script>
    function saveContact() {
        var date_from = $('#date_from').val();
        var services = '';
        var bcontacts = '';
        var vcontacts = '';
        var cname = '';
        var busno = '';
        var chk1 = '';
        var tot1 = '';
        ck = 0;
        $(":checkbox").each(function() {
            if (this.checked) {
                chk1 = this.value;
                var i = this.value;
                var dataa = $('#hd' + i).val();
                var data = dataa.split(",");
                var contact1 = $('#dm' + i).val();
                var contact2 = $('#vm' + i).val();
                var name = $('#cn' + i).val();
                var bus_no = $('#bno' + i).val();
                var servno = $('#service_num' + i).val();
                var letters = /^[0-9a-zA-Z]+$/;
                var strFilter1 = /^[-+]?\d*\.?\d*$/;
                //alert(contact1);
                //alert(contact2);
                //alert(data[1]);
                if (bus_no == '') {
                    alert('Kindly provide  Bus  Number');
                    $('#bno' + i).focus();
                    return false;
                }
                if (!letters.test(bus_no)) {
                    alert('Enter Bus Number Without Spaces');
                    $('#bno' + i).focus();
                    return false;
                }
                if (name == '') {
                    alert('Kindly provide  Contact Name');
                    $('#cn' + i).focus();
                    return false;
                }
                if (contact1 == '') {
                    alert('Kindly provide Direct Bus Mobile Number');
                    $('#dm' + i).focus();
                    return false;
                }
                if (!strFilter1.test(contact1))
                {
                    alert("Please enter only numbers in the \"Direct Bus Mobile Number\" field.");
                    $('#dm' + i).focus();
                    return false;
                }
                if (contact1.length < 10)
                {
                    alert("Please enter at least 10 characters in the \"Direct Bus Mobile\" field.");
                    $('#dm' + i).focus();
                    return false;
                }

                if (contact1.length > 11)
                {
                    alert("Please enter at most 11 characters in the \"Direct Bus Mobile\" field.");
                    $('#dm' + i).focus();
                    return false;
                }
                if (contact2 == '' && data[1] == 'yes') {
                    alert('Kindly provide VanPickUp Mobile Number');
                    $('#vm' + i).focus();
                    return false;
                }
                if (!strFilter1.test(contact2) && data[1] == 'yes') {
                    alert('Please enter only numbers in the \"VanPickUp Mobile Number\" field.');
                    $('#vm' + i).focus();
                    return false;
                }
                if (contact2.length < 10 && data[1] == 'yes')
                {
                    alert("Please enter at least 10 characters in the \"VanPickUp Mobile\" field.");
                    $('#vm' + i).focus();
                    return false;
                }

                if (contact2.length > 11 && data[1] == 'yes')
                {
                    alert("Please enter at most 11 characters in the \"VanPickUp Mobile\" field.");
                    $('#vm' + i).focus();
                    return false;
                }
                //adding all bus numbers
                if (busno == '')
                {
                    busno = bus_no;
                }
                else
                    busno = busno + "#" + bus_no;

                if (cname == '')
                {
                    cname = name;
                }
                else
                    cname = cname + "#" + name;

                //adding all service numbers
                if (services == '')
                {
                    services = servno;
                }
                else
                    services = services + "#" + servno;
                //adding all direct bus numbers
                if (bcontacts == '')
                    bcontacts = contact1;
                else
                    bcontacts = bcontacts + "#" + contact1;
                //adding all vanpickup numbers
                if (vcontacts == '')
                    vcontacts = contact2;
                else
                    vcontacts = vcontacts + "#" + contact2;
            }
            //alert(services+"@"+bcontacts+"@"+vcontacts);
        });
        //alert(services+"#"+date_from+"#"+bcontacts+"#"+vcontacts+"#"+busno+"#"+cname);
        if (chk1 == '' || chk1 == 0)
        {
            alert('please select atleast one checkbox to update contact no and bus no');
            return false;
        }
        else if (services != '') {
            $.post('storeContact', {date_from: date_from, services: services, bcontacts: bcontacts, vcontacts: vcontacts, busno: busno, cname: cname}, function(res) {
                //alert(res);
                //$('#spn').html(resp);
                if (res == 0) {
                    //$('#spamsg2').html("<span style='color:red;margin:200px'>\n\message has  not been sent</span>");
                    alert("message has not been sent");
                    //$('#spamsg2').html("message has  not been sent");
                }
                else
                    //$('#spamsg2').html("<span style='color:red;margin:200px'>\n\message has  been sent</span>");
                    alert("message has  been sent");
                window.location = "<?php echo base_url('Updations/vihicle_assignment'); ?>";
            });
        }
    }
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Vehicle Assignment<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" valign="top"><table width="62%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td height="25">&nbsp;</td>
										<td height="25">&nbsp;</td>
										<td height="25">&nbsp;</td>
										<td height="25">&nbsp;</td>
										<td height="25">&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td height="25">Select City:</td>
										<td height="25"><?php
											$js = 'id="city" class="form-control"';
											echo form_dropdown('city', $city, "", $js);
											?></td>
										<td height="25" class="label">Date:</td>
										<td height="25"><input type="text" name="date_from" id="date_from" class="form-control" value='<?php echo(Date("Y-m-d")); ?>'></td>
										<td height="25"><input  type="button" class="btn btn-primary form-control" value="Submit" id="getservice2" name="getservice2" onClick="getService()"></td>
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
							<td><span id="loadlayout"> </span> <span id="spamsg" ></span> <span id="spamsg2"></span></td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>