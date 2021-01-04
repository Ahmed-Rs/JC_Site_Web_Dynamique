<?php
require 'database.php';

if(!empty($_GET['id'])) 
{
    $id = checkInput($_GET['id']);
}

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

    // Afficher les erreurs
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

        $isImageUpdated = false;                            // Si aucun fichier n'a été ajouté, on laisse à false.

    } else {
        $isImageUpdated = true;                             // Sinon true
        $isUploadSuccess = true;                            // Et fichier téléchargé
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
                $imageError = "Une erreur de téléchargement est survenue";
                $isUploadSuccess = false;    
            }
        }
    }
    if (($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) { // Si image mise à jour et téléchargée avec succès OU image non modifiée : 2 cas de succès
        
        $db = Database::connect();

        if ($isImageUpdated) {          // Si image mise à jour, remplir la base de données avec les nouvelles (ou bien les anciennes si non modifiées) 

            $requete = $db->prepare('UPDATE items
                                     SET name = ?, description = ?, price = ?, category = ?, image = ?
                                     WHERE id = ?');
            $requete->execute(array($name, $description, $price, $category, $image, $id));

        } else {                        // Si image non mise à jour, remplir la base de données avec les nouvelles (ou bien les anciennes si non modifiées) mais sans l'image
            $requete = $db->prepare('UPDATE items
            SET name = ?, description = ?, price = ?, category = ?
            WHERE id = ?');
            $requete->execute(array($name, $description, $price, $category, $id));

        }
        Database::disconnect();
        header('location: index.php');

    } elseif ($isImageUpdated && !$isUploadSuccess) { // On garde l'affichage du nom de l'image initialement présente pour qu'elle corresponde à l'image visible, au loieu de mettre le nom du fichier refusé
        $db = Database::connect();
        $requete = $db->prepare('SELECT image
                                 FROM items
                                 WHERE id = ?');
        $requete->execute(array($id));
        $item = $requete->fetch();
        $image = $item['image'];
    }

} else {                                // Si rien n'a encore été posté, c-à-d à l'entrée de la page, aficher les informations relative à l'item en question, en se basant sur l'id visible dans l'url.

    $db = Database::connect();
    $requete = $db->prepare('SELECT *
                             FROM items
                             WHERE id = ?');
    $requete->execute(array($id));
    $item = $requete->fetch();
    $name        = $item['name'];
    $description = $item['description'];
    $price       = $item['price'];
    $category    = $item['category'];
    $image       = $item['image'];

    Database::disconnect();

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
                <div class="col-sm-6">
                    <h1><strong>Modifier un item</strong></h1><br>

                    <form class="form" role="form" action="<?php echo 'update.php?id=' . $id; ?>" method="POST" enctype="multipart/form-data">
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
                                    foreach($db->query('SELECT * FROM categories') as $row) { // $row est un tableau contenant les informations id et categorie de la table categories
                                        if ($row['id'] == $category) {  // Si en parcourant la table on a une correspondance avec la catégorie en cours

                                            echo '<option selected="selected" value="' . $row['id'] . '">' . $row['name'] . '</option>'; // On affiche dans le champs automatiquement la catégorie de l'item en cours
                                        } else {

                                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                        }
                                    }
                                    Database::disconnect();                            
                                ?>
                            </select>
                            <span class="help-inline"><?= $categoryError ?></span>
                        </div>
                        <div class="form-group">
                            <label for="">Image: </label>
                            <p><?= $image ?></p>
                            <label for="image">Sélectionner une image:</label>
                            <input type="file" id="image" name="image">
                            <span class="help-inline"><?= $imageError ?></span>
                        </div>
                        <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Modifier</button>
                            <a href="index.php" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6">
                    <div class="thumbnail">
                        <img src='../images/<?= $image ?>' alt="">
                        <div class="price"><?= number_format((float)$price, 2, '.', '') ?> €</div>
                        <div class="caption">
                            <h4><?= $name ?></h4>
                            <p><?= $description ?></p>
                            <a href="#" class="btn btn-order" role="button"><span class="glyphicon glyphicon-shopping-cart"></span>Commander</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </body>
</html>