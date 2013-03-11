		<form action="./index.php?preprocess=users&amp;action=login" method="POST">
			<table width="100%" class="content" cellspacing="0" cellpadding="0">
				<tr>
					<td class="Title">
						Login
					</td>
				</tr>
				<tr>
					<td class="HLine"></td>
				</tr>
				<tr>
					<td class="Body" align="center">
						Username
					</td>
				</tr>
				<tr>
					<td align="center">
						<input name="user" style="width: 150px;">
					</td>
				</tr>
				<tr>
					<td class="Body" align="center">
						Password
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="password" name="pass" style="width: 150px;">
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="submit" value="Login" style="width: 75px;">
					</td>
				</tr>
				<tr>
					<td class="Body" align="center">
						( <a href="./index.php?main=users&amp;action=register" class="Small">Register</a> | <a href="./index.php?main=users&amp;action=password" class="Small">Lost Password</a> )
					</td>
				</tr>
			</table>
		</form>