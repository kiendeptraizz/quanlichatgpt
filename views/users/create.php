<!DOCTYPE html>
<html>

<head>
    <title>Thêm mới người dùng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Thêm mới người dùng</h1>
        <form method="POST" action="index.php?action=create">
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Gói đăng ký (Nhập số tháng)</label>
                <input type="text" name="subscription_plan" id="subscription_plan" class="form-control" pattern="[0-9]+"
                    title="Vui lòng nhập số tháng" required>
            </div>

            <div class="form-group">
                <label>Tài khoản</label>
                <select name="account" id="account" class="form-control" required>
                    <option value="">Chọn tài khoản</option>
                    <?php
                    if (isset($availableAccounts)) {
                        while ($account = $availableAccounts->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($account['account_name']) . "'>" .
                                htmlspecialchars($account['account_name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="date" name="end_date" id="end_date" class="form-control" readonly required>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" class="form-control" required>
                    <option value="còn hiệu lực">Còn hiệu lực</option>
                    <option value="hết hiệu lực">Hết hiệu lực</option>
                </select>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Link Facebook</label>
                <input type="url" name="facebook_link" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set ngày hiện tại cho ngày bắt đầu
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').value = today;

            // Thêm các event listeners
            document.getElementById('start_date').addEventListener('change', function() {
                calculateEndDate();
            });

            document.getElementById('subscription_plan').addEventListener('input', function() {
                calculateEndDate();
            });

            // Khởi tạo ban đầu
            calculateEndDate();
        });

        function calculateEndDate() {
            const startDate = document.getElementById('start_date').value;
            const monthsInput = document.getElementById('subscription_plan').value;
            const months = parseInt(monthsInput);

            if (startDate && !isNaN(months)) {
                const date = new Date(startDate);
                date.setMonth(date.getMonth() + months);
                const endDate = date.toISOString().split('T')[0];
                document.getElementById('end_date').value = endDate;
            }
        }
    </script>
</body>

</html>