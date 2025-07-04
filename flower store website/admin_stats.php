<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
   header('location:login.php');
   exit;
}

$query = mysqli_query($conn, "
   SELECT 
      p.id, 
      p.name, 
      p.image, 
      p.price, 
      p.stock,
      COALESCE(SUM(CASE WHEN o.delivery_status = 'Đã giao' THEN oi.quantity ELSE 0 END),0) AS total_sold,
      COALESCE(SUM(CASE WHEN o.delivery_status = 'Đã giao' THEN oi.quantity * oi.price ELSE 0 END),0) AS total_revenue
   FROM products p
   LEFT JOIN order_items oi ON p.id = oi.product_id
   LEFT JOIN orders o ON oi.order_id = o.id
   GROUP BY p.id
   ORDER BY total_sold DESC
") or die('Query failed');

$labels = [];
$data = [];
mysqli_data_seek($query, 0);
while ($row = mysqli_fetch_assoc($query)) {
   $labels[] = $row['name'];
   $data[] = $row['total_sold'];
}
mysqli_data_seek($query, 0);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Thống kê sản phẩm</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      body {
         background: #f7f7f7;
         font-family: Arial, sans-serif;
      }
      .title {
         text-align: center;
         margin: 30px 0 20px;
         font-size: 28px;
         color: #222;
      }
      .stats-table {
         width: 95%;
         margin: auto;
         border-collapse: collapse;
         background: #fff;
         border-radius: 8px;
         overflow: hidden;
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
      .stats-table th, .stats-table td {
         padding: 12px;
         text-align: center;
         border-bottom: 1px solid #ddd;
      }
      .stats-table th {
         background-color: #f2f2f2;
         color: #333;
      }
      .stats-table tr:hover {
         background-color: #f9f9f9;
      }
      .stats-table img {
         max-height: 60px;
         border-radius: 4px;
      }
      .status-btn {
         padding: 6px 12px;
         border: none;
         border-radius: 20px;
         font-weight: bold;
         cursor: pointer;
         color: #fff;
      }
      .status-instock {
         background-color: #28a745;
      }
      .status-outstock {
         background-color: #dc3545;
      }
      canvas {
         display: block;
         width: 100% !important;
         max-width: 1400px;
         height: auto !important;
         margin: 40px auto;
         background: #fff;
         border-radius: 12px;
         padding: 20px;
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
   </style>
</head>
<body>

<?php @include 'admin_header.php'; ?>

<section class="products">
   <h1 class="title"> Thống kê sản phẩm </h1>

   <canvas id="salesChart"></canvas>

   <table class="stats-table">
      <tr>
         <th>Hình ảnh</th>
         <th>Tên sản phẩm</th>
         <th>Giá bán</th>
         <th>Đã bán</th>
         <th>Tồn kho</th>
         <th>Trạng thái</th>
         <th>Doanh thu</th>
      </tr>
      <?php if (mysqli_num_rows($query) > 0): ?>
         <?php while($row = mysqli_fetch_assoc($query)): ?>
            <?php
               $is_out = $row['stock'] <= 0;
               $btn_class = $is_out ? 'status-outstock' : 'status-instock';
               $btn_text = $is_out ? 'Hết hàng' : 'Còn hàng';
            ?>
            <tr>
               <td><img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" alt=""></td>
               <td><?php echo htmlspecialchars($row['name']); ?></td>
               <td><?php echo number_format($row['price'], 0, ',', '.'); ?>₫</td>
               <td><?php echo $row['total_sold']; ?></td>
               <td><?php echo $row['stock']; ?></td>
               <td>
                  <button class="status-btn <?php echo $btn_class; ?>" onclick="toggleStatus(this)">
                     <?php echo $btn_text; ?>
                  </button>
               </td>
               <td><?php echo number_format($row['total_revenue'], 0, ',', '.'); ?>₫</td>
            </tr>
         <?php endwhile; ?>
      <?php else: ?>
         <tr><td colspan="7">Chưa có dữ liệu sản phẩm.</td></tr>
      <?php endif; ?>
   </table>
</section>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
   type: 'bar',
   data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [{
         label: 'Sản phẩm đã bán',
         data: <?php echo json_encode($data); ?>,
         backgroundColor: 'rgba(54, 162, 235, 0.6)',
         borderColor: 'rgba(54, 162, 235, 1)',
         borderWidth: 1
      }]
   },
   options: {
      responsive: true,
      plugins: {
         legend: { display: false },
         title: {
            display: true,
            text: 'Biểu đồ số lượng sản phẩm đã bán',
            font: {
               size: 20
            }
         }
      },
      scales: {
         y: {
            beginAtZero: true,
            title: {
               display: true,
               text: 'Số lượng'
            }
         },
         x: {
            ticks: {
               maxRotation: 45,
               minRotation: 30,
               autoSkip: false
            }
         }
      }
   }
});

// Toggle trạng thái (demo frontend)
function toggleStatus(button) {
   if (button.classList.contains('status-outstock')) {
      button.classList.remove('status-outstock');
      button.classList.add('status-instock');
      button.innerText = 'Còn hàng';
   } else {
      button.classList.remove('status-instock');
      button.classList.add('status-outstock');
      button.innerText = 'Hết hàng';
   }

   // Gọi Ajax để cập nhật vào DB nếu cần
}
</script>

<script src="js/script.js"></script>
</body>
</html>
