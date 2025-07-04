<?php
@include 'config.php';

if (isset($_POST['submit'])) {
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $token = bin2hex(random_bytes(50));

   $check_user = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
   if (mysqli_num_rows($check_user) > 0) {
      mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");
      $reset_link = "http://localhost/flower/reset_password.php?token=$token";

      echo "<div class='message'>
               <span>Link đặt lại mật khẩu đã được tạo: <a href='$reset_link'>Đặt lại mật khẩu</a></span>
               <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
            </div>";
   } else {
      echo "<div class='message'>
               <span>Email không tồn tại!</span>
               <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
            </div>";
   }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Quên mật khẩu</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="form-container">
   <form action="" method="post">
      <h3>Quên mật khẩu</h3>
      <input type="email" name="email" class="box" placeholder="Nhập email đã đăng ký" required>
      <input type="submit" name="submit" class="btn" value="Gửi link đặt lại">
      <p><a href="login.php">Quay lại đăng nhập</a></p>
   </form>
</section>

</body>
</html>
