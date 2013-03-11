								<table class="content" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="SmallHead">
											Page Index:
											<for:i = 1 To page(num_pages)>
												<if:i = page(current_page)>
													<var:i>
												<else>
													<a href="./index.php?main=news&page=<var:i>"><var:i></a>
												<end:if>
											<next:for>
										</td>
									</tr>
								</table>
