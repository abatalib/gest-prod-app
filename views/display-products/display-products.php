<?php
include_once PATH_COMPOSANTS . 'header.php';

//if (session_status() == PHP_SESSION_NONE)
//  session_start();

$username='';
if(isset($_SESSION['client']) && !empty($_SESSION['client']))
  $username = $_SESSION['client']['username'];

//récupérer 
$products = Db::getRecords('SELECT COUNT(*) AS Nbre FROM product',true);

if($products->Nbre<1)
    die("Pas de produits");

?>

<script src=<?= PATH_ASSETS . "js" . SEP . "list-products.js" ?>></script>

<!-- Custom styles for this template -->
<link href=<?= PATH_ASSETS . "css" .SEP. "simple-sidebar.css"?> rel="stylesheet">

<div class="d-flex" id="wrapper">
    <!--sidebar pour afficher les catégories-->
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading">Catégories </div>
      <div class="py-2">
        <div class="py-2">
          <!--pour afficher l'ensemble des catégories-->
          <div class="float-left px-2">
            <a href='#' id='displayAllCategory'>Toutes</a>
          </div>
          <div class="float-right">
              <i class="fas fa-sort-amount-down-alt mr-3 btn-sort" data="asc" title="Tri ascendant" id="asc"></i>
              <i class="fas fa-sort-amount-down mr-3 btn-sort" data="desc" title="Tri descendant" id="desc"></i>
          </div>
        </div>
      </div>
      <!-- ajax pour ajouter la liste des catégories -->
      <div class="list-group list-group-flush overflow-auto vh-100" id="div-category">

      </div>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <!--icone panier-->
      <div class="float-right mr-4">
        <div id="div-basket"></div>
      </div>
      <!--icone user-->
      <div class="float-right mr-4">
        <div class="dropdown">

          <?php if($username==''): ?>

            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownuser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user"></i>
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownuser">
              <a class="dropdown-item" href="connexion">Connexion</a>
            </div>

          <?php else: ?>

            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownuser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user"></i> <?=$username?>
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownuser">
              <a class="dropdown-item" href="logout">Déconnexion</a>
            </div>

          <?php endif; ?>

        </div>
      </div>

      <div class="container-fluid mb-5">
        <h1 class="mt-5 mx-4"><small class="text-muted">LISTE DES PRODUITS </small></h1>
        <h6 class="lib-category mx-5 mb-4">Toutes les catégories</h6>
        
        <table id="listProducts" class="table table-striped w-100">
            <thead>
                <tr>
                    <th></th>
                    <th>Réf.</th>
                    <th>Désignation</th>
                    <th>Type</th>
                    <th>Prix</th>
                    <th>Expédition</th>
                    <th>action</th>
                    <th>Description</th>
                    <th>Fabricant</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->
  
<?php

  require_once 'view-photo-modal.php';
?>



<?php

include_once PATH_COMPOSANTS . 'footer.php';