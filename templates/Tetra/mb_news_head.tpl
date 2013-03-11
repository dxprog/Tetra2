								<table cellspacing="0" cellpadding="0" width="100%" class="content">
									<tr bgcolor="#7d55af">
										<td class="Title">
											News
										</td>
									</tr>
									<tr>
										<td class="HLine"></td>
									</tr>
									<tr>
										<td class="SmallHead">
											Page Index:
											<for:i = 1 To page(num_pages)>
												<if:i = page(current_page)>
													( <var:i> )
												<else>
													<a href="./index.php?main=news&page=<var:i>"><var:i></a>
												<end:if>
											<next:for>
										</td>
									</tr>
								</table>
								<br>