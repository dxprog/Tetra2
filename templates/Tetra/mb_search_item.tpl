					<tr>
						<td class="Body" valign="top">
							<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:topic(topic_id)>&amp;page=<var:post(message_page)>#<var:post(message_id)>"><var:topic(topic_title)></a>
						</td>
						<td class="Body" valign="top">
							<var:post(message_body)>
						</td>
						<td class="Body" align="right" valign="top">
							<var:post(message_date)><br>
							by <a href="./index.php?main=users&amp;action=profile&amp;id=<var:post(user_id)>"><var:post(user_name)></a>
						</td>
					</tr>