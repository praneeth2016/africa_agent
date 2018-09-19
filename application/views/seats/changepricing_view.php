<script>
    $(document).ready(
            function () {
                $('#fare_date').datepicker({
                    "autoclose": true
                });
            }
    );

</script>
<script type="text/javascript">
    function getRoutes() {
        $('#ress').html("");
        var svc = $('#svc').val();
        var fare_date = $('#fare_date').val();
        $('#hid').val('');
        if (svc == 0) {
            alert('select service number');
            return false;
        }
        else {
            $.post("getRoutes", {svc: svc, fare_date: fare_date}, function (res) {
                $('#resp').html(res);
            });
        }
    }
    function updateFare() {
        var i = $("#hdd").val();
        var fdate = $('#fdate').val();
        var tdate = $('#tdate').val();
        var service = $('#svc').val();
        var travelid = " <?php echo $this->session->userdata('bktravels_travel_id'); ?>";
        //alert(travelid);
        var btype = $('#btype').val();
        var sfare = "";
        var lbfare = "";
        var fid = "";
        var tid = "";
        var ubfare = "";
        var t = "<?php echo date('Y-m-d'); ?>";

        for (var j = 1; j <= i; j++)
        {
            if (sfare == "")
            {
                sfare = $("#sfare" + j).val();
            }
            else
            {
                sfare = sfare + "/" + $("#sfare" + j).val();
            }
            if (lbfare == "")
            {
                lbfare = $("#lbfare" + j).val();
            }
            else
            {
                lbfare = lbfare + "/" + $("#lbfare" + j).val();
            }
            if (ubfare == "")
            {
                ubfare = $("#ubfare" + j).val();
            }
            else
            {
                ubfare = ubfare + "/" + $("#ubfare" + j).val();

            }
            if (fid == "")
            {
                fid = $("#fid" + j).val();
            }
            else
            {
                fid = fid + "/" + $("#fid" + j).val();
            }
            if (tid == "")
            {
                tid = $("#tid" + j).val();
            }
            else
            {
                tid = tid + "/" + $("#tid" + j).val();
            }
            if (typeof sfare == "undefined")
            {
                sfare = "";
            }
            if (typeof lbfare == "undefined")
            {
                lbfare = "";
            }
            if (typeof ubfare == "undefined")
            {
                ubfare = "";
            }
            //alert(sfare);
            // alert(tid+"##"+fid); 
        }
        if (fdate < t || tdate < t)
        {
            alert("Date shoud not less than today date");
        }
        else if (tdate < fdate)
        {
            alert("To date shoud not less than From date");
        }
        else
        {
            var con = confirm("Are You Sure You Want To Update Fares");
            if (con == true)
            {
                $("#up").val("Please Wait..");
                $("#up").attr("disabled", true);
                $.post("updatePrice", {fdate: fdate, tdate: tdate, serno: service, btype: btype, fid: fid, tid: tid, travelid: travelid, lbfare: lbfare, ubfare: ubfare, sfare: sfare}, function (res)
                {//alert(res);
                    if (res == 0) {
                        $('#ress').html("<span style='color:red;margin:200px'>Not updated</span>");
                    }
                    else {
                        $('#ress').html("<span style='color:red;margin:200px'> updated</span>");
                    }
                    $("#up").val("Update");
                    $("#up").attr("disabled", false);
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
					<h3 class="panel-title">Change Price<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
				<table width="100%" border="0" cellspacing="1" cellpadding="1" style="margin-top:15px">
					<tr>
						<td>&nbsp;</td>
						<td align="center"><span>Service Name :</span></td>
						<td><?php
							$id = 'id="svc" class="inputlarge"';
							echo form_dropdown('svc', $result, '', $id);
							?>        </td>
						<td colspan="2" align="center"><span>Date</span></td>
						<td><input type="text" name="fare_date" id="fare_date" class="inputmedium" readonly="" value="<?php echo Date("Y-m-d"); ?>"   /></td>
						<td>&nbsp;</td>
						<td><input  type="button" class="btn btn-primary" name="btn" id='btn' value="Submit" onclick="getRoutes()" /></td>
						<td><input type="hidden" name="hid" id='hid' value=''/>
							<input type='hidden' name='fromto' id='fromto' val=''/>
							<input type='hidden' name='sequence' id='sequence' val=''/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="8" id="resp">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="8" id="ress">&nbsp;</td>
					</tr>
				</table>
			</div>
			</div>
		</main>
	</section>
</div>