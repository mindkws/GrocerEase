<?php
	session_start();
	include 'connect.php';
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['Customer'])) {
			header("Location: customermain.php");
			exit();
		}
		
		if (isset($_POST['login'])) {
			$id = $_POST['username'];
			$plain = $_POST['password'];
			$password = hash('sha256', $plain);
			
			$stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if ($result->num_rows === 1) {
				$row = $result->fetch_assoc();
				
				if ($password === $row['emp_password']) {
					$_SESSION['emp_id'] = $row['emp_id'];
					header("Location: employeemain.php");
					exit();
				} else {
					echo "<p>Invalid password for employee.</p>";
				}
			} else {
				$stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = ?");
				$stmt->bind_param("s", $id);
				$stmt->execute();
				$result = $stmt->get_result();
				
				if ($result->num_rows === 1) {
					$row = $result->fetch_assoc();
					
					if ($password === $row['admin_password']) {
						$_SESSION['admin_id'] = $row['admin_id'];
						header("Location: adminmain.php");
						exit();
					} else {
						echo "<p>Invalid password for admin.</p>";
					}
				} else {
					echo "<p>Invalid ID.</p>";
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>GrocerEase Login</title>
    <link rel="stylesheet" href="css/style.css">
	<style>
	img {
  		display: block;
		border-radius: 50%;
  		margin-left: auto;
  		margin-right: auto;
	}
</style>
  </head>
  <body>
    <div class="center">
                <img src="pictures/logowall.png" alt="GrocerEase" style="width: 150px; height: auto;">
      <h1>Login</h1>
      <form method="post" action="login.php">
        <div class="txt_field">
          <input type="text" name="username">
          <span></span>
          <label>ID</label>
        </div>
        <div class="txt_field">
          <input type="password" name="password">
          <span></span>
          <label>Password</label>
        </div>
        <input type="submit" name="login" value="Login">
        <div class="signup_link">
          Are you a customer?
        </div>
        <div class="signup_link">
          <input type="submit" name="Customer" value="I am a Customer">
        </div>
      </form>
    </div>
  </body>
</html>
