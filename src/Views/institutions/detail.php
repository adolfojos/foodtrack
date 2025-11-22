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
                            <h5>Detalle de la Institución</h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">
                            <a title="Exportar PDF" href="<?= BASE_URL ?>actors/pdf/<?php echo $institution->id; ?>" class="btn waves-effect waves-light">
                                <i class="material-icons left">picture_as_pdf</i> Exportar PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6">
                        <div class="card-panel blue-grey lighten-5">
                            <span class="blue-grey-text text-darken-4">
                                <h6><i class="material-icons left">account_box</i> Datos Principales</h6>
                                <p><strong>Nombre:</strong> <?= htmlspecialchars($institution->name ?? 'N/A') ?></p>
                                <p><strong>Código Plantel:</strong> <?= htmlspecialchars($institution->campus_code ?? '-') ?></p>
                                <p><strong>Código SICA:</strong> <?= htmlspecialchars($institution->sica_code ?? '-') ?></p>
                                <div class="divider" style="margin: 15px 0;"></div>
                                <p><strong>Matrícula Total:</strong> <?= number_format($institution->total_enrollment ?? 0, 0, ',', '.') ?> estudiantes</p>
                                <p><strong>Teléfono:</strong> <?php if ($institution->active): ?>
                                        <span class="new badge green" data-badge-caption="Activa"></span>
                                    <?php else: ?>
                                        <span class="new badge red" data-badge-caption="Inactiva"></span>
                                    <?php endif; ?></p>
                            </span>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="card-panel teal lighten-5">
                            <span class="teal-text text-darken-4">
                                <h6><i class="material-icons left">verified_user</i> Ubicación y Contacto</h6>
                                <p>
                                    <strong>Teléfono</strong>: <?= htmlspecialchars($institution->phone ?? '-') ?>
                                </p>
                                <p>
                                    <strong>Municipio:</strong> <?= htmlspecialchars($institution->municipality ?? '-') ?>
                                </p>
                                <p>
                                    <strong>Parroquia:</strong> <?= htmlspecialchars($institution->parish ?? '-') ?>
                                </p>
                                <div class="divider" style="margin: 15px 0;"></div>
                                <p>
                                    <strong>Director:</strong>
                                    <?php 
                                        // Asumimos que $directors viene como un array (incluso si solo tiene 1 elemento)
                                        if (!empty($directors) && is_array($directors)) {
                                            $director = reset($directors); // Tomar solo el primer director
                                            echo htmlspecialchars($director->full_name) . " (C.I. " . htmlspecialchars($director->national_id) . ")";
                                        } else {
                                            // Fallback si no hay director asignado o si la relación es 1:1
                                            echo htmlspecialchars($institution->director_full_name ?? 'N/A - Sin Director'); 
                                        }
                                        ?>
                                </p>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row right-align">
                    <a title="Modificar" href="<?= BASE_URL ?>institutions/create/<?= htmlspecialchars($institution->id) ?>" class="btn waves-effect waves-light orange darken-1">
                        <i class="material-icons left">edit</i> Editar Actor
                    </a>
                    <a href="<?= BASE_URL ?>institutions" class="btn grey waves-effect waves-light">
                        <i class="material-icons left">arrow_back</i> Volver a la Lista
                    </a>
                </div> 
        </div> 
    </div> 
</article>