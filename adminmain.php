<?php 
    session_start(); 
    include 'connect.php';
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }
    
$content = isset($_GET['content']) ? $_GET['content'] : 'profile'; 

if (isset($_GET['edit_emp_id'])) {
    $_SESSION['edit_emp_id'] = $_GET['edit_emp_id'];
    header("Location: adminmain.php?content=editemp");
    exit();
}
if (isset($_GET['edit_prod_id'])) {
    $_SESSION['edit_prod_id'] = $_GET['edit_prod_id']; 
    header("Location: adminmain.php?content=editprod");
    exit();
}
if (isset($_GET['edit_loca_id'])) {
    $_SESSION['edit_loca_id'] = $_GET['edit_loca_id']; 
    header("Location: adminmain.php?content=editloca"); 
    exit();
}
if (isset($_GET['edit_promo_id'])) {
    $_SESSION['edit_promo_id'] = $_GET['edit_promo_id']; 
    header("Location: adminmain.php?content=editpromo"); 
    exit();
}
if (isset($_GET['edit_admin_id'])) {
    $_SESSION['edit_admin_id'] = $_GET['edit_admin_id']; 
    header("Location: adminmain.php?content=editadmin");
    exit();
}
    
if ($content == 'profile') {
    $sql = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $admin_fname = $row['admin_fname'];
    $admin_lname = $row['admin_lname'];
    $admin_gender = $row['admin_gender'];
    $admin_dob = $row['admin_dob'];
    $admin_pic = $row['admin_pic'];
} 
elseif ($content == 'employee') {
    $sql = "SELECT * FROM employee";
    $result = $conn->query($sql);
    $sqladm = "SELECT * FROM admin";
    $resultadm = $conn->query($sqladm);
} 
elseif ($content == 'product') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
elseif ($content == 'assignment') {
    $sql = "CALL GetLocationsWithEmployees()"; 
    $result = $conn->query($sql);
}
elseif ($content == 'promotion') {
    $sql = "CALL GetPromotionProductInfo()";
    $result = $conn->query($sql);
}
elseif ($content == 'changepass') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
elseif ($content == 'editemp') {
    $sql = "SELECT * FROM employee";
    $result = $conn->query($sql);
}
elseif ($content == 'editprod') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
elseif ($content == 'editpromo') {
    $sql = "SELECT * FROM promotion";
    $result = $conn->query($sql);
}
elseif ($content == 'editadmin') {
    $sql = "SELECT * FROM admin";
    $result = $conn->query($sql);
}
else {
    echo "Invalid content!";
    exit();
}

