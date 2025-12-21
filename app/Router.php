<?php
namespace App;

class Router {
    private $routes = [];
    private $namedRoutes = [];
    
    public function get($path, $handler, $name = null) { 
        $this->addRoute('GET', $path, $handler, $name); 
    }
    
    public function post($path, $handler, $name = null) { 
        $this->addRoute('POST', $path, $handler, $name); 
    }
    
    private function addRoute($method, $path, $handler, $name) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method' => $method, 
            'pattern' => '#^' . $pattern . '$#', 
            'handler' => $handler
        ];
        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }
    
    public function dispatch($method, $uri) {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->callHandler($route['handler'], $params);
            }
        }
        
        http_response_code(404);
        return $this->callHandler('ErrorController@notFound', []);
    }
    
    private function callHandler($handler, $params) {
        // Si c'est une fonction anonyme
        if (is_callable($handler)) {
            return call_user_func($handler, $params);
        }
        
        // Si c'est un contrôleur
        list($controller, $method) = explode('@', $handler);
        $controller = "App\\Controllers\\" . $controller;
        
        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }
        
        $instance = new $controller();
        
        if (!method_exists($instance, $method)) {
            throw new \Exception("Method {$method} not found in {$controller}");
        }
        
        // CORRECTION : Passe les paramètres comme UN SEUL tableau
        return $instance->$method($params);
    }
    
    public function url($name, $params = []) {
        $path = $this->namedRoutes[$name] ?? '/';
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        return $path;
    }
}