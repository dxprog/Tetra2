						<tr>
							<td class="Content">
								<set:NumItems = NumItems + 1>
								<input type="checkbox" name="nav<var:NumItems>" value="<var:caption>|<var:url>"<if:InBar gt 0> checked<end:if>><var:caption>
							</td>
						</tr>