$activeProfile = ($content == 'profile') ? 'active' : '';
$activeEmployee = ($content == 'employee') ? 'active' : '';
$activeProduct = ($content == 'product') ? 'active' : '';
$activePromotion = ($content == 'promotion') ? 'active' : '';
$activeChangepass = ($content == 'changepass') ? 'active' : '';
?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/admin.css">
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
                <!-- Top, Primary -->
                <ul class="nav-list primary-nav">
                    <li class="nav-item">
                        <a href="adminmain.php?content=profile" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">person</span>
                            <span class="nav-label">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminmain.php?content=employee" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">badge</span>
                            <span class="nav-label">Account</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminmain.php?content=product" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">shopping_cart</span>
                            <span class="nav-label">Product</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminmain.php?content=assignment" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">assignment</span>
                            <span class="nav-label">Assignment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminmain.php?content=promotion" class="nav-link">
                            <span class="nav-icon material-symbols-outlined">percent</span>
                            <span class="nav-label">Promotion</span>
                        </a>
                    </li>
                </ul>
                <!-- Bottom, Secondary -->
                <ul class="nav-list secondary-nav">
                <li class="nav-item">
                        <a href="adminmain.php?content=changepass" class="nav-link">
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
            <!-- Display content based on the selected menu item -->
            <?php if ($content == 'profile') : ?>
                <div class="profile-container">
                    <div class="profile-picture">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($admin_pic); ?>" alt="Admin Picture" class="profile-img" />
                    </div>
                    <div class="profile-info">
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($admin_fname); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($admin_lname); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($admin_gender); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($admin_dob); ?></p>
                    </div>
                </div>


            <?php elseif ($content == 'employee') : ?>
                <!-- Display employee data here -->
                <div class="employee-list">
                <div class="form-container">
                <form action="update_employee.php" method="POST" enctype="multipart/form-data">
                        <div class="form_row">
                                <h2>Add New Account</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="fname" name="fname" required>
                            </div>
                            <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="lname" name="lname" required>
                            </div>
                            <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                                <option value="O">Other</option>
                            </select>
                            </div>
                        </div>

                        <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Birth date</label>
                            <input type="date" id="dob" name="dob" required>
                            </div>
                            <div class="form-group">
                            <label for="position">Picture</label>
                            <input type="file" id="pic" name="pic" required>
                            </div>
                        </div>

                        <div class="form-row">
                        <button type="submit" name="update_type" value="employee_add">Add Employee</button>
                        <button type="submit" name="update_type" value="admin_add">Add Admin</button>
                        </div>
                </form>
            </div>
                <h2>Account List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="employee-item">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['emp_pic']); ?>" alt="Employee Picture" class="emp_list_pic" />
                        <span class="employee-name"><strong>Name:</strong> <?php echo htmlspecialchars($row['emp_fname'] . ' ' . $row['emp_lname']); ?></span>
                        <span class="employee-id"><strong>ID:</strong> <?php echo htmlspecialchars($row['emp_id']); ?></span>
                        <span class="employee-id"><strong>Role:</strong> Employee</span>
                        <span class="employee-gender"><strong>Gender:</strong> <?php echo htmlspecialchars($row['emp_gender']); ?></span>
                        <span class="employee-dob"><strong>Date of Birth:</strong> <?php echo htmlspecialchars(date("d-m-Y", strtotime($row['emp_dob']))); ?></span>
                        <span class="employee-actions">
                        <a href="adminmain.php?content=editemp&edit_emp_id=<?php echo $row['emp_id']; ?>">Edit</a>
                        </span>
                    </div>
                <?php endwhile; ?>
                <?php while ($row = $resultadm->fetch_assoc()) : ?>
                    <div class="employee-item">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['admin_pic']); ?>" alt="Employee Picture" class="emp_list_pic" />
                        <span class="employee-name"><strong>Name:</strong> <?php echo htmlspecialchars($row['admin_fname'] . ' ' . $row['admin_lname']); ?></span>
                        <span class="employee-id"><strong>ID:</strong> <?php echo htmlspecialchars($row['admin_id']); ?></span>
                        <span class="employee-id"><strong>Role:</strong> Admin</span>
                        <span class="employee-gender"><strong>Gender:</strong> <?php echo htmlspecialchars($row['admin_gender']); ?></span>
                        <span class="employee-dob"><strong>Date of Birth:</strong> <?php echo htmlspecialchars(date("d-m-Y", strtotime($row['admin_dob']))); ?></span>
                        <span class="employee-actions">
                        <a href="adminmain.php?content=editadmin&edit_admin_id=<?php echo $row['admin_id']; ?>">Edit</a>
                        </span>
                    </div>
                <?php endwhile; ?>
                </div>

            <?php elseif ($content == 'product') : ?>
                <div class="employee-list">
                <div class="form-container">
                <form action="update_employee.php" method="POST" enctype="multipart/form-data">
                        <div class="form_row">
                                <h2>Add New Product</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                            <label for="pname">Product Name</label>
                            <input type="text" id="pname" name="pname" required>
                            </div>
                            <div class="form-group">
                            <label for="pprice">Price</label>
                            <input type="number" id="pprice" name="pprice" min = "-1" step="0.01"required>
                            </div>
                            <div class="form-group">
                            <label for="pquantity">Quantity</label>
                            <input type="number" id="pquantity" name="pquantity" min = "0" step="1"required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <label for="pinformation">Information</label>
                            <input type="text" id="pinformation" name="pinformation" required>
                            </div>
                            <div class="form-group">
                            <label for="plocation">Location</label>
                            <input type="text" id="plocation" name="plocation" required>
                            </div>
                            <div class="form-group">
                            <label for="ppic">Picture</label>
                            <input type="file" id="ppic" name="ppic" required>
                            </div>
                        </div>

                        <div class="form-row">
                        <button type="submit" name="update_type" value="product_add">Add New Product</button>
                        </div>
                </form>
            </div>
                <h2>Product List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="employee-item">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['prod_pic']); ?>" class="emp_list_pic" />
                        <span class="employee-gender"><strong>Name:</strong> <?php echo htmlspecialchars($row['prod_name']); ?></span>
                        <span class="employee-id"><strong>ID:</strong> <?php echo htmlspecialchars($row['prod_id']); ?></span>
                        <span class="employee-gender"><strong>Price:</strong> <?php echo htmlspecialchars($row['prod_price']); ?></span>
                        <span class="employee-gender"><strong>Quantity:</strong> <?php echo htmlspecialchars($row['prod_quantity']); ?></span>
                        <span class="employee-gender"><strong>Location: Zone </strong> <?php echo htmlspecialchars($row['prod_loca']); ?></span>
                        <span class="employee-actions">
                        <a href="adminmain.php?content=editprod&edit_prod_id=<?php echo $row['prod_id']; ?>">Edit</a>
                        </span>
                    </div>
                <?php endwhile; ?>
                </div>
            <?php elseif ($content == 'editprod') : ?>
                <form action="update_employee.php" method="post" enctype="multipart/form-data">
                    <?php if (isset($_SESSION['edit_prod_id'])): ?>
                        <h2>Edit Product Information</h2>
                        <input type="hidden" name="edit_prod_id" value="<?php echo htmlspecialchars($_SESSION['edit_prod_id']); ?>">
                        <p>Product ID: <?php echo htmlspecialchars($_SESSION['edit_prod_id']); ?></p>
                        <?php
                            $edit_prod_id = $_SESSION['edit_prod_id'];
                            $query = "SELECT * FROM product WHERE prod_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $edit_prod_id);
                            $stmt->execute();
                            $result = $stmt->get_result()->fetch_assoc(); // Fetch the product details as an associative array
                            ?>
                    <?php else: ?>
                        <p>Product ID not found in session!</p>
                    <?php endif; ?>

                    <div class="form-group">
                    <label for="pname">Product Name:</label>
                    <input type="text" id="pname" name="pname" value="<?php echo isset($result['prod_name']) ? htmlspecialchars($result['prod_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                    <label for="pprice">Product Price:</label>
                    <input type="number" id="pprice" name="pprice" step="0.01" min = "-1"value="<?php echo isset($result['prod_price']) ? htmlspecialchars($result['prod_price']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                    <label for="pquantity">Product Quantity:</label>
                    <input type="number" id="pquantity" name="pquantity" step="1" min = "0" value="<?php echo isset($result['prod_quantity']) ? htmlspecialchars($result['prod_quantity']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                    <label for="pinformation">Product Information:</label>
                    <input type="text" id="pinformation" name="pinformation" value="<?php echo isset($result['prod_info']) ? htmlspecialchars($result['prod_info']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                    <label for="plocation">Product Location ID:</label>
                    <input type="text" id="plocation" name="plocation" value="<?php echo isset($result['prod_loca']) ? htmlspecialchars($result['prod_loca']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                    <label for="ppic">Product Picture:</label>
                    <input type="file" id="ppic" name="ppic" value="" >
                    </div>
                    
                    <div class="form-action">
                    <button type="submit" name="update_type" value="product_edit">Update</button>
                    <button type="submit" name="update_type" value="product_delete">DELETE THIS PRODUCT FROM THE LIST</button>
                    </div>
                </form>

            <?php elseif ($content == 'assignment') : ?>
                <div class="employee-list">
                    <div class="form-container">
                        <form action="update_employee.php" method="POST" enctype="multipart/form-data">
                                <div class="form_row">
                                        <h2>Assign Employee</h2>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                    <label for="loca_id">Location ID (Zone)</label>
                                    <input type="text" id="loca_id" name="loca_id" required>
                                    </div>
                                    <div class="form-group">
                                    <label for="emp_id">Employee ID</label>
                                    <input type="text" id="emp_id" name="emp_id" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                <button type="submit" name="update_type" value="loca_assign">Assign</button>
                                </div>
                        </form>
                    </div>
                    <h2>Location List</h2>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <div class="employee-item">
                            <span class="employee-gender"><strong>Location:</strong> <?php echo htmlspecialchars($row['loca_id']); ?></span>
                            <span class="employee-id"><strong>Employee ID:</strong> <?php echo htmlspecialchars($row['LocationEmpID']); ?></span>
                            <span class="employee-gender"><strong>Name:</strong> <?php echo htmlspecialchars($row['emp_fname'] . ' ' . $row['emp_lname']); ?></span>
                            <span class="employee-gender"><strong>Birth Date:</strong> <?php echo htmlspecialchars($row['emp_dob']); ?></span>
                            <span class="employee-pic">
                                <?php if (!empty($row['emp_pic'])) : ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['emp_pic']); ?>" alt="Employee Picture" class="emp_list_pic">
                                <?php else : ?>
                                    <p>No Picture Available</p>
                                <?php endif; ?>
                            
                                </span>
                        </div>
                    <?php endwhile; ?>
                </div>



            <?php elseif ($content == 'promotion') : ?>
                <div class="employee-list">
                <div class="form-container">
                <form action="update_employee.php" method="POST">
                        <div class="form_row">
                                <h2>Add New Promotion</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                            <label for="promo_name">Promotion Name</label>
                            <input type="text" id="promo_name" name="promo_name" required>
                            </div>
                            <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required>
                            </div>
                            <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                            <label for="discount">Discount %</label>
                            <input type="number" id="discount" name="discount" min = "0" step = "1" required>
                            </div>
                            <div class="form-group">
                            <label for="prod_id">Product ID</label>
                            <input type="number" id="prod_id" name="prod_id" min = "0" step = "1" required>
                            </div>

                        </div>

                        <div class="form-row">
                        <button type="submit" name="update_type" value="promotion_add">Add Promotion</button>
                        </div>
                </form>
            </div>
                <h2>Promotion List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="employee-item">
                        <span class="employee-gender"><strong><?php echo htmlspecialchars($row['promo_name']); ?></strong> </span>
                        <span class="employee-gender"><strong>Start:</strong> <?php echo htmlspecialchars($row['promo_start']); ?></span>
                        <span class="employee-gender"><strong>End:</strong> <?php echo htmlspecialchars($row['promo_end']); ?></span>
                        <span class="employee-gender"><strong>Discount:</strong> <?php echo htmlspecialchars($row['promo_discount']); ?>%</span>
                        <span class="employee-gender"><?php echo htmlspecialchars($row['prod_name']); ?></span>
                        <span class="employee-pic">
                                <?php if (!empty($row['prod_pic'])) : ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['prod_pic']); ?>" alt="Employee Picture" class="emp_list_pic">
                                <?php else : ?>
                                    <p>No Picture Available</p>
                                <?php endif; ?>
                            
                                </span>
                        <span class="employee-actions">
                        <a href="adminmain.php?content=editpromo&edit_promo_id=<?php echo $row['promo_id']; ?>">Edit</a>
                        </span>
                    </div>
                <?php endwhile; ?>
                </div>
                
            <?php elseif ($content == 'changepass') : ?>
                <div class="form-container">
                    <form id="changePasswordForm" method="POST" action="update_employee.php">
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
                            <button type="submit" name="update_type" value="password_edit">Change Password</button>
                        </div>
                    </form>
                </div>

            <?php elseif ($content == 'editemp') : ?>
                <form action="update_employee.php" method="post" enctype="multipart/form-data">
                    <?php if (isset($_SESSION['edit_emp_id'])): ?>
                        <!-- You can pass the emp_id in a hidden input -->
                        <h2>Edit Employee</h2>
                        <input type="hidden" name="edit_emp_id" value="<?php echo htmlspecialchars($_SESSION['edit_emp_id']); ?>">
                        <p>Employee ID: <?php echo htmlspecialchars($_SESSION['edit_emp_id']); ?></p>
                    <?php else: ?>
                        <p>Employee ID not found in session!</p>
                    <?php endif; ?>

                    <div class="form-group">
                    <label for="emp_fname">First Name:</label>
                    <input type="text" id="emp_fname" name="emp_fname" value="" required>
                    </div>

                    <div class="form-group">
                    <label for="emp_lname">Last Name:</label>
                    <input type="text" id="emp_lname" name="emp_lname" value="" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="emp_gender">Gender:</label>
                        <select id="emp_gender" name="emp_gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="O">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                    <label for="emp_dob">Date of Birth:</label>
                        <input type="date" id="emp_dob" name="emp_dob" value="" required>
                        <div class="form-group">
                            <label>Picture</label>
                            <input type="file" id="epic" name="epic">
                            </div>
                    </div>

                    <div class="form-action">
                    <button type="submit" name="update_type" value="employee_edit">Update</button>
                    <button type="submit" name="update_type" value="employee_delete">DELETE THIS EMPLOYEE FROM DATABASE</button>
                    </div>
                </form>

            <?php elseif ($content == 'editadmin') : ?>
                <form action="update_employee.php" method="post" enctype="multipart/form-data">
                    <?php if (isset($_SESSION['edit_admin_id'])): ?>

                        <h2>Edit Admin</h2>
                        <input type="hidden" name="edit_emp_id" value="<?php echo htmlspecialchars($_SESSION['edit_admin_id']); ?>">
                        <p>Employee ID: <?php echo htmlspecialchars($_SESSION['edit_admin_id']); ?></p>
                    <?php else: ?>
                        <p>Employee ID not found in session!</p>
                    <?php endif; ?>

                    <div class="form-group">
                    <label for="emp_fname">First Name:</label>
                    <input type="text" id="emp_fname" name="emp_fname" value="" required>
                    </div>

                    <div class="form-group">
                    <label for="emp_lname">Last Name:</label>
                    <input type="text" id="emp_lname" name="emp_lname" value="" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="emp_gender">Gender:</label>
                        <select id="emp_gender" name="emp_gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="O">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                    <label for="emp_dob">Date of Birth:</label>
                        <input type="date" id="emp_dob" name="emp_dob" value="" required>
                        <div class="form-group">
                            <label>Picture</label>
                            <input type="file" id="epic" name="epic">
                            </div>
                    </div>

                    <div class="form-action">
                    <button type="submit" name="update_type" value="admin_edit">Update</button>
                    <button type="submit" name="update_type" value="admin_delete">DELETE THIS ADMIN FROM DATABASE</button>
                    </div>
                </form>
                
            <?php elseif ($content == 'editpromo') : ?>
                <form action="update_employee.php" method="post">
                    <?php if (isset($_SESSION['edit_promo_id'])): ?>
                       
                        <h2>Edit Promotion</h2>
                        <input type="hidden" name="edit_promo_id" value="<?php echo htmlspecialchars($_SESSION['edit_promo_id']); ?>">
                        <p>Promotion ID: <?php echo htmlspecialchars($_SESSION['edit_promo_id']); ?></p>
                    <?php else: ?>
                        <p>Promotion ID not found in session!</p>
                    <?php endif; ?>

                    <div class="form-group">
                    <label for="pname">Promotion Name:</label>
                    <input type="text" id="pname" name="pname" value="" required>
                    </div>

                    <div class="form-group">
                    <label for="starter">Start Date:</label>
                    <input type="date" id="starter" name="starter" value="" required> 
                    </div>
                    <div class="form-group">
                    <label for="ender">End Date:</label>
                    <input type="date" id="ender" name="ender" value="" required>
                    </div>
                    
                    <div class="form-group">
                    <label for="discounter">Discount Percentage:</label>
                        <input type="number" id="discounter" name="discounter" value="" min = "0" required>
                    </div>

                    <div class="form-group">
                    <label for="prod_ids">Product ID:</label>
                        <input type="number" id="prod_ids" name="prod_ids" value="" min = "0" required>
                    </div>
        
                    <div class="form-action">
                    <button type="submit" name="update_type" value="promotion_edit">Update</button>
                    <button type="submit" name="update_type" value="promotion_delete">DELETE THIS PROMOTION</button>
                    </div>
                </form>


            <?php endif; ?>
        </main>
    </div>
</body>
</html>
