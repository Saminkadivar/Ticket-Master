<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Owner Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('../admin/uploads/p5.jpg') no-repeat center center / cover;
            font-family: 'Arial', sans-serif;
            position: relative;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 31, 63, 0.85);
            z-index: -1;
        }

        .container {
            max-width: 500px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        h2 {
            color: #ffffff;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
            color: #ffffff;
        }

        .form-control {
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .form-control:focus {
            box-shadow: 0 0 5px rgba(0, 31, 63, 0.8);
            border-color: #001F3F;
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-custom {
            background-color: #001F3F;
            color: white;
            font-weight: bold;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            transition: 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #002855;
        }

        .text-link {
            color: #ffffff;
            text-decoration: none;
        }

        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container mt-5">
        <a href="index.php" class="back-arrow text-link"><i class="bi bi-arrow-left-circle"></i> Back to Home</a>
        <h2>Theater Owner Registration</h2>
        <form action="request_theater_account.php" method="POST">
            <div class="mb-3">
                <input type="text" name="owner_name" class="form-control" placeholder="Owner Name" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="owner_phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Theater Name" required>
            </div>
            <div class="mb-3">
                <input type="text" name="location" class="form-control" placeholder="Location" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="request_theater_account" class="btn btn-custom w-100">Request Account</button>
            <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-link">Login</a></p>
        </form>
    </div>
</body>
</html>
