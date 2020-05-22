<div class="settings">
    <div class="row">
        <div class="col">
            <form class="form-settings">
                <div class="form-group row">
                    <label for="map" class="col-sm-2 col-form-label">Carte</label>
                    <div class="col-sm-10">
                        <button class="btn btn-block btn-light btn-choosemap">Explorer</button>
                    </div>
                    <label for="gametype" class="col-sm-2 col-form-label">Mode de jeu</label>
                    <div class="col-sm-10">
                        <select id="gametype" class="form-control">
                            <?php
                            foreach($gametypes as $gametype){
                                ?><option value="<?= $gametype->getId();?>" <?=($gametype->getCode() == $cvars['g_gametype'])?"selected":"";?>><?= $gametype->getName();?></option><?php
                            }
                            ?>
                        </select>
                    </div>
                    <hr/>
                    <label for="timelimit" class="col-sm-2 col-form-label">Dur&eacute;e de la partie</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input class="form-control" id="timelimit" placeholder="10" value="<?=$cvars["timelimit"];?>"/>
                            <div class="input-group-append">min</div>
                        </div>
                    </div>
                    <label for="roundtime" class="col-sm-2 col-form-label">Dur&eacute;e d'un round</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input class="form-control" id="roundtime" placeholder="10" value="<?=$cvars["g_roundtime"];?>"/>
                            <div class="input-group-append">min</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<pre>
    <?php var_dump($cvars);?>
</pre>