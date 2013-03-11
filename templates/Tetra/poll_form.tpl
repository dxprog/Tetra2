					<form action="./index.php?main=poll&action=create_poll" method="post">
						<table width="100%" class="content" cellpadding="0" cellspacing="0">
							<tr>
								<td class="Title" colspan="2">
									Create Poll
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
							</tr>
							<tr>
								<td class="Body">
									Poll title:
								</td>
								<td class="Body">
									<input name="title" maxlength="150">
								</td>
							</tr>
							<tr>
								<td class="HLine" colspan="2"></td>
							</tr>
							<for:i = 1 To 10>
							<tr>
								<td class="Body">
									Item <var:i>:
								</td>
								<td class="Body">
									<input name="item<var:i>" maxlength="100">
								</td>
							</tr>
							<next:for>
							<tr>
								<td class="HLine" colspan="2"></td>
							</tr>
							<tr>
								<td class="Head" colspan="2" align="center">
									<input type="submit" value="Create" style="width: 75px;">
								</td>
							</tr>
						</table>
					</form>