<?php
use Web\Url;
use Usage\Text;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Liste des groupes
        </h1>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="widget-container fluid-height clearfix">
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="classes-table">
                        <thead>
                            <th>#</th>
                            <th>Numéro</th>
							<th>Code</th>
                            <th>Nombre d'élève dans le groupe</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
							<?php $i=1; ?>
							<?php foreach($groups as $group):?>
								<tr>
									<td><span id="group_<?= $group->id_grpe; ?>"><?= $i ?></span></td>
									<td><?= $group->num_grpe; ?></td>
									<td><?= $group->code; ?></td>
									<td><?= $group->nbStudent; ?></td>
									<td>
                                        <button onclick="del('//<?=Url::connect("ajax_delete_group", $group->id_grpe);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="/group/<?= $group->id_grpe; ?>/<?= Text::slug($group->num_grpe); ?>" class="btn btn-info">Voir</a>
                                    </td>
                                </tr>
								<?php $i++; ?>
							<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Ajouter un groupe
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?php echo Url::connect("ajax_add_group");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Nom du groupe</label>
                            <input class="form-control" id="name" name="name" type="text" required />
                        </div>
                        <button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>