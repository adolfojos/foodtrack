<!-- Views/receptions/detail.php -->
<aside id="left-side-menu">
    <ul class="collapsible collapsible-accordion">
        <li class="no-padding">
            <a href="ROUTE_1.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Section Name 1
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
                            <a title="Modificar" href="<?= BASE_URL ?>receptions/pdf/<?php echo $reception->id; ?>" class="btn waves-effect waves-light">
                                <i class="material-icons left">picture_as_pdf</i> Exportar PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row row-end">
                    <div class="col s12">
                        <div class="clearfix"></div>
                    </div>
                </div>

                <!-- 1. INFORMACIÓN GENERAL -->
                <div class="row">
                    <div class="col s12 m6">
                        <div class="card-panel blue-grey lighten-5">
                            <span class="blue-grey-text text-darken-4">
                    <h6><i class="material-icons left">info_outline</i> Información General</h6>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($reception->date)) ?></p>
                    <p><strong>Tipo de Recepción:</strong> <?= htmlspecialchars($reception->reception_type) ?></p>
                    
                    <?php if ($reception->summary_quantity > 0): ?>
                        <p><strong>Cantidad Resumen:</strong> <?= (int)$reception->summary_quantity ?> (Ej. Bolsas)</p>
                    <?php endif; ?>
                </span>
                        </div>
                    </div>

                    <!-- 2. ACTORES INVOLUCRADOS -->
                    <div class="col s12 m6">
                        <div class="card-panel teal lighten-5">
                            <span class="teal-text text-darken-4">
                    <h6><i class="material-icons left">person_pin</i> Actores</h6>
                    <p><strong>Inspector Responsable:</strong> <?= htmlspecialchars($reception->inspector_name) ?></p>
                    <p><strong>Vocero Parroquial:</strong> <?= htmlspecialchars($reception->vocero_parroquial_name) ?></p>
                </span>
                        </div>
                    </div>
                </div>

                <!-- 3. DETALLE DE PRODUCTOS RECIBIDOS -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel white">
                            <h6><i class="material-icons left">storage</i> Productos Recibidos</h6>
                            <?php if (!empty($items)): ?>
                            <table class="striped responsive-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Producto</th>
                                        <th style="width: 25%;" class="center-align">Cantidad</th>
                                        <th style="width: 25%;" class="center-align">Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($item->product_name) ?>
                                        </td>
                                        <td class="center-align">
                                            <?= htmlspecialchars($item->quantity) ?>
                                        </td>
                                        <td class="center-align">
                                            <?= htmlspecialchars($item->unit) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p class="center-align red-text"><strong>No se registraron productos para esta recepción.</strong></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 4. OBSERVACIONES -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel amber lighten-5">
                            <span class="amber-text text-darken-4">
                    <h6><i class="material-icons left">comment</i> Observaciones</h6>
                    <div class="flow-text">
                        <?= $reception->notes ? nl2br(htmlspecialchars($reception->notes)) : 'No hay observaciones registradas.' ?>
                    </div>
                </span>
                        </div>
                    </div>
                </div>
                <div class="row right-align">
                    <a href="<?= BASE_URL ?>receptions" class="btn grey waves-effect waves-light">
                        <i class="material-icons left">arrow_back</i> Volver a Recepciones
                    </a>
                </div>
</article>
<style>
    .clearfix { clear: both; }
        .card-panel h6 {
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: 500;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 5px;
        }
        .card-panel p {
            margin: 5px 0;
        }
    
</style>