		<form action="./index.php?preprocess=users&action=login" method="POST">
			<table width="100%" id="content">
				<tr>
					<td class="Title" align="center" colspan="2">
						Login
					</td>
				</tr>
				<tr>
					<td class="Content">
						Username:
					</td>
					<td class="Content">
						<input name="user">
					</td>
				</tr>
				<tr>
					<td class="Content">
						Password:
					</td>
					<td class="Content">
						<input type="password" name="pass">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="Login" style="width: 50px;">
					</td>
				</tr>
				<tr>
					<td class="Content" align="center">
						<a href="./index.php?main=users&action=register" class="Link">Register</a>
					</td>
				</tr>
			</table>
		</form>