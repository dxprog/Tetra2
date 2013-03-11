				<form action="/preprocess/docs/admin/add_section" method="post">
					<table class="content" cellpadding="0" cellspacing="0">
						<tr>
							<td class="Title">
								Add Section
							</td>
						</tr>
						<tr>
							<td class="VLine"></td>
						</tr>
						<tr>
							<td class="Body">
								Name: <input name="section">
							</td>
						</tr>
						<tr>
							<td class="Body">
								Parent: <select name="parent"><var:sections></select>
							</td>
						</tr>
						<tr>
							<td class="Body">
								Description:<br>
								<textarea name="description" style="width: 350px; height: 200px;"></textarea>
							</td>
						</tr>
						<tr>
							<td align="center" class="Body">
								<input type="submit" value="Create">
							</td>
						</tr>
					</table>
				</form>