<!DOCTYPE html>
<html>

<head>
    <title>Người dùng sắp hết hạn</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Người dùng sắp hết hạn</h1>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Ngày kết thúc</th>
                    <th>Email</th>
                    <th>Link Facebook</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <?php if (!empty($row['facebook_link'])): ?>
                                <a href="<?php echo $row['facebook_link']; ?>" target="_blank">Link Facebook</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=edit&id=<?php echo $row['id']; ?>"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <button onclick="deleteUser(<?php echo $row['id']; ?>)"
                                class="btn btn-danger btn-sm">Xóa</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteUser(id) {
            if (confirm('Bạn có chắc chắn muốn xóa người dùng này không?')) {
                $.ajax({
                    url: 'index.php?action=delete&id=' + id,
                    type: 'GET',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Có lỗi xảy ra khi xóa người dùng');
                        console.error('Error:', error);
                    }
                });
            }
        }
    </script>
</body>

</html>