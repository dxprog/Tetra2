						<tr>
							<td class="Content">
								<a href="./index.php?main=mb&action=view_forum&id=<var:forum(forum_id)>" class="Link"><var:forum(forum_name)></a><br>
								<var:forum(forum_description)>
							</td>
							<td class="Content" align="center">
								<var:num_topics>
							</td>
							<td class="Content" align="right">
								<if:num_topics != 0>
									<var:n_topic(message_date)> by <a href="./index.php?main=users&action=profile&id=<var:n_topic(user_id)>" class="Link"><var:n_topic(user_name)></a> in<br>
									<a href="./index.php?main=mb&action=view_thread&id=<var:n_topic(topic_id)>" class="Link"><var:n_topic(topic_title)></a>
								<end:if>
							</td>
						</tr>