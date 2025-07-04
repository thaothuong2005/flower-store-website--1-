<?php
@include 'config.php';
session_start();

// Kiểm tra đăng nhập admin (tuỳ bạn, ví dụ dùng session 'admin_id')
if(!isset($_SESSION['admin_id'])){
    header('location:admin_login.php');
    exit;
}

// Xoá đánh giá
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM reviews WHERE id='$delete_id'") or die('query failed');
    header('location:admin_reviews.php');
}

// Trả lời đánh giá
if(isset($_POST['reply'])){
    $reply = mysqli_real_escape_string($conn, $_POST['admin_reply']);
    $review_id = (int)$_POST['review_id'];
    mysqli_query($conn, "UPDATE reviews SET admin_reply='$reply' WHERE id='$review_id'") or die('query failed');
    $message = "Đã trả lời đánh giá!";
}

$reviews = mysqli_query($conn, "SELECT reviews.*, products.name as product_name 
    FROM reviews LEFT JOIN products ON reviews.product_id=products.id 
    ORDER BY reviews.created_at DESC") or die('query failed');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý đánh giá</title>
<link rel="stylesheet" href="css/admin_style.css">
<style>
table { width: 100%; border-collapse: collapse; }
th, td { border:1px solid #ddd; padding: 8px; text-align:left; }
th { background: #f2f2f2; }
textarea { width:100%; }
.message { margin: 1rem 0; color: green; }
</style>
</head>
<body>
<h2>Quản lý đánh giá</h2>

<?php if(isset($message)) echo "<div class='message'>$message</div>"; ?>

<table>
<tr>
   <th>ID</th>
   <th>Sản phẩm</th>
   <th>Người dùng</th>
   <th>Sao</th>
   <th>Nội dung</th>
   <th>Admin trả lời</th>
   <th>Hành động</th>
</tr>
<?php while($row = mysqli_fetch_assoc($reviews)){ ?>
<tr>
   <td><?php echo $row['id']; ?></td>
   <td><?php echo htmlspecialchars($row['product_name']); ?></td>
   <td><?php echo htmlspecialchars($row['user_name']); ?></td>
   <td><?php echo str_repeat("⭐",$row['rating']); ?></td>
   <td><?php echo nl2br(htmlspecialchars($row['comment'])); ?></td>
   <td>
      <form method="POST" style="margin:0;">
         <input type="hidden" name="review_id" value="<?php echo $row['id']; ?>">
         <textarea name="admin_reply" placeholder="Nhập trả lời..."><?php echo htmlspecialchars($row['admin_reply']); ?></textarea>
         <button type="submit" name="reply">Gửi</button>
      </form>
   </td>
   <td>
      <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xoá đánh giá này?')">Xoá</a>
   </td>
</tr>
<?php } ?>
</table>

</body>
</html>
