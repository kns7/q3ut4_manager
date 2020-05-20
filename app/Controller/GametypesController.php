<?php

namespace App\Controller;

use App\Controller\Controller;

class GametypesController extends Controller
{
    public function getList()
    {
        return GametypesQuery::create()->orderByName()->find();
    }

    public function get($id)
    {
        return GametypesQuery::create()->findPk($id);
    }
}