<?php
@include 'config.php';

$token = $_GET['token'] ?? '';

if (isset($_POST['submit'])) {
   $new_pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);

   if ($new_pass != $cpass) {
      echo "<div class='message'>
               <span>Mật khẩu không khớp!</span>
               <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
            </div>";
   } else {
      $check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");
      if (mysqli_num_rows($check) > 0) {
         mysqli_query($conn, "UPDATE users SET password='$new_pass', reset_token=NULL WHERE reset_token='$token'");
         echo "<div class='message'>
                  <span>Đổi mật khẩu thành công! <a href='login.php'>Đăng nhập ngay</a></span>
                  <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
               </div>";
      } else {
         echo "<div class='message'>
                  <span>Token không hợp lệ!</span>
                  <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
               </div>";
      }
   }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Đặt lại mật khẩu</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="form-container">
   <form action="" method="post">
      <h3>Đặt lại mật khẩu</h3>
      <input type="password" name="password" class="box" placeholder="Mật khẩu mới" required>
      <input type="password" name="cpassword" class="box" placeholder="Nhập lại mật khẩu" required>
      <input type="submit" name="submit" class="btn" value="Xác nhận đổi mật khẩu">
      <p><a href="login.php">Quay lại đăng nhập</a></p>
   </form>
</section>

</body>
</html>
