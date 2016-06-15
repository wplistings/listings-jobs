<?php

namespace Listings\Jobs\Admin;

class Admin
{
    public function __construct()
    {
        $this->cpt = new Cpt();
    }
}