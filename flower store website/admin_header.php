<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <div class="flex">

      <a href="admin_page.php" class="logo">Bảng<span>Quản Trị</span></a>

      <nav class="navbar">
         <a href="admin_page.php">Trang chủ</a>
         <a href="admin_products.php">Sản phẩm</a>
         <a href="admin_stats.php">Thống kê</a>
         <a href="admin_orders.php">Đơn hàng</a>
         <a href="admin_users.php">Người dùng</a>
         <a href="admin_contacts.php">Tin nhắn</a>
         <a href="admin_review.php">Đánh giá</a>

      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p>Tên đăng nhập: <span><?php echo $_SESSION['admin_name']; ?></span></p>
         <p>Email: <span><?php echo $_SESSION['admin_email']; ?></span></p>
         <a href="logout.php" class="delete-btn">Đăng xuất</a>
        
      </div>

   </div>

</header>
