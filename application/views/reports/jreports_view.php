<link href="<?php echo base_url('css/jquery-ui.css'); ?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url('js/jquery-ui.js'); ?>"></script>
<script type="text/javascript">
    $(function ()
    {
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false
        });

        $("#date_to").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false
        });
    });
</script>
<script>
    function Report()
    {
        var from = $('#date_from').val();
        var to = $('#date_to').val();
        //var paytype=$('#paytype').val();
        var serviceno = $('#services').val();

        //alert(serviceno);
        //alert(ag);
        if (serviceno == 0) {
            alert('Kindly Select The Service Name/Number');
            $('#services').focus()
        } else {
            var output = $("input[name='output']:checked").val();
            if (output == 'screen')
            {
                window.open('Get_journeyReport?from=' + from + '&to=' + to + '&output=' + output + '&serviceno=' + serviceno);

            }
            else if (output == 'csv')
            {
                document.location.href = "getjourneyDownload?output1=" + output + "&date_from=" + from + "&date_to=" + to;
            }
            else if (output == 'xls')
            {
                document.location.href = "getjourneyDownload?output1=" + output + "&date_from=" + from + "&date_to=" + to;
            }
        }

    }



</script>
<div class="navigationheader"> REPORTS &nbsp;&nbsp; &gt;&gt; &nbsp;&nbsp; Journey Date Reports </div>
<table width="83%" border="0" cellspacing="1" cellpadding="1" align="center">
    <tr>
        <td width="4%" height="30">&nbsp;</td>
        <td width="12%" height="30">Service Name </td>
        <td width="3%" height="30" align="center"><strong>:</strong></td>
        <td width="77%" height="30"><?php
            $js = 'id="services" class="size"';
            echo form_dropdown('services', $services, '', $js);
            ?></td>    
        <td width="4%" height="30">&nbsp;</td>
    </tr>
    <tr>
        <td height="30">&nbsp;</td>
        <td height="30">Start Date </td>
        <td height="30" align="center"><strong>:</strong></td>
        <td height="30"><input type="text" size='12' name="date_from" id="date_from" class="jdpicker inputfield" value='<?php echo(Date("Y-m-d")); ?>'  style="cursor:pointer;background-image:url(<?php echo base_url('images/calendar.gif') ?>);background-repeat: no-repeat; background-position:right; vertical-align: middle;" ></td>
        <td height="30">&nbsp;</td>
    </tr>
    <tr>
        <td height="30">&nbsp;</td>
        <td height="30">End Date </td>
        <td height="30" align="center"><strong>:</strong></td>
        <td height="30"><input type="text" size='12' name="date_to" id="date_to" class="jdpicker inputfield" value='<?php echo(Date("Y-m-d")); ?>'  style="cursor:pointer;background-image:url(<?php echo base_url('images/calendar.gif') ?>);background-repeat: no-repeat; background-position:right; vertical-align: middle; " ></td>
        <td height="30">&nbsp;</td>
    </tr>
    <tr>
        <td height="30">&nbsp;</td>
        <td height="30" colspan="3"><input  type="radio" name="output" value="screen" id="output1" checked>
            Onscreen
            <input  type="radio" name="output" value="csv" id="output2"/>
            As CSV
            <input  type="radio" name="output" value="xls" id="output3"/>
            As Excel</td>
        <td height="30">&nbsp;</td>
    </tr>
    <tr>
        <td height="30">&nbsp;</td>
        <td height="30" colspan="3"><input  type="button" class="newsearchbtn" name="submit" id="submit" value="Submit" onClick='Report();'></td>
        <td height="30">&nbsp;</td>
    </tr>
</table>
<br />
<br />
<table width="804" border="0" cellpadding="0" style="display:none" cellspacing="0" align="center"  id="fa">
    <tr>
        <td  height="30" style="background-color:#999999; color:#FFFFFF"><strong> Ticket Information</strong></td>
    </tr>
    <br />
    <tr >
        <td id="fare">&nbsp;</td>
    </tr>
</table>
