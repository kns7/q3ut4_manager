<?php
include('header.php');
?>
<div class="row mapcycle-editor">
    <div class="col-md-9 col-sm-12">
        <div class="row">
            <div class="col-5">
                <ul class="list-group">
                    <li class="list-group-item disabled active">Cartes disponibles</li>
                </ul>
                <ul class="list-group" id="maplist">
                    <?php
                    foreach ($maps as $map){
                        ?>
                        <li class="list-group-item map-item" data-id="<?= $map->getId();?>">
                            <img src="<?= $map->getImg();?>"/><?= $map->getName();?><?= (empty($map->getSize()))?"":" (".$map->getSize().")";?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="col-1">
                <button class="mt-5 btn btn-light btn-add-map" disabled><i class="fa fa-arrow-right"></i></button>
            </div>
            <div class="col-5">
                <ul class="list-group"><li class="list-group-item disabled active">Map Cycle</li></ul>
                <ul class="list-group list-group-sortable" id="mapcycle">
                    <?php
                    foreach($mapcycle as $map){
                        ?>
                        <li class="list-group-item mapcycle-item" draggable="true" data-id="<?= $map->getId();?>">
                            <img src="<?= $map->getImg();?>"/><?= $map->getName();?><?= (empty($map->getSize()))?"":" (".$map->getSize().")";?>
                            <div class="btn btn-xs btn-outline-danger btn-remove-map"><i class="fa fa-trash-alt"></i></div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">

    </div>
</div>
<?php
include('footer.php');
