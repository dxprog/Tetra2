					<form action="./index.php?main=mb&action=edit_post&id=<var:id>" method="post">
						<table width="100%" cellspacing="0" cellpadding="0">
							<if:title_edit != 0>
								<tr>
									<td class="Head" valign="top" width="10%">
										Title:
									</td>
									<td class="Content" align="center">
										<input name="title" style="width: 98%; font-family: Verdana; font-size: 12px;" maxlangth="50" value="<var:title>">
									</td>
								</tr>
							<end:if>
							<tr>
								<td class="Head" valign="top" width="10%">
									Message:
								</td>
								<td class="Content" align="center">
									<textarea style="height: 300px; width: 98%; font-family: Verdana; font-size: 12px;" name="body"><var:message></textarea>
								</td>
							</tr>
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
												<input type="checkbox" name="sticky"<if:sticky != 0> checked<end:if>>
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
									<input type="submit" value="Edit" style="width: 75px;">
								</td>
							</tr>
						</table>
					</form>