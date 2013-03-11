								<table cellspacing="0" cellpadding="0" width="100%" class="content">
									<tr>
										<td class="Title" colspan="3">
											<table width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td class="Head">
														<span style="font-size: 14px;"><var:news(topic_title)></span>
													</td>
													<td align="right" class="Head">
														<var:news(message_date)>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="HLine"></td>
									</tr>
									<tr>
										<td class="Avatar" valign="top" style="width: 102px;">
											<img src="<var:news(user_mb_avatar)>" alt="<var:news(user_name)>" style="height: 100px; width: 100px;">
											<a href="./index.php?main=users&amp;action=profile&amp;id=<var:news(message_poster)>"><var:news(user_name)></a><br>
											<var:news(user_mb_title)><br>
											<var:news(user_rank_title)>
										</td>
										<td class="VLine"></td>
										<td valign="top" class="Body">
											<var:news(message_body)>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="HLine"></td>
									</tr>
									<tr>
										<td colspan="3" align="center" class="Head">
											<set:news(topic_numposts) = news(topic_numposts) - 1>
											( <a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:news(topic_id)>"><var:news(topic_numposts)> comments</a> | <a href="./index.php?main=mb&amp;action=post_form&amp;id=<var:news(topic_id)>">Post Comment</a> )
										</td>
									</tr>
								</table>
								<br>