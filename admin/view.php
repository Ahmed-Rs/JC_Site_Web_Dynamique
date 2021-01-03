<?php
require 'database.php';

if (!empty($_GET['id'])) {

    $id = checkInput($_GET['id']); // VERIFICATION DE SECURITE
}

$db = Database::connect();
$requete = $db->prepare('SELECT items.id, items.name, items.description, items.price, items.image, categories.name 
                         AS category 
                         FROM items 
                         LEFT JOIN categories 
                         ON items.category = categories.id
                         WHERE items.id = ?');
$requete->execute(array($id));                      // On utilise 'prepare' 'execute' pour n'utiliser qu'une seule ligne de la db
$item = $requete->fetch();
Database::disconnect();


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

                <div class="col-sm-6">
                    <h1><strong>Voir un item</strong></h1><br>
                    <form action="">
                        <div class="form-group">
                            <label>Nom:</label><?= '   ' . $item['name'] ?>
                        </div>
                        <div class="form-group">
                            <label>Description:</label><?= '   ' . $item['description'] ?>
                        </div>
                        <div class="form-group">
                            <label>Prix: </label><?= '   ' . number_format((float)$item['price'], 2, '.', '') . ' €' ?>
                        </div>
                        <div class="form-group">
                            <label>Catégorie:</label><?= '   ' . $item['category'] ?>
                        </div>
                        <div class="form-group">
                            <label>Image:</label><?= '   ' . $item['image'] ?>
                        </div>                        
                    </form><br>
                    <div class="form-actions">
                    <a href="index.php" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>

                    </div>
                </div>
                <div class="col-sm-6 site">
                    <div class="thumbnail">
                        <img src='../images/<?= $item['image'] ?>' alt="">
                        <div class="price"><?= number_format((float)$item['price'], 2, '.', '') ?> €</div>
                            <div class="caption">
                                <h4><?= $item['name'] ?></h4>
                                <p><?= $item['description'] ?></p>
                                <a href="#" class="btn btn-order" role="button"><span class="glyphicon glyphicon-shopping-cart"></span>Commander</a>
                            </div>
                    </div>
                </div>
            </div>
            
        </div>


    </body>
</html>