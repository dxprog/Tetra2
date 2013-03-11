				<form action="./index.php?main=users&action=adduser" method="post">
					<table width="100%" style="border: 1px solid #000000; background-color: white;">
						<tr>
							<td class="Head">
								Register
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%">
									<tr>
										<td class="Content">
											Username:
										</td>
										<td>
											<input name="username" maxlength="32" value="<var:username>">
										</td>
									</tr>
									<tr>
										<td class="Content">
											Password:
										</td>
										<td>
											<input type="password" name="pass1">
										</td>
									</tr>
									<tr>
										<td class="Content">
											Repeat password:
										</td>
										<td>
											<input type="password" name="pass2">
										</td>
									</tr>
									<tr>
										<td class="Content">
											E-Mail Address:
										</td>
										<td>
											<input name="email" maxlength="50" value="<var:email>">
										</td>
									</tr>
									<tr>
										<td align="center" colspan="2">
											<input type="submit" value="Register">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>