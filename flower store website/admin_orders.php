<?php
@include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if(!$admin_id){
   header('location:login.php');
   exit;
}

if(isset($_POST['update_order'])){
   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'] ?? '';
   $update_delivery = $_POST['update_delivery'] ?? '';
   $update_method = $_POST['update_method'] ?? '';
   $lat = floatval($_POST['lat'] ?? 0);
   $lng = floatval($_POST['lng'] ?? 0);

   mysqli_query($conn, "UPDATE `orders` 
      SET payment_status = '$update_payment', 
          delivery_status = '$update_delivery',
          method = '$update_method',
          delivery_lat = '$lat',
          delivery_lng = '$lng'
      WHERE id = '$order_id'") or die('Không thể cập nhật đơn hàng!');

   header('location:admin_orders.php?updated=1'); // Redirect để tránh lỗi F5 và load dữ liệu mới
   exit;
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('Không thể xóa đơn hàng!');
   header('location:admin_orders.php');
   exit;
}

// ✅ Luôn load đơn hàng để render HTML & JS
$select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('Không thể truy vấn đơn hàng!');
$orders_data = [];
while($row = mysqli_fetch_assoc($select_orders)){
    $orders_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý đơn hàng</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

<?php @include 'admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="title">Đơn hàng đã đặt</h1>

   <div class="box-container">
      <?php
      if(count($orders_data) > 0){
         foreach($orders_data as $fetch_orders){
      ?>
      <div class="box">
         <?php if($fetch_orders['delivery_status'] !== 'Đã giao'): ?>
         <div id="map_<?php echo $fetch_orders['id']; ?>" style="width: 100%; height: 200px; margin-top: 10px;"></div>
         <?php endif; ?>

         <p> Mã người dùng: <span><?php echo $fetch_orders['user_id']; ?></span> </p>
         <p> Ngày đặt: <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
         <p> Họ tên: <span><?php echo $fetch_orders['name']; ?></span> </p>
         <p> Số điện thoại: <span><?php echo $fetch_orders['number']; ?></span> </p>
         <p> Email: <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> Địa chỉ: <span><?php echo $fetch_orders['address']; ?></span> </p>
         <p> Sản phẩm: <span><?php echo $fetch_orders['total_products']; ?></span> </p>
         <p> Tổng tiền: <span><?php echo number_format($fetch_orders['total_price'], 0, ',', '.'); ?>đ</span> </p>
         <p> Phương thức thanh toán: <span>
            <?php 
               switch($fetch_orders['method']) {
                  case 'cash on delivery': echo 'Thanh toán khi nhận hàng'; break;
                  case 'momo': echo 'Ví MoMo'; break;
                  case 'bank': echo 'Chuyển khoản ngân hàng'; break;
                  default: echo $fetch_orders['method'];
               }
            ?>
         </span></p>
         
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
            <label>Trạng thái thanh toán:</label>
            <select name="update_payment">
               <option value="pending" <?php if($fetch_orders['payment_status'] == 'pending') echo 'selected'; ?>>Chưa thanh toán</option>
               <option value="completed" <?php if($fetch_orders['payment_status'] == 'completed') echo 'selected'; ?>>Đã thanh toán</option>
            </select>

            <label>Trạng thái giao hàng:</label>
            <select name="update_delivery">
               <option value="Đang xử lý" <?php if($fetch_orders['delivery_status'] == 'Đang xử lý') echo 'selected'; ?>>Đang xử lý</option>
               <option value="Đang giao" <?php if($fetch_orders['delivery_status'] == 'Đang giao') echo 'selected'; ?>>Đang giao</option>
               <option value="Đã giao" <?php if($fetch_orders['delivery_status'] == 'Đã giao') echo 'selected'; ?>>Đã giao</option>
               <option value="Đã hủy" <?php if($fetch_orders['delivery_status'] == 'Đã hủy') echo 'selected'; ?>>Đã hủy</option>
            </select>

            <input type="submit" name="update_order" value="Cập nhật" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">Xóa</a>
         </form>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">Chưa có đơn hàng nào!</p>';
      }
      ?>
   </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
   <?php
   $store_lat = 14.054510;
   $store_lng = 109.042130;

   foreach($orders_data as $order){
      $orderId = $order['id'];
      $lat = floatval($order['delivery_lat']);
      $lng = floatval($order['delivery_lng']);
      $status = $order['delivery_status'];

      if($status !== 'Đã giao'){
   ?>
   const map<?php echo $orderId; ?> = L.map('map_<?php echo $orderId; ?>').setView([<?php echo $store_lat; ?>, <?php echo $store_lng; ?>], 15);
   L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
   }).addTo(map<?php echo $orderId; ?>);

   const route<?php echo $orderId; ?> = [
      [<?php echo $store_lat; ?>, <?php echo $store_lng; ?>],
      [<?php echo $lat; ?>, <?php echo $lng; ?>]
   ];

   const motorbikeIcon<?php echo $orderId; ?> = L.icon({
      iconUrl: 'images/giaohang.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -40]
   });

   let marker<?php echo $orderId; ?> = L.marker(route<?php echo $orderId; ?>[0], { icon: motorbikeIcon<?php echo $orderId; ?> }).addTo(map<?php echo $orderId; ?>);
   marker<?php echo $orderId; ?>.bindPopup("Đang giao hàng").openPopup();

   let routeLine<?php echo $orderId; ?> = L.polyline([route<?php echo $orderId; ?>[0], route<?php echo $orderId; ?>[0]], {
      color: 'red', weight: 3, opacity: 0.8, dashArray: '5,10'
   }).addTo(map<?php echo $orderId; ?>);

   let index<?php echo $orderId; ?> = 0;

   function moveMarker<?php echo $orderId; ?>() {
      index<?php echo $orderId; ?>++;
      if(index<?php echo $orderId; ?> >= route<?php echo $orderId; ?>.length) {
         marker<?php echo $orderId; ?>.bindPopup("Đã giao hàng").openPopup();
         return;
      }
      const latlng = route<?php echo $orderId; ?>[index<?php echo $orderId; ?>];
      marker<?php echo $orderId; ?>.setLatLng(latlng);
      routeLine<?php echo $orderId; ?>.setLatLngs([route<?php echo $orderId; ?>[0], latlng]);
      map<?php echo $orderId; ?>.panTo(latlng);
      setTimeout(moveMarker<?php echo $orderId; ?>, 2000);
   }
   setTimeout(moveMarker<?php echo $orderId; ?>, 2000);
   <?php } } ?>
});
</script>

</body>
</html>
