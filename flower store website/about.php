<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giới thiệu</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Giới thiệu</h3>
    <p> <a href="home.php">Trang chủ</a> / Giới thiệu </p>
</section>

<section class="about">

    <div class="flex">

        <div class="image">
            <img src="images/about-img-1.png" alt="Hình ảnh về cửa hàng hoa">
        </div>

        <div class="content">
            <h3>Tại sao chọn chúng tôi?</h3>
            <p>Chúng tôi mang đến những bó hoa tươi thắm nhất, được tuyển chọn kỹ lưỡng mỗi ngày, giúp bạn gửi gắm yêu thương đến người thân yêu một cách tinh tế và ý nghĩa.</p>
            <a href="shop.php" class="btn">Mua hoa ngay</a>
        </div>

    </div>

    <div class="flex">

        <div class="content">
            <h3>Chúng tôi cung cấp gì?</h3>
                <p>Chúng tôi cung cấp các loại hoa tươi đa dạng cho mọi dịp lễ: sinh nhật, kỷ niệm, cưới hỏi, khai trương và cả những bó hoa truyền tải lời yêu thương chân thành. Mỗi bó hoa đều được thiết kế tinh tế và giao hàng tận nơi nhanh chóng.</p>
            <a href="contact.php" class="btn">Liên hệ</a>
        </div>

        <div class="image">
            <img src="images/about-img-2.jpg" alt="Hình ảnh các loại hoa tươi">
        </div>

    </div>

    <div class="flex">

        <div class="image">
            <img src="images/about-img-3.jpg" alt="Cửa hàng hoa uy tín">
        </div>

        <div class="content">
            <h3>Chúng tôi là ai?</h3>
                <p>Chúng tôi là cửa hàng hoa uy tín với niềm đam mê mang vẻ đẹp và cảm xúc đến mọi người. Mỗi bó hoa là một thông điệp yêu thương được gửi gắm bằng sự tận tâm và tinh tế.</p>
            <a href="#reviews" class="btn">Đánh giá</a>
        </div>

    </div>

</section>

<section class="reviews" id="reviews">

    <h1 class="title">Đánh giá khách hàng</h1>

    <div class="box-container">

        <div class="box">
            <img src="images/pic-1.png" alt="Khách hàng Ngọc Anh">
            <p>Bó hoa sinh nhật được gói rất đẹp và giao đúng giờ. Mình rất hài lòng với dịch vụ tại đây, chắc chắn sẽ quay lại lần sau!</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Ngọc Anh</h3>
        </div>

        <div class="box">
            <img src="images/pic-2.png" alt="Khách hàng Minh Khoa">
            <p>Hoa tươi, mùi thơm dễ chịu và cách cắm rất nghệ thuật. Nhân viên tư vấn cũng rất nhiệt tình!</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Minh Khoa</h3>
        </div>

        <div class="box">
            <img src="images/pic-3.png" alt="Khách hàng Thảo Thương">
            <p>Đặt hoa online mà như được chăm chút từng chi tiết! Người nhận rất vui và bất ngờ, cảm ơn shop nhiều!</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Thảo Thương</h3>
        </div>

        <div class="box">
            <img src="images/pic-4.png" alt="Khách hàng Thu Thu">
            <p>Dịch vụ giao hoa nhanh, chuyên nghiệp. Mình đã đặt hoa khai trương và nhận được đúng mẫu mình mong muốn.</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Thu Thu</h3>
        </div>

        <div class="box">
            <img src="images/pic-5.png" alt="Khách hàng Bao Bao">
            <p>Hoa rất tươi và đẹp, giá cả hợp lý. Shop còn tặng kèm thiệp viết tay rất dễ thương nữa!</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Bao Bao</h3>
        </div>

        <div class="box">
            <img src="images/pic-6.png" alt="Khách hàng Bao BiBi">
            <p>Đặt hoa cho mẹ nhân dịp 8/3, mẹ rất thích! Cảm ơn shop vì đã giúp mình gửi trọn yêu thương.</p>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Bao BiBi</h3>
        </div>

    </div>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

