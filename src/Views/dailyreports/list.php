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
        <a href="<?= BASE_URL ?>dailyreports/create" class="btn waves-effect waves-light">
            <i class="material-icons left">add</i> Nuevo Reporte
        </a>
    </div>

    <table class="striped highlight responsive-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Institución</th>
                <th>Vocero</th>
                <th>Menú</th>
                <th>Víveres</th>
                <th>Proteínas</th>
                <th>Frutas</th>
                <th>Vegetales</th>
                <th>Alumnos atendidos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r->date) ?></td>
                    <td><?= htmlspecialchars($r->institution_name) ?></td>
                    <td><?= htmlspecialchars($r->spokesperson_name) ?></td>
                    <td><?= htmlspecialchars($r->menu) ?></td>
                    <td><?= (int)$r->used_groceries ?></td>
                    <td><?= (int)$r->used_proteins ?></td>
                    <td><?= (int)$r->used_fruits ?></td>
                    <td><?= (int)$r->used_vegetables ?></td>
                    <td><?= (int)$r->students_served ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>dailyreports/delete/<?= $r->id ?>" 
                           class="btn-small red"
                           onclick="return confirm('¿Eliminar este reporte?')">
                           <i class="material-icons">delete</i>
                        </a>
                        <a href="<?= BASE_URL ?>dailyreports/pdf/<?= $r->id ?>" 
                           class="btn-small blue">
                           <i class="material-icons">picture_as_pdf</i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="10">No hay reportes registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>