<?php
$error = '';
if(!empty($_POST)):
    if(!isset($_POST['mail']) || empty($_POST['mail']) ||
        !isset($_POST['pwd']) || empty($_POST['pwd'])):

        $error = "Tous les champs devront être renseignés!";

    else:
        $mail = strip_tags($_POST['mail']);
        $pwd = strip_tags($_POST['pwd']);

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)):
            $error = "Format du courriel est erroné";
        else:
            $rep = Db::login(compact('mail','pwd'));

            //si la réponse comprend @
            //on récupère l'id du client
            $rep = explode('@',$rep);
            if($rep[0]=='ok'):
                $id=$rep[1];
            endif;
            $rep=$rep[0];

            switch($rep)
            {
                case 'not_active':
                    $error = "Compte pas encore activé!";
                    break;
                case  'Err':
                    $error = "Compte ou mot de passe erroné!";
                    break;
                case 'ok':
                    header('location:.');
                    break;
            }
        endif;

    endif;
        
endif;

require_once PATH_COMPOSANTS . 'header.php';
// $mdp = password_hash('123456', PASSWORD_BCRYPT);
// echo $mdp;
?>

<link rel="stylesheet" href=<?= PATH_ASSETS . "css".SEP."login.css"?>>

<div class="container h-100">
    <div class="d-flex justify-content-center h-100">
        <div class="user_card">
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            <div class="d-flex justify-content-center my-3">
                &nbsp;
                <?php
                    if(!empty($error)){
                        echo '<div class="bg-white text-danger w-100 text-center">'.$error.'</div>';
                    };
                ?>
            </div>
            <div class="d-flex justify-content-center">
                <form method="post" action="">
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                        </div>
                        <input type="text" name="mail" class="form-control" placeholder="e-mail" autocomplete = off value="<?php if(isset($_POST['mail'])){echo $_POST['mail'];}?>" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" name="pwd" class="form-control" value="" placeholder="mot de passe" required>
                    </div>
                    <div class="d-flex justify-content-center mt-3 login_container">
                        <button type="submit" name="button" class="btn btn-outline-primary mt-4 text-white border border-white w-100">Connexion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once PATH_COMPOSANTS . 'footer.php';