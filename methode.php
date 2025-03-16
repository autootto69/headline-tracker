<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erhebungsmethode verworfen.at | So funktioniert das Projekt</title>
	
    <meta name="description" content="So werden die Daten bei verworfen.at erhoben">
    <meta name="keywords" content="ORF, Nachrichten, Überschriften, News, Änderungen, verworfen">
    <meta name="author" content="J.C. Zeller">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="Verfolgen Sie geänderte Headlines bei ORF.at - verworfen.at">
    <meta property="og:description" content="Sehen Sie die letzten Änderungen von Überschriften bei news.orf.at in Echtzeit.">
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

<h1>Methodik der Datenerhebung</h1>
So funktioniert verworfen.at:
<ul>
    <li><strong>Automatisierte Erfassung:</strong> Die Überschriftenänderungen auf verworfen.at werden automatisiert erfasst, indem die Startseite von <a href="https://news.orf.at" target="_blank">news.orf.at</a> regelmäßig analysiert wird.</li>
    
    <li><strong>Automatisierte Datensammlung:</strong> Alle 10 Minuten ruft ein Python-Skript den HTML-Code der ORF-Nachrichtenseite ab. Dies geschieht über eine reguläre HTTP-Anfrage mit der Bibliothek <code>requests</code>.</li>
    
    <li><strong>Extraktion der Schlagzeilen:</strong> Mit <code>BeautifulSoup</code> werden die Überschriften aus bestimmten HTML-Elementen ausgelesen. Dabei werden sowohl <code>&lt;h1&gt;</code>-Überschriften als auch <code>&lt;h3&gt;</code>-Elemente berücksichtigt - dies beinhaltet die bebilderten Top-Schlagzeilen, sowie die Artikel-Headlines darunter.</li>
	
    <li><strong>Speicherung und Abgleich:</strong> Die extrahierten Schlagzeilen werden in einer MySQL-Datenbank gespeichert. Dabei wird jede neue Überschrift mit den zuletzt gespeicherten Einträgen verglichen. Wenn sich eine Überschrift zu einem bereits bekannten Artikel geändert hat, wird diese Änderung mit einem Zeitstempel dokumentiert.</li>
    
    <li><strong>Darstellung der Änderungen:</strong> Die gesammelten Daten werden auf verworfen.at in einer Tabelle dargestellt. Nutzer*innen können die letzten 20 oder 100 Änderungen einsehen oder im Archiv nach Monaten sortiert stöbern.</li>
    
	<li><strong>Zeichenbasierter Filter:</strong> Um geringfügige Änderungen herauszufiltern, wie das Einfügen von Bindestrichen oder die Umstellung einzelner Wörter, nutzt verworfen.at eine <a href="https://gist.github.com/Kovah/df90d336478a47d869b9683766cff718" target="_blank" title="Levenshtein Function bei GitHub">SQL-Funktion zur Berechnung der Levenshtein-Distanz</a>. Diese Funktion bestimmt, wie viele Zeichenänderungen (Einfügungen, Löschungen oder Ersetzungen) erforderlich sind, um eine geänderte Überschrift in die ursprüngliche Version umzuwandeln. Nutzer*innen können über eine Filteroption wählen, ob nur Überschriftenänderungen mit einer Differenz von mehr als fünf Zeichen angezeigt werden sollen. Dadurch werden minimale stilistische Anpassungen ausgeblendet.</li>
    
	<li><strong>Ausblenden von Sportmeldungen:</strong> Da viele Nutzer*innen hauptsächlich an politischen, wirtschaftlichen oder gesellschaftlichen Themen interessiert sind, bietet verworfen.at eine Filteroption, um Sportmeldungen auszublenden. Dabei wird geprüft, ob die URL eines Artikels auf sport.orf.at verweist.</li>
 	
	<li><strong>Hinweis zu Formatänderungen:</strong> Die Überschriften der bebilderten Top-Stories auf der ORF-Nachrichtenseite folgen häufig dem Muster „<i>Thema XY: Konflikt zwischen A und B eskaliert</i>“. Wenn diese Artikel aus den Top-Stories verschwinden und in die Liste der regulären Schlagzeilen verschoben werden, ändert sich oft die Formulierung – meist ohne inhaltliche Änderung. Ein Beispiel: <i>„Konflikt zwischen A und B zu Thema XY eskaliert“</i>. Der Vollständigkeit halber werden auch solche rein stilistischen Anpassungen in der Liste der Überschriftenänderungen erfasst.</li>

    <li><strong>Einschränkungen:</strong> Da ausschließlich die öffentlich sichtbaren Überschriften auf der ORF-Startseite analysiert werden, können Änderungen innerhalb von Artikeln oder auf Unterseiten nicht erfasst werden. Es kann vorkommen, dass die auf der Startseite verlinkten Artikeln andere Überschriften aufweisen als der Beitragstitel auf der Übersichtsseite. Zudem ist nicht ausgeschlossen, dass sich technische Änderungen auf ORF.at auf die Erfassungsmethode auswirken.</li>
</ul>
<br>
<strong>verworfen.at befindet sich noch in der Testphase und Details der Erhebungsmethode können sich ändern.</strong> Nach Abschluss des Debuggings soll der Code als Open-Source-Projekt auf GitHub veröffentlicht werden. Wenn Sie Fehler finden oder Verbesserungsvorschläge haben, <a href="mailto:jcz@jczeller.com">nehmen Sie bitte Kontakt auf</a>.
<br><br>
<a href="index.php">Zurück zur Startseite</a>

</body>
</html>

<?php
$conn->close();
?>
