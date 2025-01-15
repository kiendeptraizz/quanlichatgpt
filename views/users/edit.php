<!DOCTYPE html>
<html>

<head>
    <title>Sửa thông tin người dùng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Sửa thông tin người dùng</h1>
        <form method="POST" action="index.php?action=edit&id=<?php echo $userData['id']; ?>">
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username" class="form-control" required
                    value="<?php echo $userData['username']; ?>">
            </div>

            <div class="form-group">
                <label>Gói đăng ký (Nhập số tháng)</label>
                <input type="text" name="subscription_plan" id="subscription_plan" class="form-control" pattern="[0-9]+"
                    title="Vui lòng nhập số tháng" required
                    value="<?php echo str_replace(' tháng', '', $userData['subscription_plan']); ?>"
                    placeholder="Ví dụ: 1, 2, 3,...">
            </div>

            <div class="form-group">
                <label>Tài khoản</label>
                <input type="text" name="account" class="form-control" required
                    value="<?php echo $userData['account']; ?>">
            </div>

            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required
                    value="<?php echo $userData['start_date']; ?>">
            </div>

            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="date" name="end_date" id="end_date" class="form-control" readonly required
                    value="<?php echo $userData['end_date']; ?>">
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" class="form-control" required>
                    <option value="còn hiệu lực"
                        <?php echo ($userData['status'] == 'còn hiệu lực') ? 'selected' : ''; ?>>
                        Còn hiệu lực
                    </option>
                    <option value="hết hiệu lực"
                        <?php echo ($userData['status'] == 'hết hiệu lực') ? 'selected' : ''; ?>>
                        Hết hiệu lực
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required
                    value="<?php echo $userData['email']; ?>">
            </div>

            <div class="form-group">
                <label>Link Facebook</label>
                <input type="url" name="facebook_link" class="form-control"
                    value="<?php echo $userData['facebook_link']; ?>">
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script>
    function calculateEndDate() {
        const startDate = document.getElementById('start_date').value;
        const monthsInput = document.getElementById('subscription_plan').value;
        const months = parseInt(monthsInput);

        if (startDate && !isNaN(months)) {
            const date = new Date(startDate);
            date.setMonth(date.getMonth() + months);

            // Đảm bảo định dạng ngày tháng đúng
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            document.getElementById('end_date').value = `${year}-${month}-${day}`;
        }
    }

    // Thêm sự kiện lắng nghe
    document.getElementById('start_date').addEventListener('change', calculateEndDate);
    document.getElementById('subscription_plan').addEventListener('input', calculateEndDate);

    // Kiểm tra input chỉ cho phép nhập số
    document.getElementById('subscription_plan').addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
    </script>
</body>

</html>