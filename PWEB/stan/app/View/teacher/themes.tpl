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
            Liste des thèmes
        </h1>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="widget-container fluid-height clearfix">
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="themes-table">
                        <thead>
                            <th>#</th>
                            <th>Titre</th>
							<th>Description</th>
							<th>Nombre de questions</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
							<?php $i=1; ?>
							<?php foreach($themes as $theme):?>
								<tr>
									<td><span id="theme_<?= $theme->id_theme; ?>"><?= $i ?></span></td>
									<td><?= $theme->titre_theme; ?></td>
									<td><?= $theme->desc_theme; ?></td>
									<td><?= $theme->nbQuestion; ?></td>
									<td>
                                        <button onclick="del('//<?=Url::connect("ajax_delete_theme", $theme->id_theme);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="//<?= Url::connect("theme_view", $theme->id_theme, Text::slug($theme->titre_theme));?>" class="btn btn-info">Voir</a>
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
                    <i class="fa fa-shield"></i>Ajouter un thème
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?php echo Url::connect("ajax_add_theme");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="name" name="name" type="text" required />
                        </div>
						 <div class="form-group">
                            <label for="name">Description</label>
                            <textarea class="form-control" id="desc" name="desc" required></textarea>
                        </div>
                        <button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>