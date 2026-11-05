<?php
/* Hent alle registrerte studenter. */

require_once(__DIR__ . "/db-tilkobling.php");
mysqli_set_charset($db, "utf8mb4");

$sqlSetning = "SELECT BRUKERNAVN, FORNAVN, ETTERNAVN, KLASSEKODE
              FROM student
              ORDER BY brukernavn";

$sqlResultat = mysqli_query($db, $sqlSetning);
?>

<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vis alle studenter</title>
</head>
<body>

<h3>Alle studenter</h3>

<?php if (mysqli_num_rows($sqlResultat) === 0): ?>
    <p>Ingen studenter er registrert.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th scope="col">brukernavn</th>
                <th scope="col">fornavn</th>
                <th scope="col">etternavn</th>
                <th scope="col">klassekode</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = mysqli_fetch_assoc($sqlResultat)): ?>
                <tr>
                    <td><?= htmlspecialchars(
                        $student["brukernavn"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $student["fornavn"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $student["etternavn"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $student["klassekode"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $klasse["studiumkode"], ENT_QUOTES, "UTF-8"
                    ) ?></td>

                    <td><?= htmlspecialchars(
                        $student["brukernavn"], ENT_QUOTES, "UTF-8"
                    ) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="index.html">Tilbake til hovedsiden</a></p>

</body>
</html>