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

    public function getByFile(string $file)
    {
        return MapsQuery::create()->findOneByFile($file);
    }

    public function getMapCycle()
    {
        $mapcycle = [];
        if(file_exists($this->config->gamepath."/mapcycle.txt")){
            $file = fopen($this->config->gamepath."/mapcycle.txt","r");
            while(!feof($file)){
                $map = fgets($file);
                array_push($mapcycle,$this->getByFile(trim($map)));
            }
        }
        return $mapcycle;
    }

    public function setMapCycle(array $maps){
        $i=0;
        foreach($maps as $id){
            $map = MapsQuery::create()->findPk($id);
            if(!is_null($map)){
                if($i == 0){
                    file_put_contents($this->config->gamepath."/mapcycle.txt",$map->getFile());
                }else{
                    file_put_contents($this->config->gamepath."/mapcycle.txt","\n".$map->getFile(),FILE_APPEND);
                }
                $i++;
            }
        }
    }
}