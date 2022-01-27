<?php
if(isset($_POST['source']) && !empty($_POST['source'])):
    $source = strip_tags($_POST['source']);
    switch ($source)
    {
        case 'add-in-basket':
            echo Functions::addInBasket();
            break;
        case 'empty-basket':
            if(session_status() == PHP_SESSION_NONE)
                session_start();

            if(isset($_SESSION['panier']))
                unset($_SESSION['panier']); echo 'ok';
            break;
        case 'get-products-of-selected-category':
            echo Functions::getProductsOfSelectedCategory();
            break;
        case 'get-categories-sidebar':
            echo Functions::getCategoriesSideBar();
            break;
        case 'transfer-json-to-mysql':
            echo Functions::transferJsonToMysql();
            break;
        case 'update-basket':
            echo Functions::updateBasket();
            break;
        case 'change-qty-basket':
            echo Functions::changeQtyBasket();
            break;
        case 'del-product-basket':
            echo Functions::delProductBasket();
            break;
        case 'save-cde':
            echo Functions::saveCde();
            break;
    }
else:
    echo 'err';
endif;