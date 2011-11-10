<?php

/**
 * @namespace
 */
namespace Templater\View\Helper;

class ThemePath extends \Zend\View\Helper\AbstractHelper 
{
    protected $themePath = ''; 
    
    public function __construct($themePath = '') 
    {
        $this->themePath = $themePath;
    }
    
    /**
     * Render path as string
     *
     * @param  string|int $indent
     * @return string
     */
    public function __toString()
    {
       return $this->themePath;
    }
}