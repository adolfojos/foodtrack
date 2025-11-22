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
                            <a title="Exportar PDF" href="<?= BASE_URL ?>actors/pdf/<?php echo $actor->id; ?>" class="btn waves-effect waves-light">
                                <i class="material-icons left">picture_as_pdf</i> Exportar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12 m6">
                        <div class="card-panel blue-grey lighten-5">
                            <span class="blue-grey-text text-darken-4">
                                <h6><i class="material-icons left">account_box</i> Información Personal y Contacto</h6>
                                <p><strong>ID Actor:</strong> <?= htmlspecialchars($actor->id) ?></p>
                                <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($actor->full_name) ?></p>
                                <p><strong>Cédula / Identificación:</strong> <?= htmlspecialchars($actor->national_id) ?></p>

                                <div class="divider" style="margin: 15px 0;"></div>
                                
                                <p><strong>Correo Electrónico:</strong> <?= htmlspecialchars($actor->email ?? '-') ?></p>
                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($actor->phone ?? '-') ?></p>
                            </span>
                        </div>
                    </div>

                    <div class="col s12 m6">
                        <div class="card-panel teal lighten-5">
                            <span class="teal-text text-darken-4">
                                <h6><i class="material-icons left">verified_user</i> Detalles de Rol y Sistema</h6>
                                <p>
                                    <strong>Rol Asignado:</strong>
                                    <?= match ($actor->role) {
                                        'admin' => 'Administrador',
                                        'inspector' => 'Inspector',
                                        'vocero_parroquial' => 'Vocero Parroquial',
                                        'vocero_institucional' => 'Vocero Institucional',
                                        'director' => 'Director',
                                        'cocinero' => 'Cocinero/a',
                                        null => 'N/A',
                                        default => htmlspecialchars($actor->role), 
                                    } ?>
                                </p>
                                <p>
                                    <strong>Estado del Actor:</strong>
                                    <?php if ($actor->active): ?>
                                        <span class="new badge green" data-badge-caption="Activo"></span>
                                    <?php else: ?>
                                        <span class="new badge red" data-badge-caption="Inactivo"></span>
                                    <?php endif; ?>
                                </p>

                                <div class="divider" style="margin: 15px 0;"></div>

                                <p>
                                    <strong>Usuario de Acceso:</strong>
                                    <?= htmlspecialchars($actor->user_name ?? 'N/A (Sin Cuenta)') ?>
                                    <?php if ($actor->user_name): ?>
                                        <small>(ID de Usuario: <?= htmlspecialchars($actor->user_id) ?>)</small>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Creado el:</strong> <?= date('d/m/Y H:i:s', strtotime($actor->created_at)) ?></p>
                                <p><strong>Última Actualización:</strong> <?= date('d/m/Y H:i:s', strtotime($actor->updated_at)) ?></p>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel amber lighten-5">
                            <span class="amber-text text-darken-4">
                                <h6><i class="material-icons left">business</i> Relaciones Institucionales</h6>
                                <?php if (!empty($institutions)): ?>
                                    <ul class="collection" style="border: none; margin: 0;">
                                        <?php foreach ($institutions as $inst): ?>
                                            <li class="collection-item amber lighten-5">
                                                <strong class="flow-text"><?= htmlspecialchars($inst->name) ?></strong>
                                                <small>(ID: <?= $inst->id ?>)</small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="flow-text">Este actor **no está asignado** a ninguna institución.</p>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row right-align">
                    <a title="Modificar" href="<?= BASE_URL ?>actors/edit/<?= htmlspecialchars($actor->id) ?>" class="btn waves-effect waves-light orange darken-1">
                        <i class="material-icons left">edit</i> Editar Actor
                    </a>
                    <a href="<?= BASE_URL ?>actors" class="btn grey waves-effect waves-light">
                        <i class="material-icons left">arrow_back</i> Volver a la Lista
                    </a>
                </div>

            </div>
        </div>
    </div>
</article>

<style>
/* ... (Estilos CSS) ... */
</style>