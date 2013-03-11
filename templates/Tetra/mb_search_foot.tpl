					<tr>
						<td class="HLine" colspan="3"></td>
					</tr>
					<tr>
						<td class="SmallHead" colspan="2">
							Page Index:
							<for:i = 1 To page(num_pages)>
								<if:i = page(current_page)>
									( <var:i> )
								<else>
									<a href="./index.php?main=mb&amp;action=search&amp;q=<var:query>&amp;page=<var:i>"><var:i></a>
								<end:if>
							<next:for>
						</td>
						<td class="SmallHead" align="right">
							<var:total> result<if:total != 1>s<end:if>
						</td>
					</tr>
				</table>