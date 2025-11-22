<aside id="left-side-menu">
    <ul class="collapsible collapsible-accordion">
        <li class="no-padding"><a href="<?= BASE_URL ?>ruta/uno" class="waves-effect waves-grey"><i class="material-icons">menu</i>Section Name 1</a></li>
        <li class="no-padding"><a href="<?= BASE_URL ?>ruta/dos" class="waves-effect waves-grey"><i class="material-icons">menu</i>Section Name 2</a></li>
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
                            <a href="<?= BASE_URL ?>actors/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add</i> Nuevo Actor
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row row-end">
                    <div class="col s12">
                        <table id="actors_table" class="datatable bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th data-priority="0" class="hide-on-small-only">Nombre Completo</th>
                                    <th data-priority="1" class="hide-on-small-only">Rol</th>
                                    <th data-priority="2" class="hide-on-small-only">Institución(es) Asignada(s)</th>
                                    <th data-priority="5" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($actors)): ?>
                                    <?php foreach ($actors as $a): 
                                        // Mapeo de roles para la interfaz (usando match de PHP 8.0+)
                                        $roleDisplay = match ($a->role) {
                                            'admin' => 'Administrador',
                                            'inspector' => 'Inspector',
                                            'vocero_parroquial' => 'Parroquial',
                                            'vocero_institucional' => 'Institucional',
                                            'director' => 'Director',
                                            'cocinero' => 'Cocinero/a',
                                            default => htmlspecialchars($a->role), 
                                        }; ?>
                                        <tr>
                                            <td class="hide-on-small-only nowrap"><?= htmlspecialchars($a->full_name) ?></td>
                                            <td class="hide-on-small-only"><?= $roleDisplay ?></td>
                                            <td class="hide-on-small-only"> 
                                                <?= htmlspecialchars($a->institution_names ?? 'N/A') ?>
                                            </td>
                                            <td class="adjusted-size">
                                                <a title="Detail" href="<?=BASE_URL?>actors/detail/<?php echo $a->id; ?>"><i class="ico-visibility tiny"></i></a> &nbsp;
                                                <a title="Edit" href="<?= BASE_URL ?>actors/edit/<?php echo $a->id; ?>"><i class="ico-edit tiny"></i></a> &nbsp;
                                                <a title="Delete" href="<?= BASE_URL ?>actors/delete/<?php echo $a->id; ?>" onclick="return confirm('¿Está seguro que desea eliminar al actor <?php echo htmlspecialchars($a->full_name) ?>? Esta acción es irreversible.')"><i class="ico-delete tiny"></i></a> &nbsp;
                                                <a title="PDF"  href="<?= BASE_URL ?>actors/pdf/<?= $a->id ?>"><i class="material-icons">picture_as_pdf</i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="odd">
                                        <td colspan="4" class="dataTables_empty">No hay actores registrados en el sistema.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>