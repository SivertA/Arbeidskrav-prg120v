<?php
/*
 * slett-student
 * Viser registrerte studenter i en nedtrekksliste
 * og sletter studenten som velges.
 */

require_once(__DIR__ . "/db-tilkobling.php");
mysqli_set_charset($db, "utf8mb4");

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brukernavn = trim($_POST["brukernavn"] ?? "");

    if ($brukernavn === "") {
        $melding = "Du må velge en student.";
    } else {
        $sqlSetning = "DELETE FROM student WHERE brukernavn = ?";

        $stmt = mysqli_prepare($db, $sqlSetning);
        mysqli_stmt_bind_param($stmt, "s", $brukernavn);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $melding = "Studenten med brukernavn $brukernavn er slettet.";
        } else {
            $melding = "Studenten finnes ikke.";
        }

        mysqli_stmt_close($stmt);
    }
}

// Hent studentene etter eventuell sletting.
$sqlSetning = "SELECT brukernavn, fornavn, etternavn
              FROM student
              ORDER BY brukernavn";

$sqlResultat = mysqli_query($db, $sqlSetning);
?>

<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slett student</title>
</head>
<body>

<h3>Slett student</h3>

<?php if ($melding !== ""): ?>
    <p><?= htmlspecialchars($melding, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>

<?php if (mysqli_num_rows($sqlResultat) === 0): ?>
    <p>Ingen studenter er registrert.</p>
<?php else: ?>
    <form method="post"
          action=""
          id="slettStudentSkjema"
          onsubmit="return confirm('Vil du slette den valgte studenten?')">

        <label for="brukernavn">Student</label>
        <select id="brukernavn" name="brukernavn" required>
            <option value="">Velg student</option>

            <?php while ($student = mysqli_fetch_assoc($sqlResultat)): ?>
                <option value="<?= htmlspecialchars(
                    $student["brukernavn"], ENT_QUOTES, "UTF-8"
                ) ?>">
                    <?= htmlspecialchars(
                        $student["brukernavn"] . " – "
                        . $student["fornavn"] . " "
                        . $student["etternavn"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="submit"
               value="Slett student"
               name="slettStudentKnapp"
               id="slettStudentKnapp">
    </form>
<?php endif; ?>

<p><a href="index.html">Tilbake til hovedsiden</a></p>

</body>
</html>