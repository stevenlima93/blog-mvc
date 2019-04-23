<?php

require_once '../tools/common.php';

if(!isset($_SESSION['user']) OR $_SESSION['user']['is_admin'] == 0){
	header('location:../index.php');
	exit;
}

//Si $_POST['save'] existe, cela signifie que c'est un ajout d'article
if (isset($_POST['save'])) {
    $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
    $my_file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageInformations = getimagesize($_FILES['image']['tmp_name']);

    if (empty($_POST['title'])) {
        $message = 'Le titre est obligatoire !';
    } elseif (empty($_POST['published_at'])) {
        $message = 'La date est obligatoire !';
    } else {
        do {
            $new_file_name = rand();
            $destination = '../img/article/' . $new_file_name . '.' . $my_file_extension;
        } while (file_exists($destination));

        $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);
        $query = $db->prepare('INSERT INTO article (title, content, summary, is_published, published_at, image) VALUES (?, ?, ?, ?, ?, ?)');
        $newArticle = $query->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['summary'],
            $_POST['is_published'],
            $_POST['published_at'],
            $new_file_name . '.' . $my_file_extension
        ]);

        $lastIdInserte = $db->lastInsertId();
        foreach ($_POST['category_id'] as $category) {
            $queryInsert = $db->prepare('INSERT INTO article_category (article_id, category_id) VALUES (?, ?)');
            $resultInsert = $queryInsert->execute(
                [
                    $lastIdInserte,
                    $category
                ]);
        }
        $insertCat = $resultInsert;

        //redirection après enregistrement
        //si $newArticle alors l'enregistrement a fonctionné
        if ($newArticle AND $insertCat) {
            //redirection après enregistrement
            $_SESSION['message'] = 'Article ajouté !';
            header('location:article-list.php');
            exit;
        } else { //si pas $newArticle => enregistrement échoué => générer un message pour l'administrateur à afficher plus bas
            $message = "Impossible d'enregistrer le nouvel article...";
        }
    }
}
//Si $_POST['update'] existe, cela signifie que c'est une mise à jour d'article
if(isset($_POST['update'])) {
    $delCat = $db->prepare('DELETE FROM article_category WHERE article_id = ?');
    $delCat->execute(array($_POST['id']));
    $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
    $my_file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageInformations = getimagesize($_FILES['image']['tmp_name']);

    if (isset($_FILES['image']) AND empty($_FILES['image'])) {
        $message = 'L\'image est obligatoire !';
    } elseif (!in_array($my_file_extension, $allowed_extensions)) {
        $message = 'L\'extension est invalide !';
    } else {
        if (!$_FILES['image']['error'] == 4) {
            $selectImage = $db->prepare('SELECT image FROM article WHERE id = ?');
            $selectImage->execute([
                $_POST['id']
            ]);
            $recupImage = $selectImage->fetch();

            $destination = '../img/article/';
            unlink($destination . $recupImage['image']);
            do {
                $new_file_name = rand();
                $destination = '../img/article/' . $new_file_name . '.' . $my_file_extension;
            } while (file_exists($destination));

            $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            $query = $db->prepare('UPDATE article SET
		title = ?,
		content = ?,
		summary = ?,
		is_published = ?,
		published_at = ?,
		image = ?,
		WHERE id = ?');

            //mise à jour avec les données du formulaire
            $resultArticle = $query->execute([
                $_POST['title'],
                $_POST['content'],
                $_POST['summary'],
                $_POST['is_published'],
                $_POST['published_at'],
                $new_file_name . '.' . $my_file_extension,
                $_POST['id']
            ]);
            foreach ($_POST['category_id'] as $category) {
                $catInsert = $db->prepare('INSERT INTO article_category (article_id, category_id) VALUES (?, ?)');
                $test = $catInsert->execute(array($_POST['id'], $category));
            }
            //si enregistrement ok
            if ($resultArticle) {
                $_SESSION['message'] = 'Article mis à jour !';
                header('location:article-list.php');
                exit;
            } else {
                $message = 'Erreur.';
            }
        }
    }
}
//si on modifie un article, on doit séléctionner l'article en question (id envoyé dans URL) pour pré-remplir le formulaire plus bas
if(isset($_GET['article_id']) && isset($_GET['action']) && $_GET['action'] == 'edit'){

	$query = $db->prepare('SELECT * FROM article WHERE id = ?');
	$query->execute(array($_GET['article_id']));
	//$article contiendra les informations de l'article dont l'id a été envoyé en paramètre d'URL
	$article = $query->fetch();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Administration des articles - Mon premier blog !</title>
		<?php require 'partials/head_assets.php'; ?>
	</head>
	<body class="index-body">
		<div class="container-fluid">
			<?php require 'partials/header.php'; ?>
			<div class="row my-3 index-content">
				<?php require 'partials/nav.php'; ?>
				<section class="col-9">
					<header class="pb-3">
						<!-- Si $article existe, on affiche "Modifier" SINON on affiche "Ajouter" -->
						<h4><?php if(isset($article)): ?>Modifier<?php else: ?>Ajouter<?php endif; ?> un article</h4>
					</header>
					<?php if(isset($message)): //si un message a été généré plus haut, l'afficher ?>
					<div class="bg-danger text-white">
						<?= $message; ?>
					</div>
					<?php endif; ?>
					<!-- Si $article existe, chaque champ du formulaire sera pré-remplit avec les informations de l'article -->
                    <form action="article-form.php" method="post" enctype="multipart/form-data">

						<div class="form-group">
							<label for="title">Titre :</label>
							<input class="form-control" value="<?= isset($article) ? htmlentities($article['title']) : '';?>" type="text" placeholder="Titre" name="title" id="title" />
						</div>
						<div class="form-group">
							<label for="summary">Résumé :</label>
							<input class="form-control" value="<?= isset($article) ? htmlentities($article['summary']) : '';?>" type="text" placeholder="Résumé" name="summary" id="summary" />
						</div>
						<div class="form-group">
							<label for="content">Contenu :</label>
							<textarea class="form-control" name="content" id="content" placeholder="Contenu"><?= isset($article) ? htmlentities($article['content']) : '';?></textarea>
						</div>
                        <div class="form-group">
                            <label for="image">Image :</label>
                            <input class="form-control" type="file" name="image" id="image"/>
                            <?php if (isset($article['image']) AND !empty($article['image'])): ?>
                                <img class="img-fluid py-4"
                                     src="../img/article/<?= isset($article) ? $article['image'] : ''; ?>" alt="">
                                <input type="hidden" name="current-image"
                                       value="<?= isset($article) ? $article['image'] : ''; ?>">
                            <?php endif; ?>
                        </div>

						<div class="form-group">
							<label for="category_id">Catégorie :</label>
							<select multiple class="form-control" name="category_id[]" id="category_id">
								<?php
								$queryCategories = $db->query('SELECT * FROM category');
								$categories = $queryCategories->fetchAll();
								?>
								<?php foreach($categories as $key => $category) : ?>
                                    <?php $selectCat = $db->prepare('SELECT * FROM article_category WHERE article_id = ? AND category_id = ?');
                                          $selectCat->execute(array($_GET['article_id'], $category['id']));
                                          $resultCat = $selectCat->fetch(); ?>
									<option value="<?= $category['id']; ?>" <?= isset($_GET['article_id']) && $resultCat ? 'selected' : ''; ?>>
										<?= $category['name']; ?>
									</option>
								<?php endforeach; ?>

							</select>
						</div>

						<div class="form-group">
							<label for="published_at">Date de publication :</label>
							<input class="form-control" value="<?= isset($article) ? htmlentities($article['published_at']) : '';?>" type="date" placeholder="Résumé" name="published_at" id="published_at" />
						</div>

						<div class="form-group">
							<label for="is_published">Publié ?</label>
							<select class="form-control" name="is_published" id="is_published">
								<option value="0" <?= isset($article) && $article['is_published'] == 0 ? 'selected' : '';?>>Non</option>
								<option value="1" <?= isset($article) && $article['is_published'] == 1 ? 'selected' : '';?>>Oui</option>
							</select>
						</div>

						<div class="text-right">
						<!-- Si $article existe, on affiche un lien de mise à jour -->
						<?php if(isset($article)): ?>
							<input class="btn btn-success" type="submit" name="update" value="Mettre à jour" />
						<!-- Sinon on afficher un lien d'enregistrement d'un nouvel article -->
						<?php else: ?>
							<input class="btn btn-success" type="submit" name="save" value="Enregistrer" />
						<?php endif; ?>
						</div>

						<!-- Si $article existe, on ajoute un champ caché contenant l'id de l'article à modifier pour la requête UPDATE -->
						<?php if(isset($article)): ?>
						<input type="hidden" name="id" value="<?= $article['id']; ?>" />
						<?php endif; ?>

					</form>
				</section>
			</div>
		</div>
  </body>
</html>
