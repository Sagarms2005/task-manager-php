<?php
// simple Task Manager - single file for clarity
$dbhost = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_NAME') ?: 'taskdb';
$dbuser = getenv('DB_USER') ?: 'root';
$dbpass = getenv('DB_PASS') ?: ''; // XAMPP default root has no password

try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo "<h2>DB connection error:</h2><pre>" . $e->getMessage() . "</pre>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && !empty(trim($_POST['title']))) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
        $stmt->execute(['title' => trim($_POST['title'])]);
        header("Location: /task-manager/");
        exit;
    } elseif ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => intval($_POST['id'])]);
        header("Location: /task-manager/");
        exit;
    }
}

// fetch tasks
$stmt = $pdo->query("SELECT id, title, created_at FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Task Manager</title>
  <style>
    body { font-family: Arial, sans-serif; max-width:700px;margin:30px auto; }
    input, button { padding:8px; }
    .task { border-bottom:1px solid #eee; padding:10px 0; display:flex; justify-content:space-between; }
  </style>
</head>
<body>
  <h1>Task Manager</h1>

  <form method="post" style="margin-bottom:20px">
    <input name="title" placeholder="New task title" style="width:70%" required>
    <input type="hidden" name="action" value="add">
    <button type="submit">Add</button>
  </form>

  <?php if (empty($tasks)): ?>
    <p>No tasks yet.</p>
  <?php else: ?>
    <?php foreach($tasks as $t): ?>
      <div class="task">
        <div>
          <strong><?=htmlspecialchars($t['title'])?></strong><br>
          <small><?=htmlspecialchars($t['created_at'])?></small>
        </div>
        <form method="post" style="margin:0">
          <input type="hidden" name="id" value="<?=intval($t['id'])?>">
          <input type="hidden" name="action" value="delete">
          <button type="submit">Delete</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
