						</td>
					</tr>
					<tr>
						<td class="HLine"></td>
					</tr>
					<tr>
						<td class="Head" align="center">
							( <a href="./index.php?main=users&action=layout&layout=create_column">Create Column</a>
							<if:sel_type = 1>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move_col&dir=left&id=<var:sel_id>">Move Column Left</a>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move_col&dir=right&id=<var:sel_id>">Move Column Right</a>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=delete&id=<var:sel_id>">Delete Column</a>
								| <a href="./index.php?main=users&action=layout&layout=add_form&id=<var:sel_id>">Add Content</a>
							<end:if>
							<if:sel_type = 2>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move&dir=left&id=<var:sel_id>">Move Box Left</a>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move&dir=right&id=<var:sel_id>">Move Box Right</a>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move&dir=up&id=<var:sel_id>">Move Box Up</a>
								| <a href="./index.php?preprocess=users&main=users&action=layout&layout=move&dir=down&id=<var:sel_id>">Move Box Down</a>
								| <a href="./index.php?main=users&action=layout&layout=delete&id=<var:sel_id>">Delete Box</a>
							<end:if>
							)
						</td>
					</tr>
				</table>