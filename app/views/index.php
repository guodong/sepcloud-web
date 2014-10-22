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
<script src="js/app.js"></script>
<script src="js/controllers.js"></script>
<title>SepCloud</title>
</head>
<body>
	<nav class="navbar navbar-default navbar-inverse navbar-static-top"
		role="navigation">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">SepCloud Center</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="#/instance"><span
							class="glyphicon glyphicon-cloud"></span> Instance</a></li>
					<li><a href="#/user"><span
							class="glyphicon glyphicon-user"></span> User</a></li>
					<li><a href="/system"><span class="glyphicon glyphicon-cog"></span>
							System</a></li>
				</ul>
        
				<ul class="nav navbar-nav navbar-right" ng-show="admin">
					<li class="dropdown"><a href="javascript:;" class="dropdown-toggle"
						data-toggle="dropdown">{{admin.username}} <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Account</a></li>
							<li class="divider"></li>
							<li><a href="#">Logout</a></li>
						</ul></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	<div class="container-fluid">
	
		<div ng-view></div>
	
	</div>

</body>
</html>