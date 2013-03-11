					<set:bginc = bginc + 1>
					<tr>
						<set:bg = bginc % 2>
						<td valign="bottom" bgcolor="#<if:bg = 1>FFFFFF<else>FAFAFA<end:if>">
							<if:topic(topic_locked) = 0>
								<if:topic(topic_read) != 0>
									<a href="./index.php?main=mb&action=view_thread&id=<var:topic(topic_id)>" class="Link" style="font-weight: normal;"><var:topic(topic_title)></a>
								<else>
									<a href="./index.php?main=mb&action=view_thread&id=<var:topic(topic_id)>&new_post=true&headers=none" class="Link"><var:topic(topic_title)></a>
								<end:if>
							<else>
								<if:topic(topic_read) != 0>
									<a href="./index.php?main=mb&action=view_thread&id=<var:topic(topic_id)>" class="Link" style="font-weight: normal; text-decoration: strike-through;"><var:topic(topic_title)></a>
								<else>
									<a href="./index.php?main=mb&action=view_thread&id=<var:topic(topic_id)>&new_post=true&headers=none" class="Link" style="text-decoration: strike-through;"><var:topic(topic_title)></a>
								<end:if>
							<end:if>
						</td>
						<td align="center" valign="bottom" bgcolor="#<if:bg = 1>FFFFFF<else>FAFAFA<end:if>">
							<a href="./index.php?main=users&action=profile&id=<var:f_post(user_id)>" class="Link"><var:f_post(user_name)></a>
						</td>
						<td class="Content" align="center" valign="bottom" bgcolor="#<if:bg = 1>FFFFFF<else>FAFAFA<end:if>">
							<set:topic(topic_numposts) = topic(topic_numposts) - 1>
							<var:topic(topic_numposts)>
						</td>
						<td class="Content" align="right" valign="bottom" bgcolor="#<if:bg = 1>FFFFFF<else>FAFAFA<end:if>">
							<var:l_post(message_date)><br>
							by <a href="./index.php?main=users&action=profile&id=<var:l_post(user_id)>" class="Link"><var:l_post(user_name)></a>
						</td>
					</tr>
