<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash <?= $_SESSION['flash_message']['type'] ?>">
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="right-align">
        <a href="<?= BASE_URL ?>receptions/create" class="btn waves-effect waves-light">
            <i class="material-icons left">add</i> Nueva Recepción
        </a>
    </div>

    <table class="striped highlight responsive-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th> <th>Inspector</th>
                <th>Vocero Parroquial</th>
                <th>Cant. Resumen</th> <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($receptions)): ?>
            <?php foreach ($receptions as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r->date) ?></td>
                    <td><?= htmlspecialchars($r->reception_type) ?></td> <td><?= htmlspecialchars($r->inspector_name) ?></td>
                    <td><?= htmlspecialchars($r->vocero_parroquial_name) ?></td>
                    <td>
                        <?php if ($r->reception_type === 'CLAP'): ?>
                            <?= (int)$r->summary_quantity ?> Bolsas
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r->notes) ?></td>
                    <td>
                        <a title="Detail" href="<?=BASE_URL?>receptions/detail/<?php echo $r->id; ?>"><i class="ico-visibility tiny"></i></a> &nbsp;
                        <a href="<?= BASE_URL ?>receptions/delete/<?= $r->id ?>" 
                        class="btn-small red"
                        onclick="return confirm('¿Eliminar esta recepción?')">
                        <i class="material-icons">delete</i>
                        </a>
                        <a href="<?= BASE_URL ?>receptions/pdf/<?= $r->id ?>" 
                        class="btn-small blue">
                        <i class="material-icons">picture_as_pdf</i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No hay recepciones registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>