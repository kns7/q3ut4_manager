<?php
include('header.php');
?>
<div class="row">
    <div class="col-md-9 col-sm-12">
        <div class="row">
            <div class="col-12">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Map actuelle</strong>
                        <h3 class="mb-0 map-name"><?= (!is_null($status->map))?$status->map->getName():"-";?></h3>
                        <p class="card-text mb-auto text-muted">
                            <small>
                                <strong>Durée de la partie: </strong> <span class="timelimit-status"><?= $status->cvars['timelimit'];?></span> min<br/>
                                <strong>Durée du round: </strong> <span class="roundtime-status"><?= $status->cvars['g_roundtime'];?></span> min<br/>
                                <strong>Taille de la carte: </strong> <span class="map-size"><?= $status->map->getSize();?></span>
                            </small>
                        </p>
                    </div>
                    <div class="col-auto">
                        <?php
                        if(!is_null($status->map)){
                            ?><img src="<?= $status->map->getImg();?>" class="map-img" alt="<?= $status->map->getFile();?>"/><?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Mode de jeu</strong>
                        <h3 class="mb-0 gametype-name"><?= (!is_null($status->gametype))?$status->gametype->getName():"-";?></h3>
                        <p class="card-text mb-auto text-muted gametype-description"><?= (!is_null($status->gametype))?$status->gametype->getDescription():"-";?></p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Joueurs en ligne</strong>
                        <div class="m-5 alert alert-info text-center alert-noplayers <?=($players !== false && count($players) > 0)?"hidden":"";?>"><i class="fa fa-info-circle"></i> Personne en ce moment sur le serveur...</div>
                        <table class="table table-hover table-players <?=($players !== false && count($players) > 0)?"":"hidden";?>">
                            <thead>
                            <tr>
                                <th>Joueur</th>
                                <th class="text-right">Score</th>
                                <th class="text-right">Ping</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if($players !== false && count($players) > 0){
                                foreach($players as $p){
                                    ?>
                                    <tr>
                                        <td><?= $p['name'];?></td>
                                        <td class="text-right"><?= $p['score'];?></td>
                                        <td class="text-right"><?= $p['ping'];?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-10 offset-md-0 offset-sm-2 mb-4">
        <button type="button" class="btn btn-block btn-light btn-settings"><i class="fa fa-cogs"></i> Paramètres</button>
        <a type="button" class="btn btn-block btn-light" href="/mapcycle-editor"><i class="fas fa-list-ol"></i> Editer le MapCycle</a>
        <button type="button" class="btn btn-block btn-light btn-reload"><i class="fa fa-sync-alt"></i> Recharger le serveur</button>
    </div>
</div>
<?php
include('footer.php');
