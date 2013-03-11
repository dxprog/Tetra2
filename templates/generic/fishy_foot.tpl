							<tr>
								<td>
									<table width="100%">
										<tr>
											<td colspan="4" align="center" style="border: 1px dashed #000000; font-family: Verdana; font-size: 12px;">
												Enter Guess
											</td>
										</tr>
										<tr>
											<td align="center">
												<input type="hidden" name="Fish1" value="1">
												<img src="./templates/generic/styles/images/fish1.png" name="FishImg1" onClick="ChangeCurrentFish (1);">
											</td>
											<td align="center">
												<input type="hidden" name="Fish2" value="1">
												<img src="./templates/generic/styles/images/fish1.png" name="FishImg2" onClick="ChangeCurrentFish (2);">
											</td>
											<td align="center">
												<input type="hidden" name="Fish3" value="1">
												<img src="./templates/generic/styles/images/fish1.png" name="FishImg3" onClick="ChangeCurrentFish (3);">
											</td>
											<td align="center">
												<input type="hidden" name="Fish4" value="1">
												<img src="./templates/generic/styles/images/fish1.png" name="FishImg4" onClick="ChangeCurrentFish (4);">
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="width: 640px; height: 202px; background-repeat: no-repeat;" background="./templates/generic/styles/images/sandbar.png" align="center">
									<table width="100%">
										<tr>
											<td>
												<img src="./templates/generic/styles/images/fish1.png" onClick="ChangeFish (1);">
											</td>
											<td>
												<img src="./templates/generic/styles/images/fish2.png" onClick="ChangeFish (2);">
											</td>
											<td>
												<img src="./templates/generic/styles/images/fish3.png" onClick="ChangeFish (3);">
											</td>
											<td>
												<img src="./templates/generic/styles/images/fish4.png" onClick="ChangeFish (4);">
											</td>
											<td>
												<img src="./templates/generic/styles/images/fish5.png" onClick="ChangeFish (5);">
											</td>
											<td>
												<img src="./templates/generic/styles/images/fish6.png" onClick="ChangeFish (6);">
											</td>
										</tr>
									</table>
									<img src="./templates/generic/styles/images/submit.png" alt="Submit Guess" onClick="document.forms['fishy'].submit();">
								</td>
							</tr>
						</table>
					</form>