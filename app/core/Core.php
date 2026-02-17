<?php
class Core {

    protected $currentController = 'Auth';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {

        $url = $this->getUrl();

        // ✅ Ruta correcta al controlador
        $controllerPath = APPPATH . '/controllers/' . ucwords($url[0]) . '.php';

        if (isset($url[0]) && file_exists($controllerPath)) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }

        // ✅ Cargar controlador
        require_once APPPATH . '/controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // ✅ Buscar método
        if (isset($url[1]) && method_exists($this->currentController, $url[1])) {
            $this->currentMethod = $url[1];
            unset($url[1]);
        }

        // ✅ Parámetros
        $this->params = $url ? array_values($url) : [];

        call_user_func_array(
            [$this->currentController, $this->currentMethod],
            $this->params
        );
    }

    public function getUrl() {
    if (isset($_GET['url'])) {
        // 🔥 CAMBIO CRÍTICO: Usar trim para quitar la / del principio
        $url = trim($_GET['url'], '/'); 
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return explode('/', $url);
    }

    return ['Auth'];
}
}
