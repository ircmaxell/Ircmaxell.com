<?php

namespace ircmaxell\com;

class Router {
    
    protected $routes = array();

    public function addRoute($method, $regex, Closure $route) {
        $this->routes[$regex] = array($method, $route);
        return $this;
    }
    
    public function dispatch(\ircmaxell\com\Request $request, \ircmaxell\com\Response $response) {
        foreach ($this->routes as $regex => $route) {
            if (($route[0] == '*' || $route[0] == $request->getMethod()) && preg_match($regex, $request->getUri(), $match)) {
                if ($route[1]($match, $request, $response)) {
                    return $response;
                }
            }
        }
        $response->setStatus(404);
        return $response;
    }
    
}