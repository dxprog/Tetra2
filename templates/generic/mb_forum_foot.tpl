						<if:forum(forum_id) != 0>
							<tr>
								<td colspan="4" align="center" class="Head">
									[ <a href="./index.php?main=mb&action=topic_form&id=<var:forum(forum_id)>" class="Link">Create New Topic</a> ]
								</td>
							</tr>
						<end:if>
						<tr>
							<td colspan="4" class="Content">
								Page Index:
								<for:i = 1 To page(num_pages)>
									<if:i = page(current_page)>
										[ <var:i> ]
									<else>
										<a href="./index.php?main=mb&action=view_forum&id=<var:forum(forum_id)>&page=<var:i>" class="Link"><var:i></a>
									<end:if>
								<next:for>
							</td>
						</tr>
					</table>