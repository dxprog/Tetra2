					<table width="100%">
						<tr>
							<td class="Head" align="center">
								Login
							</td>
						</tr>
						<tr>
							<td class="content">
								<form action="./index.php?preprocess=users&action=login" method="post">
									<table width="100%" border="0">
										<tr>
											<td class="Content">
												Username:
											</td>
											<td>
												<input name="user" style="width: 100px; font-family: Verdana, Sans-serif; font-size: 11px; color: #000000">
											</td>
										</tr>
										<tr>
											<td class="Content">
												Password:
											</td>
											<td>
												<input type="password" name="pass" style="width: 100px; font-family: Verdana, Sans-serif; font-size: 11px; color: #000000">
											</td>
										</tr>
										<tr>
											<td colspan="2" align="center">
												<input type="submit" value="Login">
											</td>
										</tr>
										<tr>
											<td class="Content" align="center" colspan="2">
												<a href="./index.php?main=users&action=register" class="Link">Register</a>
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>