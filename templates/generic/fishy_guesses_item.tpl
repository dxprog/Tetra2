										<tr>
											<td>
												<table width="100%" style="border: 1px dashed #000000;">
													<tr>
														<td>
															<img src="./templates/generic/styles/images/fish<var:Fish1>.png">
														</td>
														<td>
															<img src="./templates/generic/styles/images/fish<var:Fish2>.png">
														</td>
														<td>
															<img src="./templates/generic/styles/images/fish<var:Fish3>.png">
														</td>
														<td>
															<img src="./templates/generic/styles/images/fish<var:Fish4>.png">
														</td>
														<td>
															<table>
																<tr>
																	<td>
																		<for:i = 0 To 3>
																			<set:temp = i mod 2>
																			<if:temp = 0>
																	</td>
																</tr>
																<tr>
																	<td>
																			<end:if>
																			<if:results(rcrp) gt 0>
																				<set:results(rcrp) = results(rcrp) - 1>
																				<img src="./templates/generic/styles/images/rcrp.png" alt="Right Color, Right Spot">
																			<else>
																				<if:results(rcwp) gt 0>
																					<set:results(rcwp) = results(rcwp) - 1>
																					<img src="./templates/generic/styles/images/rcwp.png" alt="Right Color, Wrong Spot">
																				<else>
																					<img src="./templates/generic/styles/images/nb.png" alt="Incorrect">
																				<end:if>
																			<end:if>
																		<next:for>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>