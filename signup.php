<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DB Config
    $host = "localhost";
    $db   = "social_network";
    $user = "root";
    $pass = "";
    $charset = 'utf8mb4';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (Exception $e) {
        die("DB Connection Failed: " . $e->getMessage());
    }

    // Sanitize
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $age       = (int)($_POST['age'] ?? 0);

    // Validate
    if (strlen($full_name) < 3) $errors[] = "Full Name must be at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($age < 1 || $age > 120) $errors[] = "Enter a valid age.";

    // Check if email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email already registered.";
    }

    // Insert into DB
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, age) VALUES (?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $password_hash, $age]);

        $_SESSION['success'] = "Account created. You can now log in!";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">

  <form method="post" class="bg-gray-800 p-10 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-3xl font-bold text-center text-cyan-400 mb-6">Create Account</h2>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-600 p-4 rounded mb-4">
        <?php foreach ($errors as $error): ?>
          <div class="text-white"><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <label class="block mb-1 font-medium">Full Name</label>
    <input name="full_name" type="text" required class="w-full mb-4 p-3 rounded bg-gray-700 focus:ring focus:ring-cyan-400" value="<?php echo htmlspecialchars($_POST['full_name'] ?? '') ?>">

    <label class="block mb-1 font-medium">Email</label>
    <input name="email" type="email" required class="w-full mb-4 p-3 rounded bg-gray-700 focus:ring focus:ring-cyan-400" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">

    <label class="block mb-1 font-medium">Password</label>
    <input name="password" type="password" required class="w-full mb-4 p-3 rounded bg-gray-700 focus:ring focus:ring-cyan-400">

    <label class="block mb-1 font-medium">Age</label>
    <input name="age" type="number" min="1" max="120" required class="w-full mb-6 p-3 rounded bg-gray-700 focus:ring focus:ring-cyan-400" value="<?php echo htmlspecialchars($_POST['age'] ?? '') ?>">

    <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-black font-bold py-3 rounded transition">Sign Up</button>
    <p class="mt-6 text-center text-sm text-gray-400">
  Already have an account? <a href="login.php" class="text-cyan-400 hover:underline">Log In</a>
</p>
  
</form>

</body>
</html>
