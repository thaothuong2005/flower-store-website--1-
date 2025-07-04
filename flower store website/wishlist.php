<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['add_to_cart'])){

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if(mysqli_num_rows($check_cart_numbers) > 0){
        $message[] = 'Sản phẩm đã có trong giỏ hàng';
    }else{

        $check_wishlist_numbers = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

        if(mysqli_num_rows($check_wishlist_numbers) > 0){
            mysqli_query($conn, "DELETE FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
        }

        mysqli_query($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
        $message[] = 'Đã thêm sản phẩm vào giỏ hàng';
    }

}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `wishlist` WHERE id = '$delete_id'") or die('query failed');
    header('location:wishlist.php');
}

if(isset($_GET['delete_all'])){
    mysqli_query($conn, "DELETE FROM `wishlist` WHERE user_id = '$user_id'") or die('query failed');
    header('location:wishlist.php');
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Danh sách yêu thích</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<?php
// Hiển thị thông báo nếu có
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<section class="heading">
    <h3>Danh sách yêu thích của bạn</h3>
    <p> <a href="home.php">Trang chủ</a> / Yêu thích </p>
</section>

<section class="wishlist">

    <h1 class="title">Sản phẩm đã thêm</h1>

    <div class="box-container">

    <?php
        $grand_total = 0;
        $select_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE user_id = '$user_id'") or die('query failed');
        if(mysqli_num_rows($select_wishlist) > 0){
            while($fetch_wishlist = mysqli_fetch_assoc($select_wishlist)){
    ?>
    <form action="" method="POST" class="box">
        <a href="wishlist.php?delete=<?php echo $fetch_wishlist['id']; ?>" class="fas fa-times" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi danh sách yêu thích?');"></a>
        <a href="view_page.php?pid=<?php echo $fetch_wishlist['pid']; ?>" class="fas fa-eye" title="Xem chi tiết"></a>
        <img src="uploaded_img/<?php echo $fetch_wishlist['image']; ?>" alt="" class="image">
        <div class="name"><?php echo $fetch_wishlist['name']; ?></div>
         <div class="price">Giá: <?php echo number_format($fetch_wishlist['price'], 0, ',', '.'); ?>đ</div>
        <input type="hidden" name="product_id" value="<?php echo $fetch_wishlist['pid']; ?>">
        <input type="hidden" name="product_name" value="<?php echo $fetch_wishlist['name']; ?>">
        <input type="hidden" name="product_price" value="<?php echo $fetch_wishlist['price']; ?>">
        <input type="hidden" name="product_image" value="<?php echo $fetch_wishlist['image']; ?>">
        <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">
        
    </form>
    <?php
    $grand_total += $fetch_wishlist['price'];
        }
    }else{
        echo '<p class="empty">Danh sách yêu thích của bạn đang trống</p>';
    }
    ?>
    </div>

    <div class="wishlist-total">
        <p>Tổng giá trị : <span><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</span></p>
        <a href="shop.php" class="option-btn">Tiếp tục mua sắm</a>
        <a href="wishlist.php?delete_all" class="delete-btn <?php echo ($grand_total > 0)?'':'disabled' ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa hết danh sách yêu thích?');">Xóa tất cả</a>
    </div>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

