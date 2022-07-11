<?php

namespace App\View;

class View {

    private $_view;
    private $_partial;
    private $_data = [];

    public function __construct($view)
    {
        $this->setView($view);
        $this->setAlert($alert, $alert_title, $alert_message);
    }

    public function setView($view): void
    {
        $this->_view = $view;
    }

    public function assign($key, $value): void
    {
        $this->_data[$key] = $value;
    }

    public function includePartial($partial, ?array $config = null): void
    {
        if (!file_exists("view/" . $partial . ".php")) {
            die("le fichier " . $partial . " n'existe pas");
        }

        include "view/" . $partial . ".php";
    }

}