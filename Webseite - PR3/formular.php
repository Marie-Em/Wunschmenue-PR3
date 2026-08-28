<?php

$host = "localhost";
$dbname = "menuewuensche";
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Zieladresse für Benachrichtigungen
$empfaenger = "info@beispiel.de";

// Funktion für den Mailversand
function sendmenuemail($empfaenger, $kueche, $vorname, $nachname, $menueauswahl, $ernaehrungsbesonderheiten, $andere_besonderheiten, $mitteilung) {

    $betreff = "Neue Menüauswahl ($kueche) von $vorname $nachname";

    $nachrichtentext = "Es wurde eine neue Menüauswahl eingereicht:\n\n";
    $nachrichtentext .= "Küche: $kueche\n";
    $nachrichtentext .= "Vorname: $vorname\n";
    $nachrichtentext .= "Nachname: $nachname\n";
    $nachrichtentext .= "Menüauswahl: $menueauswahl\n";
    $nachrichtentext .= "Ernährungsbesonderheiten: " . ($ernaehrungsbesonderheiten ?: "keine") . "\n";
    $nachrichtentext .= "Andere Besonderheiten: " . ($andere_besonderheiten ?: "keine") . "\n";
    $nachrichtentext .= "Mitteilung: " . ($mitteilung ?: "keine") . "\n";

    $headers = "From: webformular@localhost\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($empfaenger, $betreff, $nachrichtentext, $headers);
}


// Wenn Französisches Menü-Formular abgeschickt wurde
if (isset($_POST["FAbschicken"])) {

    // Eingaben aus dem Formular holen
    $vorname = $mysqli->escape_string($_POST["vorname"]);
    $nachname = $mysqli->escape_string($_POST["nachname"]);
    $menueauswahl = $mysqli->escape_string($_POST["FMenueauswahl"]);
    $andere_besonderheiten = $mysqli->escape_string($_POST["andere_besonderheiten"]);
    $mitteilung = $mysqli->escape_string($_POST["mitteilung"]);

    // Checkboxen auslesen
    if (isset($_POST["FErnaehrungsbesonderheiten"])) {

        $ernaehrungsbesonderheiten = $_POST["FErnaehrungsbesonderheiten"];

        foreach ($ernaehrungsbesonderheiten as $key => $wert) {
            $ernaehrungsbesonderheiten[$key] = $mysqli->escape_string($wert);
        }

        $ernaehrungsbesonderheiten = implode(", ", $ernaehrungsbesonderheiten);

    } else {
        $ernaehrungsbesonderheiten = "";
    }

    $sql = "INSERT INTO french_menues
            (vorname, nachname, menueauswahl, ernaehrungsbesonderheiten,
             andere_besonderheiten, mitteilung)
            VALUES
            ('$vorname', '$nachname', '$menueauswahl',
             '$ernaehrungsbesonderheiten', '$andere_besonderheiten',
             '$mitteilung')";

    if ($mysqli->query($sql)) {

        // Mailversand nach erfolgreichem Insert in die Datenbank
        $mailErfolg = sendmenuemail($empfaenger, "Französische Küche", $vorname, $nachname, $menueauswahl, $ernaehrungsbesonderheiten, $andere_besonderheiten, $mitteilung);

        echo "<h1>Vielen Dank!</h1>";
        echo "<p>Ihre Menüauswahl wurde erfolgreich gespeichert.</p>";

        if (!$mailErfolg) {
            echo "<p><em>Hinweis: Die Benachrichtigungs-E-Mail konnte nicht versendet werden.</em></p>";
        }

    } else {

        echo "<h1>Fehler!</h1>";
        echo "<p>Die Daten konnten nicht gespeichert werden.</p>";
        echo "<p>Fehlermeldung: " . $mysqli->error . "</p>";
    }


// Wenn Italienisches Menü-Formular abgeschickt wurde
} elseif (isset($_POST["IAbschicken"])) {

    $vorname = $mysqli->escape_string($_POST["vorname"]);
    $nachname = $mysqli->escape_string($_POST["nachname"]);
    $menueauswahl = $mysqli->escape_string($_POST["IMenueauswahl"]);
    $andere_besonderheiten = $mysqli->escape_string($_POST["andere_besonderheiten"]);
    $mitteilung = $mysqli->escape_string($_POST["mitteilung"]);

    if (isset($_POST["IErnaehrungsbesonderheiten"])) {

        $ernaehrungsbesonderheiten = $_POST["IErnaehrungsbesonderheiten"];

        foreach ($ernaehrungsbesonderheiten as $key => $wert) {
            $ernaehrungsbesonderheiten[$key] = $mysqli->escape_string($wert);
        }

        $ernaehrungsbesonderheiten = implode(", ", $ernaehrungsbesonderheiten);

    } else {
        $ernaehrungsbesonderheiten = "";
    }

    $sql = "INSERT INTO italian_menues
            (vorname, nachname, menueauswahl, ernaehrungsbesonderheiten,
             andere_besonderheiten, mitteilung)
            VALUES
            ('$vorname', '$nachname', '$menueauswahl',
             '$ernaehrungsbesonderheiten', '$andere_besonderheiten',
             '$mitteilung')";

    if ($mysqli->query($sql)) {

        // Mailversand nach erfolgreichem Insert in die Datenbank
        $mailErfolg = sendmenuemail($empfaenger, "Italienische Küche", $vorname, $nachname, $menueauswahl, $ernaehrungsbesonderheiten, $andere_besonderheiten, $mitteilung);

        echo "<h1>Vielen Dank!</h1>";
        echo "<p>Ihre Menüauswahl wurde erfolgreich gespeichert.</p>";

        if (!$mailErfolg) {
            echo "<p><em>Hinweis: Die Benachrichtigungs-E-Mail konnte nicht versendet werden.</em></p>";
        }

    } else {

        echo "<h1>Fehler!</h1>";
        echo "<p>Die Daten konnten nicht gespeichert werden.</p>";
        echo "<p>Fehlermeldung: " . $mysqli->error . "</p>";
    }

} else {

    echo "<p>Das Formular wurde nicht abgeschickt.</p>";
}

$mysqli->close();

?>