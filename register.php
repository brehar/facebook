<?php
	require('secure/Access.php');

	$email = $_POST['email'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$password = $_POST['password'];
	$birthday = $_POST['birthday'];
	$gender = $_POST['gender'];

	if (empty($email) || empty($firstname) || empty($lastname) || empty($password) || empty($birthday) || empty($gender)) {
		$return['status'] = '400';
		$return['message'] = 'Missing user information.';

		echo json_encode($return);

		return;
	}

	$email = htmlentities($email);
	$firstname = htmlentities($firstname);
	$lastname = htmlentities($lastname);
	$password = htmlentities($password);
	$birthday = htmlentities($birthday);
	$gender = htmlentities($gender);

	$salt = openssl_random_pseudo_bytes(100);
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
