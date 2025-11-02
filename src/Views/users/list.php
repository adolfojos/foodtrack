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
                            <h5><?php echo $title; ?></h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">
                            <a class="btn" href="<?= BASE_URL ?>users/create" title="Create new user">+ Create new user</a>
                        </div>
                    </div>
                </div>                

                <!-- Tabla de equipos -->
                <div class="row row-end">
                    <div class="col s12">
                        <table id="users" class="bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th data-priority="0" class="hide-on-small-only">ID</th>
                                    <th data-priority="1" class="hide-on-small-only">Username</th>
                                    <th data-priority="2" class="hide-on-small-only">Role</th>
                                    <th data-priority="3" class="no-sort">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr class="odd">
                                        <td colspan="4" class="dataTables_empty">No data available in this table</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                        <tr id="<?php echo $u->id; ?>" class="row_table">
                                            <td class="hide-on-small-only nowrap"><?php echo $u->id; ?></td>
                                            <td class="uppercase"><?php echo htmlspecialchars($u->username); ?></td>
                                            <td class="hide-on-small-only"><?php echo htmlspecialchars(ucfirst($u->role)); ?></td>
                                            <td class="adjusted-size">
                                                <a title="Edit" href="<?= BASE_URL ?>users/edit/<?php echo $u->id; ?>"><i class="ico-edit tiny"></i></a>
                                                <a title="Delete" href="<?= BASE_URL ?>users/delete/<?php echo $u->id; ?>" onclick="return confirm('Are you sure you want to delete the user <?php echo $u->username; ?>?')"><i class="ico-delete tiny"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- card-panel -->
        </div> <!-- col -->
    </div> <!-- conten-body -->
</article>
