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
                            <a href="<?= BASE_URL ?>deliveries/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add_shopping_cart</i> Nueva Entrega
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row row-end">
                    <div class="col s12">
                        <table id="deliveries_table" class="datatable bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th data-priority="0" class="hide-on-small-only">Fecha</th>
                                    <th data-priority="1">Institución</th>
                                    <th data-priority="2" class="hide-on-small-only">Tipo</th>
                                    <th data-priority="3">Cant. Entregada</th>
                                    <th data-priority="4" class="hide-on-small-only">Receptor</th>
                                    <th data-priority="5" class="no-sort">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($deliveries)): ?>
                                    <?php foreach ($deliveries as $d): ?>
                                        <tr>
                                            <td class="hide-on-small-only nowrap">
                                                <?= date('d/m/Y', strtotime($d->date)) ?>
                                            </td>
                                            
                                            <td>
                                                <?= htmlspecialchars($d->institution_name) ?>
                                            </td>

                                            <td class="hide-on-small-only">
                                                <span class="chip white-text 
                                                    <?= ($d->reception_type === 'CLAP') ? 'blue lighten-1' : 
                                                       (($d->reception_type === 'PROTEINA') ? 'orange lighten-1' : 'green lighten-1') ?>">
                                                    <?= htmlspecialchars($d->reception_type ?? 'OTRO') ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($d->reception_type === 'CLAP'): ?>
                                                    <strong><?= (int)$d->qty_groceries ?></strong> Bolsas
                                                
                                                <?php elseif ($d->reception_type === 'PROTEINA'): ?>
                                                    <strong><?= number_format($d->qty_proteins, 2, ',', '.') ?></strong> Kg
                                                
                                                <?php elseif ($d->reception_type === 'FRUVERT'): ?>
                                                    <strong><?= number_format($d->qty_fruits, 2, ',', '.') ?></strong> Kg
                                                
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>

                                            <td class="hide-on-small-only">
                                                <?= htmlspecialchars($d->receiver_name) ?>
                                            </td>

                                            <td class="adjusted-size">
                                                <a title="Ver Detalle" href="<?= BASE_URL ?>deliveries/detail/<?= $d->id ?>">
                                                    <i class="ico-visibility tiny"></i>
                                                </a> &nbsp;
                                                
                                                <a title="Editar" href="<?= BASE_URL ?>deliveries/edit/<?= $d->id ?>">
                                                    <i class="ico-edit tiny"></i>
                                                </a> &nbsp;

                                                <a title="Eliminar" href="<?= BASE_URL ?>deliveries/delete/<?= $d->id ?>" 
                                                   onclick="return confirm('¿Está seguro que desea eliminar esta entrega? Se revertirá el inventario.')">
                                                    <i class="ico-delete tiny"></i>
                                                </a> &nbsp;
                                                
                                                <a title="PDF" href="<?= BASE_URL ?>deliveries/pdf/<?= $d->id ?>">
                                                    <i class="material-icons">picture_as_pdf</i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="center-align">No hay entregas registradas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>  
        </div>  
    </article>