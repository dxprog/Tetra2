						<tr>
							<td colspan="2">
								<hr class="Rule">
							</td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" width="100%">
									<tr>
										<td class="Head" colspan="2">
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td>
														<a href="./index.php?main=mb&action=view_thread&id=<var:news(topic_id)>" class="Link"><var:news(topic_title)></a>
													</td>
													<td align="right" class="Head">
														<span style="font-size: 11px;"><var:news(message_date)></span>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class="Head" style="width: 103px;" valign="top">
											<img src="<var:news(user_mb_avatar)>" alt="<var:news(user_name)>"><br>
											<var:news(user_name)><br>
											<var:news(user_rank_title)>
										</td>
										<td valign="top" class="Content">
											<var:news(message_body)>
										</td>
									</tr>
									<tr>
										<td class="Head" colspan="2" align="center">
											<set:news(topic_numposts) = news(topic_numposts) - 1>
											[ <a href="./index.php?main=mb&action=view_thread&id=<var:news(topic_id)>" class="Link"><var:news(topic_numposts)> <if:news(topic_numposts != 1>comments<else>comment<end:if></a> ]
										</td>
									</tr>
								</table>
							</td>
						</tr>