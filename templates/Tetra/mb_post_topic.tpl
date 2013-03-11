					<form enctype="multipart/form-data" action="./index.php?main=mb&amp;action=create_topic&amp;id=<var:id>" method="post">
						<table width="100%" cellspacing="0" cellpadding="0" class="content">
							<tr>
								<td class="Title" colspan="3">
									Create Topic
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td valign="top" style="width: 100px;" class="Head">
									Title:
								</td>
								<td class="VLine"></td>
								<td class="Body" align="center">
									<input name="title" value="<var:title>" maxlength="50" style="width: 98%;">
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="3"></td>
							</tr>
							<tr>
								<td valign="top" style="width: 100px;" class="Head">
									Message:
								</td>
								<td class="VLine"></td>
								<td class="Body" align="center">
									<textarea style="height: 300px; width: 98%; font-family: Verdana; font-size: 12px;" name="body"><var:message></textarea>
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
												<input type="checkbox" name="flag" checked>
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