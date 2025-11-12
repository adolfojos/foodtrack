<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <!-- Mensajes flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash <?= $_SESSION['flash_message']['type'] ?>">
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="right-align">
        <a href="<?= BASE_URL ?>deliveries/create" class="btn waves-effect waves-light">
            <i class="material-icons left">add</i> Nueva Entrega
        </a>
    </div>

    <table class="striped highlight responsive-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Institución</th>
                <th>Recepción</th>
                <th>Receptor</th>
                <th>Víveres</th>
                <th>Proteínas</th>
                <th>Frutas</th>
                <th>Vegetales</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($deliveries)): ?>
            <?php foreach ($deliveries as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d->date) ?></td>
                    <td><?= htmlspecialchars($d->institution_name) ?></td>
                    <td><?= htmlspecialchars($d->reception_date ?? '-') ?></td>
                    <td><?= htmlspecialchars($d->receiver_name) ?></td>
                    <td><?= (int)$d->qty_groceries ?></td>
                    <td><?= (int)$d->qty_proteins ?></td>
                    <td><?= (int)$d->qty_fruits ?></td>
                    <td><?= (int)$d->qty_vegetables ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>deliveries/delete/<?= $d->id ?>" 
                        class="btn-small red"
                           onclick="return confirm('¿Eliminar esta entrega?')">
                           <i class="material-icons">delete</i>
                        </a>
                        <a href="<?= BASE_URL ?>deliveries/pdf/<?= $d->id ?>" 
                           class="btn-small blue">
                           <i class="material-icons">picture_as_pdf</i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9">No hay entregas registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
