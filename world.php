<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

function h($value) {
  return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$country = isset($_GET['country']) ? trim($_GET['country']) : '';

$stmt = $conn->prepare("SELECT name, continent, independence_year, head_of_state
                         FROM countries WHERE name LIKE :country");
$stmt->execute([':country' => "%$country%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (empty($results)): ?>
  <p class="status">No countries found.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>continent</th>
        <th>Independence</th>
        <th>Head of State</th>
      </tr>
    </thead>
  <tbody>
    <?php foreach ($results as $row): ?>
      <tr>
        <td><?= h($row['name']); ?></td>
        <td><?= h($row['continent']); ?></td>
        <td><?= h($row['independence_year']); ?></td>
        <td><?= h($row['head_of_state']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  </table>
<?php endif; ?>
    