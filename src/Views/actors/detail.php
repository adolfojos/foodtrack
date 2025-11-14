<main class="container">
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <div class="card">
                <div class="card-content">
                    
                    <span class="card-title center-align">
                        <i class="material-icons">person</i> Ficha de Actor
                    </span>
                    <h4 class="center-align grey-text text-darken-2">
                        <?= htmlspecialchars($actor->full_name) ?>
                    </h4>
                    
                    <div class="divider"></div>
                    
                    <div class="section">
                        <h5>Datos Generales</h5>
                        <table class="striped">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td><?= htmlspecialchars($actor->id) ?></td>
                                </tr>
                                <tr>
                                    <th>Rol</th>
                                    <td>
                                        <strong><?= htmlspecialchars(ucfirst($actor->role)) ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        <?php if ($actor->active): ?>
                                            <span class="new badge green" data-badge-caption="Activo"></span>
                                        <?php else: ?>
                                            <span class="new badge red" data-badge-caption="Inactivo"></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="divider"></div>

                    <div class="section">
                        <h5>Relaciones</h5>
                        <table class="striped">
                            <tbody>
                                <tr>
                                    <th>Instituciones Vinculadas</th>
                                    <td>
                                        <?php if (!empty($institutions)): ?>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <?php foreach ($institutions as $inst): ?>
                                                    <li>
                                                        <?= htmlspecialchars($inst->name) ?>
                                                        <small>(ID: <?= $inst->id ?>)</small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A (Sin Asignar)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usuario de Acceso</th>
                                    <td>
                                        <?= htmlspecialchars($actor->user_name ?? 'N/A (Sin Cuenta)') ?>
                                        <?php if ($actor->user_name): ?>
                                            <small>(ID: <?= htmlspecialchars($actor->user_id) ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
                <div class="card-action right-align">
                    <a href="<?= BASE_URL ?>actors/create/<?= htmlspecialchars($actor->id) ?>" class="btn-small orange waves-effect waves-light">
                        <i class="material-icons left">edit</i> Editar
                    </a>
                    <a href="<?= BASE_URL ?>actors/pdf/<?= htmlspecialchars($actor->id) ?>" target="_blank" class="btn-small red darken-1 waves-effect waves-light">
                        <i class="material-icons left">picture_as_pdf</i> PDF
                    </a>
                    <a href="<?= BASE_URL ?>actors" class="btn-small grey waves-effect waves-light">
                        <i class="material-icons left">arrow_back</i> Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>