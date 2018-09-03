<?php
	require('secure/Access.php');

	if (empty($_POST['email']) || empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['password']) || empty($_POST['birthday']) || empty($_POST['gender'])) {
		$return['status'] = '400';
		$return['message'] = 'Missing user information.';

		echo json_encode($return);

		return;
	}

	$email = htmlentities($_POST['email']);
	$firstname = htmlentities($_POST['firstname']);
	$lastname = htmlentities($_POST['lastname']);
	$password = htmlentities($_POST['password']);
	$birthday = htmlentities($_POST['birthday']);
	$gender = htmlentities($_POST['gender']);

	$salt = openssl_random_pseudo_bytes(20);
	$encryptedPassword = sha1($password . $salt);

	$access = new Access();

	$access->connect();

	$user = $access->selectUser($email);

	if (!empty($user)) {
		$return['status'] = '400';
		$return['message'] = 'That email address is already in use.';

		echo json_encode($return);

		$access->disconnect();

		return;
	}

	$result = $access->insertUser($email, $firstname, $lastname, $encryptedPassword, $salt, $birthday, $gender);

	if ($result) {
		$user = $access->selectUser($email);

		$return['status'] = '200';
		$return['message'] = 'You have successfully been registered.';
		$return['email'] = $email;
		$return['firstname'] = $firstname;
		$return['lastname'] = $lastname;
		$return['birthday'] = $birthday;
		$return['gender'] = $gender;
		$return['id'] = $user['id'];
	} else {
		$return['status'] = '400';
		$return['message'] = 'There was a problem completing your registration at this time.';
	}

	echo json_encode($return);

	$access->disconnect();
?>
