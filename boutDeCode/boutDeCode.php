<?php
require '../include/init.php';
adminSecurity();
/*function adminSecurity()
{
    if (!isUserAdmin()) {
        if (!isUserConnected()) {
            header('Location: connexion.php');
        } else {
            header('Location: index.php');
        }
        
        die;
    }
}
*/
$pseudo = $nom = $prenom = $email = $civilite = $statut = '';

$errors = [];
//stockage des erreurs

if (!empty($_POST)) {
    sanitizePost();
    //supprime les caractères avec trim()
    extract($_POST);
     
    if (empty($pseudo)) {
        $errors['pseudo'] = 'Le nom est obligatoire';
    } else {
        $query = 'SELECT COUNT(*) FROM membre WHERE pseudo = '
            . $pdo->quote($pseudo);
        
        if (isset($_GET['id'])) {
            $query .= ' AND id_membre != ' . (int)$_GET['id'];
        }
        
        $stmt = $pdo->query($query);
        $nb = $stmt->fetchColumn();
        
        if ($nb != 0) {
            $errors['pseudo'] = "Un membre existe déjà avec ce nom";
        }
    }
    
    if (empty($nom)) {
        $errors['nom'] = 'Le nom est obligatoire';
    }
    
    if (empty($prenom)) {
        $errors['prenom'] = 'Le prenom est obligatoire';
    }
    
    if (empty($email)) {
        $errors['email'] = 'L\'email est obligatoire';
    }
    
    if (empty($civilite)) {
        $errors['civilite'] = 'la civilite est obligatoire';
    }
    
    if (empty($statut)) {
        $errors['statut'] = 'Le statut est obligatoire';
    }
    
    if (empty($errors)) {
        if (isset($_GET['id'])) {
            $query = 'UPDATE membre SET'
                . ' pseudo = :pseudo,'
                . ' nom = :nom,'
                . ' prenom = :prenom'
                . ' email = :email,'
                . ' civilite = :civilite,'
                . ' statut = :statut'
                . ' WHERE id = :id_membre'
            ;
            $pdo->prepare($query);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_INT);
            $stmt->bindParam(':civilite', $civilite, PDO::PARAM_INT);
            $stmt->bindParam(':statut', $statut, PDO::PARAM_INT);

            $stmt->execute();
            
            setFlashMessage('Le statut de la commande #' . $_POST['id'] . ' a été modifié');
        } else {
            $query = 'INSERT INTO membre (pseudo, nom, prenom, email, civilite, statut)'
                . 'VALUES (:pseudo, :nom, :prenom, :email, :civilite, :statut)'
            ;
           
            $flashMessage = 'Le membre a bien été créé';
        }
        
        $pdo->prepare($query);
        
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_INT);
        $stmt->bindParam(':civilite', $civilite, PDO::PARAM_INT);
        $stmt->bindParam(':statut', $statut, PDO::PARAM_INT);

        
        if (isset($_GET['id'])) {
            $stmt->bindParam(':id_membre', $_GET['id'], PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        setFlashMessage($flashMessage);
        /*
        function setFlashMessage($message, $type = 'success')
        {
        $_SESSION['flashMessage'] = [
        'message' => $message,
        'type' => $type,
        ];
        }
        
        */
        header('Location: dashboard.php');
        die;
    }
} elseif (isset($_GET['id'])) {
    $query = 'SELECT * FROM membre WHERE id_membre = ' . (int)$_GET['id'];
    $stmt = $pdo->query($query);
    $membre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (empty($membre)) {
        header('Location: dashboard.php');
        die;
    }
    
    extract($membre);

}

require '../layout/admin-top.php';
?>
<h2><?= ((isset($_GET['id'])) ? 'Modification' : 'Nouveau') . ' membre'; ?></h2>
<?php
if (!empty($errors)) {
    echo '<div class="alert alert-danger" role="alert">'
        . '<strong>Le formulaire contient des erreurs</strong>'
        . '</div>'
    ;
}

?>
<form method="post" enctype="multipart/form-data">
    <div class="form-group <?= getErrorClass('pseudo', $errors); ?>">
        <label class="control-label">Pseudo</label>
        <input type="text" name="pseudo" value="<?= $pseudo; ?>" class="form-control">
        <?= getErrorMsg('pseudo', $errors); ?>
    </div>
    <div class="form-group <?= getErrorClass('nom', $errors); ?>">
        <label class="control-label">Nom</label>
        <input name="nom" class="form-control" value="<?= $nom; ?>">
        <?= getErrorMsg('nom', $errors); ?>
    </div>
    <div class="form-group <?= getErrorClass('prenom', $errors); ?>">
        <label class="control-label">Prenom</label>
        <input type="text" class="form-control" name="prenom" value="<?= $prenom; ?>">
        <?= getErrorMsg('prenom', $errors); ?>
    </div>
       
    <div class="form-group <?= getErrorClass('email', $errors); ?>">
        <label class="control-label">Email</label>
        <input type="text" name="email" value="<?= $email; ?>" class="form-control">
        <?= getErrorMsg('email', $errors); ?>
    </div>
       
    <div class="form-group <?= getErrorClass('civilite', $errors); ?>">
        <label class="control-label">Civilite</label>
        <input type="text" name="civilite" value="<?= $civilite; ?>" class="form-control">
        <?= getErrorMsg('ville', $errors); ?>
    </div>
    <div class="form-group <?= getErrorClass('statut', $errors); ?>">
      <label class="col-md-4 control-label" for="statut">Statut</label>
      <div class="col-md-2">
        <select  name="statut" value="<?= $statut; ?>" class="form-control">
        <option value=""></option>
        <option value="0" <?php if ('0' == $statut) {echo 'selected';} ?>>0</option>
      <option value="1" <?php if ('1' == $statut) {echo 'selected';} ?>>1</option>
    </select>
    <?= getErrorMsg('statut', $errors); ?>
  </div>
</div>
       
    
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="dashboard.php" class="btn btn-default">Annuler</a>
</form>

<?php
require '../layout/admin-bottom.php'; 
?>
