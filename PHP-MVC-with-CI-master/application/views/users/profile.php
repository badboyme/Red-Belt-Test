<html>
<head>
	<title><?= $data['friend_data'][0]['user_alias'] ?></title>
	<link rel="stylesheet" type="text/css" href="/assets/users/profile.css">
</head>
<body>
	<header>
		<a href="/users/home">Home</a>
		<a href="/users/validate">Logout</a>
	</header>
	<h1><?= $data['friend_data'][0]['user_alias'] ?>'s Profile</h1>
	<h3>Name: <?= $data['friend_data'][0]['user_name'] ?></h3>
	<h3>Email Address: <?= $data['friend_data'][0]['user_email'] ?></h3>
</body>
</html>