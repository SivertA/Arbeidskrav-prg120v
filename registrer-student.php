<?php
/*
 * registrer-student
 * Viser et skjema og registrerer studenter i databasen.
 */

require_once("db-tilkobling.php");
mysqli_set_charset($db, "utf8mb4");

$melding = "";
$brukernavn = "";
$fornavn = "";
$etternavn = "";
$klassekode = "";

// Behandle skjemaet når det sendes inn.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brukernavn = trim($_POST["brukernavn"] ?? "");
    $fornavn = trim($_POST["fornavn"] ?? "");
    $etternavn = trim($_POST["etternavn"] ?? "");
    $klassekode = trim($_POST["klassekode"] ?? "");

    if (
        $brukernavn === "" ||
        $fornavn === "" ||
        $etternavn === "" ||
        $klassekode === ""
    ) {
        $melding = "Alle feltene må fylles ut.";
    } elseif (
        mb_strlen($brukernavn, "UTF-8") > 7 ||
        mb_strlen($fornavn, "UTF-8") > 50 ||
        mb_strlen($etternavn, "UTF-8") > 50 ||
        mb_strlen($klassekode, "UTF-8") > 5
    ) {
        $melding = "Ett eller flere felt inneholder for mange tegn.";
    } else {
        // Sjekk om brukernavnet allerede finnes.
        $sqlSetning = "SELECT brukernavn
                      FROM student
                      WHERE brukernavn = ?";

        $stmt = mysqli_prepare($db, $sqlSetning);
        mysqli_stmt_bind_param($stmt, "s", $brukernavn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $antallStudenter = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);

        // Kontroller også at valgt klasse finnes.
        $sqlSetning = "SELECT klassekode
                      FROM klasse
                      WHERE klassekode = ?";

        $stmt = mysqli_prepare($db, $sqlSetning);
        mysqli_stmt_bind_param($stmt, "s", $klassekode);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $antallKlasser = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($antallStudenter > 0) {
            $melding = "Brukernavnet er registrert fra før.";
        } elseif ($antallKlasser === 0) {
            $melding = "Du må velge en klasse som finnes.";
        } else {
            // Registrer studenten.
            $sqlSetning = "INSERT INTO student
                          (brukernavn, fornavn, etternavn, klassekode)
                          VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($db, $sqlSetning);
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $brukernavn,
                $fornavn,
                $etternavn,
                $klassekode
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $melding = "Studenten $brukernavn ($fornavn $etternavn) "
                     . "er registrert i klasse $klassekode.";

            // Tøm feltene etter vellykket registrering.
            $brukernavn = "";
            $fornavn = "";
            $etternavn = "";
            $klassekode = "";
        }
    }
}

// Hent klassene som skal vises i nedtrekkslisten.
$sqlSetning = "SELECT klassekode, klassenavn
              FROM klasse
              ORDER BY klassekode";

$klasser = mysqli_query($db, $sqlSetning);
?>

<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer student</title>
</head>
<body>

<h3>Registrer student</h3>

<?php if ($melding !== ""): ?>
    <p><?= htmlspecialchars($melding, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>

<?php if (mysqli_num_rows($klasser) === 0): ?>

    <p>Du må registrere en klasse før du kan registrere studenter.</p>
    <a href="registrer-klasse.php">Registrer klasse</a>

<?php else: ?>

    <form method="post" action="" id="registrerStudentSkjema">
        <label for="brukernavn">Brukernavn</label>
        <input type="text"
               id="brukernavn"
               name="brukernavn"
               maxlength="7"
               value="<?= htmlspecialchars($brukernavn, ENT_QUOTES, "UTF-8") ?>"
               required>
        <br>

        <label for="fornavn">Fornavn</label>
        <input type="text"
               id="fornavn"
               name="fornavn"
               maxlength="50"
               value="<?= htmlspecialchars($fornavn, ENT_QUOTES, "UTF-8") ?>"
               required>
        <br>

        <label for="etternavn">Etternavn</label>
        <input type="text"
               id="etternavn"
               name="etternavn"
               maxlength="50"
               value="<?= htmlspecialchars($etternavn, ENT_QUOTES, "UTF-8") ?>"
               required>
        <br>

        <label for="klassekode">Klasse</label>
        <select id="klassekode" name="klassekode" required>
            <option value="">Velg klasse</option>

            <?php while ($klasse = mysqli_fetch_assoc($klasser)): ?>
                <option
                    value="<?= htmlspecialchars($klasse["klassekode"], ENT_QUOTES, "UTF-8") ?>"
                    <?= $klassekode === $klasse["klassekode"] ? "selected" : "" ?>
                >
                    <?= htmlspecialchars(
                        $klasse["klassekode"] . " – " . $klasse["klassenavn"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br>

        <input type="submit"
               value="Registrer student"
               id="registrerStudentKnapp"
               name="registrerStudentKnapp">

        <input type="reset" value="Nullstill">
    </form>

<?php endif; ?>

<p><a href="index.html">Tilbake til hovedsiden</a></p>

</body>
</html>