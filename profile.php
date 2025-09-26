<?php
session_start();

if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['full_name'];

// Sample dummy posts for feed
$posts = [
    [
        'user' => 'Alice Johnson',
        'content' => 'Excited to announce I just started a new role at TechCorp!',
        'time' => '2 hours ago',
    ],
    [
        'user' => 'Bob Smith',
        'content' => 'Just finished a great book on modern PHP practices.',
        'time' => '5 hours ago',
    ],
    [
        'user' => 'Carol Lee',
        'content' => 'Looking forward to the upcoming tech conference next month.',
        'time' => '1 day ago',
    ],
];

// Sample dummy news/trends
$news = [
    'LinkedIn hits 1 billion users worldwide',
    'Remote work trends in 2025',
    'Top 10 programming languages in demand',
    'How AI is shaping the future of work',
];
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LinkedIn Style Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #121212;
      color: #e1e9f0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    nav {
      background-color: #212121;
    }
    .nav-link-active {
      border-bottom: 3px solid #0a66c2;
      color: #0a66c2 !important;
    }
    .nav-link:hover {
      color: #0a66c2 !important;
    }
    /* Scrollbars for feed and news panel */
    .scrollable {
      overflow-y: auto;
      max-height: calc(100vh - 64px);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="flex items-center justify-between px-6 py-3 sticky top-0 z-50 shadow-md">
    <div class="flex items-center space-x-4">
      <!-- LinkedIn Logo -->
      <a href="#" class="text-cyan-400 text-3xl font-bold">in</a>

      <!-- Search Bar -->
      <div class="relative hidden md:block">
        <input type="text" placeholder="Search" class="bg-gray-800 rounded-full pl-10 pr-4 py-1 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-400" />
        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
      </div>
    </div>

    <!-- Nav Links -->
    <ul class="hidden md:flex space-x-8 text-gray-400 font-semibold uppercase tracking-wide">
      <li>
        <a href="#" class="nav-link nav-link-active flex flex-col items-center text-sm">
          <i class="fas fa-home text-xl mb-1"></i>
          Home
        </a>
      </li>
      <li>
        <a href="#" class="nav-link flex flex-col items-center text-sm">
          <i class="fas fa-user-friends text-xl mb-1"></i>
          My Network
        </a>
      </li>
      <li>
        <a href="#" class="nav-link flex flex-col items-center text-sm">
          <i class="fas fa-briefcase text-xl mb-1"></i>
          Jobs
        </a>
      </li>
      <li>
        <a href="#" class="nav-link flex flex-col items-center text-sm">
          <i class="fas fa-comment-dots text-xl mb-1"></i>
          Messaging
        </a>
      </li>
      <li>
        <a href="#" class="nav-link flex flex-col items-center text-sm">
          <i class="fas fa-bell text-xl mb-1"></i>
          Notifications
        </a>
      </li>
      <li>
        <a href="#" class="nav-link flex flex-col items-center text-sm">
          <i class="fas fa-user-circle text-2xl mb-1"></i>
          Me
        </a>
      </li>
    </ul>

    <!-- Right: Logout -->
    <div>
      <a href="logout.php" class="text-gray-400 hover:text-red-600 font-semibold text-sm">Logout</a>
    </div>
  </nav>

  <!-- Main content 3-column layout -->
  <div class="flex flex-grow max-w-7xl mx-auto px-4 md:px-6 py-6 gap-6">

    <!-- Left Sidebar: Profile -->
    <aside class="hidden md:flex flex-col w-64 bg-gray-900 rounded-lg p-6 shadow-lg sticky top-20 self-start h-[calc(100vh-80px)]">
      <div class="flex flex-col items-center mb-6">
        <!-- Small Profile Circle -->
        <div class="w-16 h-16 rounded-full bg-cyan-600 flex items-center justify-center text-3xl font-bold uppercase text-gray-100 select-none mb-3">
          <?php echo htmlspecialchars(substr($username, 0, 1)); ?>
        </div>
        <h2 class="text-xl font-bold text-gray-100 mb-1 text-center"><?php echo htmlspecialchars($username); ?></h2>
        <p class="text-cyan-400 text-center font-semibold text-sm">Full Stack Developer</p>
        <p class="text-gray-400 text-center text-xs mt-1">San Francisco, CA</p>
      </div>
      <p class="text-gray-300 text-sm text-center px-2">
        Passionate developer with experience building scalable web applications.
      </p>
    </aside>

    <!-- Center Feed -->
    <main class="flex-grow bg-gray-900 rounded-lg p-6 shadow-lg overflow-y-auto max-h-[calc(100vh-80px)]">

      <h1 class="text-3xl font-extrabold mb-6 text-cyan-400">Home Feed</h1>

      <?php foreach ($posts as $post): ?>
        <article class="bg-gray-800 rounded-lg p-5 mb-6 shadow hover:shadow-cyan-600 transition-shadow cursor-pointer">
          <header class="flex items-center mb-4 space-x-4">
            <div class="w-12 h-12 rounded-full bg-cyan-600 flex items-center justify-center text-xl font-bold uppercase text-gray-100 select-none">
              <?php echo htmlspecialchars(substr($post['user'], 0, 1)); ?>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-100"><?php echo htmlspecialchars($post['user']); ?></h3>
              <time class="text-sm text-gray-400"><?php echo htmlspecialchars($post['time']); ?></time>
            </div>
          </header>
          <p class="text-gray-300"><?php echo htmlspecialchars($post['content']); ?></p>
          <div class="mt-4 flex space-x-6 text-gray-400 text-sm">
            <button class="hover:text-cyan-400 flex items-center space-x-2">
              <i class="fas fa-thumbs-up"></i><span>Like</span>
            </button>
            <button class="hover:text-cyan-400 flex items-center space-x-2">
              <i class="fas fa-comment"></i><span>Comment</span>
            </button>
            <button class="hover:text-cyan-400 flex items-center space-x-2">
              <i class="fas fa-share"></i><span>Share</span>
            </button>
          </div>
        </article>
      <?php endforeach; ?>

    </main>

    <!-- Right Sidebar: News -->
    <aside class="hidden lg:flex flex-col w-64 bg-gray-900 rounded-lg p-6 shadow-lg sticky top-20 self-start h-[calc(100vh-80px)]">
      <h2 class="text-2xl font-extrabold mb-6 text-cyan-400">Trending News</h2>
      <ul class="space-y-4 text-gray-300 text-sm">
        <?php foreach ($news as $item): ?>
          <li class="hover:text-cyan-400 cursor-pointer border-b border-gray-700 pb-2">
            <i class="fas fa-bolt text-yellow-400 mr-2"></i><?php echo htmlspecialchars($item); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

  </div>

</body>
</html>
