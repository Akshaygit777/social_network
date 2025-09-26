<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$postStmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$postStmt->execute([$_SESSION['user_id']]);
$posts = $postStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-6">


  <div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-cyan-400">Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
    <a href="logout.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700 font-semibold">Logout</a>
  </div>


  <div class="bg-gray-800 p-6 rounded-lg mb-10 shadow">
    <h2 class="text-2xl font-bold mb-4">Your Profile</h2>
    <div class="flex items-center space-x-6">
      <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover border-2 border-cyan-400" />
      <div>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?></p>
   
      </div>
    </div>
  </div>

  <!-- Post Form -->
  <div class="bg-gray-800 p-6 rounded-lg shadow mb-10">
    <h2 class="text-2xl font-bold mb-4">Create a Post</h2>
    <form id="postForm" enctype="multipart/form-data" method="post" action="ajax/add_post.php">
      <div class="mb-4">
        <label for="description" class="block font-semibold mb-2">Description</label>
        <textarea name="description" id="description" class="w-full p-3 rounded bg-gray-700 text-white" rows="3" required></textarea>
      </div>
      <div class="mb-4">
        <label for="image" class="block font-semibold mb-2">Post Image</label>
        <input type="file" name="image" id="image" accept="image/*" class="text-white" required />
      </div>
      <button type="submit" class="bg-cyan-500 px-6 py-2 rounded font-semibold hover:bg-cyan-600">Post</button>
    </form>
  </div>


  <div>
    <h2 class="text-2xl font-bold mb-4">Your Posts</h2>
    <div id="postList" class="space-y-6">
      <?php foreach ($posts as $post): ?>
        <div class="bg-gray-800 p-4 rounded-lg shadow">
          <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="w-full h-60 object-cover rounded mb-4">
          <p class="text-lg"><?= htmlspecialchars($post['description']) ?></p>
          <div class="mt-4 flex items-center justify-between">
            <div>
              <button class="like-btn bg-green-600 hover:bg-green-700 px-3 py-1 rounded" data-id="<?= $post['id'] ?>">Like</button>
              <button class="dislike-btn bg-red-600 hover:bg-red-700 px-3 py-1 rounded" data-id="<?= $post['id'] ?>">Dislike</button>
            </div>
            <form method="post" action="ajax/remove_post.php" onsubmit="return confirm('Delete this post?')">
              <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
              <button type="submit" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Scripts for AJAX (placeholder, to be implemented later) -->
  <script>
    // AJAX for like/dislike (to be added in ajax/update_likes.php)
    $(".like-btn, .dislike-btn").click(function () {
      const postId = $(this).data("id");
      const type = $(this).hasClass("like-btn") ? "like" : "dislike";

      $.post("ajax/update_likes.php", { post_id: postId, action: type }, function (response) {
        alert("Updated: " + response);
        // Optional: Reload or update UI
      });
    });
  </script>

</body>
</html>
