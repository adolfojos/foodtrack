
<aside id="left-side-menu">
    <ul class="collapsible collapsible-accordion">
        <li class="no-padding">
            <a href="ROUTE_1.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Section Name 1
            </a>
        </li>
        <li class="no-padding">
            <a href="ROUTE_2.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Section Name 2
            </a>
        </li>
    </ul>
</aside>
<article>
    <div class="conten-body">
        <div class="col s12 m12 l12">
            <div class="card-panel">
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5><?= htmlspecialchars($title) ?></h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">
                            <a title="Modificar" href="<?= BASE_URL ?>institutions/create/<?php echo $institution->id; ?>" class="btn waves-effect waves-light">
                                <i class="material-icons left">edit</i> Modificar
                            </a>
                        </div>
                    </div>
                </div>                
                <div class="row row-end">
                    <div class="col s12">
                        <ul>
                            <li>
                                <b>Nombre:</b> <?= !empty($institution->name) ? htmlspecialchars($institution->name) : '-' ?>
                            </li>
                            <li>
                                <b>Codigo Plante:</b> <?= !empty($institution->campus_code) ? htmlspecialchars($institution->campus_code) : '-' ?>
                            </li>
                            <li>
                                <b>Codigo Sica:</b> <?= !empty($institution->sica_code) ? htmlspecialchars($institution->sica_code) : '-' ?>
                            </li>
                            <li>
                                <b>Matricula:</b> <?= !empty($institution->total_enrollment) ? htmlspecialchars($institution->total_enrollment) : '-' ?>
                            </li>
                            <li>
                                <br>
                                <b>Director:</b> <?= !empty($institution->director) ? htmlspecialchars($institution->director) : '-' ?>
                            </li>
                            <li>
                                <b>Teléfono:</b> <?= !empty($institution->phone) ? htmlspecialchars($institution->phone) : '-' ?>
                            </li>
                            <li>
                                <br
                                <b>Municipio:</b> <?= !empty($institution->municipality) ? htmlspecialchars($institution->municipality) : '-' ?>
                            </li>
                            <li>
                                <b>Parroquia:</b> <?= !empty($institution->parish) ? htmlspecialchars($institution->parish) : '-' ?>
                            </li>
                            <li>
                                <b>Estado del Plante:</b> <?= $institution->active ? '<span class="green-text">Activa</span>' : '<span class="red-text">Inactiva</span>' ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>