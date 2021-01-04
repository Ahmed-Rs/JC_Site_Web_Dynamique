<?php
require 'database.php';

if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

if (!empty($_POST)) {
    $id = checkInput($_POST['id']);
    $db = Database::connect();
    $requete = $db->prepare('DELETE FROM items
                             WHERE id = ?');
    $requete->execute(array($id));
    Database::disconnect();
    header('location: index.php');
}


function checkInput ($data) {

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <title>Burger Code</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>

        <h1 class="text-logo"><span class="glyphicon glyphicon-cutlery"></span> Burger <span class="glyphicon glyphicon-cutlery"></span></h1>
        <div class="container admin">
            <div class="row">
                <h1><strong>Supprimer un item</strong></h1><br>

                <form class="form" role="form" action="delete.php" method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>"> <!-- Methode alternative de récupération de l'id, ici par la méthode post -->
                    <p class="alert alert-warning">Êtes-vous sûr de vouloir supprimer ?</p>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning">Oui</button>
                        <a href="index.php" class="btn btn-default">Non</a>
                    </div>
                </form>
            </div>
        </div>

    </body>
</html>