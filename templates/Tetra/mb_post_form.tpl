					<form enctype="multipart/form-data" action="./index.php?main=mb&amp;action=post&amp;id=<var:id>" method="post">
						<table width="100%" cellspacing="0" cellpadding="0" class="content">
							<tr>
								<td class="Title" colspan="3">
									Post Reply
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<if:user(user_id) = 1>
								<tr>
									<td valign="top" style="width: 100px;" class="Head">
										Nickname:
									</td>
									<td class="VLine"></td>
									<td class="Body">
										<input name="guest_name" maxlenght="25">
									</td>
								</tr>
							<end:if>
							<tr>
								<td valign="top" style="width: 100px;" class="Head">
									Message:
								</td>
								<td class="VLine"></td>
								<td class="Body" align="center">
									<textarea style="height: 300px; width: 98%; font-family: Verdana; font-size: 12px;" name="body"><var:quote></textarea>
								</td>
							</tr>
							<if:user(user_id) != 0>
								<tr>
									<td class="HLine" colspan="3"></td>
								</tr>
								<tr>
									<td class="Head" valign="top">
										Attachment:
									</td>
									<td class="VLine"></td>
									<td class="Body">
										<input type="radio" name="attach" value="0" checked> No attachment<br>
										<input type="radio" name="attach" value="1"> Attach:<br>
										&nbsp; &nbsp; File: <input type="file" name="attachment"><br>
										&nbsp; &nbsp; Caption: <input name="caption">
									</td>
								</tr>
							<end:if>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" valign="top">
									Options:
								</td>
								<td class="VLine"></td>
								<td class="Body">
									<table>
										<tr>
											<td>
												<input type="checkbox" name="flag"<if:flagged != 0> checked<end:if>>
											</td>
											<td class="Body">
												Add to flagged threads
											</td>
										</tr>
									<if:user(user_rank) gt 1>
										<tr>
											<td>
												<input type="checkbox" name="sticky"<if:sticky != 0> sticky<end:if>>
											</td>
											<td class="Body">
												Make thread sticky
											</td>
										</tr>
										<tr>
											<td class="Body">
												<input type="checkbox" name="lock">
											</td>
											<td class="Body">
												Lock topic (action is permanent)
											</td>
										</tr>
									<end:if>
									</table>
								</td>
							</tr>
							<tr>
								<td class="Head"></td>
								<td class="VLine"></td>
								<td align="center" class="Body">
									<input type="submit" value="Post" style="width: 75px;">
								</td>
							</tr>
						</table>
					</form>