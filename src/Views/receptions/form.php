<!-- Views/receptions/form.php -->
<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>receptions/save" class="col s12">
        <div class="row">
            <div class="input-field col s6">
                <input type="date" name="date" id="date" required>
                <label for="date">Fecha de recepción</label>
            </div>

            <div class="input-field col s6">
                <input type="number" name="total_bags" id="total_bags" min="0" required>
                <label for="total_bags">Total de bolsas</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6">
                <select name="inspector_id" required>
                    <option value="" disabled selected>Seleccione inspector</option>
                    <?php foreach ($inspectors as $i): ?>
                        <option value="<?= $i->id ?>"><?= htmlspecialchars($i->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Inspector responsable</label>
            </div>

            <div class="input-field col s6">
                <select name="vocero_parroquial_id" required>
                    <option value="" disabled selected>Seleccione vocero</option>
                    <?php foreach ($spokespersons as $p): ?>
                        <option value="<?= $p->id ?>"><?= htmlspecialchars($p->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Vocero parroquial</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                <textarea name="notes" id="notes" class="materialize-textarea"></textarea>
                <label for="notes">Observaciones</label>
            </div>
        </div>

        <div class="row right-align">
            <button type="submit" class="btn waves-effect waves-light">
                <i class="material-icons left">save</i> Guardar
            </button>
            <a href="<?= BASE_URL ?>receptions" class="btn grey">
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
