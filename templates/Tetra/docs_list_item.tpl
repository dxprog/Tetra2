						<tr>
							<td class="HLine" colspan="2"></td>
						</tr>
						<tr>
							<td class="Body" style="width: 68px;" align="center">
								<if:thumbnail = 1>
									<img src="/docs/thumb/<var:doc(doc_id)>" alt="<var:doc(doc_title)>" style="border: 1px solid #000000;">
								<else>
									<img src="/templates/Tetra/styles/<var:user(user_style)>/images/doc_<var:doc_type>.png" alt="<var:doc_type>" style="border: 1px solid #000000;">
								<end:if>
							</td>
							<td class="Body">
								<span style="font-size: 16px; font-weight: bold;"><a href="javascript:DocWindow (<var:doc(doc_id)>)"><var:doc(doc_title)></a></span><br>
								<b>Poster: </b><var:doc(user_name)><br>
								<b>Document type: </b><var:doc(type_description)><br>
								<b>File type: </b><var:doc_type> (<var:ext>)<br>
							</td>
						</tr>