<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
   header('location:login.php');
   exit;
}

// Chỉ xử lý khi bấm nút đặt hàng (có POST và tồn tại $_POST['order'])
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])){

   $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
   $number = mysqli_real_escape_string($conn, $_POST['number'] ?? '');
   $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
   $method = mysqli_real_escape_string($conn, $_POST['method'] ?? '');
   $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
   $placed_on = date('Y-m-d H:i:s');
   $payment_status = 'pending';
   $delivery_status = 'Đang xử lý';

   $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id'") or die('Lỗi lấy giỏ hàng');

   $total_price = 0;
   $total_products = '';

   if(mysqli_num_rows($cart_query) > 0){
       while($cart_item = mysqli_fetch_assoc($cart_query)){
           $product_name = $cart_item['name'];
           $qty = $cart_item['quantity'];
           $price = $cart_item['price'];
           $total_price += ($price * $qty);
           $total_products .= $product_name . ' ('.$qty.') - ';
       }

       mysqli_query($conn, "INSERT INTO orders(user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status, delivery_status) 
       VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$total_price', '$placed_on', '$payment_status', '$delivery_status')") or die('Lỗi thêm order');

       $order_id = mysqli_insert_id($conn);

       mysqli_data_seek($cart_query, 0);
       while($cart_item = mysqli_fetch_assoc($cart_query)){
           $pid = $cart_item['pid'];
           $qty = $cart_item['quantity'];
           $price = $cart_item['price'];

           mysqli_query($conn, "INSERT INTO order_items(order_id, product_id, quantity, price)
           VALUES('$order_id', '$pid', '$qty', '$price')") or die('Lỗi thêm order_items');
       }

       mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'") or die('Lỗi xoá giỏ hàng');

       echo "Đặt hàng thành công!";
   } else {
       echo "Giỏ hàng của bạn đang trống!";
   }
}

// Nếu chỉ GET → không echo gì thêm, để hiển thị giao diện bình thường
?>
