									<a name="<var:post(message_id)>"></a>
									<tr>
										<td colspan="3" class="HLine"></td>
									</tr>
									<tr>
										<td class="Avatar" valign="top">
											<img src="<var:post(user_mb_avatar)>" alt="<var:post(user_name)>" style="height: 100; width: 100;">
											<a href="./index.php?main=users&amp;action=profile&amp;id=<var:post(message_poster)>"><var:post(user_name)></a><br>
											<var:post(user_mb_title)><br>
											<var:post(user_rank_title)>
										</td>
										<td class="VLine"></td>
										<td valign="top" class="Body" style="padding: 0px;">
											<table width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td class="Head" align="right">
														<span style="font-size: 12px;">Posted on <var:post(message_date)></span>
													</td>
												</tr>
												<tr>
													<td class="HLine"></td>
												</tr>
												<tr>
													<td class="Body">
														<var:post(message_body)>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="HLine"></td>
									</tr>
									<tr>
										<td colspan="3" align="center" class="Head">
											(
											<if:user(user_id) != post(message_poster)>
												<a href="./index.php?main=mb&amp;action=post_form&amp;id=<var:topic(topic_id)>&amp;message=<var:post(message_id)>">Quote</a> | <a href="./index.php?main=mb&amp;action=topic_form&amp;id=52&amp;to=<var:post(message_poster)>">Send Private Message</a> |
											<end:if>
											<if:can_edit = 1>
												<a href="./index.php?main=mb&amp;action=edit_form&amp;id=<var:post(message_id)>">Edit</a>
											<end:if>
											)
										</td>
									</tr>