<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Honeypot extends BaseConfig
{
    public $hidden      = true;
    public $template    = '<label>{field}</label><input type="text" name="{name}" value="">';
    public $container   = '<div style="display:none">{template}</div>';
}