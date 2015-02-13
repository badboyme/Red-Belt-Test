<html>
<head>
	<title>Friends</title>
	<link rel="stylesheet" type="text/css" href="/assets/users/home.css">
</head>
<body>
	<div id="container">
		<header>
			<h1>Hello, <?= $data['friend_data'][0]['user_alias']?>!</h1>
			<a href="/users/validate">Logout</a>
		</header>
		<h3>Here is the list of your friends:</h3>
		<table>
			<tr>
				<th>Alias</th>
				<th>Action</th>
			</tr>

			<?php
			// If user has no friends, model sets "self" field.
			// If that field is not set, display friends
			if (!isset($data['friend_data'][0]['self']))
			{
				foreach ($data['friend_data'] as $key => $value)
				{
					echo "<tr><td><p>".$value['friend_alias']."</p></td>";
					echo "<td><a href='/users/profile/".$value['friend_id']."'>View Profile</a> <a href='/users/delete/".$value['friend_id']."'>Remove as Friend</a>";
					echo "</td></tr>";
				}
			}
			else
			{
				echo "<tr><td>You have no friends!</td></tr>";
			}

			?>
		</table>
		<h3>Other Users not on your friend's list:</h3>
		<table>
			<tr>
				<th>Alias</th>
				<th>Action</th>
			</tr>

			<?php

			// Display all other users' data
			foreach ($data['all_data'] as $key => $value)
			{
				echo "<tr><td><a href='/users/profile/".$value['id']."'>".$value['alias']."</td>";
				echo "<td><button><a href='/users/add/".$value['id']."'>Add as Friend</a></button>";
				echo "</td></tr>";
			}

			?>
		</table>
	</div>
</body>
</html>