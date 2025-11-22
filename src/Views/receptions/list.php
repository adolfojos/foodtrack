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
                            <a href="<?= BASE_URL ?>receptions/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add</i> Nueva Recepción
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row row-end">
                    <div class="col s12">
                        <table id="receptions_table" class="datatable bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th data-priority="0" class="hide-on-small-only">Fecha</th>
                                    <th data-priority="1" class="hide-on-small-only">Tipo</th>
                                    <th data-priority="2" class="hide-on-small-only">Cant. Resumen</th>
                                    <th data-priority="5" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($receptions)): ?>
                                    <?php foreach ($receptions as $r): ?>
                                        <tr>
                                            <td class="hide-on-small-only nowrap"><?= htmlspecialchars($r->date) ?></td>
                                            <td class="hide-on-small-only"><?= htmlspecialchars($r->reception_type) ?></td>
                                            
                                            <td class="hide-on-small-only">
                                                <?php if ($r->reception_type === 'CLAP'): ?>
                                                    <strong><?= (int)$r->summary_quantity ?></strong> Bolsas
                                                
                                                <?php elseif ($r->reception_type === 'FRUVERT' || $r->reception_type === 'PROTEINA'): ?>
                                                    <strong><?= (float)$r->summary_quantity ?></strong> Kg
                                                
                                                <?php elseif ($r->summary_quantity > 0): ?>
                                                    <?= (float)$r->summary_quantity ?> Und
                                                
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="adjusted-size">
                                                <a title="Detail" href="<?=BASE_URL?>receptions/detail/<?php echo $r->id; ?>"><i class="ico-visibility tiny"></i></a> &nbsp;
                                                <a title="Edit" href="<?= BASE_URL ?>receptions/edit/<?php echo $r->id; ?>"><i class="ico-edit tiny"></i></a> &nbsp;
                                                <a title="Delete" href="<?= BASE_URL ?>receptions/delete/<?php echo $r->id; ?>" onclick="return confirm('¿Está seguro que desea eliminar esta recepción?')"><i class="ico-delete tiny"></i></a> &nbsp;
                                                <a title="PDF" href="<?= BASE_URL ?>receptions/pdf/<?= $r->id ?>"><i class="material-icons">picture_as_pdf</i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No hay recepciones registradas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>  </div>  </div>  </article>