<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config.php'; // Make sure config.php connects using PDO

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Social Network</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen text-gray-100 font-sans">

  <form method="post" class="bg-gray-800 p-10 rounded-xl shadow-xl w-full max-w-md">
    <h2 class="text-4xl font-extrabold mb-8 text-center text-cyan-400">Login</h2>

    <?php if (!empty($errors)): ?>
      <div class="mb-6 bg-red-700 p-4 rounded text-white font-semibold" role="alert">
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

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

    <input
      type="submit" value="Log In"
      class="w-full bg-cyan-500 hover:bg-cyan-600 text-gray-900 font-bold py-3 rounded cursor-pointer transition-colors"
    />

    <p class="mt-6 text-center text-sm text-gray-400">
      Don’t have an account? <a href="signup.php" class="text-cyan-400 hover:underline">Sign Up</a>
    </p>
  </form>

</body>
</html>
