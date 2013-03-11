				<form action="./index.php?preprocess=users&amp;main=users&amp;action=layout&amp;layout=add&amp;id=<var:id>" method="post">
					<table width="50%" class="content" cellspacing="0" cellpadding="0">
						<tr>
							<td class="Title">
								Add Content
							</td>
						</tr>
						<tr>
							<td class="HLine"></td>
						</tr>
						<tr>
							<td class="Body">
								<select name="content">
									<var:options>
								</select>
							</td>
						</tr>
						<tr>
							<td class="Body" align="center">
								<input type="submit" value="Add">
							</td>
						</tr>
					</table>
				</form>