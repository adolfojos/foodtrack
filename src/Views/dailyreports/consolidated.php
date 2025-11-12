<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>dailyreports/consolidated" class="row">
        <div class="input-field col s4">
            <select name="institution_id" required>
                <option value="" disabled selected>Seleccione institución</option>
                <?php foreach ($institutions as $i): ?>
                    <option value="<?= $i->id ?>"><?= htmlspecialchars($i->name) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Institución</label>
        </div>
        <div class="input-field col s3">
            <input type="date" name="start_date" required>
            <label>Desde</label>
        </div>
        <div class="input-field col s3">
            <input type="date" name="end_date" required>
            <label>Hasta</label>
        </div>
        <div class="input-field col s2">
            <button type="submit" class="btn waves-effect waves-light">
                <i class="material-icons left">search</i> Consultar
            </button>
        </div>
    </form>

    <?php if ($result): ?>
        <h5>Resultados para <?= htmlspecialchars($result->institution_name) ?></h5>
        <table class="striped highlight">
            <thead>
                <tr>
                    <th>Víveres</th>
                    <th>Proteínas</th>
                    <th>Frutas</th>
                    <th>Vegetales</th>
                    <th>Alumnos atendidos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= (int)$result->total_groceries ?></td>
                    <td><?= (int)$result->total_proteins ?></td>
                    <td><?= (int)$result->total_fruits ?></td>
                    <td><?= (int)$result->total_vegetables ?></td>
                    <td><?= (int)$result->total_students ?></td>
                </tr>
            </tbody>
        </table>

        <div class="right-align" style="margin-top:20px;">
            <a href="<?= BASE_URL ?>dailyreports/consolidatedPdf/<?= $result->institution_name ?>/<?= $_POST['start_date'] ?>/<?= $_POST['end_date'] ?>" 
               class="btn blue">
               <i class="material-icons left">picture_as_pdf</i> Exportar PDF
            </a>
        </div>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>