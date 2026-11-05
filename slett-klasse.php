<?php
/*
 * slett-klasse
 * Viser klasser i en nedtrekksliste.
 * En klasse kan bare slettes hvis den ikke har studenter.
 */

require_once(__DIR__ . "/db-tilkobling.php");
mysqli_set_charset($db, "utf8mb4");

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $klassekode = trim($_POST["klassekode"] ?? "");

    if ($klassekode === "") {
        $melding = "Du må velge en klasse.";
    } else {
        // Undersøk om noen studenter tilhører klassen.
        $sqlSetning = "SELECT brukernavn
                      FROM student
                      WHERE klassekode = ?";

        $stmt = mysqli_prepare($db, $sqlSetning);
        mysqli_stmt_bind_param($stmt, "s", $klassekode);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $antallStudenter = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($antallStudenter > 0) {
            $melding = "Klassen kan ikke slettes fordi den har "
                     . "registrerte studenter.";
        } else {
            $sqlSetning = "DELETE FROM klasse WHERE klassekode = ?";

            $stmt = mysqli_prepare($db, $sqlSetning);
            mysqli_stmt_bind_param($stmt, "s", $klassekode);

            try {
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) === 1) {
                    $melding = "Klassen $klassekode er slettet.";
                } else {
                    $melding = "Klassen finnes ikke.";
                }
            } catch (mysqli_sql_exception $feil) {
                // Fremmednøkkelen beskytter også mot sletting dersom
                // en student ble registrert etter kontrollen over.
                if ($feil->getCode() === 1451) {
                    $melding = "Klassen kan ikke slettes fordi den har "
                             . "registrerte studenter.";
                } else {
                    throw $feil;
                }
            }

            mysqli_stmt_close($stmt);
        }
    }
}

// Hent klassene etter eventuell sletting.
$sqlSetning = "SELECT klassekode, klassenavn
              FROM klasse
              ORDER BY klassekode";

$sqlResultat = mysqli_query($db, $sqlSetning);
?>

<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slett klasse</title>
</head>
<body>

<h3>Slett klasse</h3>

<?php if ($melding !== ""): ?>
    <p><?= htmlspecialchars($melding, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>

<?php if (mysqli_num_rows($sqlResultat) === 0): ?>
    <p>Ingen klasser er registrert.</p>
<?php else: ?>
    <form method="post"
          action=""
          id="slettKlasseSkjema"
          onsubmit="return confirm('Vil du slette den valgte klassen?')">

        <label for="klassekode">Klasse</label>
        <select id="klassekode" name="klassekode" required>
            <option value="">Velg klasse</option>

            <?php while ($klasse = mysqli_fetch_assoc($sqlResultat)): ?>
                <option value="<?= htmlspecialchars(
                    $klasse["klassekode"], ENT_QUOTES, "UTF-8"
                ) ?>">
                    <?= htmlspecialchars(
                        $klasse["klassekode"] . " – " . $klasse["klassenavn"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="submit"
               value="Slett klasse"
               name="slettKlasseKnapp"
               id="slettKlasseKnapp">
    </form>
<?php endif; ?>

<p><a href="index.html">Tilbake til hovedsiden</a></p>

</body>
</html>