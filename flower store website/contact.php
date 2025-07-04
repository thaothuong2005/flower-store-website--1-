<?php

@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
}

// Gửi tin nhắn
if(isset($_POST['send'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE user_id='$user_id' AND name='$name' AND email='$email' AND number='$number' AND message='$msg'") or die('Lỗi truy vấn');

    if(mysqli_num_rows($select_message) > 0){
        $message[] = 'Bạn đã gửi tin nhắn này trước đó!';
    }else{
        mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')") or die('Lỗi khi gửi tin nhắn');
        $message[] = 'Tin nhắn đã được gửi thành công!';
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Liên hệ</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css -->
   <link rel="stylesheet" href="css/style.css">

   <style>
   .user-messages {
      max-width: 800px;
      margin: 2rem auto;
      border: 1px solid #ddd;
      border-radius: .5rem;
      padding: 1rem;
      background: #fafafa;
   }
   .user-messages h3 {
      margin-bottom: 1rem;
      font-size: 1.4rem;
   }
   .user-messages .box {
      border-bottom: 1px solid #eee;
      padding: .8rem 0;
   }
   .user-messages .admin-reply {
      margin-top: .3rem;
      padding: .5rem;
      background: #f0f8ff;
      border-left: 3px solid #007bff;
      border-radius: .3rem;
   }
   </style>
</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Liên hệ với chúng tôi</h3>
    <p><a href="home.php">Trang chủ</a> / Liên hệ</p>
</section>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<section class="contact">
    <form action="" method="POST">
        <h3>Gửi tin nhắn cho chúng tôi!</h3>
        <input type="text" name="name" placeholder="Nhập tên của bạn" class="box" required> 
        <input type="email" name="email" placeholder="Nhập email của bạn" class="box" required>
        <input type="number" name="number" placeholder="Nhập số điện thoại" class="box" required>
        <textarea name="message" class="box" placeholder="Nhập nội dung tin nhắn..." required cols="30" rows="10"></textarea>
        <input type="submit" value="Gửi tin nhắn" name="send" class="btn">
    </form>
</section>

<section class="user-messages">
   <h3>Tin nhắn của bạn</h3>
   <?php
   $user_messages = mysqli_query($conn, "SELECT * FROM `message` WHERE user_id='$user_id' ORDER BY id DESC") or die('Lỗi truy vấn');
   if(mysqli_num_rows($user_messages) > 0){
       while($row = mysqli_fetch_assoc($user_messages)){
           echo '<div class="box">';
           echo '<p><strong>Nội dung:</strong> '.htmlspecialchars($row['message']).'</p>';
           if(!empty($row['admin_reply'])){
               echo '<div class="admin-reply"><strong>Admin trả lời:</strong> '.nl2br(htmlspecialchars($row['admin_reply'])).'</div>';
           }
           echo '</div>';
       }
   }else{
       echo '<p>Bạn chưa gửi tin nhắn nào.</p>';
   }
   ?>
</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
