<?php

/* 
 * Abstract class all the controllers should extend.
 */

abstract class AbstractSlimController
{
    protected $m_request;
    protected $m_response;
    protected $m_args;
    
    
    public function __construct(Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
    {
        $this->m_request = $request;
        $this->m_response = $response;
        $this->m_args = $args;
    }
    
    
    public abstract static function registerWithApp(\Slim\App $app);
    
    
    // Internal getters for type hinting until php 7.4
    protected function getResponse() : \Slim\Http\Response { return $this->m_response; }
    protected function getRequest() : Slim\Http\Request { return $this->m_request; }
}

