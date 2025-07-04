<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
}

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $category = mysqli_real_escape_string($conn, $_POST['category']); // thêm dòng này
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $details = mysqli_real_escape_string($conn, $_POST['details']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('Truy vấn thất bại');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'Tên sản phẩm đã tồn tại!';
   }else{
      $insert_product = mysqli_query($conn, "INSERT INTO `products`(name, category, details, price, image) VALUES('$name', '$category', '$details', '$price', '$image')") or die('Truy vấn thất bại');

      if($insert_product){
         if($image_size > 2000000){
            $message[] = 'Kích thước ảnh quá lớn!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Thêm sản phẩm thành công!';
         }
      }
   }

}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_delete_image = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('Truy vấn thất bại');
   $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('Truy vấn thất bại');
   mysqli_query($conn, "DELETE FROM `wishlist` WHERE pid = '$delete_id'") or die('Truy vấn thất bại');
   mysqli_query($conn, "DELETE FROM `cart` WHERE pid = '$delete_id'") or die('Truy vấn thất bại');
   header('location:admin_products.php');

}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sản phẩm</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="add-products">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Thêm sản phẩm mới</h3>
      <input type="text" class="box" required placeholder="Nhập tên sản phẩm" name="name">

      <!-- thêm dropdown chọn loại hoa -->
      <select name="category" class="box" required>
         <option value="">-- Chọn loại hoa --</option>
         <option value="dam-cuoi">Hoa đám cưới</option>
         <option value="sinh-nhat">Hoa sinh nhật</option>
         <option value="ngay-le">Hoa ngày lễ</option>
         <option value="qua-tang">Quà tặng</option>
      </select>

      <input type="number" min="0" class="box" required placeholder="Nhập giá sản phẩm" name="price">
      <textarea name="details" class="box" required placeholder="Nhập mô tả sản phẩm" cols="30" rows="10"></textarea>
      <input type="file" accept="image/jpg, image/jpeg, image/png" required class="box" name="image">
      <input type="submit" value="Thêm sản phẩm" name="add_product" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('Truy vấn thất bại');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <div class="box">
         <div class="price"><?php echo number_format($fetch_products['price'], 0, ',', '.'); ?>đ</div>
         <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <div class="details"><?php echo $fetch_products['details']; ?></div>
         <div class="category">Loại: <?php echo $fetch_products['category']; ?></div> <!-- hiển thị loại hoa -->
         <a href="admin_update_product.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Cập nhật</a>
         <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">Xóa</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Chưa có sản phẩm nào được thêm!</p>';
      }
      ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>
