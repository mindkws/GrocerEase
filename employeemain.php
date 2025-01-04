<?php 
    session_start(); 
    include 'connect.php';
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (!isset($_SESSION['emp_id'])) {
        header("Location: login.php");
        exit();
    }
    
$content = isset($_GET['content']) ? $_GET['content'] : 'profile'; 

if (isset($_GET['more_prodinfo'])) {
    $_SESSION['more_prodinfo'] = $_GET['more_prodinfo']; 
    echo "Session ID set: " . $_SESSION['more_prodinfo'];
    header("Location: employeemain.php?content=morebtn");
    exit();
}

if ($content == 'profile') {
    $sql = "SELECT * FROM employee WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['emp_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $emp_fname = $row['emp_fname'];
    $emp_lname = $row['emp_lname'];
    $emp_gender = $row['emp_gender'];
    $emp_dob = $row['emp_dob'];
    $emp_pic = $row['emp_pic'];
} 

elseif ($content == 'product') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
elseif ($content == 'assignment') {
    $sql = "CALL GetLocationsWithEmployees()"; // Call the stored procedure
    $result = $conn->query($sql);
}
elseif ($content == 'changepass') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
elseif ($content == 'morebtn') {
    if (isset($_SESSION['more_prodinfo'])) {
        $prod_id = $_SESSION['more_prodinfo'];

        $sql = "SELECT * FROM product WHERE prod_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $prod_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
            $prod_name = $row['prod_name'];
            $prod_price = $row['prod_price'];
            $prod_info= $row['prod_info'];
            $prod_quantity = $row['prod_quantity'];
            $prod_pic = $row['prod_pic'];
            $prod_loca = $row['prod_loca'];
    }
}


}

$activeProfile = ($content == 'profile') ? 'active' : '';
$activeProduct = ($content == 'product') ? 'active' : '';
$activeChangepass = ($content == 'changepass') ? 'active' : '';
$activeMornbtn = ($content == 'morebtn') ? 'active' : '';
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/emp.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <header class="sidebar-header">
                <a href="login.php" class="header-logo">
                    <img src="pictures/logowall.png" alt="GrocerEase" style="width: 45px; height: auto;">
                </a>
                <a class="titlee">GrocerEase</a>
            </header>
            <nav class="sidebar-nav">
                <!-- Top sidemenu-->
                <ul class="nav-list primary-nav">
                    <li class="nav-item">
                        <a href="employeemain.php?content=profile" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">person</span>
                            <span class="nav-label">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="employeemain.php?content=product" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">shopping_cart</span>
                            <span class="nav-label">Product</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="employeemain.php?content=assignment" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">assignment</span>
                            <span class="nav-label">Assignment</span>
                        </a>
                    </li>
                </ul>
                <!-- bottom, logout button -->
                <ul class="nav-list secondary-nav">
                    <li class="nav-item">
                        <a href="employeemain.php?content=changepass" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">password</span>
                            <span class="nav-label">Change Password</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">logout</span>
                            <span class="nav-label">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="profile">
            <!-- Profile Content -->
            <?php if ($content == 'profile') : ?>
                <div class="profile-container">
                    <div class="profile-picture">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($emp_pic); ?>" alt="Employee Picture" class="profile-img" />
                    </div>
                    <div class="profile-info">
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($emp_fname); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($emp_lname); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($emp_gender); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($emp_dob); ?></p>
                    </div>
                </div>

            <!-- Product List Content -->
            <?php elseif ($content == 'product') : ?>
                <div class="product-list">
                <h2>Product List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="product-item" >
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['prod_pic']); ?>" alt="Product Picture" class="product-pic" />
                        <span class="product-id"><strong>ID :</strong> <?php echo htmlspecialchars($row['prod_id']); ?></span>
                        <span class="product-name"><strong>Name :</strong> <?php echo htmlspecialchars($row['prod_name']); ?></span>
                        <span class="product-price"><strong>Price :</strong> <?php echo htmlspecialchars($row['prod_price']); ?></span>
                        <span class="product-quantity"><strong>Quantity :</strong> <?php echo htmlspecialchars($row['prod_quantity']); ?></span>
                        <span class="product-quantity"><strong>Location: Zone</strong> <?php echo htmlspecialchars($row['prod_loca']); ?></span>
                        <span class="product-actions">
                        <a href="employeemain.php?content=morebtn&more_prodinfo=<?php echo $row['prod_id']; ?>">More</a>
                        </span>
                    </div>
                <?php endwhile; ?>
                </div>

            <!-- Assignment Content -->
            <?php elseif ($content == 'assignment') : ?>
                <div class="product-list">
                <h2>Assigned Location List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                        <div class="product-item">
                            <span class="product-id"><strong>Location:</strong> <?php echo htmlspecialchars($row['loca_id']); ?></span>
                            <span class="product-id"><strong>Employee ID:</strong> <?php echo htmlspecialchars($row['LocationEmpID']); ?></span>
                            <span class="product-id"><strong>Name:</strong> <?php echo htmlspecialchars($row['emp_fname'] . ' ' . $row['emp_lname']); ?></span>
                            <span class="product-id"><strong>Birth Date:</strong> <?php echo htmlspecialchars($row['emp_dob']); ?></span>
                            <span class="employee-pic">
                                <?php if (!empty($row['emp_pic'])) : ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['emp_pic']); ?>" alt="Employee Picture" style="width: 100px; height: 100px;">
                                <?php else : ?>
                                    <p>No Picture Available</p>
                                <?php endif; ?>
                            
                                </span>
                        </div>
                    <?php endwhile; ?>
                </div>


            <!-- More btn Content -->
            <?php elseif ($content == 'morebtn') : ?>
                    <div class="product-info">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($prod_pic); ?>" alt="Product Picture" class="moreprod-img" />
                        <p><strong>ID :</strong> <?php echo htmlspecialchars($prod_id); ?></p>
                        <p><strong>Name :</strong> <?php echo htmlspecialchars($prod_name); ?></p>
                        <p><strong>Price :</strong> <?php echo htmlspecialchars($prod_price); ?></p>
                        <p><strong>Quantity :</strong> <?php echo htmlspecialchars($prod_quantity); ?></p>
                        <p><strong>Product Info :</strong> <?php echo htmlspecialchars($prod_info); ?></p>
                        <p><strong>Product Location :</strong> <?php echo htmlspecialchars($prod_loca); ?></p>

                    <div class="quantity-action">
                        <!-- Edit quantity form -->
                        <form action="update_employee.php" method="post">
                        <input type="hidden" id="id_prod" name ="id_prod" value=<?php echo htmlspecialchars($_SESSION['more_prodinfo']); ?>>
                        <label for="prod_quantity"> Edit Quantity : </label>
                        <input type="number" id="prod_quantity" name="prod_quantity" min="0" required>
                        <button type="submit" name="update_type" value="edit_quantity">Confirm</button>
                    </form>
                    </div>

            <?php elseif ($content == 'changepass') : ?>
                <div class="form-container">
                    <form method="POST" action="update_employee.php">
                        <div class="form_row">
                            <h2>Change Password</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="xxxx">New Password</label>
                                <input type="password" id="xxxx" name="xxxx" required>
                            </div>
                            <div class="form-group">
                                <label for="xxxxx">Confirm Password</label>
                                <input type="password" id="xxxxx" name="xxxxx" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <button type="submit" name="update_type" value="emp_password_edit">Change Password</button>
                        </div>
                    </form>
                </div>



            <?php endif; ?>
        
        </main>
    </div>
</body>
</html>
