<?php
include('header.php');
?>
<div class="row">
    <div class="col-8">
        <div class="row">
            <div class="col-6">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Map actuelle</strong>
                        <h3 class="mb-0"><?= (!is_null($status->map))?$status->map->getName():"-";?></h3>
                        <p class="card-text mb-auto"><?= (!is_null($status->map))?$status->map->getDescription():"-";?></p>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <?php
                        if(!is_null($status->map)){
                            ?><img src="<?= $status->map->getImgUrl();?>" alt="<?= $status->map->getFile();?>"/><?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Mode de jeu</strong>
                        <h3 class="mb-0"><?= (!is_null($status->gametype))?$status->gametype->getName():"-";?></h3>
                        <p class="card-text mb-auto text-muted"><?= (!is_null($status->gametype))?$status->gametype->getDescription():"-";?></p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">Joueurs en ligne</strong>
                        <p class="card-text mb-auto text-muted">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Joueur</th>
                                <th>Score</th>
                                <th>Ping</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if($players === false){
                                echo "Bad rconpassword";
                            }else{
                            if(count($players) == 0){
                                ?>
                                </tbody>
                            </table>
                            <div class="m-5 alert alert-info text-center">Personne en ce moment sur le serveur...</div>
                            <?php
                            }else{
                                foreach($players as $p){
                                    ?>
                                    <tr>
                                        <td><?= $p['name'];?></td>
                                        <td><?= $p['score'];?></td>
                                        <td><?= $p['ping'];?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        </tbody>
                        </table>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-block btn-light"><i class="fa fa-cogs"></i> Paramètres</button>
        <button type="button" class="btn btn-block btn-light"><i class="fa fa-sync-alt"></i> Recharger le serveur</button>
    </div>
</div>


<?php
include('footer.php');
