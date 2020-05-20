<?php

namespace App\Controller;

use MapsQuery;

class MapsController extends Controller
{
    /**
     * Get the list of all Maps
     * @return \Maps[]|\Propel\Runtime\Collection\ObjectCollection
     */
    public function getList()
    {
        return MapsQuery::create()->orderByName()->find();
    }

    /**
     * Get a map
     * @param $id
     * @return array|\Maps|mixed
     */
    public function get($id)
    {
        return MapsQuery::create()->findPk($id);
    }

    public function getPerFile(string $file)
    {
        return MapsQuery::create()->findOneByFile($file);
    }
}