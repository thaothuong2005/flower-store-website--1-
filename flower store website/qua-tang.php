<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
if(!isset($user_id)){
   header('location:login.php');
}

// Thêm vào wishlist
if(isset($_POST['add_to_wishlist'])){
   $pid = $_POST['product_id'];
   $name = $_POST['product_name'];
   $price = $_POST['product_price'];
   $image = $_POST['product_image'];

   $check_wishlist = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id='$user_id' AND pid='$pid'") or die('query failed');
   $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND pid='$pid'") or die('query failed');

   if(mysqli_num_rows($check_wishlist) > 0){
      $message[] = 'Sản phẩm đã có trong danh sách yêu thích';
   }elseif(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Sản phẩm đã có trong giỏ hàng';
   }else{
      mysqli_query($conn, "INSERT INTO wishlist (user_id, pid, name, price, image) VALUES ('$user_id','$pid','$name','$price','$image')") or die('query failed');
      $message[] = 'Đã thêm sản phẩm vào danh sách yêu thích';
   }
}

// Thêm vào giỏ hàng
if(isset($_POST['add_to_cart'])){
   $pid = $_POST['product_id'];
   $name = $_POST['product_name'];
   $price = $_POST['product_price'];
   $image = $_POST['product_image'];
   $qty = $_POST['product_quantity'];

   $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND pid='$pid'") or die('query failed');

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Sản phẩm đã có trong giỏ hàng';
   }else{
      mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='$user_id' AND pid='$pid'") or die('query failed');
      mysqli_query($conn, "INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES ('$user_id','$pid','$name','$price','$qty','$image')") or die('query failed');
      $message[] = 'Đã thêm sản phẩm vào giỏ hàng';
   }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quà tặng</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
/* Ảnh không bị tràn */
.box img {
   width: 100%;
   height: auto;
   object-fit: cover;
   border-radius: .5rem;
}
/* Nút yêu thích nằm trên nút giỏ hàng */
.flex-btn {
   display: flex;
   flex-direction: column;
   gap: .5rem;
   margin-top: .5rem;
}
</style>
</head>
<body>

<?php @include 'header.php'; ?>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<section class="products">
<h1 class="title">Quà tặng</h1>
<div class="box-container">
<?php
$select_products = mysqli_query($conn, "SELECT * FROM products WHERE category='qua-tang'") or die('query failed');
if(mysqli_num_rows($select_products) > 0){
   while($row = mysqli_fetch_assoc($select_products)){
?>
<form action="" method="POST" class="box">
   <a href="view_page.php?pid=<?php echo $row['id']; ?>" class="fas fa-eye"></a>
   <img src="uploaded_img/<?php echo $row['image']; ?>" alt="">
   <div class="name"><?php echo $row['name']; ?></div>
   <div class="flex">
      <div class="price"><?php echo number_format($row['price'],0,',','.'); ?>đ</div>
      <input type="number" name="product_quantity" min="1" value="1" class="qty">
   </div>
   <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
   <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
   <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
   <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
   <div class="flex-btn">
      <button type="submit" name="add_to_wishlist" class="option-btn">Yêu thích</button>
      <input type="submit" name="add_to_cart" value="Thêm vào giỏ hàng" class="btn">
   </div>
</form>
<?php
   }
}else{
   echo '<p class="empty">Chưa có sản phẩm!</p>';
}
?>
</div>
 <div class="more-btn">
      <a href="shop.php" class="option-btn">xem thêm</a>
   </div>
</section>

<?php @include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
