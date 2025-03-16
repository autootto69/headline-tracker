<?php
include("../db_config.php"); 
$conn->set_charset("utf8mb4");

$filter = isset($_GET["filter"]) && $_GET["filter"] == "1" ? "1" : "0";
$hide_sport = isset($_GET["hide_sport"]) && $_GET["hide_sport"] == "1" ? "1" : "0";
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archiv für 2025-03</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="https://verworfen.at/favicon.png">
</head>
<body>
    <h1>Archiv für 2025-03</h1>

    <!-- Filter Form -->
    <form method="GET" action="<?php echo basename(__FILE__); ?>">
        <span style="font-size: 12px;">
            <label>
                <strong>Filter:</strong> 
                <input type="checkbox" name="filter" value="1" onchange="this.form.submit()" <?php echo ($filter == "1") ? "checked" : ""; ?>>
                >5 Zeichen
            </label>
            <label>
                <input type="checkbox" name="hide_sport" value="1" onchange="this.form.submit()" <?php echo ($hide_sport == "1") ? "checked" : ""; ?>>
                Sport ausblenden
            </label>
        </span>
    </form>

    <table>
        <tr>
            <th>Datum</th>
            <th>Originale Überschrift</th>
            <th>Geänderte Überschrift</th>
            <th>Link</th>
            <th>Änderung bemerkt</th>
        </tr>
<?php
    $query = "SELECT original_headline, new_headline, article_url, detected_at 
              FROM headline_changes 
              WHERE DATE_FORMAT(detected_at, '%Y-%m') = '2025-03' 
              AND original_headline != new_headline ";

    if ($filter == "1") {
        $query .= "AND levenshtein_distance > 5 ";
    }

    if ($hide_sport == "1") {
        $query .= "AND article_url NOT LIKE '%sport.orf.at%' ";
    }

    $query .= "ORDER BY detected_at DESC";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . date("d.m.Y", strtotime($row["detected_at"])) . "</td>";
        echo "<td>" . htmlspecialchars($row["original_headline"], ENT_QUOTES, "UTF-8") . "</td>";
        echo "<td>" . htmlspecialchars($row["new_headline"], ENT_QUOTES, "UTF-8") . "</td>";
        echo "<td><a href='" . htmlspecialchars($row["article_url"], ENT_QUOTES, "UTF-8") . "' target='_blank'>Link</a></td>";
        echo "<td>" . date("H:i", strtotime($row["detected_at"])) . "</td>";
        echo "</tr>";
    }
?>
    </table>

    <br><a href="../archive.php?filter=<?php echo $filter; ?>&hide_sport=<?php echo $hide_sport; ?>">Zurück zum Archiv</a> | 
    <a href="../index.php?filter=<?php echo $filter; ?>&hide_sport=<?php echo $hide_sport; ?>">Zurück zur Startseite</a>

</body>
</html>
