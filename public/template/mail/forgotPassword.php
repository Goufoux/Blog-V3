<!DOCTYPE html>
<html>
    <head>
        <title>Mot de passe oublié</title>
    </head>
    <body>
        <h1>Bonjour <?php echo $user->getName() . ' ' . $user->getFirstName(); ?></h1>
        <p>
            Une demande de réinitialisation de votre mot de passe a été effectué le <?php date('d/m/y à H:i:s'); ?>, <br />
            veuillez suivre ce <a href="blogv3/connect/verifToken?token=<?php echo $token; ?>">lien</a> pour terminer la procédure de réinitialisation.<br />
            Attention, ce lien est valable pendant 48h, après ce délai veuillez effectuer une nouvelle demande de réinitialisation.
        </p>
        <p>
            Si vous n'êtes pas à l'origine de cette demande, veuillez ne pas tenir compte de cette email.<br />
            Pour des raisons de sécurité, modifier votre mot de passe.
        </p>
        <p>
            Cette email a été généré automatiquement, merci de ne pas y répondre.
        </p>
    </body>
</html>