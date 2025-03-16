<?php
include("db_config.php");
$conn->set_charset("utf8mb4");

$mode = isset($_GET['mode']) && $_GET['mode'] == "100" ? "100" : "20";
$limit = ($mode == "100") ? 100 : 20;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>verworfen.at | Geänderte Überschriften von ORF.at</title>
	
    <meta name="description" content="Verfolgen Sie automatisch erkannte Überschriften-Änderungen auf ORF.at in Echtzeit.">
    <meta name="keywords" content="ORF, Nachrichten, Überschriften, News, Änderungen, verworfen">
    <meta name="author" content="J.C. Zeller">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="Verfolgen Sie geänderte Headlines bei ORF.at - verworfen.at">
    <meta property="og:description" content="Sehen Sie die letzten Änderungen von Überschriften bei news.orf.at - Headline-Änderungen in Echtzeit.">
    <meta property="og:url" content="https://verworfen.at">
    <meta property="og:image" content="https://verworfen.at/og-image.png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Verfolgen Sie geänderte Headlines bei ORF.at - verworfen.at">
    <meta name="twitter:description" content="Sehen Sie die letzten Änderungen von Überschriften bei news.orf.at in Echtzeit.">
    <meta name="twitter:image" content="https://verworfen.at/og-image.png">

    <link rel="icon" type="image/png" href="https://verworfen.at/favicon.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Überschrift verworfen?</h1>
Regelmäßigen Leser*innen von <a href="https://news.orf.at" title="ORF Nachrichten" target="_blank">news.orf.at</a> wird bestimmt auffallen, dass sich die Schlagzeilen der Artikel dort häufig ändern. Manchmal ändert sich mit den Headlines auch der <a href="https://en.wikipedia.org/wiki/Spin_(propaganda)" title="Was ist Spin?" target="_blank">Spin</a>, mit dem ein bestimmtes Thema dargestellt wird.<br><br>
<b>Hier werden alle Überschriftenänderungen auf der Nachrichtenseite des ORF gesammelt und dokumentiert.</b> Alle 10 Minuten wird automatisch nach Änderungen gesucht und die Liste aktualisiert.
<br><br>
<h2>Die <?php echo $limit; ?> neuesten Headline-Änderungen</h2>

<form method="GET" action="index.php">
<span style="font-size: 12px;">
    <label>
        <strong>Filter:</strong> <input type="checkbox" name="filter" value="1" onchange="this.form.submit()" 
        <?php if(isset($_GET['filter']) && $_GET['filter'] == "1") echo "checked"; ?>>
        >5 Zeichen<font size="2" style="font-weight: normal"><sup><a href="#fn">1</a></font></sup>
    </label>
	<label>
        <input type="checkbox" name="hide_sport" value="1" onchange="this.form.submit()"
        <?php if(isset($_GET['hide_sport']) && $_GET['hide_sport'] == "1") echo "checked"; ?>>
        Sport ausblenden<font size="2" style="font-weight: normal"><sup><a href="#fn">2</a></font></sup>
    </label>
	
</span>
</form>

