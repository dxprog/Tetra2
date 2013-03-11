									<tr>
										<td class="Body">
											<var:poll_item(vote_caption)> (<var:poll_item(vote_count)> votes)
											<if:poll_item(vote_count) gt 0>
												<set:perc = poll_item(vote_count) / total_votes>
												<set:perc = perc * 100>
												<table width="100%">
													<tr>
														<td width="<var:perc>%">
															<table width="100%" cellpadding="0" cellspacing="0" class="Content">
																<tr>
																	<td class="Title" <if:perc lt 50>style="padding: 0px;"<end:if>><if:perc lt 50></td><else><var:perc>%</td><end:if>
																</tr>
															</table>
														</td>
														<td class="Body">
															<if:perc lt 50>
															- <var:perc>%
															<end:if>
														</td>
													</tr>
												</table>
											<end:if>
										</td>
									</tr>