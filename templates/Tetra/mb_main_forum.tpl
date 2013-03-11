					<tr>
						<set:x = x + 1>
						<set:grid = x % 2>
						<td class="<if:grid = 0>Body<else>Plain<end:if>" valign="top">
							<a href="./index.php?main=mb&amp;action=view_forum&amp;id=<var:forum(forum_id)>"><var:forum(forum_name)></a><br>
							<var:forum(forum_description)>
						</td>
						<td class="<if:grid = 0>Body<else>Plain<end:if>" align="center" valign="top">
							<var:num_topics>
						</td>
						<td class="<if:grid = 0>Body<else>Plain<end:if>" align="right">
							<if:num_topics gt 0>
								<var:n_topic(message_date)> by <a href="./index.php?main=users&amp;action=profile&amp;id=<var:n_topic(user_id)>"><var:n_topic(user_name)></a> in <br>
								<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:n_topic(topic_id)>"><var:n_topic(topic_title)></a>
							<end:if>
						</td>
					</tr>