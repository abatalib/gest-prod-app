<?php
if (session_status() == PHP_SESSION_NONE)
  session_start();

if(!isset($_SESSION['panier']) || empty($_SESSION['panier'])):
   header('location:.');
endif;


$username='';
if(isset($_SESSION['client']) && !empty($_SESSION['client']))
  $username = $_SESSION['client']['username'];

//pour récupérer le contenu du panier
$refs_calcul = array_keys($_SESSION['panier']);
$refs = array_keys($_SESSION['panier']);


$refs_calcul = json_encode($refs_calcul);

$refs = implode(",", $refs);
$produits = Db::getProductsBasket($refs);
$nbre = sizeof($produits);

include_once PATH_COMPOSANTS . 'header.php';

?>

<style>
body {background:white;}
th{
    font-size: 13px;
    width: 1%; 
    white-space: nowrap
}
td{
    font-size: 12px;
    color: #666;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

/* Chrome */
input::-webkit-inner-spin-button,
input::-webkit-outer-spin-button { 
	-webkit-appearance: none;
	margin:0;
}

/* Opéra*/
input::-o-inner-spin-button,
input::-o-outer-spin-button { 
	-o-appearance: none;
	margin:0
}

.sub {
    border: none; 
    padding: 0px 14px; 
    margin: 5px 0px;
    border-radius: 10px;   
    width: 50px;
    }
.sub:hover {
    background: #252728;
    color: white; 
    }
</style>

<script src=<?= PATH_ASSETS . "js" . SEP . "manage-basket.js" ?>></script>

<div class="container" style="background:white">
    <div style="float:right">
        <a href="." class="btn btn-dark btn-continu-achat">Continuer mes achats</a>
    </div>
    
    <div style="float:right; margin-right:20px">
        <div class="dropdown">

            <?php if($username==''): ?>

                <button class="btn btn-dark dropdown-toggle btn-continu-achat" type="button" id="dropdownuser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownuser">
                    <a class="dropdown-item" href="connexion">Connexion</a>
                </div>

            <?php else: ?>

                <button class="btn btn-primary dropdown-toggle btn-continu-achat" type="button" id="dropdownuser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i> <?=$username?>
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownuser">
                    <a class="dropdown-item" href="logout">Déconnexion</a>
                </div>

            <?php endif; ?>

        </div>
    </div>
    <div>
        <img src=<?= PATH_ASSETS . "images/lock.png"?> alt="logo paiement sécurisé" class="logo-panier">
    </div>
    <div>
        <span class="span-paiement-securise">PAIEMENT SÉCURISÉ</span>
    </div>
    <div class="row mt-5">
        <div class="col-sm-6 ">
            <h3>Panier (<?= $nbre ?> produits)</h3>
        </div>
        <div class="col-sm-6">
            <!-- vier le panier -->
            <div class="dropdown mr-0" style="float: right">
                <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true" style="border: 0px solid transparent; width: 100px">
                    <i class="fa fa-trash-o"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:10px; width: 250px; margin-left:-100px">
                    <span style="font-size: 13px; color: #666">Etes vous sûr de vider le panier ?</span>

                    <button class="btn btn-dark mt-2 empty-basket" style="width: 40%; margin-left:5%;">Oui</button>
                    
                    <button class="btn btn-outline-dark mt-2" style="width: 40%; margin-left:5%">Non</button>
                </div>
            </div>
            
            <!-- enregistrer la commande -->
                <div class="dropdown mr-2" style="float: right">

                    <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true" style="border: 0px solid transparent; width: 100px">
                        <i class="fa fa-check-square-o"></i>
                    </button>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:10px; width: 250px; margin-left:-100px">
                        <span style="font-size: 13px; color: #666">Enregistrer ma commande</span>
                        
                        <button class="btn btn-dark mt-2 save-basket" style="width: 40%; margin-left:5%;">Oui</button>
                        
                        <button class="btn btn-outline-dark mt-2" style="width: 40%; margin-left:5%">Annuler</button>
                    </div>

                </div>

        </div>
    </div>
<hr>

        <div class="alert alert-success alert-dismissible fade show" id="msg" role="alert" style="display: none">   
            Commande sauvegardée
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

    <div class="row">
        <div class="col-8">
            <?php $total=0; ?>
            <?php foreach($produits as $produit): 
                $ref = $produit->ref;
                $qte = $_SESSION['panier'][$ref];
                $total_product = $produit->price*$qte;
                ?>
                <div class="mb-3 div-liste-articles" id="div-ref-<?=$produit->ref?>">
                    <?php $total += $produit->price*$qte; ?>
                    <div class="row">
                        <div class="col-md-2 pr-0">
                            <img src=<?= $produit->image ?> class="img-article-panier">
                        </div>
                        <div class="col-md-3 pl-0">
                            <span class="span-descript-article">Réf. : <?= $produit->ref ?></span><br>
                            <span class="span-descript-article"><?= $produit->description ?></span><br>
                        </div>
                        <!-- prix de l'article -->
                        <div class="col-md-2">
                            <input class="price-product-basket" value="<?= number_format ($produit->price, 2) ?>" id="px<?= $produit->ref ?>" disabled="disabled">
                        </div>
                        <!-- qté -->
                        <div class="col-md-2" style="padding-left: 40px">

                            <button class="btn pt-0 sub btn-change-qty" data="<?=$produit->ref?>" id="incr<?=$produit->ref?>">+</button>

                            <input class="form-control" id="qty<?=$ref?>" style="width:50px; text-align: center; background: white" value="<?=$qte?>" readonly>
                            
                            <button class="btn sub btn-change-qty" data="<?=$produit->ref?>" id="decr<?=$produit->ref?>">-</button>
                
                        </div>
                        <!-- prix total de l'article -->
                        <div class="col-md-3">
                            <div class="row">

                                <!-- total d'un produit -->
                                <div class="col-12 text-right">
                                    <input class="total-price-product-basket" value="<?= number_format ($total_product, 2) ?>" id="total_product<?= $produit->ref ?>" disabled="disabled">
                                </div>

                                <!-- supprimer un produit -->
                                <div class="col-12 text-right">
                                    <div class="dropdown mr-0">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true" style="background: #f7f7f7; border: 0px solid transparent;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:10px">
                                            <span style="font-size: 13px; color: #666">Etes vous sûr de supprimer le produit ?</span>

                                            <button class="btn btn-dark mt-2 delete-product" data="<?=$produit->ref?>" style="width: 40%; margin-left:5%;" >Oui</button>
                    
                                            <button class="btn btn-outline-dark mt-2" style="width: 40%; margin-left:5%">Non</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="div-liste-articles col-4">
            <div class="div-resume-commande"><h4>Résumé des commandes</h4></div>
            <hr>
            <div class="row mt-4 mb-4">
                <div class="col-md-4" style="display:inline">Sous-total</div>
                <div class="col-md-8" style="display:inline;">
                    <input class="total-basket" value="<?= number_format ($total, 2)?> DH" id="totalg" disabled="disabled">
                </div>
            </div>
            <div>
                <button class="btn btn-dark" style="width: 100%">PAYER</button>
            </div>
            <hr>
            <div class="mb-3">
                <b>Mode de paiement</b>
            </div>
            <div>
                <img src=<?= PATH_ASSETS . "images/mastercard.png"?> class="img-paiement">
                <img src=<?= PATH_ASSETS . "images/visa.png"?> class="img-paiement">
                <img src=<?= PATH_ASSETS . "images/paypal.png"?> class="img-paiement">
                <img src=<?= PATH_ASSETS . "images/cmi-card.png"?> class="img-paiement">
            </div>
        </div>
    </div>
</div>
<script>
    // window.onload = recalcul(<?=$refs_calcul?>);
</script>

<?php
// if($ajouter === 1): ?>
<script>
//permet de désactiver la fenêtre qui demande de renvoyer le formulaire
    // if ( window.history.replaceState ) {
    //     window.history.replaceState( null, null, window.location.href );
    // }
    // function affiche (){
    //     let msg = document.getElementById('msg');
    //     msg.setAttribute("style", "display: block; width:100%")
    //     setTimeout(() => {
    //         msg.setAttribute("style", "display: none;")
    //         location.reload();
    
    //     }, 4000);
    // }

    // affiche()
</script>
<?php 
// endif;
// <script src="assets/js/jquery.js"></script>
// <script src="assets/js/popper.min.js"></script>
// <script src="assets/js/bootstrap.min.js"></script>

include_once PATH_COMPOSANTS . 'footer.php';