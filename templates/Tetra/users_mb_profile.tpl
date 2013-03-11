						<tr>
							<td class="HLine" colspan="3"></td>
						</tr>
						<tr>
							<td class="Head" colspan="3">
								MessageBoard Profile
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="3"></td>
						</tr>
						<tr>
							<td class="Head">
								Signature:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_mb_signature)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head" valign="top">
								Avatar:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<img src="<var:u_info(user_mb_avatar)>" alt="<var:u_info(user_name)>">
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head" valign="top">
								Title:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<var:u_info(user_mb_title)>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="2"></td>
							<td class="Body"></td>
						</tr>
						<tr>
							<td class="Head" valign="top">
								Last Post:
							</td>
							<td class="VLine"></td>
							<td class="Body">
								<a href="./index.php?main=mb&amp;action=view_thread&amp;id=<var:post(topic_id)>&amp;page=<var:post(post_page)>#<var:post(message_id)>"><var:post(topic_title)></a>
							</td>
						</tr>