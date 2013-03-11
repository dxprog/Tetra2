					<table style="border: 1px solid #000000" width="100%">
						<tr>
							<td class="Head" align="center">
								<if:user(user_id) = 1>
									Login
								<else>
									Welcome, <var:user(user_name)>
								<end:if>
							</td>
						</tr>
						<tr>
							<td class="content">
								<if:user(user_id) = 1>
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
												<td class="Content" align="center">
													<a href="./index.php?main=users&action=register" class="Link">Register</a>
												</td>
											</tr>
										</table>
									</form>
								<else>
									<a href="./index.php?preprocess=users&action=logout" class="Link">Logout</a><br>
									<a href="./index.php?main=users&action=edit_profile" class="Link">Edit User Profile</a><br>
									<if:user(user_rank) = 3>
										<a href="./index.php?main=admin" class="Link">Administrative Tools</a>
									<end:if>
								<end:if>
							</td>
						</tr>
					</table>