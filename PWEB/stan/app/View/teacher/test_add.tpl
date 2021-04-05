<?php
use Web\Url;
use Web\Assets;
use Usage\Text;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Créer un test
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="add">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le test
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_add_test");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="titre" name="titre" type="text" required />
                        </div>
						<div class="form-group" id="group_input">
							<label for="name">Groupe</label>
							<select class="form-control" name="group_id" id="group_id">
								<?php foreach($groups as $group) : ?>
									<option value="<?= $group->id_grpe; ?>"><?= $group->num_grpe; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<button class="btn btn-primary pull-right" type="submit">Créer</button>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>