<?php

namespace App\Controller;

use GametypesQuery;

class GametypesController extends Controller
{
    /**
     * Get list of all Gametypes
     * @return mixed
     */
    public function getList()
    {
        return GametypesQuery::create()->orderByName()->find();
    }

    /**
     * Get a Gametype
     * @param $id
     * @return array|\Gametypes|mixed
     */
    public function get($id)
    {
        return GametypesQuery::create()->findPk($id);
    }


    /**
     * Get a Gametype by Code (value of g_gametype)
     * @param $code
     * @return \Gametypes
     */
    public function getByCode($code)
    {
        return GametypesQuery::create()->findOneByCode($code);
    }
}