					<table width="100%" cellpadding="0" cellspacing="0" class="content">
						<tr>
							<td class="Title" colspan="4">
								<var:forum(forum_name)>
							</td>
							<td class="Head" align="right">
								<a href="./index.php?main=mb&amp;action=topic_form&amp;id=<var:forum(forum_id)>">New Topic</a>
							</td>
						</tr>
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
						<tr>
							<td class="HLine" colspan="5"></td>
						</tr>
						<tr>
							<td class="SmallHead"></td>
							<td class="SmallHead">
								<b>Topic</b>
							</td>
							<td class="SmallHead" align="center">
								<b>Creator</b>
							</td>
							<td class="SmallHead" align="center">
								<b>Replies</b>
							</td>
							<td class="SmallHead" align="right">
								<b>Last Post</b>
							</td>
						</tr>
						<tr>
							<td class="HLine" colspan="5"></td>
						</tr>