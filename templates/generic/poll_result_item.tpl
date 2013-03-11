							<tr>
								<td align="center" class="Content">
									<b><var:item_caption> (<var:item_votes> votes)</b>
								</td>
							</tr>
							<tr>
								<td class="Content">
									<if:item_percent != 0>
										<table width="<var:item_percent>%">
											<tr>
												<td class="Head">
													<if:item_percent gt 50>
														<var:item_percent>%
													<else>
														&nbsp;
													<end:if>
												</td>
											</tr>
										</table>
									<end:if>
									<if:item_percent = 0 or item_percent lt 50>
										<var:item_percent>%
									<end:if>
								</td>
							</tr>