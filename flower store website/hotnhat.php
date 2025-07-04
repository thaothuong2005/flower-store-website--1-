<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
   header('location:login.php');
   exit;
}

// Xử lý thêm vào wishlist
if(isset($_POST['add_to_wishlist'])){
   $pid = mysqli_real_escape_string($conn, $_POST['product_id']);
   $name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $image = mysqli_real_escape_string($conn, $_POST['product_image']);

   $check_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE pid='$pid' AND user_id='$user_id'") or die('Query failed');
   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE pid='$pid' AND user_id='$user_id'") or die('Query failed');

   if(mysqli_num_rows($check_wishlist) > 0){
      $message[] = 'Đã có trong danh sách yêu thích!';
   } elseif(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Đã có trong giỏ hàng!';
   } else {
      mysqli_query($conn, "INSERT INTO `wishlist`(user_id, pid, name, price, image) 
      VALUES('$user_id', '$pid', '$name', '$price', '$image')") or die('Query failed');
      $message[] = 'Đã thêm vào danh sách yêu thích!';
   }
}

// Xử lý thêm vào giỏ hàng
if(isset($_POST['add_to_cart'])){
   $pid = mysqli_real_escape_string($conn, $_POST['product_id']);
   $name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $qty = (int)$_POST['product_quantity'];

   $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE pid='$pid' AND user_id='$user_id'") or die('Query failed');

   if(mysqli_num_rows($check_cart) > 0){
      $message[] = 'Đã có trong giỏ hàng!';
   } else {
      // Xoá nếu có trong wishlist
      mysqli_query($conn, "DELETE FROM `wishlist` WHERE pid='$pid' AND user_id='$user_id'") or die('Query failed');
      mysqli_query($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) 
      VALUES('$user_id', '$pid', '$name', '$price', '$qty', '$image')") or die('Query failed');
      $message[] = 'Đã thêm vào giỏ hàng!';
   }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sản phẩm bán chạy nhất</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php @include 'header.php'; ?>

<?php
// Hiển thị thông báo
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message">'.$msg.'</div>';
   }
}
?>

<section class="products">
   <h1 class="title">🌸 Sản phẩm bán chạy nhất 🌸</h1>
   <div class="box-container">
   <?php
   // Lấy top 8 sản phẩm bán chạy nhất
   $query = mysqli_query($conn, "
      SELECT p.*, COALESCE(SUM(oi.quantity),0) AS total_sold
      FROM products p
      LEFT JOIN order_items oi ON p.id = oi.product_id
      LEFT JOIN orders o ON oi.order_id = o.id AND o.delivery_status = 'Đã giao'
      GROUP BY p.id
      ORDER BY total_sold DESC
      LIMIT 9
   ") or die('Query failed');

   if(mysqli_num_rows($query) > 0){
      while($row = mysqli_fetch_assoc($query)){
   ?>
   <form action="" method="POST" class="box">
      <a href="view_page.php?pid=<?php echo $row['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" alt="" class="image">
      <div class="name"><?php echo htmlspecialchars($row['name']); ?></div>
      <div class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>₫</div>
      <!-- Đã xoá phần sao -->
      <input type="number" name="product_quantity" value="1" min="1" class="qty">
      <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
      <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
      <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['image']); ?>">
      <input type="submit" value="Yêu thích" name="add_to_wishlist" class="option-btn">
      <input type="submit" value="Thêm vào giỏ" name="add_to_cart" class="btn">
   </form>
   <?php
      }
   } else {
      echo '<p class="empty">Chưa có sản phẩm nào được bán!</p>';
   }
   ?>
   </div>
</section>

<?php @include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
