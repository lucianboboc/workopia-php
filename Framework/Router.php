<?php

class Router
{
    /**
     * Add a new route
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function registerRoute($method, $uri, $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    protected $routes = [];

    /**
     * Add a GET route
     * @param $uri
     * @param $controller
     * @return void
     */
    public function get($uri, $controller)
    {
        $this->registerRoute('GET', $uri, $controller);
    }

    /**
     * Add a POST route
     * @param $uri
     * @param $controller
     * @return void
     */
    public function post($uri, $controller)
    {
        $this->registerRoute('POST', $uri, $controller);
    }

    /**
     * Add a PUT route
     * @param $uri
     * @param $controller
     * @return void
     */
    public function put($uri, $controller)
    {
        $this->registerRoute('PUT', $uri, $controller);
    }

    /**
     * Add a DELETE route
     * @param $uri
     * @param $controller
     * @return void
     */
    public function delete($uri, $controller)
    {
        $this->registerRoute('DELETE', $uri, $controller);
    }

    /**
     * Route the request
     * @param $uri
     * @param $method
     * @return void
     */
    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri and $route['method'] === $method) {
                require basePath('App/' . $route['controller']);
                return;
            }
        }

        $this->error(404);
    }

    /**
     * Load error page
     * @param int $httpCode
     */
    private function error($httpCode = 404)
    {
        http_response_code($httpCode);
        require basePath("App/controllers/error/{$httpCode}.php");
        exit();

    }
}
