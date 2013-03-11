						<if:nav_type = "user">
							<table width="100%" style="border: 1px solid #000000; background-color: white;">
								<tr>
									<td class="Head">
										Welcome, <var:user(user_name)>
									</td>
								</tr>
						<else>
							<table width="100%" border="0" cellpadding="0">
								<tr>
									<td colspan="10">
										<table width="100%" cellpadding="0" style="border: 1px solid #000000; background-color: #EEEEEE; height: 20px;">
											<tr>
						<end:if>