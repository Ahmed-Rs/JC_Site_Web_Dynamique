<?php
require 'database.php';
$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = "";


if (!empty($_POST)) {
    $name            = checkInput($_POST['name']);
    $description     = checkInput($_POST['description']);
    $price           = checkInput($_POST['price']);
    $category        = checkInput($_POST['category']);
    $image           = checkInput($_FILES['image']['name']); // On utilise la super globale $_FILES pour récupérer un fichier et comme c'est un array de array on va chercher son 'name'.
    $imagePath       = '../images/' . basename($image);
    $imageExtension  = pathinfo($imagePath, PATHINFO_EXTENSION);
    $isSuccess       = true;
    $isUploadSuccess = false;

    if (empty($name)) {
        $nameError = "Ce champs ne doit pas rester vide !";
        $isSuccess = false;
    }
    if (empty($description)) {
        $descriptionError = "Ce champs ne doit pas rester vide !";
        $isSuccess = false;
    }

    if (empty($price)) {
        $priceError = "Ce champs ne doit pas rester vide !";
        $isSuccess = false;
    }
    if (empty($category)) {
        $categoryError = "Ce champs ne doit pas rester vide !";
        $isSuccess = false;
    }
    if (empty($image)) {
        $imageError = "Vous devez ajouter un fichier !";
        $isSuccess = false;
    } else {
        $isUploadSuccess = true;
        if ($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif") {

            $imageError = "Les fichiers autorisés sont: .jpg, .jpeg, .png, .gif";
            $isUploadSuccess = false;
        }
        if (file_exists($imagePath)) {

            $imageError = "Le fichier existe dejà";
            $isUploadSuccess = false;
        }
        if ($_FILES['image']['size'] > 500000) {
            
            $imageError = "Le taille du fichier ne doit pas dépasser 500KB";
            $isUploadSuccess = false;
        }
        if ($isUploadSuccess) {
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {  // On vérifie si l'image n'a pas été transférée du dossier temporaire vers le dossier images définitif.
                $imageError = "Une téléchargement est survenue";
                $isUploadSuccess = false;    
            }
        }

    }
    if ($isSuccess && $isUploadSuccess) {
        $db = Database::connect();
        $requete = $db->prepare('INSERT INTO items (name, description, price, category, image)
                                 VALUES (?, ?, ?, ?, ?)');

        $requete->execute(array($name, $description, $price, $category, $image));
        Database::disconnect();
        header('location: index.php');
    }

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
                <h1><strong>Ajouter un item</strong></h1><br>

                <form class="form" role="form" action="insert.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nom: </label><br>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?= $name ?>">
                        <span class="help-inline"><?= $nameError ?></span>
                    </div>    
                    <div class="form-group">    
                        <label for="description">Description: </label><br>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?= $description ?>">
                        <span class="help-inline"><?= $descriptionError ?></span>
                    </div>    
                    <div class="form-group">    
                        <label for="price">Prix: (en €)</label><br>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?= $price ?>">
                        <span class="help-inline"><?= $priceError ?></span>
                    </div>    
                    <div class="form-group">    
                        <label for="category">Catégorie: </label><br>
                        <select class="form-control" name="category" id="category">
                            <?php 
                                $db = Database::connect();
                                foreach($db->query('SELECT * FROM categories') as $row) {
                                    
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }
                                Database::disconnect();                            
                            ?>
                        </select>
                        <span class="help-inline"><?= $categoryError ?></span>
                    </div>
                    <div class="form-group"> 
                        <label for="image">Sélectionner une image:</label>
                        <input type="file" id="image" name="image">
                        <span class="help-inline"><?= $imageError ?></span>
                    </div>
                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Ajouter</button>
                        <a href="index.php" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                    </div>
                </form>

            </div>

        </div>

    </body>
</html>