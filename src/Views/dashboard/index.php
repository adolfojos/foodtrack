
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

                        </div>
                    </div>
                </div>                
                <!-- Estadísticas-->
                <div class="row row-end">
                    <div class="divider"></div>
                    <div class="col s12">
                        <h6 class="col s12">Estadísticas de Actores (Usuarios)</h6>
                        <?php
                            $kpis_actores = [
                                ['color' => 'light-blue darken-1', 'icon' => 'group', 'role' => 'vocero_parroquial', 'label' => 'Parroquiales'],
                                ['color' => 'lime darken-1', 'icon' => 'work', 'role' => 'vocero_institucional', 'label' => 'Institucionales'],
                                ['color' => 'amber darken-1', 'icon' => 'school', 'role' => 'director', 'label' => 'Directores'],
                                ['color' => 'teal darken-1', 'icon' => 'restaurant', 'role' => 'cocinero', 'label' => 'Cocineros'],
                                // Puedes añadir más roles aquí si los necesitas
                                ];
                            foreach ($kpis_actores as $kpi_a): 
                            // Obtener el conteo seguro, si el rol no existe, es 0
                            $count = $actorStats[$kpi_a['role']] ?? 0;
                        ?>
                            <div class="col s12 m6 l3">
                                <div class="card-panel <?= $kpi_a['color'] ?> white-text center-align kpi-card-main">
                                    <i class="material-icons"><?= $kpi_a['icon'] ?></i>
                                    <h5><?= htmlspecialchars($count) ?></h5>
                                    <p>
                                        <?= htmlspecialchars($kpi_a['label']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div> <!-- card-panel -->
        </div> <!-- col -->
    </div> <!-- conten-body -->
</article>