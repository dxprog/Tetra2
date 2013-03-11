					<form action="./index.php?preprocess=users&action=update_settings" method="post" enctype="multipart/form-data">
						<table width="100%" cellspacing="0" class="content">
							<tr>
								<td class="Title" colspan="3">
									User Profile Settings
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" width="20%">
									Enter Password:
								</td>
								<td class="VLine"></td>
								<td class="Body" width="80%">
									<input name="pass" type="password"> <font color="red">* Required for your security</font>
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head">
									Change Password:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input type="password" name="new_pass1">
								</td>
							</tr>
							<tr>
								<td class="Head">
									Repeat:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input type="password" name="new_pass2">
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head">
									E-Mail:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="email" value="<var:user(user_email)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									Make e-mail visible:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input type="checkbox" name="show"<if:user(user_showemail) = 1>checked<end:if>>
								</td>
							</tr>
							<tr>
								<td class="Head">
									<a href="./date.html" target="_blank">Date format</a>
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="date" value="<var:user(user_tf)>">
								</td>
							</tr>
							<tr>
								<td class="Head">
									.NET Passport:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="msn" maxlength="10" value="<var:user(user_msn)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									AIM Username:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="aim" maxlength="10" value="<var:user(user_aim)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									ICQ Number:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="icq" maxlength="10" value="<var:user(user_icq)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									IRC Nickname:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="irc" value="<var:user(user_irc)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									Website:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="http" value="<var:user(user_http)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head">
									Real Name:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input name="rname" value="<var:user(user_rname)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									Location:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<input maxlength="30" name="from" value="<var:user(user_from)>" style="width: 50%;">
								</td>
							</tr>
							<tr>
								<td class="Head">
									Birth Date:
								</td>
								<td class="VLine"></td>
								<td class="Body">
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