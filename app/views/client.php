<!DOCTYPE html>
<html ng-app="app">
<head>
<meta charset="UTF-8">
<link rel="stylesheet"
	href="bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet"
	href="/css/client.css">

<script src="bower_components/jquery/jquery.min.js"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="bower_components/angular/angular.js"></script>
<script src="bower_components/angular-route/angular-route.js"></script>
<script src="bower_components/storedb/storedb.js"></script>
<script src="js/app.js"></script>
<script src="js/controllers.js"></script>
<title>SepCloud</title>
</head>
<body>
	
	<div class="container-fluid">
		<div ng-view></div>
	</div>
	<input type="hidden" id="client" cmd="start">
</body>
</html>