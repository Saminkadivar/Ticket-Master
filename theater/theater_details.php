<?php  
include('includes/connection.php');
include 'includes/sidebar.php';
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Theater Owners</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Manage Theater Owners</h2>
        
        <?php
        // Handle delete request
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $conn->query("DELETE FROM theaters WHERE id=$id");
            header("Location: manage_theater_owners.php");
        }

        // Fetch theater owners
        $sql = "SELECT id, name, owner_name, owner_email, owner_phone, owner_address, status, created_at FROM theaters";
        $result = $conn->query($sql);
        ?>
   
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Theater Name</th>
                    <th>Owner Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['owner_name']; ?></td>
                        <td><?php echo $row['owner_email']; ?></td>
                        <td><?php echo $row['owner_phone']; ?></td>
                        <td><?php echo $row['owner_address']; ?></td>
                        <td>
                            <span class="badge <?php echo ($row['status'] == 'active') ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <a href="edit_theater_owner.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="manage_theater_owners.php?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php $conn->close(); ?>
    </div>
</body>
</html>
