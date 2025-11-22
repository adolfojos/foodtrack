<article class="container">
    <div class="card-panel">
        <div class="row">
            <div class="col s12 m8">
                <h4>Detalle de Entrega #<?= $delivery->id ?></h4>
            </div>
            <div class="col s12 m4 right-align">
                <a href="<?= BASE_URL ?>deliveries" class="btn grey waves-effect waves-light">
                    <i class="material-icons left">arrow_back</i> Volver
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m6">
                <ul class="collection with-header">
                    <li class="collection-header grey lighten-4"><h5>Datos Generales</h5></li>
                    <li class="collection-item">
                        <i class="material-icons tiny left">school</i>
                        <b>Institución:</b> <br> <?= htmlspecialchars($delivery->institution_name) ?>
                    </li>
                    <li class="collection-item">
                        <i class="material-icons tiny left">event</i>
                        <b>Fecha:</b> <?= date('d/m/Y', strtotime($delivery->date)) ?>
                    </li>
                    <li class="collection-item">
                        <i class="material-icons tiny left">person</i>
                        <b>Recibido por:</b> <br> <?= htmlspecialchars($delivery->receiver_name) ?>
                    </li>
                </ul>
            </div>

            <div class="col s12 m6">
                <ul class="collection with-header">
                    <li class="collection-header teal lighten-5">
                        <h5>Resumen - <?= htmlspecialchars($delivery->reception_type ?? 'GENERAL') ?></h5>
                    </li>

                    <?php if ($delivery->reception_type === 'CLAP'): ?>
                        <li class="collection-item">
                            <span class="title grey-text">Cantidad Entregada</span>
                            <h5><?= (int)$delivery->qty_groceries ?> <small>Bolsas/Combos</small></h5>
                        </li>
                        <li class="collection-item">
                            <i class="material-icons tiny left green-text">check_circle</i>
                            Origen: Recepción CLAP (Total <?= (int)$delivery->source_bags_total ?> bolsas)
                        </li>

                    <?php elseif ($delivery->reception_type === 'PROTEINA'): ?>
                        <li class="collection-item">
                            <span class="title grey-text">Peso Total Entregado</span>
                            <h5><?= number_format($delivery->qty_proteins, 2, ',', '.') ?> <small>Kg</small></h5>
                        </li>
                        <li class="collection-item">
                            <i class="material-icons tiny left orange-text">restaurant</i>
                            Rubro: Proteína (Carne, Pollo, Huevos, etc.)
                        </li>

                    <?php elseif ($delivery->reception_type === 'FRUVERT'): ?>
                        <li class="collection-item">
                            <span class="title grey-text">Peso Total Entregado</span>
                            <h5><?= number_format($delivery->qty_fruits, 2, ',', '.') ?> <small>Kg</small></h5>
                        </li>
                        <li class="collection-item">
                            <i class="material-icons tiny left green-text">eco</i>
                            Rubro: Frutas y Verduras
                        </li>

                    <?php else: ?>
                        <?php if($delivery->qty_groceries > 0): ?>
                            <li class="collection-item"><b>Bolsas:</b> <?= $delivery->qty_groceries ?></li>
                        <?php endif; ?>
                        <?php if($delivery->qty_proteins > 0): ?>
                            <li class="collection-item"><b>Proteína:</b> <?= $delivery->qty_proteins ?> Kg</li>
                        <?php endif; ?>
                        <?php if($delivery->qty_fruits > 0): ?>
                            <li class="collection-item"><b>Fruver:</b> <?= $delivery->qty_fruits ?> Kg</li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="card-panel grey lighten-5" style="border: 1px solid #e0e0e0;">
                    <h5 style="margin-top:0;">Salida de Inventario (Detalle)</h5>
                    
                    <p class="grey-text">
                        <?php if ($delivery->reception_type === 'CLAP'): ?>
                            <i class="material-icons tiny">info</i> 
                            Estos productos se calcularon multiplicando el contenido de 1 bolsa por las 
                            <strong><?= (int)$delivery->qty_groceries ?></strong> bolsas entregadas.
                        <?php else: ?>
                            <i class="material-icons tiny">info</i> 
                            Estos productos representan la parte proporcional (Kg/Und) asignada a esta escuela del total recibido.
                        <?php endif; ?>
                    </p>
                    
                    <table class="striped responsive-table bordered white">
                        <thead class="grey darken-3 white-text">
                            <tr>
                                <th>Producto</th>
                                <th class="center">Cantidad Descontada</th>
                                <th class="center">Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($items)): ?>
                                <?php foreach($items as $item): ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: 500;"><?= htmlspecialchars($item->product_name) ?></span>
                                        </td>
                                        <td class="center">
                                            <b style="font-size: 1.1em;"><?= number_format($item->quantity, 2, ',', '.') ?></b>
                                        </td>
                                        <td class="center"><?= htmlspecialchars($item->unit) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="center red-text">
                                        No se registraron ítems detallados en esta entrega.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</article>