<?php
// Database connection settings
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

// Escapes output for safe HTML display (also handles NULL values)
function h($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<p class="error">Could not connect to the database.</p>';
    exit;
}

// Read GET parameters; an empty country matches everything
$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$lookup  = isset($_GET['lookup']) ? $_GET['lookup'] : '';
$search  = "%$country%";

if ($lookup === 'cities') {
    // Join cities to countries so we can search by country name
    $stmt = $conn->prepare("SELECT c.name, c.district, c.population
                            FROM cities c
                            JOIN countries cs ON c.country_code = cs.code
                            WHERE cs.name LIKE :country
                            ORDER BY c.population DESC");
    $stmt->execute([':country' => $search]);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Prepared statement guards against SQL injection
    $stmt = $conn->prepare("SELECT name, continent, independence_year, head_of_state
                            FROM countries
                            WHERE name LIKE :country");
    $stmt->execute([':country' => $search]);
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php if ($lookup === 'cities'): ?>

  <?php if (empty($cities)): ?>
    <p class="status">No cities found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>District</th>
          <th>Population</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cities as $city): ?>
          <tr>
            <td><?= h($city['name']); ?></td>
            <td><?= h($city['district']); ?></td>
            <td><?= h($city['population']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

<?php else: ?>

  <?php if (empty($countries)): ?>
    <p class="status">No countries found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Continent</th>
          <th>Independence</th>
          <th>Head of State</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($countries as $row): ?>
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

<?php endif; ?>