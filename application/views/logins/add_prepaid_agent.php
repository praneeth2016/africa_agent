
<div class="clearfix"><table align="center"   style="margin: 0px auto;" cellspacing="2">
              <tr>
              	<td height="26" colspan="5" align="center" valign="top" class="headingg">&nbsp;</td>
   	</tr>
              <tr>
                <td height="26" colspan="5" align="center" valign="top" class="headingg"></td>
              </tr>
              <tr>
              	<td>&nbsp;</td>
              	<td class="label">Agent_type</td>
              	<td>&nbsp;</td>
              	<td><label>
              		<select name="agent_type" id="agent_type">
					<option value="0">--Select--</option>
					<option value="branch">Branch</option>
					<option value="postpaid">Postpaid</option>
					<option value="prepaid">Prepaid</option>
       			</select>
              	</label></td>
              	<td>&nbsp;</td>
       	</tr>
              <tr>
                <td width="246">&nbsp;</td>
                <td width="132" class="label"> Name:</td>
                <td width="11">&nbsp;</td>
                <td width="451"><input type="text" id="name" name="name" class="inputfield" value="<?php echo set_value('name'); ?>" /></td>
                <td width="94">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="label">Username:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="user_name" name="user_name"   class="inputfield" value="<?php echo set_value('user_name'); ?>" onChange="checkUser();" /></td>
                <td align="left"><span id="un"></span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="label">Email:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="email_address" name="email_address"  class="inputfield"value="<?php echo set_value('email_address'); ?>" /></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="label">Password:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="password" name="password" class="inputfield" value="<?php echo set_value('password'); ?>" /></td>
                <td>&nbsp;</td>
              </tr>              
              <tr>
                <td>&nbsp;</td>
                <td class="label">Contact No:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="contact" name="contact" class="inputfield" value="<?php echo set_value('contact'); ?>" maxlength="10" /></td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td class="label">Land Line:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="landline" name="landline" class="inputfield" value="<?php echo set_value('landline'); ?>" /></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="label">Location:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="locat" name="locat" class="inputfield" value="" />				</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td class="label">Branch:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="branch" name="branch" class="inputfield" value="" /></td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td class="label">Branch Address:</td>
                <td>&nbsp;</td>
                <td><input type="text" id="branch_address" name="branch_address"  class="inputfield" value="<?php echo set_value('branch_address'); ?>" /></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="label">Main Address:</td>
                <td>&nbsp;</td>
                <td><textarea rows="3" cols="18" id="address" name="address"  class="inputfield" value="<?php echo set_value('address'); ?>" ></textarea></td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td class="label">Booking Type : </td>
                <td>&nbsp;</td>
                <td>
<input name="by_cash" id="by_cash" type="checkbox" value="" checked="checked" />
By Cash:
<input name="by_phone" id="by_phone" type="checkbox" value="" />                  
By Phone:
<input name="by_agent" id="by_agent" type="checkbox" value="" />
By Agent:
<input name="by_phone_agent" id="by_phone_agent" type="checkbox" value="" />
By Phone Agent:
</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
			    <td >&nbsp;</td>
			    <td colspan="3" style="background-color:#CCCCCC"><strong>User Privilages </strong></td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="payment_reports" id="payment_reports" value="">&nbsp;&nbsp;PAYMENT REPORTS</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="booking_reports" id="booking_reports" value="">&nbsp;&nbsp;BOOKING REPORTS</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="passenger_reports" id="passenger_reports" value="">&nbsp;&nbsp;PASSENGER REPORTS</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="vehicle_assignment" id="vehicle_assignment" value="">&nbsp;&nbsp;VEHICLE ASSIGNMENT</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="ticket_booking" id="ticket_booking" value="">&nbsp;&nbsp;TICKET BOOKING</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="check_fare" id="check_fare" value="">&nbsp;&nbsp;CHECK FARE</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="ticket_status" id="ticket_status" value="">&nbsp;&nbsp;TICKET STATUS</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td>&nbsp;</td>
                <td height="22" colspan="3"><input type="checkbox" name="ticket_cancellation" id="ticket_cancellation" value="">&nbsp;&nbsp;TICKET CANCELLAION</td>
                <td>&nbsp;</td>
              </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="ticket_modify" id="ticket_modify" value="">&nbsp;&nbsp;TICKET MODIFY</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="board_passenger_reports" id="board_passenger_reports" value="">&nbsp;&nbsp;BOARDING PASSENGER REPORT</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="ticket_reschedule" id="ticket_reschedule" value="">&nbsp;&nbsp;TICKET RESCHEDULE</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
			    <td>&nbsp;</td>
			    <td height="22" colspan="3"><input type="checkbox" name="group_boarding_passenger_reports" id="group_boarding_passenger_reports" value="">&nbsp;&nbsp;GROUP BOARDING PASSENGER REPORTS</td>
			    <td>&nbsp;</td>
		      </tr>
			  <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td>&nbsp;</td>
                <td><input type="submit" class="btn btn-primary" id="add_new" name="add_new" value="Add" onClick="validate()" /></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="5" id="result"></td>
              </tr>
            </table>
</div>