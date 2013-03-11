						<tr>
							<td class="HLine" colspan="5"></td>
						</tr>
						<tr>
							<td class="SmallHead" colspan="5">
								Page Index:
								<for:i = 1 To page(num_pages)>
									<if:i = page(current_page)>
										<var:i>
									<else>
										<a href="./index.php?main=mb&amp;action=view_forum&amp;id=<var:forum(forum_id)>&amp;page=<var:i>"><var:i></a>
									<end:if>
								<next:for>
							</td>
						</tr>
					</table>