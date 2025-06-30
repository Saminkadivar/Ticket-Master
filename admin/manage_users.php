<?php  
    include('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/Style1.css"> <!-- Ensure this file contains your styles -->

    <style>
   
    </style>
</head>
<body>

    <?php include('includes/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <div class="card">
                <br>
                <h2 class="mb-4 text-center">Manage Users</h2>

                <?php
                // Handle delete request
                if (isset($_GET['delete'])) {
                    $id = $_GET['delete'];
                    $conn->query("DELETE FROM users WHERE User_id=$id");
                    header("Location: manage_users.php");
                }

                // Fetch users
                $sql = "SELECT User_id, First_name, Last_name, email, Phone_no, City, created_at FROM users";
                $result = $conn->query($sql);
                ?>

                <div class="table-container">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone No</th>
                                    <th>City</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['User_id']; ?></td>
                                        <td><?php echo $row['First_name']; ?></td>
                                        <td><?php echo $row['Last_name']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['Phone_no']; ?></td>
                                        <td><?php echo $row['City']; ?></td>
                                        <td><?php echo $row['created_at']; ?></td>
                                        <td class="actions">
                                            <!-- <a href="edit_user.php?id=<?php// echo $row['User_id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a> -->
                                            <a href="manage_users.php?delete=<?php echo $row['User_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">No users found.</div>
                    <?php endif; ?>

                    <?php $conn->close(); ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
