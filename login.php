<?php
	require('secure/Access.php');

	if (empty($_POST['email']) || empty($_POST['password'])) {
		$return['status'] = '400';
		$return['message'] = 'Missing user information.';

		echo json_encode($return);

		return;
	}

	$email = htmlentities($_POST['email']);
	$password = htmlentities($_POST['password']);

	$access = new Access();

	$access->connect();

	$user = $access->selectUser($email);

	if ($user) {
		$encryptedPassword = $user['password'];
		$salt = $user['salt'];

		if ($encryptedPassword == sha1($password . $salt)) {
			$return['status'] = '200';
			$return['message'] = 'Successfully logged in.';
			$return['email'] = $user['email'];
			$return['firstname'] = $user['firstname'];
			$return['lastname'] = $user['lastname'];
			$return['birthday'] = $user['birthday'];
			$return['gender'] = $user['gender'];
			$return['id'] = $user['id'];
		} else {
			$return['status'] = '401';
			$return['message'] = 'Incorrect password.';
		}
	} else {
		$return['status'] = '401';
		$return['message'] = 'User not found.';
	}

	echo json_encode($return);

	$access->disconnect();
?>
