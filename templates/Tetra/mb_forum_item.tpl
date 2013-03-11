						<tr>
							<set:x = x + 1>
							<set:grid = x % 2>
							<td class="<if:grid = 0>Body<else>Plain<end:if>">
								<if:topic(topic_locked) = 1>
									<img src="./templates/Tetra/styles/Purple/images/mb_locked.gif" alt="Locked">
								<else>
									<if:topic(topic_sticky) = 1>
										<img src="./templates/Tetra/styles/Purple/images/mb_sticky.gif" alt="Sticky">
									<else>
										<if:topic(topic_read) = 0>
											<img src="./templates/Tetra/styles/Purple/images/mb_new.gif" alt="New Posts">
										<else>
											<img src="./templates/Tetra/styles/Purple/images/mb_old.gif" alt="No New Posts">
										<end:if>
									<end:if>
								<end:if>
							</td>
							<td class="<if:grid = 0>Body<else>Plain<end:if>">
								<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:topic(topic_id)>"><var:topic(topic_title)></a>
							</td>
							<td class="<if:grid = 0>Body<else>Plain<end:if>" align="center">
								<a href="./index.php?main=users&amp;action=profile&amp;id=<var:f_post(user_id)>"><var:f_post(user_name)></a>
							</td>
							<td class="<if:grid = 0>Body<else>Plain<end:if>" align="center">
								<set:topic(topic_numposts) = topic(topic_numposts) - 1>
								<var:topic(topic_numposts)>
							</td>
							<td class="<if:grid = 0>Body<else>Plain<end:if>" align="right">
								<var:l_post(message_date)><br>
								by <a href="./index.php?main=users&amp;action=profile&amp;id=<var:l_post(user_id)>"><var:l_post(user_name)></a>
							</td>
						</tr>