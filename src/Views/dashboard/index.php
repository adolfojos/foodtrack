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

<!-- Main content -->
<article>
    <div class="conten-body">
        <!-- KPI panel -->
        <div class="col s12 m12 l12">
            <div class="card-panel">
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5><?php echo $title; ?></h5>
                        </div>
                    </div>
                </div>

                <div class="card-content bodytext">
                    <div class="row">
                        
                    </div>

                    <!-- Action button -->
                    <div class="row btn-actions">
                        <div class="col s12">
                            <a class="btn btn-large green darken-1">
                                <i class="material-icons left">add</i> Open Ticket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table of latest pending tickets -->
        <div class="col s12 m12 l12">
            <div class="card-panel">
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5>Latest Pending Tickets</h5>
                        </div>
                    </div>
                </div>

                <div class="row row-end">
                    <div class="col s12">
                        <div class="text-info">
                            <div class="text-content">
                                These are the latest pending tickets registered in the system.
                            </div>
                        </div>

                        <table id="table" class="bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th class="hide-on-small-only">ID</th>
                                    <th>Device</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="row_table">
                                    <td class="hide-on-small-only"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="adjusted-size">
                                        <a href="<?= BASE_URL ?>tickets/view/1" class="btn btn-sm btn-primary">View</a>
                                        <a href="<?= BASE_URL ?>tickets/assign/1" class="btn btn-sm btn-warning">Assign</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-muted">No recent pending tickets.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Tickets In Progress -->
        <div class="col s12 m12 l12">
            <div class="card-panel">
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5>Latest Tickets In Progress</h5>
                        </div>
                    </div>
                </div>

                <div class="row row-end">
                    <div class="col s12">
                        <div class="text-info">
                            <div class="text-content">
                                These are the tickets currently in progress registered in the system.
                            </div>
                        </div>
                        <table class="bordered highlight table-responsive">
                            <thead>
                                <tr>
                                    <th class="hide-on-small-only">ID</th>
                                    <th>Device</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="row_table">
                                    <td class="hide-on-small-only"> </td>
                                    <td>  </td>
                                    <td>  </td>
                                    <td> </td>
                                    <td class="adjusted-size">
                                        <a href="<?= BASE_URL ?>tickets/view/1" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-muted">No tickets in progress.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
