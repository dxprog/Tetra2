					<tr>
						<td class="HLine"></td>
					</tr>
					<tr>
						<td class="<if:topic(topic_read) = 1>Body<else>SmallHead<end:if>">
							<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:topic(topic_id)>&amp;page=<var:topic(page)>#<var:topic(topic_lastpost)>"><var:topic(topic_title)></a><br>
							by <a href="./index.php?main=users&amp;action=profile&amp;id=<var:post(user_id)>"><var:post(user_name)></a><br>
							on <var:post(message_date)>
						</td>
					</tr>