<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection (adjust your credentials)
    $host = "localhost";
    $db   = "social_network";
    $user = "root";
    $pass = "";
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    // Sanitize inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = (int)($_POST['age'] ?? 0);

    // Validate inputs
    if (strlen($full_name) < 3) {
        $errors[] = "Full Name must be at least 3 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($age < 1 || $age > 120) {
        $errors[] = "Age must be between 1 and 120.";
    }

    // Check if email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        }
    }

    // Handle profile picture upload
    $profile_pic_path = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file = $_FILES['profile_pic'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading profile picture.";
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed_types)) {
            $errors[] = "Profile picture must be a JPG, PNG, or GIF image.";
        } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = "Profile picture must be less than 2MB.";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe_name = uniqid('profile_', true) . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $destination = $upload_dir . $safe_name;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = "Failed to save profile picture.";
            } else {
                // Store relative path for DB
                $profile_pic_path = 'uploads/' . $safe_name;
            }
        }
    }

    if (empty($errors)) {
        // Hash password securely
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into DB
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, age, profile_pic) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $password_hash, $age, $profile_pic_path]);

        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: login.php"); // Redirect to login page (create it separately)
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - Social Network</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen text-gray-100 font-sans">

  <form method="post" enctype="multipart/form-data" class="bg-gray-800 p-10 rounded-xl shadow-xl w-full max-w-md">
    <h2 class="text-4xl font-extrabold mb-8 text-center text-cyan-400">Sign Up</h2>

    <?php if (!empty($errors)): ?>
      <div class="mb-6 bg-red-700 p-4 rounded text-white font-semibold" role="alert">
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <label for="full_name" class="block mb-2 font-semibold">Full Name</label>
    <input
      type="text" name="full_name" id="full_name" required
      class="w-full mb-5 px-4 py-3 rounded bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-400"
      value="<?php echo htmlspecialchars($_POST['full_name'] ?? '') ?>"
    />

    <label for="email" class="block mb-2 font-semibold">Email</label>
    <input
      type="email" name="email" id="email" required
      class="w-full mb-5 px-4 py-3 rounded bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-400"
      value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>"
    />

    <label for="password" class="block mb-2 font-semibold">Password</label>
    <input
      type="password" name="password" id="password" required
      class="w-full mb-5 px-4 py-3 rounded bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-400"
    />

    <label for="age" class="block mb-2 font-semibold">Age</label>
    <input
      type="number" name="age" id="age" min="1" max="120" required
      class="w-full mb-5 px-4 py-3 rounded bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-400"
      value="<?php echo htmlspecialchars($_POST['age'] ?? '') ?>"
    />

    <label for="profile_pic" class="block mb-2 font-semibold">Profile Picture</label>
    <input
      type="file" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/gif"
      class="w-full mb-8 text-gray-300"
    />

    <input
      type="submit" value="Sign Up"
      class="w-full bg-cyan-500 hover:bg-cyan-600 text-gray-900 font-bold py-3 rounded cursor-pointer transition-colors"
    />
  </form>

</body>
</html>
