							<if:no_users = 1>
								Nobody online
							<else>
								<a href="./index.php?main=users&amp;action=profile&amp;id=<var:users(user_id)>"><var:users(user_name)></a>
							<end:if>