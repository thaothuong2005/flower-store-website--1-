<?php
@include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
}

// Xoá tin nhắn
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `message` WHERE id = '$delete_id'") or die('Xóa thất bại');
   header('location:admin_contacts.php');
}

// Trả lời tin nhắn
if(isset($_POST['reply'])){
   $reply = mysqli_real_escape_string($conn, $_POST['admin_reply']);
   $msg_id = (int)$_POST['message_id'];
   mysqli_query($conn, "UPDATE `message` SET admin_reply='$reply' WHERE id='$msg_id'") or die('Cập nhật thất bại');
   $message[] = 'Đã gửi trả lời cho khách hàng!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản trị - Liên hệ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
   .reply-form textarea {
      width: 100%;
      padding: .5rem;
      border: 1px solid #ccc;
      border-radius: .4rem;
      margin-top: .5rem;
   }
   .reply-form button {
      margin-top: .5rem;
      background: #28a745;
      color: white;
      border: none;
      padding: .4rem .8rem;
      border-radius: .4rem;
      cursor: pointer;
   }
   .admin-reply {
      margin-top: .5rem;
      padding: .5rem;
      background: #f0f8ff;
      border-left: 3px solid #007bff;
      border-radius: .3rem;
   }
   </style>
</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="messages">
   <h1 class="title">Tin nhắn từ khách hàng</h1>

   <?php
   if(isset($message)){
      foreach($message as $msg){
         echo '<div class="message">'.$msg.'</div>';
      }
   }
   ?>

   <div class="box-container">
      <?php
       $select_message = mysqli_query($conn, "SELECT * FROM `message`") or die('Lỗi truy vấn');
       if(mysqli_num_rows($select_message) > 0){
          while($fetch_message = mysqli_fetch_assoc($select_message)){
      ?>
      <div class="box">
         <p>ID người dùng: <span><?php echo $fetch_message['user_id']; ?></span></p>
         <p>Họ tên: <span><?php echo $fetch_message['name']; ?></span></p>
         <p>Số điện thoại: <span><?php echo $fetch_message['number']; ?></span></p>
         <p>Email: <span><?php echo $fetch_message['email']; ?></span></p>
         <p>Nội dung: <span><?php echo $fetch_message['message']; ?></span></p>

         <?php if(!empty($fetch_message['admin_reply'])){ ?>
            <div class="admin-reply">
               <strong>Admin trả lời:</strong> <?php echo nl2br(htmlspecialchars($fetch_message['admin_reply'])); ?>
            </div>
         <?php } ?>

         <form method="POST" class="reply-form">
            <input type="hidden" name="message_id" value="<?php echo $fetch_message['id']; ?>">
            <textarea name="admin_reply" placeholder="Nhập trả lời cho tin nhắn này..." required><?php echo htmlspecialchars($fetch_message['admin_reply']); ?></textarea>
            <button type="submit" name="reply">Gửi trả lời</button>
         </form>

         <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?');" class="delete-btn">Xóa</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Không có tin nhắn nào!</p>';
      }
      ?>
   </div>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>
