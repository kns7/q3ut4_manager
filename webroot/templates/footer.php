<div class="settings border rounded overflow-hidden flex-md-row shadow-lg p-3"></div>
</div> <!-- Container Fluid-->
<div class="overlay"></div>
<div class="loader"><i class="fas fa-spinner fa-spin"></i> Chargement</div>
<!-- Modal Box -->
<div class="modal modal-settings" tabindex="-1" role="dialog" id="settings">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content settings-content"></div>
    </div>
</div>

<div class="modal modal-confirm" tabindex="-1" role="dialog" id="reload-confirm">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Rechargement du serveur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info reload-newconf">
                    <p>
                        Voici la nouvelle configuration qui va &ecirc;tre lanc&eacute;e:
                    </p>
                </div>
                <p>Tu es s&ucirc;r de relancer la partie en cours ? </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><i class="fa fa-times"></i> Non, pas vraiment...</button>
                <button type="button" class="btn btn-outline-success"><i class="fa fa-check"></i> C'est parti !</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>