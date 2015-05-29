<%@ page language="java" contentType="text/html; charset=utf-8"
	pageEncoding="utf-8"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Spreadsheet Viewer</title>
<style type="text/css">
body{
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
	color: #ccc;
}
h1{
color:black;
}
table{
border-spacing: 0.5rem;

 border-collapse: collapse;
 color:black;
 }
td{
	border:1px solid black;	
	text-align: left;
}
</style>
</head>

<body>
	<h1>LibreOffice Spreadsheet Viewer</h1>
	<p>Upload a LibreOffice Spreadsheet and view it in browser!</p>
	<div class="">${requestScope.message}</div>
	<div class="office-table">${requestScope.table}</div>
	<div>
		<form action="Xxe" method="post" enctype="multipart/form-data">
			Select File to Upload:<input type="file" name="file1"> <br>
			<input type="submit" value="Upload">
		</form>
	</div>
</body>

</html>
