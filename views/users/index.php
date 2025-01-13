<!DOCTYPE html>
<html>

<head>
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    .table th,
    .table td {
        vertical-align: middle;
    }

    .status-active {
        color: green;
        font-weight: bold;
    }

    .status-expired {
        color: red;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Danh sách người dùng</h1>
            <div>
                <a href="index.php?action=create" class="btn btn-primary">Thêm người dùng</a>
                <a href="index.php?action=getExpiringUsers" class="btn btn-warning">Người dùng sắp hết hạn</a>
                <button type="button" class="btn btn-success" data-toggle="collapse" data-target="#accountManagement">
                    Quản lý tài khoản
                </button>
            </div>
        </div>

        <!-- Phần quản lý tài khoản -->
        <div id="accountManagement" class="collapse mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quản lý tài khoản</h5>
                </div>
                <div class="card-body">
                    <!-- Form thêm tài khoản mới -->
                    <form action="index.php?action=addAccounts" method="POST" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Chọn tháng</label>
                                    <input type="month" id="monthYearSelect" name="month_year" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ngày bắt đầu</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ngày kết thúc</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Danh sách tài khoản (mỗi dòng một tài khoản)</label>
                            <textarea name="accounts" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm tài khoản</button>
                    </form>

                    <!-- Danh sách tài khoản -->
                    <h6>Danh sách tài khoản trong tháng</h6>
                    <div class="row" id="accountsList">
                        <!-- Danh sách tài khoản sẽ được thêm vào đây bằng JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách người dùng -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Gói đăng ký</th>
                    <th>Tài khoản</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Email</th>
                    <th>Facebook</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['subscription_plan']; ?></td>
                    <td><?php echo $row['account']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></td>
                    <td>
                        <span class="status-<?php echo ($row['status'] == 'còn hiệu lực') ? 'active' : 'expired'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <?php if ($row['facebook_link']): ?>
                        <a href="<?php echo $row['facebook_link']; ?>" target="_blank">Link</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                            onclick="editAccount(<?php echo $row['id']; ?>)">Sửa</button>
                        <button class="btn btn-danger btn-sm"
                            onclick="deleteAccount(<?php echo $row['id']; ?>)">Xóa</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    function loadAccounts(monthYear) {
        fetch(`index.php?action=getAvailableAccounts&month_year=${monthYear}-01`)
            .then(response => response.json())
            .then(accounts => {
                const accountsList = document.getElementById('accountsList');
                accountsList.innerHTML = '';

                if (accounts && accounts.length > 0) {
                    accounts.forEach(account => {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';
                        col.innerHTML = `
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${account.account_name}</h6>
                                    <p class="card-text">
                                        <small>Bắt đầu: ${new Date(account.start_date).toLocaleDateString()}</small><br>
                                        <small>Kết thúc: ${new Date(account.end_date).toLocaleDateString()}</small><br>
                                        <small>Số người dùng: ${account.user_count || 0}</small><br>
                                        <span class="badge badge-${account.status === 'available' ? 'success' : 'danger'}">
                                            ${account.status === 'available' ? 'Khả dụng' : 'Đã sử dụng'}
                                        </span>
                                    </p>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-sm" onclick="viewUsers('${account.account_name}', '${monthYear}-01')">
                                            Xem người dùng
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editAccount(${account.id})">
                                            Sửa
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteAccount(${account.id})">
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        accountsList.appendChild(col);
                    });
                } else {
                    accountsList.innerHTML =
                        '<div class="col-12"><p class="text-muted">Không có tài khoản nào trong tháng này</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                accountsList.innerHTML =
                    '<div class="col-12"><p class="text-danger">Có lỗi xảy ra khi tải danh sách tài khoản</p></div>';
            });
    }

    function viewUsers(accountName, monthYear) {
        console.log('Viewing users for:', accountName, monthYear); // Debug log
        fetch(
                `index.php?action=getAccountUsers&account_name=${encodeURIComponent(accountName)}&month_year=${monthYear}`
            )
            .then(response => response.json())
            .then(users => {
                const modalContent = `
                    <div class="modal fade" id="usersModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Người dùng của tài khoản ${accountName}</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ${users.length > 0 ? `
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tên</th>
                                                    <th>Email</th>
                                                    <th>Ngày bắt đầu</th>
                                                    <th>Ngày kết thúc</th>
                                                    <th>Trạng thái</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${users.map(user => `
                                                    <tr>
                                                        <td>${user.username}</td>
                                                        <td>${user.email}</td>
                                                        <td>${new Date(user.start_date).toLocaleDateString()}</td>
                                                        <td>${new Date(user.end_date).toLocaleDateString()}</td>
                                                        <td>${user.status}</td>
                                                        <td>
                                                            <a href="index.php?action=edit&id=${user.id}" class="btn btn-warning btn-sm">Sửa</a>
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    ` : '<p class="text-muted">Không có người dùng nào</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Thêm modal vào body và hiển thị
                const modalWrapper = document.createElement('div');
                modalWrapper.innerHTML = modalContent;
                document.body.appendChild(modalWrapper);
                $('#usersModal').modal('show');

                // Xóa modal khi đóng
                $('#usersModal').on('hidden.bs.modal', function() {
                    this.remove();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải danh sách người dùng');
            });
    }

    function editAccount(id) {
        console.log('Editing account:', id); // Debug log
        window.location.href = `index.php?action=editAccount&id=${id}`;
    }

    function deleteAccount(id) {
        console.log('Deleting account:', id); // Debug log
        if (confirm('Bạn có chắc chắn muốn xóa tài khoản này?')) {
            window.location.href = `index.php?action=deleteAccount&id=${id}`;
        }
    }

    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        const currentDate = new Date();
        const currentMonthYear =
            `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
        loadAccounts(currentMonthYear);

        // Thêm event listener cho input tháng
        document.getElementById('monthYearSelect').addEventListener('change', function() {
            loadAccounts(this.value);
        });
    });
    </script>
</body>

</html>