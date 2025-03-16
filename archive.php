<?php
include("db_config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archiv geänderter Überschriften</title>
    <link rel="icon" type="image/png" href="https://verworfen.at/favicon.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Archiv geänderter Überschriften</h1>

Hier finden Sie das Archiv aller Überschriftenänderungen bei news.orf.at - sortiert nach Monaten seit dem Projektstart am 9. März 2025.

<ul>
<?php
$query = "SELECT DISTINCT DATE_FORMAT(detected_at, '%Y-%m') AS month 
          FROM headline_changes ORDER BY month DESC";
$months = $conn->query($query);

while ($month = $months->fetch_assoc()) {
    echo "<li><a href='archives/{$month['month']}.php'>{$month['month']}</a></li>";
}
?>
</ul>

<a href="index.php">Zurück zur Startseite</a>

</body>
</html>

<?php
$conn->close();
?>
