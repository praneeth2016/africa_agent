<script>
    function cpwd()
    {
        var oldpassword = $("#oldpassword").val();
        var newpassword = $("#newpassword").val();
        var conpassword = $("#conpassword").val();

        if (oldpassword == "")
        {
            alert("Please Provide Old Password");
            $("#oldpassword").focus();
            return false;
        }

        if (newpassword == "")
        {
            alert("Please Provide New Password");
            $("#newpassword").focus();
            return false;
        }

        if (conpassword == "")
        {
            alert("Please Provide Confirm Password");
            $("#conpassword").focus();
            return false;
        }
        if (conpassword != newpassword)
        {
            alert("New Password and Confirm Pasword should be match!");
            $("#conpassword").focus();
            return false;
        }
        else
        {
            $.post("<?php echo base_url('Login/password_update'); ?>", {oldpassword: oldpassword, newpassword: newpassword, conpassword: conpassword}, function (res) {
                //alert(res);
                if (res == 1) //success
                {
                    alert('Password updated successfully');
                }
                else
                {
                    alert('Not updated');
                }
            });
        }
    }
</script><div class="clearfix">

    <p>&nbsp;</p>
    <p>&nbsp;</p>
</div>
<div class="row-fluid m-t-small"><div class="row-fluid">

        <div class="span4">
            <form class="form-horizontal">
                <div class="row-fluid">
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label">Name</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">User Name</label>
                            <div class="controls">
                                <input type="text" id="newpassword" name="newpassword" class="bg-focus">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Password</label>
                            <div class="controls">
                                <input type="text" id="conpassword" name="conpassword" class="bg-focus" >
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Email</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Address</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Mobile</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Land Line</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Location</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Branch</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Branch Address</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" class="bg-focus" value="<?php echo $name; ?>">
                            </div>
                        </div>

                    </div>
                </div>

        </div>

        <div class="span6"> </div>
        <?php
        foreach ($agent_data as $row) {
            $name = $row->name;
        }
        ?>
        <div class="span4">

            <div class="row-fluid">
                <div class="span5">
                    <div class="control-group">
                        <label class="control-label">By Cash</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">By Phone</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">By Agent</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">By Phone Agent</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">By Employee</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">PAYMENT REPORTS</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">BOOKING REPORTS</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">PASSENGER REPORTS</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">VEHICLE ASSIGNMENT</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
        <div class="span4">

            <div class="row-fluid">
                <div class="span5">
                    <div class="control-group">
                        <label class="control-label">TICKET BOOKING</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">CHECK FARE</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">TICKET STATUS</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">TICKET CANCELLATION</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">TICKET MODIFY</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">TICKET RESCHEDULE</label>
                        <div class="controls">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><button type="button" class="btn btn-primary" onClick="cpwd();" >Change</button></label>                    
                    </div>

                </div>
            </div>
            </form>
        </div>

    </div></div>