<table>

    <tr>
        <th>Datum</th>
        <th>Originale Überschrift</th>
        <th>Geänderte Überschrift</th>
        <th>Link<font size="2" style="font-weight: normal"><sup><a href="#fn">3</a></font></sup></th>
        <th>Änderung bemerkt</th>
    </tr>
    <?php
		$query = "SELECT original_headline, new_headline, article_url, detected_at 
				  FROM headline_changes 
				  WHERE original_headline != new_headline ";

		if (isset($_GET['filter']) && $_GET['filter'] == "1") {
			$query .= "AND levenshtein_distance > 5 ";
		}

		if (isset($_GET['hide_sport']) && $_GET['hide_sport'] == "1") {
			$query .= "AND article_url NOT LIKE '%sport.orf.at%' ";
		}

		$query .= "ORDER BY detected_at DESC LIMIT $limit";

		$result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . date("d.m.Y", strtotime($row['detected_at'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['original_headline'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['new_headline'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td><a href='" . htmlspecialchars($row['article_url'], ENT_QUOTES, 'UTF-8') . "' target='_blank'>Link</a></td>";
        echo "<td>" . date("H:i", strtotime($row['detected_at'])) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<span style="font-size: 12px;" id="fn">
1) Mit diesem Filter werden nur Überschriften angezeigt bei denen sich mehr als fünf Zeichen verändert haben.<br>
2) Blendet alle Überschriften von Artikeln aus, die auf sport.orf.at verlinken.<br>
3) Die Überschriften-Änderungen beziehen sich auf die Headlines, so wie sie auf der Übersichtsseite news.orf.at angezeigt werden. Die verlinkten individuellen Artikeln können davon abweichende Schlagzeilen aufweisen oder noch die originale Überschrift verwenden.<br>
4) Spekulationen dazu, inwiefern redaktionelle Entscheidungen durch externe Einflussnahme oder Message Control geprägt sind, sind den Besucher*innen dieser Seite überlassen.
</span></p>
<span>

<br>
<?php
$filterParam = isset($_GET['filter']) && $_GET['filter'] == '1' ? '&filter=1' : '';
$sportParam = isset($_GET['hide_sport']) && $_GET['hide_sport'] == '1' ? '&hide_sport=1' : '';
$queryParams = $filterParam . $sportParam;
?>
<a href="index.php?mode=100<?php echo $queryParams; ?>">Letzte 100 Änderungen anzeigen</a> | 
<a href="archive.php">Zum Archiv</a>

<br>
<br>

<?php
$query_24h = "SELECT COUNT(*) AS count_last_24h 
              FROM headline_changes 
              WHERE original_headline != new_headline 
              AND detected_at >= NOW() - INTERVAL 1 DAY";
$result_24h = $conn->query($query_24h);
$row_24h = $result_24h->fetch_assoc();
$count_last_24h = $row_24h['count_last_24h'];

$query_total = "SELECT COUNT(*) AS total_changes 
                FROM headline_changes 
                WHERE original_headline != new_headline";
$result_total = $conn->query($query_total);
$row_total = $result_total->fetch_assoc();
$total_changes = $row_total['total_changes'];
?>

<center><span style="font-size: 10px;"><strong>Statistik:</strong><br>Änderungen in den letzten 24 Stunden: <?php echo number_format($count_last_24h); ?><br>Insgesamt erfasste Änderungen:  <?php echo number_format($total_changes); ?></span></center>

<!-- Footer -->
<footer style="margin-top: 40px; font-size: 14px; color: #555; text-align: center;">
    <p><a href="methode.php">Erhebungsmethode</a><br><br>
	<a href="https://github.com/autootto69/headline-tracker/" target="_blank" title="Source Code bei GitHub">GitHub</a></p>
	</p>
	
	<strong>verworfen.at</strong> ist ein unabhängiges, unkommerzielles und ehrenamtliches Projekt für Medientransparenz und <b>steht in keinerlei Verbindung zum österreichischen Rundfunk (ORF)</b>. 
    <a href="about.php">Über den Initiator.</a></p>
    <p><strong>Erklärung zum Datenschutz:</strong> Diese Website setzt kein Tracking ein, speichert keine Cookies oder persönlichen Daten und nutzt keine Analysetools.</p>
	<p><strong>Haftungsausschluss:</strong> verworfen.at übernimmt keine Gewähr für die Richtigkeit, Vollständigkeit und Aktualität der Angaben auf dieser Website. Alle Informationen dienen ausschließlich allgemeinen Informationszwecken und haben keine rechtliche Verbindlichkeit.</p>
	<p><span style="font-size: 12px;"><strong>Impressum: </strong>Johannes C. Zeller | +43 664 42 63 151 | Sedella, Spain | <a href="mailto:jcz@jczeller.com">jcz@jczeller.com</a> | Gerichtsstandort: Málaga</span></p>
</footer>


</body>
</html>

<?php
$conn->close();
?>
