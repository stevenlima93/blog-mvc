<?php

function modifProfile($profile){

    $db = dbConnect();

//Si $_POST['update'] existe, cela signifie que c'est une mise à jour d'utilisateur
    if ($profile) {

        //début de la chaîne de caractères de la requête de mise à jour
        $queryString = 'UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, bio = :bio ';
        //début du tableau de paramètres de la requête de mise à jour
        $queryParameters = [
            'firstname' => $_POST['firstname'],
            'lastname' => $_POST['lastname'],
            'email' => $_POST['email'],
            'bio' => $_POST['bio'],
            'id' => $_POST['id'] ];

        //uniquement si l'admin souhaite modifier le mot de passe
        if (!empty($_POST['password'])) {

            if ($_POST['password_confirm'] == $_POST['password']) {
                //concaténation du champ password à mettre à jour
                $queryString .= ', password = :password ';
                //ajout du paramètre password à mettre à jour
                $queryParameters['password'] = hash('md5', $_POST['password']);
            } else {
                return $message = "Les mots de passe doivent être identiques";
            }
        }
        //fin de la chaîne de caractères de la requête de mise à jour
        $queryString .= 'WHERE id = :id';

        //préparation et execution de la requête avec la chaîne de caractères et le tableau de données
        $query = $db->prepare($queryString);
        $result = $query->execute($queryParameters);

        if ($result) {
            $_SESSION['user']['firstname'] = $_POST['firstname'];
             return $message = 'Modification réussi';
        } else {
           return $message = 'Erreur.';
        }
    }
}
function recupUser(){

    $db = dbConnect();

//si on modifie un utilisateur, on doit séléctionner l'utilisateur en question (id envoyé dans URL) pour pré-remplir le formulaire plus bas
    $query = $db->prepare('SELECT * FROM user WHERE id = ?');
    $query->execute(array($_SESSION['user']['id']));
//$user contiendra les informations de l'utilisateur dont l'id a été envoyé en paramètre d'URL
   return $user = $query->fetch();
}