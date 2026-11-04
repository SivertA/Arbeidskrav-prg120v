<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer klasse</title>
</head>
<body>

<?php
/*
 * registrer-klasse
 * Viser et skjema og registrerer klassekode,
 * klassenavn og studiumkode i databasen.
 */
?>

<h3>Registrer klasse</h3>

<form method="post" action="" id="registrerKlasseSkjema">
    <label for="klassekode">Klassekode</label>
    <input type="text" id="klassekode" name="klassekode"
           maxlength="5" required>
    <br>

    <label for="klassenavn">Klassenavn</label>
    <input type="text" id="klassenavn" name="klassenavn"
           maxlength="50" required>
    <br>

    <label for="studiumkode">Studiumkode</label>
    <input type="text" id="studiumkode" name="studiumkode"
           maxlength="50" required>
    <br>

    <input type="submit" value="Registrer klasse"
           id="registrerKlasseKnapp" name="registrerKlasseKnapp">
    <input type="reset" value="Nullstill" id="nullstill">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $klassekode = trim($_POST["klassekode"] ?? "");
    $klassenavn = trim($_POST["klassenavn"] ?? "");
    $studiumkode = trim($_POST["studiumkode"] ?? "");

    if ($klassekode === "" || $klassenavn === "" || $studiumkode === "") {
        print("<p>Alle feltene må fylles ut.</p>");
    } elseif (
        mb_strlen($klassekode, "UTF-8") > 5 ||
        mb_strlen($klassenavn, "UTF-8") > 50 ||
        mb_strlen($studiumkode, "UTF-8") > 50
    ) {
        print("<p>Klassekode kan ha maks 5 tegn. "
            . "Klassenavn og studiumkode kan ha maks 50 tegn.</p>");
    } else {
        include("db-tilkobling.php");

        mysqli_set_charset($db, "utf8mb4");

        // Undersøk om klassekoden allerede finnes.
        $sqlSetning = "SELECT klassekode FROM klasse WHERE klassekode = ?";
        $stmt = mysqli_prepare($db, $sqlSetning);
        mysqli_stmt_bind_param($stmt, "s", $klassekode);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $antallRader = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($antallRader != 0) {
            print("<p>Klassen er registrert fra før.</p>");
        } else {
            // Registrer den nye klassen.
            $sqlSetning = "INSERT INTO klasse
                          (klassekode, klassenavn, studiumkode)
                          VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($db, $sqlSetning);
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $klassekode,
                $klassenavn,
                $studiumkode
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $melding = htmlspecialchars(
                "$klassekode $klassenavn $studiumkode",
                ENT_QUOTES,
                "UTF-8"
            );

            print("<p>Følgende klasse er nå registrert: $melding</p>");
        }
    }
}
?>

</body>
</html>