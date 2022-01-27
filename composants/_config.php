<?php
class Autoload
{
    public static function start()
    {
        //détecter automatiquement la classe demandée pour la charger
        spl_autoload_register(array(__CLASS__,'load'));
        $dir = dirname(__DIR__);

        //arrêter des constantes comportants les chemins à exploiter dans l'appli
        define('SEP', DIRECTORY_SEPARATOR);
        define('PATH_COMPOSANTS', 'composants' . SEP);
        define('PATH_SCRIPTS', PATH_COMPOSANTS . SEP . 'scripts' . SEP);
        define('PATH_CLASS', PATH_COMPOSANTS . 'class' . SEP);
        define('PATH_ASSETS', 'assets' . SEP);
        define('PATH_VIEWS', 'views' . SEP);
    }

    public static function load($class) 
    {
        if (file_exists(PATH_CLASS.$class.'.php')):
            include(PATH_CLASS.$class.'.php');
        endif;
    }
}



