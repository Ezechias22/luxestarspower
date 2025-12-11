<?php

namespace App;

class Router
{
    private $routes = [];
    private $namedRoutes = [];
    private $currentRoute = null;
    private $basePath = '';
    
    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
    }
    
    public function get($uri, $action, $name = null)
    {
        return $this->addRoute('GET', $uri, $action, $name);
    }
    
    public function post($uri, $action, $name = null)
    {
        return $this->addRoute('POST', $uri, $action, $name);
    }
    
    public function put($uri, $action, $name = null)
    {
        return $this->addRoute('PUT', $uri, $action, $name);
    }
    
    public function delete($uri, $action, $name = null)
    {
        return $this->addRoute('DELETE', $uri, $action, $name);
    }
    
    public function any($uri, $action, $name = null)
    {
        $this->addRoute('GET', $uri, $action, $name);
        return $this->addRoute('POST', $uri, $action, $name);
    }
    
    private function addRoute($method, $uri, $action, $name = null)
    {
        $uri = $this->basePath . '/' . trim($uri, '/');
        $uri = $uri === $this->basePath ? '/' : $uri;
        
        $route = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'regex' => $this->compileRoute($uri),
            'params' => [],
            'middleware' => []
        ];
        
        $this->routes[] = $route;
        
        if ($name) {
            $this->namedRoutes[$name] = $uri;
        }
        
        return $this;
    }
    
    public function middleware($middleware)
    {
        if (!empty($this->routes)) {
            $lastIndex = count($this->routes) - 1;
            $this->routes[$lastIndex]['middleware'] = is_array($middleware) ? $middleware : [$middleware];
        }
        return $this;
    }
    
    private function compileRoute($uri)
    {
        // Convert route parameters to regex
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uri);
        $regex = '#^' . $regex . '$#';
        return $regex;
    }
    
    public function dispatch()
    {
        $requestUri = $this->getRequestUri();
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Handle method spoofing for forms
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                if (preg_match($route['regex'], $requestUri, $matches)) {
                    $this->currentRoute = $route;
                    
                    // Extract parameters
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    
                    // Run middleware
                    foreach ($route['middleware'] as $middleware) {
                        $middlewareClass = "App\\Middlewares\\{$middleware}";
                        if (class_exists($middlewareClass)) {
                            $instance = new $middlewareClass();
                            $result = $instance->handle();
                            if ($result === false) {
                                return;
                            }
                        }
                    }
                    
                    // Execute action
                    return $this->executeAction($route['action'], $params);
                }
            }
        }
        
        // No route found
        $this->handleNotFound();
    }
    
    private function executeAction($action, $params = [])
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }
        
        if (is_string($action)) {
            list($controller, $method) = explode('@', $action);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller not found: {$controllerClass}");
            }
            
            $instance = new $controllerClass();
            
            if (!method_exists($instance, $method)) {
                throw new \Exception("Method not found: {$method}");
            }
            
            return call_user_func_array([$instance, $method], $params);
        }
    }
    
    private function getRequestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove base path if exists
        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        return '/' . trim($uri, '/') ?: '/';
    }
    
    public function generateUrl($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route not found: {$name}");
        }
        
        $uri = $this->namedRoutes[$name];
        
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }
        
        return url($uri);
    }
    
    private function handleNotFound()
    {
        // Check for .php extension and redirect
        $uri = $this->getRequestUri();
        if (substr($uri, -4) === '.php') {
            $cleanUri = substr($uri, 0, -4);
            redirect($cleanUri, 301);
        }
        
        abort(404);
    }
    
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
