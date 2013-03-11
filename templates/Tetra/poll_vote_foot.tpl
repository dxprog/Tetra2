										<tr>
											<td class="Body" align="center">
												<input type="submit" value="Vote" style="width: 100px;">
											</td>
										</tr>
									<if:user(user_rank) = 3>
										<tr>
											<td class="HLine"></td>
										</tr>
										<tr>
											<td class="Body" align="center">
												( <a href="./index.php?main=poll&amp;action=poll_form">Create Poll</a> )
											</td>
										<tr>
											<td class="HLine"></td>
										</tr>
									<end:if>
									</table>
								</form>
								<br>