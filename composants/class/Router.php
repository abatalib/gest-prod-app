<?php

class Router
{
    private $routes = [
        '/' => 
            [
                'path' => PATH_VIEWS . 'display-products/display-products',
                'title' => "title",
            ],
        'transfert-json-to-mysql' => 
            [
                'path' => PATH_VIEWS . 'transfert-json-mysql',
                'title' => 'Transfert des données depuis JSON à MySQL'
            ],
        'panier' => 
            [
                'path' => PATH_VIEWS . 'basket',
                'title' => 'Consultation de panier'
            ],
        'connexion' => 
            [
                'path' => PATH_VIEWS . 'connexion',
                'title' => 'Login'
            ],


            //scripts
        'ajax-scripts' => 
            [
                'path' => PATH_SCRIPTS . 'ajax-scripts',
                'title' => 'Routeur des requêtes ajax'
            ],
        'logout' => 
            [
                'path' => PATH_SCRIPTS . 'logout',
                'title' => 'Déconnexion'
            ],
        ];

    public function get(string $page)
    {
        
        try {

            if(strpos($page,'?')):
                $page = substr($page,0,strpos($page,'?'));
            endif;

            if (array_key_exists ($page,$this->routes)):
                isset($this->routes[$page]['title']) ? $title = $this->routes[$page]['title'] : $title = "";

                include($this->routes[$page]['path'] . '.php');

            else:
                $title = "Page 404";
                include_once(PATH_COMPOSANTS . "page404.php");
            endif;
            
        } catch(Exception $e){
            throw $e;
        }
    }
}