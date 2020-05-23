<div class="modal-header">
    <h5>Paramètres du serveur</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row justify-content-center">
        <div class="col">
            <form class="form-settings">
                <div class="form-group row">
                    <label for="map" class="col-sm-4 col-form-label">Carte</label>
                    <div class="col-sm-2"><img class="map-preview" src="<?= (!is_null($actual['map']))?$actual['map']->getImg():"/maps/unknown.jpg";?>"/></div>
                    <div class="col-sm-6">
                        <select class="form-control" id="map">
                        <?php
                        foreach($maps as $map){
                            ?><option value="<?= $map->getId();?>" data-img="<?=$map->getImg();?>" <?=($map->getFile() == $cvars['mapname'])?"selected":"";?>><?= $map->getName();?></option><?php
                        }
                        ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="gametype" class="col-sm-4 col-form-label">Mode de jeu</label>
                    <div class="col-sm-8">
                        <select id="gametype" class="form-control">
                            <?php
                            foreach($gametypes as $gametype){
                                ?><option value="<?= $gametype->getId();?>" <?=($gametype->getCode() == $cvars['g_gametype'])?"selected":"";?>><?= $gametype->getName();?></option><?php
                            }
                            ?>
                        </select>
                        <p class="gametype-preview text-muted"><small><?= $actual['gametype']->getDescription();?></small></p>
                    </div>
                </div>
                <hr/>
                <div class="form-group row">
                    <label for="timelimit" class="col-sm-4 col-form-label">Dur&eacute;e de la partie</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input class="form-control" id="timelimit" placeholder="10" value="<?=$cvars["timelimit"];?>"/>
                            <div class="input-group-append"><span class="input-group-text">min</span></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="roundtime" class="col-sm-4 col-form-label">Dur&eacute;e d'un round</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input class="form-control" id="roundtime" placeholder="10" value="<?=$cvars["g_roundtime"];?>"/>
                            <div class="input-group-append"><span class="input-group-text">min</span></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="reload" class="col-sm-4 col-form-label">Relancer tout de suite ? </label>
                    <div class="col-sm-2">
                        <input type="checkbox" id="reload" checked data-toggle="toggle" data-on="Oui" data-off="Non">
                    </div>
                    <div class="col-sm-6">
                        <p>
                            <small>
                                <strong class="text-primary">Oui</strong> Le serveur charge immédiatement la nouvelle configuration et relance la partie
                                <br/>
                                <strong class="text-dark">Non</strong> Le serveur chargera la nouvelle configuration au prochain changement de carte
                            </small>
                        </p>
                        <p>
                            <div class="alert alert-warning"><small><i class="fa fa-exclamation-circle"></i> Un changement de carte relancera tout de suite la nouvelle configuration ! </small></div>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Retour</button>
    <button type="button" class="btn btn-outline-success"><i class="fa fa-save"></i> Enregistrer</button>
</div>