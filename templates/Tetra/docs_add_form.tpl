					<form action="/docs/add_doc" method="post" enctype="multipart/form-data">
						<table class="content" cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<td class="Title" colspan="3">
									Add Document
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" style="width: 20%;">
									Title:
								</td>
								<td class="HLine"></td>
								<td class="Body" style="width: 80%;">
									<input name="title" style="width: 75%;">
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" style="width: 20%;">
									Type:
								</td>
								<td class="HLine"></td>
								<td class="Body" style="width: 80%;">
									<select name="type"><var:doc_types></select>
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" style="width: 20%;">
									Section:
								</td>
								<td class="HLine"></td>
								<td class="Body" style="width: 80%;">
									<select name="section"><var:doc_sections></select>
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" style="width: 20%;" valign="top">
									Description:
								</td>
								<td class="HLine"></td>
								<td class="Body" style="width: 80%;">
									<textarea cols="45" rows="17" name="description"></textarea>
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" style="width: 20%;" valign="top">
									File:
								</td>
								<td class="HLine"></td>
								<td class="Body" style="width: 80%;">
									Upload:
									<input type="file" name="file"><br><br>
									<b>Or</b><br><br>
									Location:
									<input name="location" value="http://">
								</td>
							</tr>
							<tr>
								<td class="VLine" colspan="3"></td>
							</tr>
							<tr>
								<td class="Head" colspan="3" align="center">
									<input type="submit" value="Add Document">
								</td>
							</tr>
						</table>
					</form>