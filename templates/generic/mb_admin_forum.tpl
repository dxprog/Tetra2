					<form action="./index.php?main=mb&action=admin&admin=createforum" method="post">
						<table align="center">
							<if:sectionform = 0>
								<tr>
									<td>
										<b>Section:</b>
									</td>
									<td>
										<select name="f_section"><var:sections></select>
									</td>
								</tr>
							<end:if>
							<tr>
								<td class="Content">
									<b>Name:</b>
								</td>
								<td>
									<input name="f_name" maxlength="50">
								</td>
							</tr>
							<if:sectionform = 0>
								<tr>
									<td class="Content">
										<b>Description:</b>
									</td>
									<td>
										<input name="f_desc" maxlength="255">
									</td>
								</tr>
								<tr>
									<td class="Content" valign="top">
										<b>Who should be able to view this forum?</b>
									</td>
									<td class="Content">
										<input type="radio" name="f_view" value="0" selected>Everybody<br>
										<input type="radio" name="f_view" value="1">Only registered users<br>
										<input type="radio" name="f_view" value="2">Only admins and moderators<br>
										<input type="radio" name="f_view" value="3">Only admins<br>
									</td>
								</tr>
								<tr>
									<td class="Content" valign="top">
										<b>Who can create topics on this forum?</b>
									</td>
									<td class="Content">
										<input type="radio" name="f_topic" value="0">Everybody<br>
										<input type="radio" name="f_topic" value="1" selected>Only registered users<br>
										<input type="radio" name="f_topic" value="2">Only admins and moderators<br>
										<input type="radio" name="f_topic" value="3">Only admins<br>
									</td>
								</tr>
								<tr>
									<td class="Content" valign="top">
										<b>Who can reply to topics on this forum?</b>
									</td>
									<td class="Content">
										<input type="radio" name="f_post" value="0">Everybody<br>
										<input type="radio" name="f_post" value="1" selected>Only registered users<br>
										<input type="radio" name="f_post" value="2">Only admins and moderators<br>
										<input type="radio" name="f_post" value="3">Only admins<br>
									</td>
								</tr>
								<tr>
									<td class="Content" valign="top">
										<b>Use topics from this<br>
										forum for news items</b>
									</td>
									<td>
										<input type="checkbox" name="f_news">
									</td>
								</tr>
							<end:if>
							<tr>
								<td colspan="2" align="center">
									<input type="submit" value="Create">
								</td>
							</tr>
						</table>
					</form>