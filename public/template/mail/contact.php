<!DOCTYPE html>
<html>
    <head>
        <title>Blog Genarkys - Formulaire de contact</title>
    </head>
    <body>
        <h1>Bonjour,</h1>
            <p>
                Un formulaire de contact a été posté le : <?php echo date('d/m/y'); ?><br />
                Contenu <br />
            </p>
            <hr>
            <?php if (empty($datas)):?>
                <p>
                    Aucun donnée n'a été transmise.
                </p>
            <?php else: ?>
                <ul> 
                <?php foreach ($datas as $key => $value): ?>
                    <li><?php echo $key . ' : ' . $value; ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
    </body>
</html>