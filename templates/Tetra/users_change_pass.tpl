				<form action="./index.php?main=users&amp;action=password&amp;stage=3&amp;uid=<var:u_info(user_id)>&amp;actid=<var:u_info(user_actid)>" method="post">
					<table class="content" cellspacing="0" cellpadding="0">
						<tr>
							<td class="Title">
								Change Password
							</td>
						</tr>
						<tr>
							<td class="HLine"></td>
						</tr>
						<tr>
							<td class="Body">
								Enter new password:<br>
								<input name="pass" type="password">
							</td>
						</tr>
						<tr>
							<td class="Body">
								<input type="submit" style="width: 75px;" value="Send">
							</td>
						</tr>
					</table>
				</form>