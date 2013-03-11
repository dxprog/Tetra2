					<form enctype="multipart/form-data" action="./index.php?main=mb&action=create_topic&id=<var:id>&to=<var:to>" method="post">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="Head" valign="top" width="10%">
									Title:
								</td>
								<td class="Content" align="center">
									<input name="title" style="width: 98%; font-family: Verdana; font-size: 12px;" maxlangth="50">
								</td>
							</tr>
							<tr>
								<td class="Head" valign="top" width="10%">
									Message:
								</td>
								<td class="Content" align="center">
									<textarea style="height: 300px; width: 98%; font-family: Verdana; font-size: 12px;" name="body"><var:quote></textarea>
								</td>
							</tr>
							<if:user(user_id) != 0>
								<tr>
									<td class="Head" valign="top">
										Attachment:
									</td>
									<td class="Content">
										<input type="radio" name="attach" value="0" checked> No attachment<br>
										<input type="radio" name="attach" value="1"> Attach:<br>
										&nbsp; &nbsp; File: <input type="file" name="attachment"><br>
										&nbsp; &nbsp; Caption: <input name="caption">
									</td>
								</tr>
							<end:if>
							<tr>
								<td class="Head" valign="top">
									Options:
								</td>
								<td class="Content">
									<table>
										<tr>
											<td>
												<input type="checkbox" name="flag"<if:flagged != 0> checked<end:if>>
											</td>
											<td class="Content">
												Add to flagged threads
											</td>
										</tr>
									<if:user(user_rank) gt 1>
										<tr>
											<td>
												<input type="checkbox" name="sticky"<if:sticky != 0> sticky<end:if>>
											</td>
											<td class="Content">
												Make thread sticky
											</td>
										</tr>
									<end:if>
									</table>
								</td>
							</tr>
							<tr>
								<td class="Head"></td>
								<td align="center">
									<input type="submit" value="Post" style="width: 75px;">
								</td>
							</tr>
						</table>
					</form>