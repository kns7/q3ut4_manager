<?php
include('header.php');
?>
<div class="row">
    <h4>Liste des joueurs connectés</h4>
    <div class="col-4">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Joueur</th>
                <th>Score</th>
                <th>Ping</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($players) == 0){
                ?>
                <tr>
                    <th colspan="3"><div class="alert-info">Personne en ce moment sur le serveur...</div></th>
                </tr>
                <?php
            }else{
                foreach($players as $p){
                    ?>
                    <tr>
                        <th><?= $p['name'];?></th>
                        <th><?= $p['score'];?></th>
                        <th><?= $p['ping'];?></th>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <th></th>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<?php
include('footer.php');
