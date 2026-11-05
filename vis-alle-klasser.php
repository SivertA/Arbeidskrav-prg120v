<?php
/* Hent alle registrerte klasser. */

require_once(__DIR__ . "/db-tilkobling.php");
mysqli_set_charset($db, "utf8mb4");

$sqlSetning = "SELECT klassekode, klassenavn, studiumkode
              FROM klasse
              ORDER BY klassekode";

$sqlResultat = mysqli_query($db, $sqlSetning);
?>

<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vis alle klasser</title>
</head>
<body>

<h3>Alle klasser</h3>

<?php if (mysqli_num_rows($sqlResultat) === 0): ?>
    <p>Ingen klasser er registrert.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th scope="col">Klassekode</th>
                <th scope="col">Klassenavn</th>
                <th scope="col">Studiumkode</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($klasse = mysqli_fetch_assoc($sqlResultat)): ?>
                <tr>
                    <td><?= htmlspecialchars(
                        $klasse["klassekode"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $klasse["klassenavn"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $klasse["studiumkode"], ENT_QUOTES, "UTF-8"
                    ) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="index.html">Tilbake til hovedsiden</a></p>

</body>
</html>