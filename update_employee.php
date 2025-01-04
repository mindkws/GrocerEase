<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateType = isset($_POST['update_type']) ? $_POST['update_type'] : '';

    switch ($updateType) {
        case 'employee_edit':
            if (!isset($_SESSION['edit_emp_id'])) {
                echo "<script>alert('No employee selected for editing!');</script>";
                header("Location: adminmain.php?content=employee");
                exit();
            }
            $emp_id = $_SESSION['edit_emp_id'];
                if (empty($emp_id)) {
                    echo "<script>alert('Invalid employee ID!');</script>";
                    header("Location: adminmain.php?content=employee");
                    exit();
                }
                if (isset($_FILES['epic']['tmp_name']) && $_FILES['epic']['error'] == 0) {
                    $pic = file_get_contents($_FILES['epic']['tmp_name']);
                } else {
                    $pic = null; 
                }
                $emp_id = $_POST['edit_emp_id'];
                $emp_fname = $_POST['emp_fname'];
                $emp_lname = $_POST['emp_lname'];
                $emp_gender = $_POST['emp_gender'];
                $emp_dob = $_POST['emp_dob'];

            $sql = "UPDATE employee SET emp_fname = ?, emp_lname = ?, emp_gender = ?, emp_pic = ?, emp_dob = ? WHERE emp_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt -> bind_param("sssbss",$emp_fname,$emp_lname,$emp_gender,$pic,$emp_dob,$emp_id);
            if ($pic) {
                $stmt->send_long_data(3, $pic); 
            }
            $stmt->execute();

            unset($_SESSION['edit_emp_id']);
            header("Location: adminmain.php?content=employee");
            exit();
        case 'admin_edit':
                if (!isset($_SESSION['edit_admin_id'])) {
                    echo "<script>alert('No employee selected for editing!');</script>";
                    header("Location: adminmain.php?content=employee");
                    exit();
                }
                $emp_id = $_SESSION['edit_admin_id'];
                    if (empty($emp_id)) {
                        echo "<script>alert('Invalid employee ID!');</script>";
                        header("Location: adminmain.php?content=employee");
                        exit();
                    }
                    if (isset($_FILES['epic']['tmp_name']) && $_FILES['epic']['error'] == 0) {
                        $pic = file_get_contents($_FILES['epic']['tmp_name']);
                    } else {
                        $pic = null;
                    }
                
                $emp_id = $_SESSION['edit_admin_id'];
                $emp_fname = $_POST['emp_fname'];
                $emp_lname = $_POST['emp_lname'];
                $emp_gender = $_POST['emp_gender'];
                $emp_dob = $_POST['emp_dob'];
    
                $sql = "UPDATE admin SET admin_fname = ?, admin_lname = ?, admin_gender = ?, admin_pic = ?, admin_dob = ? WHERE admin_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt -> bind_param("sssbss",$emp_fname,$emp_lname,$emp_gender,$pic,$emp_dob,$emp_id);
                if ($pic) {
                    $stmt->send_long_data(3, $pic); 
                }
                $stmt->execute();
    
                unset($_SESSION['edit_admin_id']);
                header("Location: adminmain.php?content=employee");
                exit();
    

        case 'employee_add':

            if (isset($_FILES['pic']['tmp_name']) && $_FILES['pic']['error'] == 0) {
                $pic = file_get_contents($_FILES['pic']['tmp_name']); 
            } else {
                $pic = null; 
            }

            $sql = "SELECT emp_id FROM employee ORDER BY emp_id";
            $result = $conn->query($sql);

            $existing_ids = [];
            while ($row = $result->fetch_assoc()) {
                
                $existing_ids[] = (int) substr($row['emp_id'], 1); 
            }

            $new_id_number = 1; 
            while (in_array($new_id_number, $existing_ids)) {
                $new_id_number++; 
            }

            $password = hash('sha256', 'password1234');
            $id = 'E' . $new_id_number;
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];

            $sql = "INSERT INTO employee (emp_id,emp_password,emp_fname,emp_lname,emp_gender,emp_pic,emp_dob) VALUES (?,?,?,?,?,?,?)";

            $stmt = $conn->prepare($sql);
            $stmt -> bind_param("sssssbs",$id,$password,$fname,$lname,$gender,$pic,$dob);
            if ($pic) {
                $stmt->send_long_data(5, $pic);
            }
            $stmt->execute();
            header("Location: adminmain.php?content=employee");
            exit();
        case 'admin_add':
            if (isset($_FILES['pic']['tmp_name']) && $_FILES['pic']['error'] == 0) {
                $pic = file_get_contents($_FILES['pic']['tmp_name']); 
            } else {
                $pic = null; 
            }

            $sql = "SELECT COUNT(*) AS total_admin FROM admin";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_admin = $row['total_admin'];

            $password = hash('sha256', 'password1234');
            $id = 'A' . ($total_admin + 1);
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];

            $sql = "INSERT INTO admin (admin_id,admin_password,admin_fname,admin_lname,admin_gender,admin_pic,admin_dob) VALUES (?,?,?,?,?,?,?)";

            $stmt = $conn->prepare($sql);
            $stmt -> bind_param("sssssbs",$id,$password,$fname,$lname,$gender,$pic,$dob);
            if ($pic) {
                $stmt->send_long_data(5, $pic); 
            }
            $stmt->execute();
            //echo "statement executed";
            //echo "Employee added successfully!";
            //echo "$fname";
            //echo "<img src='data:image/jpeg;base64," . base64_encode($pic) . "' alt='Uploaded Image' />";
            header("Location: adminmain.php?content=employee");
            exit();
        case 'employee_delete':
                try {
                    if (!isset($_SESSION['edit_emp_id'])) {
                        echo "<script>alert('No employee selected for editing!');</script>";
                        header("Location: adminmain.php?content=employee");
                        exit();
                    }
            
                    $emp_id = $_SESSION['edit_emp_id'];
            
                    if (empty($emp_id)) {
                        echo "<script>alert('Invalid employee ID!');</script>";
                        header("Location: adminmain.php?content=employee");
                        exit();
                    }
            
                    $sql = "DELETE FROM employee WHERE emp_id = ?;";
                    $stmt = $conn->prepare($sql);
            
                    if (!$stmt) {
                        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
                    }
            
                    $stmt->bind_param("s", $emp_id);
            
                    if ($stmt->execute()) {
                        unset($_SESSION['edit_emp_id']);
                        header("Location: adminmain.php?content=employee");
                        exit();
                    } else {
                        throw new Exception("Failed to execute SQL statement: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    unset($_SESSION['edit_emp_id']);
                    echo "<script>
                            alert('Something went wrong, Please check wether this employee was assigned to location or not: {$e->getMessage()}');
                            window.location.href = 'adminmain.php?content=employee';
                          </script>";
                    exit();
                }
        case 'admin_delete':
                    try {
                        if (!isset($_SESSION['edit_admin_id'])) {
                            echo "<script>alert('No employee selected for editing!');</script>";
                            header("Location: adminmain.php?content=employee");
                            exit();
                        }
                
                        $emp_id = $_SESSION['edit_admin_id'];
                        if (empty($emp_id)) {
                            echo "<script>alert('Invalid employee ID!');</script>";
                            header("Location: adminmain.php?content=employee");
                            exit();
                        }

                        $sql = "DELETE FROM admin WHERE admin_id = ?;";
                        $stmt = $conn->prepare($sql);
                
                        if (!$stmt) {
                            throw new Exception("Failed to prepare SQL statement: " . $conn->error);
                        }
                
                        $stmt->bind_param("s", $emp_id);
                
                        if ($stmt->execute()) {
                            unset($_SESSION['edit_admin_id']);
                            header("Location: adminmain.php?content=employee");
                            exit();
                        } else {
                            throw new Exception("Failed to execute SQL statement: " . $stmt->error);
                        }
                    } catch (Exception $e) {

                        unset($_SESSION['edit_admin_id']);
                        echo "<script>
                                alert('Something went wrong, Please check wether this employee was assigned to location or not: {$e->getMessage()}');
                                window.location.href = 'adminmain.php?content=employee';
                              </script>";
                        exit();
                    }
        case 'product_add':
                    if (isset($_FILES['ppic']['tmp_name']) && $_FILES['ppic']['error'] == 0) {
                        $pic = file_get_contents($_FILES['ppic']['tmp_name']); 
                    } else {
                        $pic = null; 
                    }
        
                    $sql = "SELECT prod_id FROM product ORDER BY prod_id";
                    $result = $conn->query($sql);
        
                    $existing_ids = [];
                    while ($row = $result->fetch_assoc()) {
                        
                        $existing_ids[] = (int) $row['prod_id']; 
                    }

                    $new_id_number = 1; 
                    while (in_array($new_id_number, $existing_ids)) {
                        $new_id_number++;  
                    }
        
                    $id = $new_id_number;
                    $name = $_POST['pname'];
                    $price = $_POST['pprice'];
                    $quantity = $_POST['pquantity'];
                    $info = $_POST['pinformation'];
                    $location = $_POST['plocation'];
        
                    $sql = "INSERT INTO product (prod_id,prod_name,prod_info,prod_pic,prod_price,prod_quantity,prod_loca) VALUES (?,?,?,?,?,?,?)";
        
                    $stmt = $conn->prepare($sql);
                    $stmt -> bind_param("issbdis",$id,$name,$info,$pic,$price,$quantity,$location);
                    if ($pic) {
                        $stmt->send_long_data(3, $pic); 
                    }
                    $stmt->execute();
                    header("Location: adminmain.php?content=product");
                    exit();


        case 'product_edit':
                    if (!isset($_SESSION['edit_prod_id'])) {
                        echo "<script>alert('No product selected for editing!');</script>";
                        header("Location: adminmain.php?content=employee");
                        exit();
                    }
                    $id_prod = $_SESSION['edit_prod_id'];
                        if (empty($id_prod)) {
                            echo "<script>alert('Invalid product ID!');</script>";
                        }

                        if (isset($_FILES['ppic']['tmp_name']) && $_FILES['ppic']['error'] == 0) {
                            $ppic = file_get_contents($_FILES['ppic']['tmp_name']);
                        } else {
                            $ppic = null;
                        }
                    
                        $name = $_POST['pname'];
                        $price = $_POST['pprice'];
                        $quantity = $_POST['pquantity'];
                        $info = $_POST['pinformation'];
                        $location = $_POST['plocation'];
        
                    $sql = "UPDATE product SET prod_name = ?, prod_info = ?, prod_pic = ?, prod_price = ?, prod_quantity = ?, prod_loca = ? WHERE prod_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt -> bind_param("ssbdisi",$name,$info,$ppic,$price,$quantity,$location,$id_prod);
                    if ($ppic) {
                        $stmt->send_long_data(2, $ppic); 
                    }
                    $stmt->execute();
                    unset($_SESSION['edit_emp_id']);
                    header("Location: adminmain.php?content=product");
                    exit();

        case 'product_delete':
                    try {
                        if (!isset($_SESSION['edit_prod_id'])) {
                            echo "<script>alert('No product selected for deleting!');</script>";
                            header("Location: adminmain.php?content=product");
                            exit();
                        }
                
                        $id_prod = $_SESSION['edit_prod_id'];
                
                        if (empty($id_prod)) {
                            echo "<script>alert('Invalid product ID!');</script>";
                            header("Location: adminmain.php?content=product");
                            exit();
                        }

                        $sql = "DELETE FROM product WHERE prod_id = ?;";
                        $stmt = $conn->prepare($sql);
                
                        if (!$stmt) {
                            throw new Exception("Failed to prepare SQL statement: " . $conn->error);
                        }
                
                        $stmt->bind_param("i", $id_prod);
                
                        if ($stmt->execute()) {
                            unset($_SESSION['edit_prod_id']);
                            header("Location: adminmain.php?content=product");
                            exit();
                        } else {
                            throw new Exception("Failed to execute SQL statement: " . $stmt->error);
                        }
                    } catch (Exception $e) {
                        unset($_SESSION['edit_prod_id']);
                        echo "<script>
                                alert('Something went wrong, Please check wether this product was assigned to location or not: {$e->getMessage()}');
                                window.location.href = 'adminmain.php?content=product';
                              </script>";
                        exit();
                    }
        case 'loca_assign':
                    
                    $zone = $_POST['loca_id'];
                    $work = $_POST['emp_id'];
                    
                    echo "test";
                    echo "Zone = ".$zone;
                    echo "work = ".$work;
                    $sql = "UPDATE location SET emp_id = ? WHERE loca_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt -> bind_param("ss",$work,$zone);
                    $stmt->execute();
                    echo "Executed";
                    header("Location: adminmain.php?content=assignment");
                    exit();
            case 'promotion_add':

                        $sql = "SELECT promo_id FROM promotion ORDER BY promo_id";
                        $result = $conn->query($sql);
            
                        $existing_ids = [];
                        while ($row = $result->fetch_assoc()) {
                            $existing_ids[] = (int) $row['promo_id'];
                        }
            
                        $new_id_number = 1;  
                        while (in_array($new_id_number, $existing_ids)) {
                            $new_id_number++;
                        }
        
                        $id = $new_id_number;
                        $name = $_POST['promo_name'];
                        $start = $_POST['start_date'];
                        $end = $_POST['end_date'];
                        $discount = $_POST['discount'];
                        $prod_id = $_POST['prod_id'];
            
                        $sql = "INSERT INTO promotion (promo_id,promo_name,promo_start,promo_end,promo_discount,prod_id) VALUES (?,?,?,?,?,?)";
            
                        $stmt = $conn->prepare($sql);
                        $stmt -> bind_param("isssii",$id,$name,$start,$end,$discount,$prod_id);
                        $stmt->execute();
                        header("Location: adminmain.php?content=promotion");
                        exit();
            
            
            case 'promotion_edit':
                        if (!isset($_SESSION['edit_promo_id'])) {
                            echo "<script>alert('No product selected for editing!');</script>";
                            header("Location: adminmain.php?content=promotion");
                            exit();
                        }
                        $id_promo = $_SESSION['edit_promo_id'];
                            if (empty($id_promo)) {
                                echo "<script>alert('Invalid promotion ID!');</script>";
                                header("Location: adminmain.php?content=promotion");
                                exit();
                            }
                        
                            $name = $_POST['pname'];
                            $start = $_POST['starter'];
                            $end = $_POST['ender'];
                            $discount = $_POST['discounter'];
                            $prod_id = $_POST['prod_ids'];
            
                        $sql = "UPDATE promotion SET promo_name = ?, promo_start = ?, promo_end = ?, promo_discount = ?, prod_id = ? WHERE promo_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt -> bind_param("sssiii",$name,$start,$end,$discount,$prod_id,$id_promo);
                        $stmt->execute();
        
                        unset($_SESSION['edit_promo_id']);
                        header("Location: adminmain.php?content=promotion");
                        exit();
            case 'promotion_delete':
                        try {
                            if (!isset($_SESSION['edit_promo_id'])) {
                                echo "<script>alert('No product selected for deleting!');</script>";
                                header("Location: adminmain.php?content=promotion");
                                exit();
                            }
                    
                            $id_promo = $_SESSION['edit_promo_id'];
                    
                            if (empty($id_promo)) {
                                echo "<script>alert('Invalid product ID!');</script>";
                                header("Location: adminmain.php?content=promotion");
                                exit();
                            }
            
                            $sql = "DELETE FROM promotion WHERE promo_id = ?;";
                            $stmt = $conn->prepare($sql);
                    
                            if (!$stmt) {
                                throw new Exception("Failed to prepare SQL statement: " . $conn->error);
                            }
                    
                            $stmt->bind_param("i", $id_promo);
                    
                            if ($stmt->execute()) {
                                unset($_SESSION['edit_promo_id']);
                                header("Location: adminmain.php?content=promotion");
                                exit();
                            } else {
                                throw new Exception("Failed to execute SQL statement: " . $stmt->error);
                            }
                        } catch (Exception $e) {
                            unset($_SESSION['edit_promo_id']);
                            echo "<script>
                                    alert('Something went wrong, Please check wether this product was assigned to location or not: {$e->getMessage()}');
                                    window.location.href = 'adminmain.php?content=product';
                                  </script>";
                            exit();
                        }
            case 'password_edit':
                //echo"Password edit case<br>";
                $xxxx = $_POST['xxxx'];
                $xxxxx = $_POST['xxxxx'];
                if($xxxx !== $xxxxx){
                    //echo"password does not match";
                    exit();
                }
                $xxxxx = "";
                $hashed = hash('sha256', $xxxx);
                //echo $hashed;
                $xxxx = '';
                if (!isset($_SESSION['admin_id'])) {
                    echo "<script>alert('No product selected for editing!');</script>";
                    header("Location: adminmain.php?content=changepass");
                    exit();
                }
                $id = $_SESSION['admin_id'];
                    if (empty($id)) {
                        echo "<script>alert('Invalid promotion ID!');</script>";
                        header("Location: adminmain.php?content=changepass");
                        exit();
                    }
                //echo $id;
                $sql = "UPDATE admin SET admin_password = ? WHERE admin_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt -> bind_param("ss",$hashed,$id);
                            $stmt->execute();

                header("Location: adminmain.php?content=changepass");
                exit();

        case 'edit_quantity':
                if (!isset($_SESSION['more_prodinfo'])) {
                    echo "<script>alert('No product selected for editing!');</script>";
                    header("Location: adminmain.php?content=employee");
                    exit();
                }

                $id_prod = $_SESSION['more_prodinfo'];
                $prod_quan = $_POST['prod_quantity'];

                $sql = "UPDATE product SET prod_quantity = ? WHERE prod_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $prod_quan, $id_prod);
                $stmt->execute();

                header("Location: employeemain.php?content=product");
                exit();

        case 'emp_password_edit':
                    //echo"Password edit case<br>";
                    $xxxx = $_POST['xxxx'];
                    $xxxxx = $_POST['xxxxx'];
                    if($xxxx !== $xxxxx){
                        echo"password does not match";
                        exit();
                    }
                    $xxxxx = "";
                    $hashed = hash('sha256', $xxxx);
                    //echo $hashed;
                    $xxxx = '';
                    if (!isset($_SESSION['emp_id'])) {
                        echo "<script>alert('No product selected for editing!');</script>";
                        //header("Location: adminmain.php?content=changepass");
                        exit();
                    }
                    $id = $_SESSION['emp_id'];
                        if (empty($id)) {
                            echo "<script>alert('Invalid promotion ID!');</script>";
                            //header("Location: adminmain.php?content=changepass");
                            exit();
                        }
                    //echo $id;
                    $sql = "UPDATE employee SET emp_password = ? WHERE emp_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt -> bind_param("ss",$hashed,$id);
                                $stmt->execute();
        
                    header("Location: employeemain.php?content=changepass");
                    exit();


        default:
            echo "<script>alert('Something went wrong, Returning to profile page');</script>";
            header("Location: adminmain.php");
            exit();
    }
}
?>
