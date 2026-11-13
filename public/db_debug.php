<?php
require_once __DIR__ . '/../Model/DBConfig.php';
$db = new Database();
$conn = $db->connect();
$result = $conn->query("SELECT id, title, price, status, teacher_id, created_at, updated_at FROM courses ORDER BY id ASC");
if (!$result) {
    echo "DB error: " . $conn->error;
    exit;
}
echo "<h2>Courses (" . $result->num_rows . ")</h2>";
echo "<table border=1 cellpadding=6><tr><th>id</th><th>title</th><th>price</th><th>status</th><th>teacher_id</th><th>created_at</th><th>updated_at</th></tr>";
while ($r = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($r['id']) . "</td>";
    echo "<td>" . htmlspecialchars($r['title']) . "</td>";
    echo "<td>" . htmlspecialchars($r['price']) . "</td>";
    echo "<td>" . htmlspecialchars($r['status']) . "</td>";
    echo "<td>" . htmlspecialchars($r['teacher_id']) . "</td>";
    echo "<td>" . htmlspecialchars($r['created_at']) . "</td>";
    echo "<td>" . htmlspecialchars($r['updated_at']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>