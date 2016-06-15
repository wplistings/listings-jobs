<?php

namespace Listings\Jobs\Admin;

use Listings\Jobs\Admin\Writepanels\JobDetails;

class Admin
{
    public function __construct()
    {
        $this->jobdetails = new JobDetails();
        $this->cpt = new Cpt();
    }
}