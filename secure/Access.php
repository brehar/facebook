<?php
	class Access {
		var $host = null;
		var $user = null;
		var $pass = null;
		var $name = null;
		var $conn = null;

		function __construct() {
			$this->host = ini_get('mysqli.default_host');
			$this->user = ini_get('mysqli.default_user');
			$this->pass = ini_get('mysqli.default_pw');
			$this->name = 'facebook';
		}

		public function connect() {
			$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);

			if (mysqli_connect_errno()) {
				echo 'Could not connect to the database.';

				return;
			}

			$this->conn->set_charset('utf8');
		}

		public function disconnect() {
			if ($this->conn) {
				$this->conn->close();
			}
		}

		public function selectUser($email) {
			$returnArray = array();
			$sql = "SELECT * FROM users WHERE email='$email'";
			$result = $this->conn->query($sql);

			if ($result && (mysqli_num_rows($result) >= 1)) {
				$row = $result->fetch_array(MYSQLI_ASSOC);

				if (!empty($row)) {
					$returnArray = $row;
				}
			}

			return $returnArray;
		}

		public function insertUser($email, $firstname, $lastname, $encryptedPassword, $salt, $birthday, $gender) {
			$sql = "INSERT INTO users SET email=?, firstname=?, lastname=?, password=?, salt=?, birthday=?, gender=?";
			$statement = $this->conn->prepare($sql);

			if (!$statement) {
				throw new Exception($statement->error);
			}

			$statement->bind_param('sssssss', $email, $firstname, $lastname, $encryptedPassword, $salt, $birthday, $gender);

			$result = $statement->execute();

			return $result;
		}
	}
?>
