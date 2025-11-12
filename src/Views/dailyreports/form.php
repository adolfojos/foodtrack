<!-- Views/dailyreports/form.php -->
<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>dailyreports/save" class="col s12">
        <div class="row">
            <div class="input-field col s6">
                <input type="date" name="date" id="date" required>
                <label for="date">Fecha del reporte</label>
            </div>

            <div class="input-field col s6">
                <select name="institution_id" required>
                    <option value="" disabled selected>Seleccione institución</option>
                    <?php foreach ($institutions as $i): ?>
                        <option value="<?= $i->id ?>"><?= htmlspecialchars($i->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Institución educativa</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6">
                <select name="delivery_id">
                    <option value="" selected>Sin entrega asociada</option>
                    <?php foreach ($deliveries as $d): ?>
                        <option value="<?= $d->id ?>">
                            <?= htmlspecialchars($d->date) ?> - <?= htmlspecialchars($d->institution_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Entrega vinculada</label>
            </div>

            <div class="input-field col s6">
                <select name="spokesperson_id" required>
                    <option value="" disabled selected>Seleccione vocero</option>
                    <?php foreach ($spokespersons as $s): ?>
                        <option value="<?= $s->id ?>"><?= htmlspecialchars($s->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Vocero institucional</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                <textarea name="menu" id="menu" class="materialize-textarea" required></textarea>
                <label for="menu">Menú preparado</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s3">
                <input type="number" name="used_groceries" id="used_groceries" min="0" value="0">
                <label for="used_groceries">Víveres usados</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="used_proteins" id="used_proteins" min="0" value="0">
                <label for="used_proteins">Proteínas usadas</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="used_fruits" id="used_fruits" min="0" value="0">
                <label for="used_fruits">Frutas usadas</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="used_vegetables" id="used_vegetables" min="0" value="0">
                <label for="used_vegetables">Vegetales usados</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6">
                <input type="number" name="students_served" id="students_served" min="0" required>
                <label for="students_served">Alumnos atendidos</label>
            </div>
        </div>

        <div class="row right-align">
            <button type="submit" class="btn waves-effect waves-light">
                <i class="material-icons left">save</i> Guardar
            </button>
            <a href="<?= BASE_URL ?>dailyreports" class="btn grey">
                <i class="material-icons left">arrow_back</i> Cancelar
            </a>
        </div>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>