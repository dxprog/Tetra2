<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><var:page_title> - Viewing Document "<var:doc(doc_title)>"</title>
		<link rel="stylesheet" type="text/css" href="/templates/<var:user(user_theme)>/styles/<var:user(user_style)>/global.css">
		<link rel="stylesheet" type="text/css" href="/templates/<var:user(user_theme)>/styles/<var:user(user_style)>/header.css">
		<link rel="stylesheet" type="text/css" href="/templates/<var:user(user_theme)>/styles/<var:user(user_style)>/generic.css">
	</head>
	<body>
		<table width="100%" class="Content" cellpadding="0" cellspacing="0">
			<tr>
				<td class="Title" colspan="2">
					<var:doc(doc_title)>
				</td>
			</tr>
			<tr>
				<td class="HLine" colspan="2"></td>
			</tr>
			<tr>
				<td class="Body" align="center" style="width: 122px;">
					<if:thumbnail = 1>
						<img src="/docs/thumb/<var:doc(doc_id)>/120" alt="<var:doc(doc_title)>" style="border: 1px solid #000000;">
						Preview
					<else>
						<img src="/templates/Tetra/styles/<var:user(user_style)>/images/doc_<var:doc_type>.png" alt="<var:doc(doc_title)>" style="border: 1px solid #000000;">
					<end:if>
				</td>
				<td class="Body" valign="top">
					<b><a href="<var:doc(doc_url)>" target="_blank">View/Download Document</a></b><br>
					<b>Poster:</b> <var:doc(user_name)><br>
					<b>Average Rating:</b> <var:doc(doc_rating)><br>
					<b>Posted on:</b> <var:doc(doc_date)><br>
					<b>Document type:</b> <var:doc(type_description)><br>
					<b>File type:</b> <var:doc_type> (<var:ext>)<br>
					<b>Description:</b><br>
					<var:doc(doc_description)>
				</td>
			</tr>
		</table>
		<br>
		<if:doc(user_rated) != 1>
		<form action="/docs/vote/<var:doc(doc_id)>/noheaders" method="post" name="Rate">
			<table width="100%" class="Content" cellpadding="0" cellspacing="0">
				<tr>
					<td class="Title" colspan="10">
						Rate this Document
					</td>
				</tr>
				<tr>
					<td class="HLine" colspan="10"></td>
				</tr>
				<tr>
				<for:i = 1 To 10>
					<td class="Body" align="center">
						<label for="Rating<var:i>"><var:i></label><br>
						<input type="radio" name="Rating" id="Rating<var:i>" value="<var:i>" onClick="document.Rate.submit ();">
					</td>
				<next:for>
				</tr>
			</table>
		</form>
		<end:if>
		<br>
		<center>
			<span style="font-family: Verdana; font-size: 16px; font-weight: bold;">Comments</span>
		</center>
	</body>
</html>