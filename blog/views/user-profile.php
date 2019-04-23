<!DOCTYPE html>
<html>
<head>

    <title>Profile - Mon premier blog !</title>
    <?php require 'partials/head_assets.php'; ?>
</head>
<body class="article-body">
<div class="container-fluid">

    <?php require 'partials/header.php'; ?>

    <div class="row my-3 article-content">


        <?php require 'partials/nav.php'; ?>

        <main class="col-9">

            <form action="index.php?page=user-profile" method="post" class="p-4 row flex-column">

                <h4 class="pb-4 col-sm-8 offset-sm-2">Mise à jour des informations utilisateur</h4>

                <?= isset($message) ? $message : '';?>
                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="firstname">Prénom <b class="text-danger">*</b></label>
                    <input class="form-control" value="<?= isset($message) ? $_POST['firstname'] : $user['firstname'];?>" type="text" placeholder="Prénom" name="firstname" id="firstname" />
                </div>
                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="lastname">Nom de famille</label>
                    <input class="form-control" value="<?= isset($message) ? $_POST['lastname'] : $user['lastname'];?>" type="text" placeholder="Nom de famille" name="lastname" id="lastname" />
                </div>
                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="email">Email <b class="text-danger">*</b></label>
                    <input class="form-control" value="<?= isset($message) ? $_POST['email'] : $user['email'];?>" type="email" placeholder="Email" name="email" id="email" />
                </div>
                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="password">Mot de passe (uniquement si vous souhaitez modifier votre mot de passe actuel)</label>
                    <input class="form-control" value="" type="password" placeholder="Mot de passe" name="password" id="password" />
                </div>

                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="password_confirm">Confirmation du mot de passe (uniquement si vous souhaitez modifier votre mot de passe actuel)</label>
                    <input class="form-control" value="" type="password" placeholder="Confirmation du mot de passe" name="password_confirm" id="password_confirm" />
                </div>
                <div class="form-group col-sm-8 offset-sm-2">
                    <label for="bio">Biographie</label>
                    <textarea class="form-control" name="bio" id="bio" placeholder="Ta vie Ton oeuvre..."><?= isset($message) ? $_POST['bio'] : $user['bio'];?></textarea>
                </div>

                <div class="text-right col-sm-8 offset-sm-2">
                    <p class="text-danger">* champs requis</p>

                    <input class="btn btn-success" type="submit" name="update" value="Mettre à jour" />
                    <!-- Si $user existe, on ajoute un champ caché contenant l'id de l'utilisateur à modifier pour la requête UPDATE -->
                    <?php if(isset($user)): ?>
                        <input type="hidden" name="id" value="<?= $user['id']?>" />
                    <?php endif; ?>
                </div>
            </form>
        </main>
    </div>

    <?php require 'partials/footer.php'; ?>

</div>
</body>
</html>