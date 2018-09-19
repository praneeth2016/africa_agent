<script type="text/javascript">

    function print(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data)
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>
<script type="text/javascript">
    function btype()
    {
        var booktype = $("#booktype").val();
        //alert(booktype);

        if (booktype == 'byemployee')
        {
            $.post('<?php echo base_url('agent_controller/byEmployeeList'); ?>', {booktype: booktype}, function (res) {
                // alert(res);
                $("#eval1").show();
                $("#eval").html(res);


            });
        }


    }
    function modify()
    {
        var board_id = $("#board").val();
        var board = $("#board option:selected").text();
        var phone = $("#phone").val();
        var altph = $("#altph").val();
        var add = $("#add").val();
        var email = $("#email").val();
        var tktno = $("#tktno").val();
        var pass = $("#pass").val();
        var seats = $("#seats").val();
        var service_num = $("#service_num").val();
        var jdate = $("#jdate").val();

        var pname = "";
        var gender = "";
        var age = "";

        for (var i = 0; i < pass; i++)
        {
            if (pname == "")
            {
                pname = $("#pn" + i).val();
            }
            else
            {
                pname = pname + "," + $("#pn" + i).val();
            }

            if (gender == "")
            {
                gender = $("#pgen" + i).val();
            }
            else
            {
                gender = gender + "," + $("#pgen" + i).val();
            }

            if (age == "")
            {
                age = $("#page" + i).val();
            }
            else
            {
                age = age + "," + $("#page" + i).val();
            }

        }
        if (phone == "")
        {
            alert("Phone Number Should not be Empty!! ");
            $("#phone").focus();
            return false;
        }
        else if (altph == "")
        {
            alert("Alternative Phone Number Should not be Empty!! ");
            $("#altph").focus();
            return false;
        }
        else if (add == "")
        {
            alert("Address Should not be Empty!! ");
            $("#add").focus();
            return false;
        }
        for (var j = 0; j < pass; j++)
        {
            if ($("#pn" + j).val() == "")
            {
                alert("Passenger Name Should not be Empty!! ");
                $("#pn" + j).focus();
                return false;
            }
            else if ($("#page" + j).val() == "")
            {
                alert("Passenger Age Should not be Empty!! ");
                $("#page" + j).focus();
                return false;
            }
        }

        var r = confirm("Are You Sure Want to Modify!!");
        if (r == true)
        {
            $.post('<?php echo site_url('Updations/modifyTicket'); ?>', {board: board, board_id: board_id, phone: phone, altph: altph, add: add, email: email, tktno: tktno, pass: pass, pname: pname, gender: gender, age: age, seats: seats, jdate: jdate, service_num: service_num}, function (res) {
                //alert(res);
                if (res == 1)
                {
                    alert("Ticket modified Successfully");
                    window.location = ' <?php site_url('Updations/change_tkt_status'); ?>';
                }

            });
        }
    }
    $(document).ready(function () {
        $("#from").change(function () {

            var from = $("#from").val();

            $.post('<?php echo site_url('agent_controller/toList'); ?>', {from: from}, function (res) {

                $("#tid").hide();
                $("#to_id").html(res);


            });
        });
    });
    $(function ()
    {

    });
    function searchBus()
    {
        var tktno = $("#tktno").val();

        if (tktno == '')
        {
            alert("Please Provide Ticket Number");
            $("#tktno").focus();
            return false;
        }

        else
        {

            $("#fa").hide();
            $("#print").hide();

            $.post('<?php echo base_url('Updations/tktStatus'); ?>', {tktno: tktno}, function (res) {
                //alert(res);
                if (res == '')
                {
                    alert("invalid Parameters");
                    window.location = ' <?php site_url('Updations/change_tkt_status'); ?>';
                }
                else
                {
                    $("#fa").show();
                    $("#print").show();
                    $("#fare").html(res);
                }

            });
        }
    }
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Change Ticket Status<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="59%" border="0" align="center" cellpadding="0" cellspacing="0">

						<tr>
							<td align="center"><table width="74%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="109" height="15" align="right" class="size">&nbsp;</td>
										<td width="41" align="center">&nbsp;</td>
										<td width="162">&nbsp;</td>        
										<td width="219">&nbsp;</td>
									</tr>
									<tr>
										<td height="35" align="left" class="size">Ticket Number</td>
										<td align="center"><strong>:</strong></td>
										<td><input type="text" name="tktno" id="tktno" class="inputfield" /></td>        
										<td align="center"><span class="size">
												<input  type="button" class="btn btn-primary" name="search" id="search" value="Ticket Status" onclick="searchBus();" />
										</span></td>
									</tr>



									<tr>
										<td height="35" colspan="5" align="center" class="size">&nbsp;</td>
									</tr>
							</table></td>
						</tr>

						<tr>
							<td></td>
						</tr>
					</table>
					<table width="700" border="0" align="center" cellpadding="2" cellspacing="0" style="font-size:14px;">
						<tbody>

							<tr align="center" valign="top">
								<td height="25" id="fare">&nbsp;</td>
							</tr>

						</tbody>
					</table>
		</div>
			</div>
		</main>
	</section>
</div>
