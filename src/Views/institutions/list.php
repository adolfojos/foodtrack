<!-- Views/institutions/list.php -->
<!-- Left side menu (base template) -->
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
                <!-- Título y botón -->
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5><?= htmlspecialchars($title) ?></h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">
                            <a href="<?= BASE_URL ?>institutions/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add</i> Nueva Institución
                            </a>
                        </div>
                    </div>
                </div> 
                <!-- Tabla de institutions -->
                <div class="row row-end">
                    <div class="col s12">
                        
                        <table id="institutions" class="datatable bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th data-priority="0" class="hide-on-small-only">Nombre</th>
                                    <th data-priority="1" class="hide-on-small-only">Director</th>
                                    <th data-priority="2" class="no-sort"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($institutions)): ?>
                                    <?php foreach ($institutions as $inst): ?>
                                        <tr id="<?php echo $inst->id; ?>" class="row_table">
                                            <td class="hide-on-small-only nowrap">
                                                <?= htmlspecialchars($inst->name) ?>
                                            </td>
                                            <td class="hide-on-small-only">
                                                <?= htmlspecialchars($inst->director) ?>
                                            </td>
                                            <td class="adjusted-size">
                                                <a title="Detail" href="<?=BASE_URL?>institutions/detail/<?php echo $inst->id; ?>"><i class="ico-visibility tiny"></i></a>   
                                                <a title="Edit" href="<?= BASE_URL ?>institutions/create/<?php echo $inst->id; ?>"><i class="ico-edit tiny"></i></a>
                                            <a title="Delete" href="<?= BASE_URL ?>institutions/delete/<?php echo $inst->id; ?>" onclick="return confirm('Are you sure you want to delete the institutions <?php echo $inst->name ?>?')"><i class="ico-delete tiny"></i></a>
                                            <!-- <a title="Block" href="<?= BASE_URL ?>institutions/block/<?= $inst->id ?>"  onclick="return confirm('¿Desactivar esta institución?')"><i class="material-icons">block</i></a> -->
                                                <a title="PDF"  href="<?= BASE_URL ?>institutions/pdf/<?= $inst->id ?>"><i class="material-icons">picture_as_pdf</i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                            <tr class="odd">
                                                <td colspan="4" class="dataTables_empty">No data available in this table</td>
                                            </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- card-panel -->
        </div> <!-- col -->
    </div> <!-- conten-body -->
</article>