<!DOCTYPE html>
<html>

<head>
    <title>Sửa tài khoản</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Sửa tài khoản</h1>
        <form id="editAccountForm">
            <input type="hidden" name="id" value="<?php echo $accountData['id']; ?>">
            <div class="form-group">
                <label>Tên tài khoản</label>
                <input type="text" name="account_name" class="form-control" required
                    value="<?php echo $accountData['account_name']; ?>">
            </div>
            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="date" name="start_date" class="form-control" required
                    value="<?php echo $accountData['start_date']; ?>">
            </div>
            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="date" name="end_date" class="form-control" required
                    value="<?php echo $accountData['end_date']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#editAccountForm').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định
            $.ajax({
                url: 'index.php?action=ajaxEditAccount',
                type: 'POST',
                data: $(this).serialize(), // Lấy dữ liệu từ form
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert(result.success);
                        window.location.href = 'index.php'; // Quay lại trang danh sách
                    } else {
                        alert(result.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật tài khoản');
                }
            });
        });
    </script>
</body>

</html>