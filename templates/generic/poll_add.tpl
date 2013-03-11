					<form action="./index.php?main=poll&action=add" method="post">
						<table class="table_head" cellspacing="0" width="50%">
							<tr>
								<td class="top_left"></td>
								<td align="center">
									Add Poll
								</td>
								<td class="top_right"></td>
							</tr>
							<tr>
								<td class="table_content" colspan="3">
									<b>Poll Caption:</b><br>
									<input name="caption" class="textbox" maxlength="100"><br>
									<b>Poll Items:</b><br>
									<input name="item1" class="textbox"><br>
									<input name="item2" class="textbox"><br>
									<input name="item3" class="textbox"><br>
									<input name="item4" class="textbox"><br>
									<input name="item5" class="textbox"><br>
									<input name="item6" class="textbox"><br>
									<input name="item7" class="textbox"><br>
									<center>
										<input type="submit" class="button" value="Create Poll">
									</center>
								</td>
							</tr>
						</table>
					</form>