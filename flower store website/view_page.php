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

// Gửi đánh giá (chỉ khi user đã mua)
if(isset($_POST['submit_review'])){
    $pid = $_POST['product_id'];
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $product_result = mysqli_query($conn, "SELECT name FROM products WHERE id='$pid'") or die('query failed');
    $product_row = mysqli_fetch_assoc($product_result);
    $product_name = $product_row['name'];

    $check_order = mysqli_query($conn, "
        SELECT * FROM orders 
        WHERE user_id='$user_id' AND total_products LIKE '%$product_name%'
    ") or die('query failed');

    if(mysqli_num_rows($check_order) > 0){
        mysqli_query($conn, "INSERT INTO reviews (product_id, user_name, rating, comment) VALUES ('$pid', '$user_name', '$rating', '$comment')") or die('query failed');
        $message[] = 'Đã gửi đánh giá thành công!';
    } else {
        $message[] = 'Bạn phải mua sản phẩm này mới được đánh giá!';
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
<title>Xem nhanh sản phẩm</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
/* Đẹp & sang phần đánh giá */
.quick-view .review-form, .quick-view .reviews-list {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 1.5rem;
    margin-top: 2rem;
    background: #fafafa;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.quick-view .review-form h3, .quick-view .reviews-list h3 {
    font-size: 1.4rem;
    margin-bottom: 1rem;
    color: #333;
}

.quick-view .review-form input[type="text"],
.quick-view .review-form textarea,
.quick-view .review-form select {
    width: 100%;
    font-size: 1.1rem;
    padding: .8rem;
    margin-top: .5rem;
    border: 1px solid #ccc;
    border-radius: .5rem;
}

.quick-view .review-form input[type="submit"] {
    margin-top: 1rem;
    background: #ff6b81;
    color: white;
    padding: .6rem 1.2rem;
    border: none;
    border-radius: .5rem;
    font-size: 1.1rem;
    cursor: pointer;
}

.quick-view .reviews-list > div {
    border-bottom: 1px solid #eee;
    padding: .7rem 0;
}

.quick-view .reviews-list strong {
    font-size: 1.2rem;
    color: #222;
}

.quick-view .reviews-list em {
    display: block;
    margin-top: .3rem;
    color: #555;
    font-style: italic;
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

<section class="quick-view">
<h1 class="title">Chi tiết sản phẩm</h1>
<?php
if(isset($_GET['pid'])){
    $pid = $_GET['pid'];
    $select_products = mysqli_query($conn, "SELECT * FROM products WHERE id='$pid'") or die('query failed');
    if(mysqli_num_rows($select_products) > 0){
        while($fetch_products = mysqli_fetch_assoc($select_products)){
?>
<form action="" method="POST">
   <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="" class="image">
   <div class="name"><?php echo $fetch_products['name']; ?></div>
   <div class="price"><?php echo number_format($fetch_products['price'],0,',','.'); ?>đ</div>
   <div class="details"><?php echo $fetch_products['details']; ?></div>
   <input type="number" name="product_quantity" value="1" min="1" class="qty">
   <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
   <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
   <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
   <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
   <input type="submit" value="Thêm vào yêu thích" name="add_to_wishlist" class="option-btn">
   <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">

   <div class="review-form">
      <h3>Gửi đánh giá sản phẩm</h3>
      <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
      <input type="text" name="user_name" placeholder="Tên của bạn" required>
      <label>Đánh giá sao:</label>
      <select name="rating" required>
         <option value="5">⭐️⭐️⭐️⭐️⭐️</option>
         <option value="4">⭐️⭐️⭐️⭐️</option>
         <option value="3">⭐️⭐️⭐️</option>
         <option value="2">⭐️⭐️</option>
         <option value="1">⭐️</option>
      </select>
      <textarea name="comment" placeholder="Nhận xét của bạn..." required></textarea>
      <input type="submit" name="submit_review" value="Gửi đánh giá">
   </div>

   <div class="reviews-list">
   <h3>Đánh giá sản phẩm</h3>
   <?php
   $review_query = mysqli_query($conn, "SELECT * FROM reviews WHERE product_id='$pid' ORDER BY created_at DESC") or die('query failed');
   if(mysqli_num_rows($review_query) > 0){
       while($review = mysqli_fetch_assoc($review_query)){
           echo '<div>';
           echo '<strong>'.htmlspecialchars($review['user_name']).'</strong> - '.str_repeat("⭐", $review['rating']).'<br>';
           echo '<em>'.htmlspecialchars($review['comment']).'</em>';
           // Hiển thị trả lời của admin
           if(!empty($review['admin_reply'])){
               echo '<div style="margin-top:.5rem; padding:.5rem 1rem; background:#eef2f7; border-left:3px solid #007bff; border-radius:.3rem; color:#333;">';
               echo '<strong>Admin trả lời:</strong> '.htmlspecialchars($review['admin_reply']);
               echo '</div>';
           }
           echo '</div>';
       }
   } else {
       echo '<p>Chưa có đánh giá nào.</p>';
   }
   ?>
</div>

</form>
<?php
        }
    } else {
        echo '<p class="empty">Chưa có chi tiết sản phẩm!</p>';
    }
}
?>
<div class="more-btn">
    <a href="home.php" class="option-btn">Quay về trang chủ</a>
</div>
</section>

<?php @include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
