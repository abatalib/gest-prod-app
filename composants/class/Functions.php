<?php

class Functions
{
    function saveCde()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        
        if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])):
            if(isset($_SESSION['client']) && !empty($_SESSION['client'])):
                $id_client=strip_tags($_SESSION['client']['id_client']);
                foreach($_SESSION['panier'] as $ref=>$qty):
                    Db::saveCde(compact('id_client','ref','qty'));
                endforeach;
            else:
                return 'c';
            endif;
        endif;
        
    }

    function addInBasket()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
         
        if(isset($_POST['ref'], $_POST['qte']) && 
            (!empty($_POST['ref'])) && !empty($_POST['qte']) &&
            (is_numeric($_POST['ref'])) && is_numeric($_POST['qte'])):
            
            $ref = $_POST['ref'];
            $qte = $_POST['qte'];
        
            $_SESSION['panier'][$ref] = $qte;
        
            $nbre = sizeof($_SESSION['panier']);
        
            return $nbre;
        else:
            return 'err';
        endif;
    }

    function getProductsOfSelectedCategory()
    {
        $connect = Db::connect();

        $column = array('ref', 'name', 'type', 'price', 'shipping', 'description', 'manufacturer', 'image');

        $query = "
        SELECT * FROM product
        ";

        if(isset($_POST['idCategory']) && $_POST['idCategory'] != '')
        {
            $query = '
                SELECT p.ref, p.name, p.type, p.price, p.shipping, p.description, p.manufacturer, p.image
                FROM r_category_product r
                INNER JOIN product p ON r.ref = p.ref
                WHERE r.id ="'.$_POST['idCategory'].'"';
        }

        if(isset($_POST['order']))
        {
        $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
        }
        else
        {
        $query .= 'ORDER BY ref DESC ';
        }

        $query1 = '';

        if($_POST["length"] != -1)
        {
        $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        $statement = $connect->prepare($query);

        $statement->execute();

        $number_filter_row = $statement->rowCount();

        $statement = $connect->prepare($query . $query1);

        $statement->execute();

        $result = $statement->fetchAll();



        $data = array();

        foreach($result as $row)
        {
        $sub_array = array();
        $sub_array[] = '';
        $sub_array[] = $row->ref;
        $sub_array[] = $row->name;
        $sub_array[] = $row->type;
        $sub_array[] = $row->price;
        $sub_array[] = $row->shipping;
        $sub_array[] = '
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cart-arrow-down"></i>
                        </button>
                        <div class="dropdown-menu p-3" style="width:200px; background: #FDF2D7">
                            <label class="text-danger">Qté</label>
                            <input type="number" class="form-control" id="qte'.$row->ref.'" value="1">

                            <button class="btn btn-warning form-control btn-add-in-basket mt-2" id="'.$row->ref.'">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>';
        $sub_array[] = $row->description;
        $sub_array[] = $row->manufacturer;
        $sub_array[] = '<img class="thumb-image-product" src="'.$row->image.'">';
        $data[] = $sub_array;
        }

        function count_all_data($connect)
        {
        $query = "SELECT * FROM product";
        $statement = $connect->prepare($query);
        $statement->execute();
        return $statement->rowCount();
        }

        $output = array(
        "draw"       =>  intval($_POST["draw"]),
        "recordsTotal"   =>  count_all_data($connect),
        "recordsFiltered"  =>  $number_filter_row,
        "data"       =>  $data
        );

        return json_encode($output);
    }

    function getCategoriesSideBar()
    {
        if(isset($_POST['sort']) && !empty($_POST['sort'])):
            $sort = strip_tags($_POST['sort']);
            
            if($sort!='asc' && $sort!='desc'):
                return 'err';
            endif;
            
            $category = Db::getRecords('SELECT * FROM category order by name '.$sort, false);
        
            $result='';
            foreach($category as $c): 
                $nbre = Db::getRecords('SELECT * FROM product as p INNER JOIN r_category_product as r
                                                ON p.ref=r.ref WHERE r.id="'.$c->id.'"', true);//$nbre->NbreProducts
                $result .='<button href="#" class="list-group-item list-group-item-action bg-light link-category" id="'.$c->id.'" name="'.$c->name.'">'.$c->name.'</button>';
            endforeach;
        
            return $result;
        endif;
    }

    function transferJsonToMysql()
    {
        //vérifier si les données sont dans la base de données
        $products = Db::getRecords('CALL getNbreProducts', true)->Nbre;

        if($products>0):
            return 'data_already_exist';
        else:
            //récupérer le fichier json
            $file_json = file_get_contents(PATH_COMPOSANTS . 'data.json');
            $file_json = json_decode($file_json);

            try {
                $tab_distinct = [];
                //transfert des catégories
                //2 boucles imbriquées 
                //boucle 1 -> parcourir les products
                //boucle 2 -> parcourir les category de chaque product
                foreach($file_json as $p):
                    $category = $p->category;
                    foreach($category as $c):
                        $id = $c->id;
                        $name = $c->name;
                        
                        //si array ne comporte pas la category en cours
                        //on l'ajoute
                        if(!key_exists($id,$tab_distinct)):
                            $tab_distinct [$id] = $name;
                            Db::saveCategoryJsonToMySQL(compact('id','name'));
                        endif;
                    endforeach;
                endforeach;
                
                //réinitialiser array pour le re-exploitation
                $tab_distinct = [];
                //transfert des products et la relation
                //2 boucles imbriquées 
                //boucle 1 -> parcourir et ajouter les products
                //boucle 2 -> parcourir les category de chaque product 
                            //et les ajouter avec product dans la table relation
                foreach($file_json as $p):
                    $ref = $p->ref; 
                    $name = $p->name; 
                    $type = $p->type; 
                    $price = $p->price; 
                    $shipping = $p->shipping; 
                    $description = $p->description; 
                    $manufacturer = $p->manufacturer; 
                    $image = $p->image;
                
                    Db::saveProductsJsonToMySQL(compact('ref','name','type','price','shipping','description','manufacturer','image'));
                
                    $category = $p->category;
                    foreach($category as $c):
                        $id = $c->id;        
                        if(!key_exists($id,$tab_distinct)):
                            $tab_distinct [$id.$ref] = "";
                            Db::saveRelationJsonToMySQL(compact('id','ref'));
                        endif;
                    endforeach;
                
                endforeach;
                
                return "ok";
            }catch (Exception $e) {
                return "err";
            }
        endif;
    }

    function changeQtyBasket()
    {
        if(isset($_POST['ref']) && !empty($_POST['ref']) &&
            isset($_POST['currentQty']) && !empty($_POST['currentQty'])):
            
            $ref = strip_tags($_POST['ref']);
            $qty = strip_tags($_POST['currentQty']);

            if (session_status() == PHP_SESSION_NONE){session_start();}
            
            $_SESSION['panier'][$ref] = $qty;
        endif;
    }

    function delProductBasket()
    {
        if(isset($_POST['ref']) && !empty($_POST['ref'])):
            
            $ref = strip_tags($_POST['ref']);

            if (session_status() == PHP_SESSION_NONE){session_start();}
            
            unset($_SESSION['panier'][$ref]);
            if(!isset($_SESSION['panier'][$ref])):
                return 'ok';
            endif;
        endif;
    }

    function updateBasket()
    {
        if (session_status() == PHP_SESSION_NONE) 
            session_start();
        
        if(!isset($_SESSION['panier'])):
            return self::getEmptyBasket();
        else:
            $refs = array_keys($_SESSION['panier']);
         
            return self::getBasketContain($refs);
        endif;
    }

    function getEmptyBasket()
    {
        $result = '
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-shopping-cart"></i>
                </button>
                <div class="dropdown-menu ombre pt-0" aria-labelledby="dropdownMenuButton" style="width: 300px; margin-left: -150px; background:#f5f5f5">
                    <div style="text-align: center; padding: 30px">
                        <span><img src='. PATH_ASSETS . "images".SEP."empty-cart.jpg".' class="img-empty-cart"></span><br>
                        <span class="descript-article">Votre panier est vide!</span>
                    </div>
                </div>
            </div>';
        return $result;
    }

    function getBasketContain($refs)
    {
        $refs = implode(",", $refs);
        $rslts = Db::getProductsBasket($refs);
        $nbre = sizeof($_SESSION['panier']);

        
        $result = '
        <div class="dropdown">
            <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-shopping-cart"></i> ('.$nbre.') 
            </button>

            <div class="dropdown-menu ombre pt-0" aria-labelledby="dropdownMenuButton" style="width: 300px; margin-left: -150px; background:#f5f5f5">
                <div>

                    <div style="text-align: center; padding: 10px">
                        <h5 style="font-weight: bold">Panier ('. $nbre .' produit(s))</h5>
                        <hr>
                    </div>

                    <div class="scrol">
                        <div class="row">
                            <div class="div-icone-panier">';

                    $total=0;
                    foreach($rslts as $rslt): 
                        $ref = $rslt->ref;
                        $qte = $_SESSION['panier'][$ref];

                        $sousTotal = $rslt->price*$qte; $total+=$sousTotal;

                        $result .= '
                                <div class="col-md-4 pr-0">
                                    <img src='.$rslt->image.' class="img-article-panier-icone">
                                </div>
                                <div class="col-md-8 pl-2 pt-1">
                                    <span class="descript-article">Réf. : '. $rslt->ref . '</span><br>
                                    <span class="descript-article">' . $rslt->name . '</span>
                                </div>

                                <div class="col-md-12">
                                    <div style="display:inline-block; margin-right: 20px">
                                        <span class="descript-article" style="font-weight: bold;">Quantité : </span>
                                        <span class="descript-article">' . $qte .'</span>
                                    </div>
                                    <div style="display:inline-block">
                                        <span class="descript-article" style="font-weight: bold;">Prix : </span>
                                        <span class="descript-article">' . number_format ($rslt->price, 2) . ' DH</span>
                                    </div>
                                    <div style="display:inline-block">
                                        <span class="descript-article" style="font-weight: bold;">Sous total : </span>
                                        <span class="descript-article">' . number_format ($sousTotal, 2) . ' DH</span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>';
                    endforeach;

                    $result .= '<div class="col-md-12">
                                    <span class="descript-article" style="font-weight: bold;">Total : </span>
                                    <span class="descript-article">' . number_format ($total, 2) . ' DH</span>
                                </div>
                            </div>
                            <!--div-icone-panier-->

                        </div>
                        <!--row-->
                        <div style="text-align: center; padding: 20px; width: 100%">
                            <a href="panier" class="btn btn-dark" style="width: 100%">VOIR MON PANIER</a>
                        </div>
                    </div>
                    <!--scrol-->
                </div>
                <!--empty-->
            </div>
            <!--dropdown-menu-->
        </div>
        <!--dropdown-->
        ';

        return $result;
    }
}