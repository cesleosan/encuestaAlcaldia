<?php
class Controller {

    public function model($model) {
        // Usamos DIRECTORY_SEPARATOR para evitar choques en Windows
        $modelPath = APPPATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        die("El modelo $model no existe en la ruta: $modelPath");
    }

   public function view($view, $data = []) {
    if (!empty($data)) {
        extract($data);
    }

    // Usamos DIRECTORY_SEPARATOR para que Windows (OneDrive) no sufra
    $viewPath = APPPATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
    
    // Normalizamos: cambia cualquier / o \ por la diagonal correcta de tu sistema
    $viewPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $viewPath);

    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        // Esto te dirá exactamente qué está fallando si persiste el error
        die('La vista no existe en la ruta real: ' . $viewPath);
    }
}
}