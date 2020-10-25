<?php

use Base\Maps as BaseMaps;

/**
 * Skeleton subclass for representing a row from the 'maps' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class Maps extends BaseMaps
{

    public function getImg()
    {
        if(file_exists(dirname(__FILE__)."/../../webroot/maps/".$this->getFile().".jpg")){
            return "maps/".$this->getFile().".jpg";
        }else{
            if(empty($this->getImgurl()) || is_null($this->getImgurl())){
                return "maps/unknown.jpg";
            }else{
                return $this->getImgurl();
            }

        }
    }

    public function getNameSize()
    {
        return $this->getName() . (empty($this->getSize()))?"":" (".$this->getSize().")";
    }
}
