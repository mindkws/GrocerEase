<?php
    session_start(); 
    include 'connect.php';
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
$content = isset($_GET['content']) ? $_GET['content'] : 'product'; 

if (isset($_GET['more_prodinfo'])) {
    $_SESSION['more_prodinfo'] = $_GET['more_prodinfo']; 
    echo "Session ID set: " . $_SESSION['more_prodinfo'];
    header("Location: customermain.php?content=more"); 
    exit();
}

if ($content == 'product') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
} 
elseif ($content == 'promotion') {
    $sql = "SELECT * FROM promotion";
    $result = $conn->query($sql);
} 
elseif ($content == 'more') {
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
        }
    }
}

else {
    echo "Invalid content!";
    exit();
}
$activeProfile = ($content == 'product') ? 'active' : '';
$activeEmployee = ($content == 'promotion') ? 'active' : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrocerEase</title>
    <link rel="stylesheet" href="css/customer.css">
</head>
<body>
    <div class="menu-bar">
        <a href="customermain.php?content=product" class="menu-item active" data-section="product">Product</a>
        <a href="customermain.php?content=promotion" class="menu-item" data-section="promotion">Promotion</a>
    </div>
    <?php if ($content == 'product') : ?>
        
        <div class="product-list">
            <h2>Product List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="product-item" >
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['prod_pic']); ?>" alt="Product Picture" class="product-pic" />
                        <span class="product-name"><strong>Name :</strong> <?php echo htmlspecialchars($row['prod_name']); ?></span>
                        <span class="product-price"><strong>Price :</strong> <?php echo htmlspecialchars($row['prod_price']); ?></span>
                        <span class="product-quantity"><strong>Location :</strong> <?php echo htmlspecialchars($row['prod_loca']); ?></span>
                        <span class="product-actions">
                        <a href="customermain.php?content=more&more_prodinfo=<?php echo $row['prod_id']; ?>">More</a>
                        </span>
                    </div>
                <?php endwhile; ?>
        </div>
        
    <?php elseif ($content == 'promotion') : ?>
        
        <div class="product-list">
            <h2>Promotion List</h2>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="product-item" >
                        <span class="product-name"><strong>Promotion :</strong> <?php echo htmlspecialchars($row['promo_name']); ?></span>
                        <span class="product-price"><strong>Start :</strong> <?php echo htmlspecialchars($row['promo_start']); ?></span>
                        <span class="product-quantity"><strong>End :</strong> <?php echo htmlspecialchars($row['promo_end']); ?></span>
                        <span class="product-quantity"><strong>Product ID:</strong> <?php echo htmlspecialchars($row['prod_id']); ?></span>
                        <span class="product-quantity"><strong>Discount :</strong> <?php echo htmlspecialchars($row['promo_discount']); ?>%</span>
                    </div>
                <?php endwhile; ?>
        </div>
    
    <?php elseif ($content == 'more') : ?>
        <div class="product-info">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($prod_pic); ?>" alt="Product Picture" class="moreprod-img" />
                        <p><strong>ID :</strong> <?php echo htmlspecialchars($prod_id); ?></p>
                        <p><strong>Name :</strong> <?php echo htmlspecialchars($prod_name); ?></p>
                        <p><strong>Price :</strong> <?php echo htmlspecialchars($prod_price); ?></p>
                        <p><strong>Quantity :</strong> <?php echo htmlspecialchars($prod_quantity); ?></p>
                        <p><strong>Product Info :</strong> <?php echo htmlspecialchars($prod_info); ?></p>

    <?php endif; ?>

    <script src="script.js"></script>
</body>
</html>