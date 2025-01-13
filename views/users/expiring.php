<!DOCTYPE html>
<html>

<head>
    <title>Người dùng sắp hết hạn</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Người dùng sắp hết hạn</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Ngày kết thúc</th>
                    <th>Email</th>
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
                        <a href="index.php?action=edit&id=<?php echo $row['id']; ?>"
                            class="btn btn-warning btn-sm">Sửa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>