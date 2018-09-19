
<script type="text/javascript">
    $(function ()
    {
        $("#txtdate").datepicker({dateFormat: 'yy-mm-dd', numberOfMonths: 1, showButtonPanel: false
        });
    });
</script>
<script type="text/javascript">
    function getlayout()
    {
        var busmodel = $('#busmodel').val();
        //alert(busmodel);
        $.post("getServiceLayout", {busmodel: busmodel}, function (res)
        {
            //alert(res);
            $('#setLayout').show();
            if (res == 0)
            {
                $('#layout').html("<span style='color:red;margin:200px'>No data available on selected service</span>");
            }
            else
            {
                $('#layout').html(res);
            }
        });
    }//getlayout()
    function selectAll()
    {
        if ($('#selectall').is(":checked"))
        {
            $('.chkbox').attr('checked', true); //checknig all cheeckboxes
            $('.textbox').attr('disabled', false); //checknig all textboxs
        }
        else
        {
            $('.chkbox').attr('checked', false); //unchecking all cheeckboxes
            $('.textbox').attr('disabled', true); //unchecking all textboxs	
        }
    }//
    function getWeekDays()
    {
        var stype = $('#stype').val();
        //alert(stype);
        if (stype == 'weekly')
        {
            $('#chkWeek').show();
        }
        else
        {
            $('#chkWeek').hide();
        }
    }//getWeekDays()
// Check for Service number and name
    function checkUser(vall)
    {	     
        //alert(snum);
        if (vall == 'SNN')
        {
            var sname = $('#sname').val();
            $("#sntext").empty();			
            $.post("checkUser", {sname: sname, vall: vall}, function (res)
            {
                //alert(res)
                if (res == 1)
                {
                    $("#sntext").html("Service Name Already Exist !!");
                    $("#snamehidden").val(1);
                }
                else
                {
                    $("#sntext").empty();
                    $("#snamehidden").val(0);

                }
            });
        }
        else if (vall == 'SNO')
        {
           
			var snum1 = $('#snum').val();
		  	var snum = snum1.replace(/\s+/g, '');			
            $("#snotext").empty();
            $.post("checkUser", {snum: snum, vall: vall}, function (res)
            {
                
                if (res == 1)
                {
                    $("#snotext").html("Service Number Already Exist !!");
                    $("#snumhidden").val(1);
                }
                else
                {
                    $("#snotext").empty();
                    $("#snumhidden").val(0);
                }
            });
        }
    }//checkUser(vall)
