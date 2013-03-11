					<form action="./index.php?preprocess=users&action=update_settings" method="post" enctype="multipart/form-data">
						<table width="100%" cellspacing="0">
							<tr>
								<td class="Head" align="center" colspan="2">
									User Profile Settings
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Enter Password:
								</td>
								<td class="Content">
									<input name="pass" type="password"> <font color="red">* Required for your security</font>
								</td>
							</tr>
							<tr>
								<td class="Head"></td>
								<td><hr class="Rule"></td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Change Password:
								</td>
								<td>
									<input type="password" name="new_pass1">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Repeat:
								</td>
								<td>
									<input type="password" name="new_pass2">
								</td>
							</tr>
							<tr>
								<td class="Head"></td>
								<td><hr class="Rule"></td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									E-Mail:
								</td>
								<td>
									<input name="email" value="<var:user(user_email)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Make e-mail visible:
								</td>
								<td>
									<input type="checkbox" name="show"<if:user(user_showemail) = 1>checked<end:if>>
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									.NET Passport:
								</td>
								<td>
									<input name="msn" maxlength="10" value="<var:user(user_msn)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									AIM Username:
								</td>
								<td>
									<input name="aim" maxlength="10" value="<var:user(user_aim)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									ICQ Number:
								</td>
								<td>
									<input name="icq" maxlength="10" value="<var:user(user_icq)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									IRC Nickname:
								</td>
								<td>
									<input name="irc" value="<var:user(user_irc)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Website:
								</td>
								<td>
									<input name="http" value="<var:user(user_http)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head"></td>
								<td><hr class="Rule"></td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Real Name:
								</td>
								<td>
									<input name="rname" value="<var:user(user_rname)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Location:
								</td>
								<td>
									<input maxlength="30" name="from" value="<var:user(user_from)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td width="20%" class="Head">
									Birth Date:
								</td>
								<td class="Content">
									Month:
									<select name="b_month">
										<for:i = 1 To 12>
											<option value="<var:i>"<if:i = b_month> selected<end:if>><var:i></option>
										<next:for>
									</select>
									Day:
									<select name="b_day">
										<for:j = 1 To 31>
											<option value="<var:j>"<if:j = b_day> selected<end:if>><var:j></option>
										<next:for>
									</select>
									Year:
									<select name="b_year">
										<for:k = 1950 To 2010>
											<option value="<var:k>"<if:k = b_year> selected<end:if>><var:k></option>
										<next:for>
									</select>
								</td>
							</tr>
						</table>