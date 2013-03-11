					<if:nav_type = "user">
						<tr>
							<td>
								&nbsp;<a href="<var:nav_url>" class="Link"><var:nav_caption></a>
							</td>
						</tr>
					<else>
						<set:width = 100 / num_nav_items>
						<td align="center" width="<var:width>%">
							<a href="<var:nav_url>" class="Link"><var:nav_caption></a>
						</td>
					<end:if>