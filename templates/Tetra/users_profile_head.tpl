					<table width="100%" class="Content" cellspacing="0" cellpadding="0">
						<tr>
							<td class="Title" colspan="3">
								Viewing <var:u_info(user_name)>'s profile
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="3"></td>
						</tr>
						<tr>
							<td class="Head" colspan="3">
								User Profile
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="3"></td>
						</tr>
						<tr>
							<td class="Head">
								Name:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_rname)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head">
								Birthdate:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_bdate)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head">
								Joined:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_joined)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head">
								Last login:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_lastlogin)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head">
								Location:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_from)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head">
								E-Mail:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_email)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<if:u_info(user_aim) != 0>
							<tr>
								<td class="Head">
									AIM Screen name:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<var:u_info(user_aim)>
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
								<td class="Body"></td>
							</tr>
						<end:if>
						<if:u_info(user_msn) != 0>
							<tr>
								<td class="Head">
									.NET Passport:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<var:u_info(user_msn)>
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
								<td class="Body"></td>
							</tr>
						<end:if>
						<if:u_info(user_icq) != 0>
							<tr>
								<td class="Head">
									ICQ Number:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<var:u_info(user_icq)>
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
								<td class="Body"></td>
							</tr>
						<end:if>
						<if:u_info(user_irc) != "">
							<tr>
								<td class="Head">
									IRC Nick:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<var:u_info(user_irc)>
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
								<td class="Body"></td>
							</tr>
						<end:if>
						<if:u_info(user_http) != "">
							<tr>
								<td class="Head">
									Website:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<a href="<var:u_info(user_http)>" target="_blank"><var:u_info(user_http)></a>
								</td>
							</tr>
						<end:if>