					<table width="100%" cellspacing="0">
						<tr>
							<td align="center" class="Head">
								[ <a href="./index.php?main=mb&action=post_form&id=<var:topic(topic_id)>" class="Link">Post Reply</a> ]
							</td>
						</tr>
						<tr>
							<td class="content">
								Page Index:
								<for:i = 1 To page(num_pages)>
									<if:i = page(current_page)>
										[ <var:i> ]
									<else>
										<a href="./index.php?main=mb&action=view_thread&id=<var:topic(topic_id)>&page=<var:i>" class="Link"><var:i></a>
									<end:if>
								<next:for>
							</td>
						</tr>
					</table>