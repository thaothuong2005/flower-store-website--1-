<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>Liên Kết Nhanh</h3>
            <a href="home.php">Trang Chủ</a>
            <a href="about.php">Giới Thiệu</a>
            <a href="contact.php">Liên Hệ</a>
            <a href="shop.php">Cửa Hàng</a>
        </div>

        <div class="box">
            <h3>Liên Kết Khác</h3>
            <a href="login.php">Đăng Nhập</a>
            <a href="register.php">Đăng Ký</a>
            <a href="orders.php">Đơn Hàng Của Tôi</a>
            <a href="cart.php">Giỏ Hàng Của Tôi</a>
        </div>

        <div class="box">
            <h3>Thông Tin Liên Hệ</h3>
            <p> <i class="fas fa-phone"></i> +0334039300 </p>
            <p> <i class="fas fa-phone"></i> +0336822136 </p>
            <p> <i class="fas fa-envelope"></i> thuongnguyenthithao82@gmail.com </p>
            <p> <i class="fas fa-map-marker-alt"></i> Bình Định, Việt Nam - 123-456 </p>
        </div>

        <div class="box">
            <h3>Theo Dõi Chúng Tôi</h3>
            <a href="#"><i class="fab fa-facebook-f"></i>Facebook</a>
            <a href="#"><i class="fab fa-twitter"></i>Twitter</a>
            <a href="#"><i class="fab fa-instagram"></i>Instagram</a>
            <a href="#"><i class="fab fa-linkedin"></i>LinkedIn</a>
        </div>

    </div>

    <div class="credit">
        &copy; Bản Quyền @ <?php echo date('Y'); ?> Bởi 
        <a href="#" onclick="toggleGroupInfo()" style="color: black; text-decoration: none;">Nhóm 9</a>
        <div id="group-info" style="display: none; margin-top: 10px;">
            <strong>Thành Viên Nhóm 9:</strong>
            <ul style="margin: 5px 0 0 15px; padding: 0;">
                <li>052305001437 - Nguyễn Thị Thảo Thương</li>
                <li>052305001657 - Nguyễn Như Linh</li>
                <li>052205012528 - Lương Xuân Bá</li>
            </ul>
        </div>
    </div>

</section>

<script>
function toggleGroupInfo() {
    const info = document.getElementById('group-info');
    info.style.display = (info.style.display === 'none') ? 'block' : 'none';
}
</script>
