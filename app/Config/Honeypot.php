<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Honeypot extends BaseConfig
{
    public $hidden      = true;
    public $label       = 'Fill This Field';
    public $name        = 'honeypot';
    public $template    = '<label>{label}</label><input type="text" name="{name}" value="">';
    public $container   = '<div style="display:none">{template}</div>';
    public $containerId = 'hpc';
}