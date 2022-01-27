<?php
include_once PATH_COMPOSANTS . 'header.php';
?>
<!--pour ajax transfert form-->
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src=<?= PATH_ASSETS . 'js'.SEP.'json-to-mysql.js' ?>></script>

<div class="container">
    <h3 class="my-4"><?=$title?></h3>
    <form id="frm-transfert" action='script_transfert-json-mysql' method="post">
    <input type="hidden" name="source" value="transfer-json-to-mysql">
        <div class="row">

                <div class="col-md-8">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-transfert">Transférer</button>
                </div>

            <div>
                <span class="text-danger" id="msg"></span>
            </div>
        </div>
    </form>
</div>