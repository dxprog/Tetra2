					<a name="<var:post(message_id)>">
					<table width="100%" cellspacing="0">
						<tr>
							<td colspan="2">
								<hr class="Rule">
							</td>
						</tr>
						<tr>
							<td class="Head" valign="top">
								<a href="./index.php?main=users&action=profile&id=<var:post(user_id)>" class="Link"><var:post(user_name)></a><br>
							</td>
							<td class="Head" align="center">
								<b>Posted on <var:post(message_date)></b>
							</td>
						</tr>
						<tr>
							<td class="Head" valign="top" style="width: 104px;">
								<if:post(user_mb_avatar) = 0>
									<img src="<var:post(user_mb_avatar)>" alt="<var:post(user_name)>">
								<end:if>
								<var:post(user_mb_title)><br>
							</td>
							<td valign="top" class="Content">
								<var:post(message_body)>
								<if:post(message_attachment) != "">
									<br>
									<hr class="Rule">
									&nbsp; &nbsp; <b>Attachment:</b> <if:user(user_id) gt 0><a href="<var:post(message_attachment)>" class="Link" target="_blank"><end:if>Download Now<if:user(user_id) gt 0></a><end:if>
								<end:if>
							</td>
						</tr>
						<tr>
							<td class="Head"></td>
							<td class="Head" align="center">
								[
								<a href="./index.php?main=mb&action=post_form&id=<var:post(message_parent)>&message=<var:post(message_id)>" class="Link">Quote</a>
								<if:post(message_poster) != user(user_id)>
									| <a href="./index.php?main=mb&action=topic_form&id=52&to=<var:post(message_poster)>" class="Link">Private Topic</a>
								<end:if>
								<if:can_edit = 1>
									| <a href="./index.php?main=mb&action=edit_form&id=<var:post(message_id)>" class="Link">Edit</a>
								<end:if>
								]
							</td>
						</tr>
					</table>