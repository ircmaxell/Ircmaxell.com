<?php

namespace ircmaxell\com;

use \ReflectionClass;

class ResourceLoader {
    
    protected $config = array();
    
    protected $dependencies = array();
    
    public function __construct(array $config, array $depdencies = array()) {
        $this->config = $config;
        $this->prepareDependencies($depdencies);
    }
    
    public function registerRoutes(\ircmaxell\com\Router $router) {
        $self = $this;
        foreach ($this->config as $route => $options) {
            $router->addRoute(
                $options['method'],
                $route,
                function($match, $request, $response) use ($self, $route) {
                    $resource = $self->loadResource($route, $match);
                    $method = $request->getMethod();
                    $resource->$method($request, $response);
                    return true;
                }
            );
        }
    }
    
    public function loadResource($route, array $match = array()) {
        $options = $this->config[$route];
        $class = $options['class'];
        $args = array();
        $argOptions = array();
        foreach ($match as $k => $v) {
            $argOptions['#match' . $k] = $v;
        }
        if (isset($options['dependencies'])) {
            foreach ($options['dependencies'] as $token => $dependclass) {
                $argOptions[$token] = $this->dependencies[$dependclass];
            }
        }
        foreach ($options['constructorArgs'] as $arg) {
            if (isset($argOptions[$arg])) {
                $args[] = $argOptions[$arg];
            } else {
                $args[] = $arg;
            }
        }
        $r = new ReflectionClass($class);
        return $r->newInstanceArgs($args);
    }
    
    protected function prepareDependencies(array $dependencies) {
        foreach ($dependencies as $dependency) {
            $this->dependencies[get_class($dependency)] = $dependency;
        }
    }
    
}