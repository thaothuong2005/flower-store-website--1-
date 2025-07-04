<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Đơn hàng của bạn</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css" />

   <!-- Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Đơn hàng của bạn</h3>
    <p><a href="home.php">Trang chủ</a> / Đơn hàng</p>
</section>

<section class="placed-orders">

    <h1 class="title">Các đơn hàng đã đặt</h1>

    <div class="box-container">

    <?php
        $select_orders = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('Lỗi truy vấn dữ liệu đơn hàng');

        if(mysqli_num_rows($select_orders) > 0){
            $undelivered_orders = [];

            while($fetch_orders = mysqli_fetch_assoc($select_orders)){
    ?>
        <div class="box">
            <p> Ngày đặt: <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
            <p> Họ tên: <span><?php echo $fetch_orders['name']; ?></span> </p>
            <p> Số điện thoại: <span><?php echo $fetch_orders['number']; ?></span> </p>
            <p> Email: <span><?php echo $fetch_orders['email']; ?></span> </p>
            <p> Địa chỉ: <span><?php echo $fetch_orders['address']; ?></span> </p>
            <p> Phương thức thanh toán: 
               <span>
                  <?php
                     $method_vi = '';
                     switch($fetch_orders['method']){
                        case 'cash on delivery':
                           $method_vi = 'Thanh toán khi nhận hàng';
                           break;
                        case 'credit card':
                           $method_vi = 'Thẻ tín dụng';
                           break;
                        case 'momo':
                           $method_vi = 'Ví MoMo';
                           break;
                        case 'bank transfer':
                           $method_vi = 'Chuyển khoản ngân hàng';
                           break;
                        default:
                           $method_vi = $fetch_orders['method'];
                     }
                     echo $method_vi;
                  ?>
               </span>
            </p>
            <p> Sản phẩm đã đặt: <span><?php echo $fetch_orders['total_products']; ?></span> </p>
            <p> Tổng tiền: <span><?php echo number_format($fetch_orders['total_price'], 0, ',', '.') . ' ₫'; ?></span> </p>

            <p> Trạng thái thanh toán: <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'tomato' : 'green'; ?>">
                <?php echo ($fetch_orders['payment_status'] == 'pending') ? 'Chưa thanh toán' : 'Đã thanh toán'; ?></span> </p>
            <p> Trạng thái giao hàng: <span style="color:blue;"><?php echo $fetch_orders['delivery_status']; ?></span></p>

            <?php if ($fetch_orders['delivery_status'] !== 'Đã giao') : ?>
                <div id="map_<?php echo $fetch_orders['id']; ?>" style="width: 100%; height: 200px; margin-top: 10px;"></div>

                <?php
                $undelivered_orders[] = [
                    'id' => $fetch_orders['id'],
                    'lat' => (float)$fetch_orders['delivery_lat'],
                    'lng' => (float)$fetch_orders['delivery_lng'],
                    'status' => $fetch_orders['delivery_status']
                ];
                ?>

            <?php else: ?>
                <p style="color:green; font-weight:bold;">Đơn hàng đã được giao.</p>
            <?php endif; ?>
        </div>
    <?php
            }
        }else{
            echo '<p class="empty">Bạn chưa đặt đơn hàng nào!</p>';
        }
    ?>
    </div>

</section>

<?php @include 'footer.php'; ?>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
   const storeLat = 14.054510;
   const storeLng = 109.042130;

   const undeliveredOrders = <?php echo json_encode($undelivered_orders ?? []); ?>;

   undeliveredOrders.forEach(order => {
      const map = L.map('map_' + order.id).setView([storeLat, storeLng], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
         attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      const route = [
         [storeLat, storeLng],
         [storeLat + 0.0005, storeLng + 0.0005],
         [storeLat + 0.0010, storeLng + 0.0010],
         [order.lat, order.lng]
      ];

      const motorbikeIcon = L.icon({
         iconUrl: 'images/giaohang.png',
         iconSize: [40, 40],
         iconAnchor: [20, 40],
         popupAnchor: [0, -40]
      });

      const storeIcon = L.icon({
         iconUrl: 'images/shop.png',
         iconSize: [35, 35],
         iconAnchor: [17, 35],
         popupAnchor: [0, -35]
      });

      L.marker([storeLat, storeLng], { icon: storeIcon })
        .addTo(map)
        .bindPopup('Cửa hàng (Cát Thành, Phù Cát, Bình Định)');

      let marker = L.marker(route[0], { icon: motorbikeIcon }).addTo(map);
      marker.bindPopup("Đang giao hàng").openPopup();

      let traveledPath = L.polyline([route[0]], {
         color: '#ff0000',
         weight: 5,
         opacity: 0.9,
         dashArray: null
      }).addTo(map);

      let index = 0;

      function moveMarker() {
         index++;
         if(index >= route.length) {
            marker.setLatLng(route[route.length - 1]);
            marker.bindPopup("Đang giao hàng").openPopup();
            return;
         }

         const latlng = route[index];
         marker.setLatLng(latlng);

         let latlngs = traveledPath.getLatLngs();
         latlngs.push(latlng);
         traveledPath.setLatLngs(latlngs);

         marker.bindPopup("Đang giao hàng").openPopup();
         map.panTo(latlng);

         setTimeout(moveMarker, 5000);
      }

      setTimeout(moveMarker, 5000);
   });
});
</script>

</body>
</html>