//getting Halts
    function getHalts()
    {	
        var halts = $('#halts').val();
        var sname = $('#sname').val();
        var snum = $('#snum').val();
        var service_from = $('#service_from').val();
        var service_to = $('#service_to').val();
        var busmodel = $('#busmodel').val();		

        if (sname == "")
        {
            alert("Enter Service Name !!");
            $('#sname').focus();
            $('#halts').val('');
            return false;
        }
        if (snum == "")
        {
            alert("Enter Service Number !!");
            $('#snum').focus();
            $('#halts').val('');
            return false;
        }
        if (service_from == 0)
        {
            alert("Select Service Start Point ");
            $('#service_from').focus();
            $('#halts').val('');
            return false;
        }
        if (service_to == 0)
        {
            alert("Select Service End Point ");
            $('#service_to').focus();
            $('#halts').val('');
            return false;
        }
        if (service_from == service_to)
        {
            alert("Service Start point and Ent point should not be same !! ");
            $('#service_to').focus();
            $('#halts').val('');
            return false;
        }
        if (busmodel == 0)
        {
            alert("Select Bus layout Model ");
            $('#busmodel').focus();
            $('#halts').val('');
            return false;
        }
        else
        {	
            $.post("getHaltsAndFares", {halts: halts, busmodel: busmodel}, function (res)
            {
                
                $('#gethalts').show();
                if (res == 0)
                {
                    $('#showhalts').empty();
                }
                else
                {
                    $('#showhalts').html(res);
                }
            });
        }
    }//getHalts()
    function validate()
    {
        var sname = $('#sname').val();
		var snum1 = $('#snum').val();
		var snum = snum1.replace(/\s+/g, '');
        //var snum = $('#snum').val();
        var service_from = $('#service_from').val();
        var service_to = $('#service_to').val();
        var busmodel = $('#busmodel').val();
        var stype = $('#stype').val();
        var halts = $('#halts').val();
        var layout_type = $('#layout_type').val();
        var contact = /^[0-9]*\.?[0-9]+$/;
        var snamehidden = $("#snamehidden").val();
        var snumhidden = $("#snumhidden").val();
        var title = $("#title").val();
        var service_tax = $("#service_tax").val();

        var weeks = "";
        //getting week days if service type is weekly
        for (var i = 1; i <= 7; i++)
        {
            if ($('#chk' + i).is(":checked"))
            {
                if (weeks == 0 || weeks == "")
                {
                    weeks = $('#chk' + i).val();
                    ;
                }
                else
                {
                    weeks = weeks + "#" + $('#chk' + i).val();
                    ;
                }
            }
        }

        if (sname == "")
        {
            alert("Enter Service Name !!");
            $('#sname').focus();
            return false;
        }
        if (snum == "")
        {
            alert("Enter Service Number !!");
            $('#snum').focus();
            return false;
        }
        if (snamehidden == 1)
        {
            alert("Service Name Already Exist, kindly enter another Name !!");
            $('#sname').focus();
            return false;
        }
        if (snumhidden == 1)
        {
            alert("Service Number Already Exist, kindly enter another number !!");
            $('#snum').focus();
            return false;
        }
        if (service_from == 0)
        {
            alert("Select Service Start Point ");
            $('#service_from').focus();
            return false;
        }
        if (service_to == 0)
        {
            alert("Select Service End Point ");
            $('#service_to').focus();
            return false;
        }
        if (service_from == service_to)
        {
            alert("Service Start point and Ent point should not be same !! ");
            $('#service_to').focus();
            return false;
        }
        if (busmodel == 0)
        {
            alert("Select Bus layout Model ");
            $('#busmodel').focus();
            return false;
        }

        /******************** Seat numbers and layout validation ******************/
        var lower_seat_no = "";
        var upper_seat_no = "";
        var lower_rowcols = "";
        var upper_rowcols = "";
        var lowertype = "";
        var uppertype = "";
        var rows = $("#rows").val();
        var cols = $("#cols").val();
        var lower_rows = $("#lower_rows").val();
        var lower_cols = $("#lower_cols").val();
        var upper_rows = $("#upper_rows").val();
        var upper_cols = $("#upper_cols").val();

        if (layout_type == 'seater')
        {
            for (i = 1; i <= rows; i++)
            {
                for (j = 1; j <= cols; j++)
                {
                    if ($('#lchk' + i + '-' + j).is(":checked"))
                    {
                        $('#ltxt' + i + '-' + j).removeAttr("disabled");
                        alert("Please DeSelect the checkbox in bus layout");
                        return false;

                    }
                    else
                    {
                        $('#ltxt' + i + '-' + j).attr("disabled", "disabled");
                        if ($('#ltxt' + i + '-' + j).val() == "")
                        {
                            alert("Please Provide values for Lower Deck Textboxes");
                            $('#ltxt' + i + '-' + j).focus();
                            return false;
                        }
                        else
                        {
                            if (typeof ($('#ltxt' + i + '-' + j).val()) != 'undefined')
                            {
                                if (lower_seat_no == "")
                                {
                                    lower_seat_no = $('#ltxt' + i + '-' + j).val();
                                    lower_rowcols = i + '-' + j;
                                }
                                else
                                {
                                    lower_seat_no = lower_seat_no + '#' + $('#ltxt' + i + '-' + j).val();
                                    lower_rowcols = lower_rowcols + '#' + i + '-' + j;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (layout_type == 'sleeper')
        {
            for (i = 1; i <= lower_rows; i++)
            {
                for (j = 1; j <= lower_cols; j++)
                {
                    if ($('#lchk' + i + '-' + j).is(":checked"))
                    {

                        $('#ltxt' + i + '-' + j).removeAttr("disabled");
                        alert("Please DeSelect the checkbox  in bus layout");
                        return false;

                    }
                    else
                    {
                        $('#ltxt' + i + '-' + j).attr("disabled", "disabled");
                        if ($('#ltxt' + i + '-' + j).val() == "")
                        {
                            alert("Please Provide values for Lower Deck Textboxes");
                            $('#ltxt' + i + '-' + j).focus();
                            return false;
                        }
                        else
                        {

                            if (typeof ($('#ltxt' + i + '-' + j).val()) != 'undefined')
                            {
                                if (lower_seat_no == "")
                                {
                                    lower_seat_no = $('#ltxt' + i + '-' + j).val();
                                    lower_rowcols = i + '-' + j;
                                }
                                else
                                {
                                    lower_seat_no = lower_seat_no + '#' + $('#ltxt' + i + '-' + j).val();
                                    lower_rowcols = lower_rowcols + '#' + i + '-' + j;
                                }
                            }

                        }
                    }
                }
            }

            for (i = 1; i <= upper_rows; i++)
            {
                for (j = 1; j <= upper_cols; j++)
                {
                    if ($('#uchk' + i + '-' + j).is(":checked"))
                    {
                        $('#utxt' + i + '-' + j).removeAttr("disabled");
                        alert("Please DeSelect the checkbox  in bus layout");
                        return false;

                    }
                    else
                    {
                        $('#utxt' + i + '-' + j).attr("disabled", "disabled");
                        if ($('#utxt' + i + '-' + j).val() == "")
                        {
                            alert("Please Provide values for Upper Deck Textboxes");
                            $('#utxt' + i + '-' + j).focus();
                            return false;
                        }
                        else
                        {
                            if (typeof ($('#utxt' + i + '-' + j).val()) != 'undefined')
                            {
                                if (upper_seat_no == "")
                                {
                                    upper_seat_no = $('#utxt' + i + '-' + j).val();
                                    upper_rowcols = i + '-' + j;
                                }
                                else
                                {
                                    upper_seat_no = upper_seat_no + '#' + $('#utxt' + i + '-' + j).val();
                                    upper_rowcols = upper_rowcols + '#' + i + '-' + j;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (layout_type == 'seatersleeper')
        {
            /*var total_rows = parseInt(lower_rows) + parseInt(rows);
             
             for(i = 1;i <= total_rows;i++)
             {
             for(j = 1;j <= cols;j++)
             {				
             if($('#lchk'+i+'-'+j).is(":checked"))
             {
             $('#ltxt'+i+'-'+j).removeAttr("disabled");
             alert("Please DeSelect the checkbox  in bus layout");
             return false;
             
             }
             else
             {
             $('#ltxt'+i+'-'+j).attr("disabled" , "disabled");
             if($('#ltxt'+i+'-'+j).val() == "")
             {
             alert("Please Provide values for Lower Deck Textboxes");
             $('#ltxt'+i+'-'+j).focus();
             return false;
             }
             else
             {
             if(typeof($('#ltxt'+i+'-'+j).val()) != 'undefined')
             {
             if(lower_seat_no == "")
             {
             lower_seat_no = $('#ltxt'+i+'-'+j).val();
             lower_rowcols = i+'-'+j;
             }
             else
             {
             lower_seat_no = lower_seat_no +'#'+ $('#ltxt'+i+'-'+j).val();
             lower_rowcols = lower_rowcols +'#'+ i+'-'+j;
             }
             }
             }
             }					
             }
             }*/
            for (i = 1; i <= lower_rows; i++)
            {
                for (j = 1; j <= lower_cols; j++)
                {
                    if ($('#lchk' + i + '-' + j).is(":checked"))
                    {
                        $('#ltxt' + i + '-' + j).removeAttr("disabled");
                        alert("Please DeSelect the checkbox  in bus layout");
                        return false;

                    }
                    else
                    {
                        $('#ltxt' + i + '-' + j).attr("disabled", "disabled");
                        if ($('#ltxt' + i + '-' + j).val() == "")
                        {
                            alert("Please Provide values for Lower Deck Textboxes");
                            $('#ltxt' + i + '-' + j).focus();
                            return false;
                        }
                        else
                        {
                            if (typeof ($('#ltxt' + i + '-' + j).val()) != 'undefined')
                            {
                                if (lower_seat_no == "")
                                {
                                    lower_seat_no = $('#ltxt' + i + '-' + j).val();
                                    lowertype = $('#lowertype' + i + j).val();
                                    lower_rowcols = i + '-' + j;
                                }
                                else
                                {
                                    lower_seat_no = lower_seat_no + '#' + $('#ltxt' + i + '-' + j).val();
                                    lowertype = lowertype + '#' + $('#lowertype' + i + j).val();
                                    lower_rowcols = lower_rowcols + '#' + i + '-' + j;
                                }
                            }
                        }
                    }
                }
            }

            for (i = 1; i <= upper_rows; i++)
            {
                for (j = 1; j <= upper_cols; j++)
                {
                    if ($('#uchk' + i + '-' + j).is(":checked"))
                    {
                        $('#utxt' + i + '-' + j).removeAttr("disabled");
                        alert("Please DeSelect the checkbox  in bus layout");
                        return false;

                    }
                    else
                    {
                        $('#utxt' + i + '-' + j).attr("disabled", "disabled");
                        if ($('#utxt' + i + '-' + j).val() == "")
                        {
                            alert("Please Provide values for Upper Deck Textboxes");
                            $('#utxt' + i + '-' + j).focus();
                            return false;
                        }
                        else
                        {
                            if (typeof ($('#utxt' + i + '-' + j).val()) != 'undefined')
                            {
                                if (upper_seat_no == "")
                                {
                                    upper_seat_no = $('#utxt' + i + '-' + j).val();
                                    uppertype = $('#uppertype' + i + j).val();
                                    upper_rowcols = i + '-' + j;
                                }
                                else
                                {
                                    upper_seat_no = upper_seat_no + '#' + $('#utxt' + i + '-' + j).val();
                                    uppertype = uppertype + '#' + $('#uppertype' + i + j).val();
                                    upper_rowcols = upper_rowcols + '#' + i + '-' + j;
                                }
                            }
                        }
                    }
                }
            }
            /*alert(lower_seat_no);
             alert(lower_rowcols);*/
            //alert(lowertype);
            //alert(uppertype);
        }

        /******************** Seat numbers and layout validation END******************/

        /*****************service routes & fares Validation ******************/
        if (halts == "")
        {
            alert("Select No.of Halts ");
            $('#halts').focus();
            return false;
        }
        if (stype == 'weekly' && weeks == "")
        {
            alert("Please select atleast one checkbox from Service Type !");

        }
        var fids = '', tids = '', f, t;
        var froms = '', tos = '', sfares = '', lbfares = '', ubfares = '', ats = '';
        var hhST = '', mmST = '', ampmST = '', hhAT = '', mmAT = '', ampmAT = '';


        for (var j = 1; j <= halts; j++)
        {
            if (fids == '')
            {
                fids = $('#from' + j).val();
            }
            else
            {
                fids = fids + "," + $('#from' + j).val();
            }
            if (tids == '')
            {
                tids = $('#to' + j).val();
            }
            else
            {
                tids = tids + "," + $('#to' + j).val();
            }
            if (froms == '')
            {
                froms = $('#from' + j + ' option:selected').text();
            }
            else
            {
                froms = froms + "," + $('#from' + j + ' option:selected').text();
            }
            if (tos == '')
            {
                tos = $('#to' + j + ' option:selected').text();
            }
            else
            {
                tos = tos + "," + $('#to' + j + ' option:selected').text();
            }



            if (layout_type == 'seater')
            {
                if (sfares == '')
                {
                    sfares = $('#sfare' + j).val();
                }
                else
                {
                    sfares = sfares + "," + $('#sfare' + j).val();
                }


            }
            else if (layout_type == 'sleeper')
            {
                if (lbfares == '')
                {
                    lbfares = $('#lbfare' + j).val();
                }
                else
                {
                    lbfares = lbfares + "," + $('#lbfare' + j).val();
                }
                if (ubfares == '')
                {
                    ubfares = $('#ubfare' + j).val();
                }
                else
                {
                    ubfares = ubfares + "," + $('#ubfare' + j).val();
                }


            }
            else
            {
                if (sfares == '')
                {
                    sfares = $('#sfare' + j).val();
                }
                else
                {
                    sfares = sfares + "," + $('#sfare' + j).val();
                }
                if (lbfares == '')
                {
                    lbfares = $('#lbfare' + j).val();
                }
                else
                {
                    lbfares = lbfares + "," + $('#lbfare' + j).val();

                }
                if (ubfares == '')
                {
                    ubfares = $('#ubfare' + j).val();
                }
                else
                {
                    ubfares = ubfares + "," + $('#ubfare' + j).val();
                }

            }
            if (hhST == '')
            {
                hhST = $('#timehrST' + j).val();
            }
            else
            {
                hhST = hhST + "," + $('#timehrST' + j).val();
            }
            if (mmST == '')
            {
                mmST = $('#timemST' + j).val();
            }
            else
            {
                mmST = mmST + "," + $('#timemST' + j).val();
            }
            if (ampmST == '')
            {
                ampmST = $('#tfmST' + j).val();
            }
            else
            {
                ampmST = ampmST + "," + $('#tfmST' + j).val();
            }
            if (hhAT == '')
            {
                hhAT = $('#timehrAT' + j).val();
            }
            else
            {
                hhAT = hhAT + "," + $('#timehrAT' + j).val();
            }
            if (mmAT == '')
            {
                mmAT = $('#timemAT' + j).val();
            }
            else
            {
                mmAT = mmAT + "," + $('#timemAT' + j).val();
            }
            if (ampmAT == '')
            {
                ampmAT = $('#tfmAT' + j).val();
            }
            else
            {
                ampmAT = ampmAT + "," + $('#tfmAT' + j).val();
            }


            if ($('#from' + j).val() == 0)
            {
                alert("Please select Source");
                $('#from' + j).focus();
                return false;
            }
            if ($('#to' + j).val() == 0)
            {
                alert("Please select Destination");
                $('#to' + j).focus();
                return false;
            }
            if ($('#from' + j).val() == $('#to' + j).val())
            {
                alert("Source and Destination Name must not be same");
                $('#to' + j).focus();
                return false;
            }
            if ($('#timehrST' + j).val() == '' || $('#timehrST' + j).val() == 'HH')
            {
                alert("Select Hours in start time");
                $('#timehrST' + j).focus();
                return false;
            }
            if ($('#timemST' + j).val() == '' || $('#timemST' + j).val() == 'MM')
            {
                alert("Select Minutes in start time");
                $('#timemST' + j).focus();
                return false;
            }
            if ($('#tfmST' + j).val() == '' || $('#tfmST' + j).val() == 'AMPM')
            {
                alert("Select Time Format in Start Time");
                $('#tfmST' + j).focus();
                return false;
            }
            if ($('#timehrAT' + j).val() == '' || $('#timehrAT' + j).val() == 'HH')
            {
                alert("Select Hours in Arrival Time");
                $('#timehrAT' + j).focus();
                return false;
            }
            if ($('#timemAT' + j).val() == '' || $('#timemAT' + j).val() == 'MM')
            {
                alert("Select Minutes in Arrival time");
                $('#timemAT' + j).focus();
                return false;
            }
            if ($('#tfmAT' + j).val() == '' || $('#tfmAT' + j).val() == 'AMPM')
            {
                alert("Select Format in Arrival time");
                $('#tfmAT' + j).focus();
                return false;
            }
            if (layout_type == 'seater')
            {
                if ($('#sfare' + j).val() == '' || $('#sfare' + j).val() == '0' || !contact.test($('#sfare' + j).val()))
                {
                    alert("Please Enter Seat Price");
                    $('#sfare' + j).focus();
                    return false;
                }

            }
            else if (layout_type == 'sleeper')
            {
                if ($('#lbfare' + j).val() == '' || $('#lbfare' + j).val() == '0' || !contact.test($('#lbfare' + j).val()))
                {
                    alert("Please Enter LowerDeck Berth Price");
                    $('#lbfare' + j).focus();
                    return false;
                }
                if ($('#ubfare' + j).val() == '' || $('#ubfare' + j).val() == '0' || !contact.test($('#ubfare' + j).val()))
                {
                    alert("Please Enter UpperDeck Berth Price");
                    $('#ubfare' + j).focus();
                    return false;
                }
            }
            else
            {
                if ($('#sfare' + j).val() == '' || $('#sfare' + j).val() == '0' || !contact.test($('#sfare' + j).val()))
                {
                    alert("Please Enter Seat Price");
                    $('#sfare' + j).focus();
                    return false;
                }
                if ($('#lbfare' + j).val() == '' || $('#lbfare' + j).val() == '0' || !contact.test($('#lbfare' + j).val()))
                {
                    alert("Please Enter LowerDeck Berth Price");
                    $('#lbfare' + j).focus();
                    return false;
                }
                if ($('#ubfare' + j).val() == '' || $('#ubfare' + j).val() == '0' || !contact.test($('#ubfare' + j).val()))
                {
                    alert("Please Enter UpperDeck Berth Price");
                    $('#ubfare' + j).focus();
                    return false;
                }
            }

            //alert('fids='+fids+'tids='+tids+'froms=\n'+froms+'tos='+tos+'sfares=\n'+sfares+'lbfares='+lbfares+'ubfares='+ubfares+"hhST="+hhST+",mmST\n"+mmST+",ampmST"+ampmST+",hhAT\n"+hhAT+",mmAT"+mmAT+",ampmAT"+ampmAT);				
        }//for
        /*****************service routes & fares Validation END ******************/
        //checking for boarding points and drop points  are inserted or not
        r = false;
        if (service_tax == "")
        {
            var r = confirm("If Service Tax is applicable to This Service Press OK else Press Cancel");
        }
        if (r == true)
        {
            $('#service_tax').focus();
            return false;
        }
        else if (service_tax != "")
        {
            var RE = /^\d*\.?\d*$/;
            if (service_tax < 0)
            {
                alert("Please Provide Service Tax greater than zero");
                $("#service_tax").focus();
                return false;
            } else if (!RE.test(service_tax))
            {
                alert("Please Provide Service Tax as Float or Integer ");
                $("#service_tax").focus();
                return false;
            }

            else
            {
                $('#create').val("Please Wait...");
                $('#create').attr("disabled", "disabled");
                $.post("getBoardOrDropVal", {snum: snum, fids: fids, tids: tids}, function (res)
                {

                    if (res == 3)
                    {
                        //alert('sname='+sname);alert('snum=\n'+snum);alert('service_from=\n'+service_from );alert('service_to=\n'+service_to );alert('busmodel=\n'+busmodel );alert('stype=\n'+stype);alert('weeks=\n'+weeks);alert('halts=\n'+halts);
                        //alert('layout_type=\n'+layout_type);//alert('lower_seat_no=\n'+lower_seat_no);alert('upper_seat_no=\n'+upper_seat_no);alert('lower_rowcols=\n'+lower_rowcols);alert('upper_rowcols=\n'+upper_rowcols);alert('rows=\n'+rows);alert('cols=\n'+cols);alert('lower_rows=\n'+lower_rows);alert('lower_cols=\n'+lower_cols);alert('upper_rows=\n'+upper_rows);alert('upper_cols=\n'+upper_cols);alert('fids=\n'+fids);alert('tids=\n'+tids);alert('froms=\n'+froms);alert('tos=\n'+tos);alert('sfares=\n'+sfares);alert('lbfares=\n'+lbfares);alert('ubfares=\n'+ubfares);alert('hhST=\n'+hhST);alert('mmST=\n'+mmST);alert('ampmST=\n'+ampmST);alert('hhAT=\n'+hhAT);alert('mmAT=\n'+mmAT);alert('ampmAT=\n'+ampmAT);

                        $.post("saveBusDetails", {sname: sname, snum: snum, service_from: service_from, service_to: service_to, busmodel: busmodel, stype: stype, weeks: weeks, halts: halts, layout_type: layout_type, lower_seat_no: lower_seat_no, upper_seat_no: upper_seat_no, lower_rowcols: lower_rowcols, upper_rowcols: upper_rowcols, rows: rows, cols: cols, lower_rows: lower_rows, lower_cols: lower_cols, upper_rows: upper_rows, upper_cols: upper_cols, fids: fids, tids: tids, froms: froms, tos: tos, sfares: sfares, lbfares: lbfares, ubfares: ubfares, hhST: hhST, mmST: mmST, ampmST: ampmST, hhAT: hhAT, mmAT: mmAT, ampmAT: ampmAT, lowertype: lowertype, uppertype: uppertype, title: title, service_tax: service_tax}, function (res)
                        {
                            //alert(res);
                            $('#create').val("Create Bus");
                            $('#create').attr("disabled", false);
                            if (res == 1)
                            {
                                alert("Service Number is already Exist, Kindly change the service number");
                            }
                            else
                            {
                                alert("Service is created successfully, kindly activate the bus!!");
                                window.location = '<?php echo base_url("Bus/createBus") ?>';
                            }
                        });
                    }
                    else if (res == 1)
                    {
                        $('#create').val("Create Bus");
                        $('#create').attr("disabled", false);
                        alert("Kindly Select the boarding points or Source is changed Kindly reselect the boarding points !!");
                    }
                    else if (res == 2)
                    {
                        $('#create').val("Create Bus");
                        $('#create').attr("disabled", false);
                        alert("Kindly Select the Drop points or Destination is changed Kindly reselect the drop points !!");
                    }
                });
            }
        }

    }//validate()
    function getBoard()
    {
        var fids = '';
        var froms = '';
        var halts = $('#halts').val();
        var snum1 = $('#snum').val();
		var snum = snum1.replace(/\s+/g, '');
        var snumhidden = $("#snumhidden").val();
        if (halts == "")
        {
            alert("Select No.of Halts ");
            $('#halts').focus();
            return false;
        }
        if (snum == "")
        {
            alert("Enter Service Number !!");
            $('#snum').focus();
            return false;
        }
        if (snumhidden == 1)
        {
            alert("Service Number Already Exist, kindly enter another number !!");
            $('#snum').focus();
            return false;
        }
        for (var j = 1; j <= halts; j++)
        {
            //fids=fids+","+$('#from'+j).val();

            if (fids == '')
            {
                fids = $('#from' + j).val();
            }
            else
            {
                fids = fids + "," + $('#from' + j).val();
            }
            if (froms == '')
            {
                froms = $('#from' + j + ' option:selected').text();
            }
            else
            {
                froms = froms + "," + $('#from' + j + ' option:selected').text();
            }

            if ($('#from' + j).val() == 0)
            {
                alert("Please select Source");
                $('#from' + j).focus();
                return false;
            }
            if ($('#to' + j).val() == 0)
            {
                alert("Please select Destination");
                $('#to' + j).focus();
                return false;
            }
            if ($('#from' + j).val() == $('#to' + j).val())
            {
                alert("Source and Destination Name must not be same");
                $('#to' + j).focus();
                return false;
            }
        }
        window.open('getBoard?froms=' + froms + '&fids=' + fids + '&snum=' + snum + '&halts=' + halts);
        //window.open('getBoard?halts='+halts);
    }//getBoard()
    function getDrop()
    {
        var tids = '';
        var tos = '';
        var halts = $('#halts').val();
        var snum1 = $('#snum').val();
		var snum = snum1.replace(/\s+/g, '');
        var snumhidden = $("#snumhidden").val();
        if (halts == "")
        {
            alert("Select No.of Halts ");
            $('#halts').focus();
            return false;
        }
        if (snum == "")
        {
            alert("Enter Service Number !!");
            $('#snum').focus();
            return false;
        }
        if (snumhidden == 1)
        {
            alert("Service Number Already Exist, kindly enter another number !!");
            $('#snum').focus();
            return false;
        }
        for (var j = 1; j <= halts; j++)
        {

            if (tids == '')
            {
                tids = $('#to' + j).val();
            }
            else
            {
                tids = tids + "," + $('#to' + j).val();
            }
            if (tos == '')
            {
                tos = $('#to' + j + ' option:selected').text();
            }
            else
            {
                tos = tos + "," + $('#to' + j + ' option:selected').text();
            }

            if ($('#from' + j).val() == 0)
            {
                alert("Please select Source");
                $('#from' + j).focus();
                return false;
            }
            if ($('#to' + j).val() == 0)
            {
                alert("Please select Destination");
                $('#to' + j).focus();
                return false;
            }
            if ($('#from' + j).val() == $('#to' + j).val())
            {
                alert("Source and Destination Name must not be same");
                $('#to' + j).focus();
                return false;
            }
        }
        window.open('getDrop?tos=' + tos + '&tids=' + tids + '&snum=' + snum + '&halts=' + halts);
    }//getDrop()
</script>
<div class="content-wrapper">    <!-- Main content -->
    <section class="main-content-bg">
		<main class="container-fluid">		
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Service Creation<span style="float: right"> <i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;</span></h3>
				</div>
				<div class="panel-body">
					<table width="94%" align="center" cellpadding="0" cellspacing="0" style="border-color:#999999">
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td style="padding-left:15px">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td width="3%" style="padding-left:15px">&nbsp;</td>
							<td width="27%" height="35" style="padding-left:15px" >Service Name : </td>
							<td width="25%" ><input type="text" name="sname" id="sname" class="inputfield" onchange="checkUser('SN');"/>
							</td>
							<td width="16%" > Service Number : </td>
							<td width="29%" height="35" ><input type="text" name="snum"  id="snum" class="inputfield" onchange="checkUser('SNO');"/></td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="19" style="padding-left:15px">&nbsp;</td>
							<td><span  id="sntext" style="color:#FF0000">&nbsp;</span>
								<input type="hidden" name="snamehidden" id="snamehidden" /></td>
							<td >&nbsp;</td>
							<td height="19"><span  id="snotext" style="color:#FF0000">&nbsp;</span>
								<input type="hidden" name="snumhidden" id="snumhidden" /></td>
						</tr>
						<tr>
							<td style="padding-left:15px" >&nbsp;</td>
							<td height="35" style="padding-left:15px" >Service Start Point  : </td>
							<td ><?php
											$cityid = 'id="service_from" class="inputfield"';
											$cityn = 'name="service_from"';
											echo form_dropdown($cityn, $cities, "", $cityid);
											?>
							</td>
							<td >Service End Point  : </td>
							<td height="35" ><?php
											$cityid = 'id="service_to" class="inputfield"';
											$cityn = 'name="service_to"';
											echo form_dropdown($cityn, $cities, "", $cityid);
											?></td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" style="padding-left:15px" >Bus Layout Model : </td>
							<td><?php
											$busmodelid = 'id="busmodel" class="inputfield" onchange="getlayout()"';
											$busmodeln = 'name="busmodel"';
											echo form_dropdown($busmodeln, $busmodel, "", $busmodelid);
											?></td>
							<td >Add Title </td>
							<td height="35" ><input type="text" name="title" id="title" class="inputfield" /></td>
						</tr>
						<tr id="setLayout" style="display:none">
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="4" id="layout"  align="center">&nbsp;</td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" style="padding-left:15px" >Service Type : </td>
							<td height="35" ><select name="stype" id="stype" class="inputfield" onchange="getWeekDays()">
									<option value="daily">Daily</option>
									<option value="weekly">Weekly</option>
									<option value="special">Special</option>
								</select>
							</td>
							<td height="35" >Service Tax </td>
							<td height="35" ><input type="text" name="service_tax" id="service_tax" class="inputfield" value="0" />
								&nbsp;&nbsp;<span style="color:red">Example: 3.98 or 4 </span></td>
						</tr>
						<tr id="chkWeek" style="display:none">
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="3" ><input type="checkbox" name="chk1" id="chk1" value="Monday" />
								&nbsp;Mon &nbsp;
								<input type="checkbox" name="chk2" id="chk2" value="Tuesday"/>
								&nbsp;Tue &nbsp;
								<input type="checkbox" name="chk3" id="chk3" value="Wednesday" />
								&nbsp;Wed &nbsp;
								<input type="checkbox" name="chk4" id="chk4" value="Thursday" />
								&nbsp;Thu &nbsp;
								<input type="checkbox" name="chk5" id="chk5" value="Friday" />
								&nbsp;Fri &nbsp;
								<input type="checkbox" name="chk6" id="chk6" value="Saturday" />
								&nbsp;Sat&nbsp;
								<input type="checkbox" name="chk7" id="chk7" value="Sunday" />
								&nbsp;Sun &nbsp;</td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="40" colspan="4" style="padding-left:15px" ><strong><u>Service Routes and Fares</u></strong></td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="45" style="padding-left:15px" >Select No.Of Halts : </td>
							<td ><select name="halts" id="halts" class="inputfield" onchange="getHalts()">
									<option value=""> - - Select - - </option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
								</select></td>
							<td >&nbsp;</td>
							<td height="35">&nbsp;</td>
						</tr>
						<tr id="gethalts" style="display:none">
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="4" style="padding-left:15px" id="showhalts">&nbsp;</td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="4" style="padding-left:15px" ><strong>Boarding Points : </strong><span style="text-decoration:underline;color:#FF0000;cursor:pointer" onclick="getBoard()">select boarding points</span> </td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="4" style="padding-left:15px" ><strong>Droping Points : </strong><span style="text-decoration:underline;color:#FF0000;cursor:pointer" onclick="getDrop()">select dropping points</span></td>
						</tr>
						<tr>
							<td style="padding-left:15px">&nbsp;</td>
							<td height="35" colspan="4" style="padding-left:15px" align="center"><input  type="button" class="btn btn-primary" name="create" id="create" value="Create Bus" align="middle" style="width:150px;height:45px" onclick="validate()" /></td>
						</tr>
					</table>
				</div>
			</div>
		</main>
	</section>
</div>