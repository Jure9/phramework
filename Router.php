<?php

namespace app\core;

use app\core\exception\NotFoundException;
use app\core\Request;

class Router
{

    protected $routes= [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request= $request;
        $this->response= $response;
    }

    public function get($path, $callback)
    {

        $this->routes['get'][$path]= $callback;
    }

    public function post($path, $callback)
    {

        $this->routes['post'][$path]= $callback;
    }

    public function resolve()
    {
        $path= $this->request->getPath();
        $method= $this->request->getMethod();
        $callback= $this->routes[$method][$path] ?? false;

        if($callback == false)
        {
            $this->response->setStatusCode(404);
            throw new NotFoundException();
        }

        if(is_string($callback))
        {
            return Application::$app->view->renderView($callback);
        }

        if (is_array($callback)) {
            $controller = new $callback[0];
            $controller->action = $callback[1];
            Application::$app->controller = $controller;
            $middlewares = $controller->getMiddlewares();
            foreach ($middlewares as $middleware) {
                $middleware->execute();
            }
            $callback[0] = $controller;
        }

        return call_user_func($callback, $this->request, $this->response);
    }
}