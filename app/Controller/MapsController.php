<?php

namespace App\Controller;

use MapsQuery;

class MapsController extends Controller
{
    public function getList()
    {
        return MapsQuery::create()->orderByName()->find();
    }

    public function get($id)
    {
        return MapsQuery::create()->findPk($id);
    }
}