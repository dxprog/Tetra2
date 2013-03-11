								<table cellspacing="0" cellpadding="0" width="100%" class="content">
									<tr>
										<td class="Title" colspan="3">
											<a href="./index.php?main=mb&amp;action=view_forum&amp;id=<var:forum(forum_id)>" class="Big"><var:forum(forum_name)></a> &gt;&gt; <var:topic(topic_title)>
										</td>
									</tr>
									<tr>
										<td class="HLine" colspan="3"></td>
									</tr>
									<tr>
										<td class="Head" colspan="3">
											<table width="100%" cellpadding="0" cellspacing="0">
												<tr>
													<td class="Plain">
														Page Index:
														<for:i = 1 To page(num_pages)>
															<if:i = page(current_page)>
																( <var:i> )
															<else>
																<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:topic(topic_id)>&amp;page=<var:i>"><var:i></a>
															<end:if>
														<next:for>
													</td>
													<td align="right">
														<if:topic(topic_locked) = 0>
															<a href="./index.php?main=mb&amp;action=post_form&amp;id=<var:topic(topic_id)>">Post Reply</a>
														<else>
															<b>Locked</b>
														<end:if>
													</td>
												</tr>
											</table>
										</td>
									</tr>