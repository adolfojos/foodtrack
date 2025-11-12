<!-- Views/deliveries/form.php -->
<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>deliveries/save" class="col s12">
        <div class="row">
            <div class="input-field col s6">
                <input type="date" name="date" id="date" required>
                <label for="date">Fecha de entrega</label>
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
                <select name="reception_id">
                    <option value="" selected>Sin recepción asociada</option>
                    <?php foreach ($receptions as $r): ?>
                        <option value="<?= $r->id ?>">
                            <?= htmlspecialchars($r->date) ?> - Inspector: <?= htmlspecialchars($r->inspector_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Recepción vinculada</label>
            </div>

            <div class="input-field col s6">
                <select name="receiver_id" required>
                    <option value="" disabled selected>Seleccione receptor</option>
                    <?php foreach ($receivers as $rec): ?>
                        <option value="<?= $rec->id ?>"><?= htmlspecialchars($rec->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Receptor (vocero/director)</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s3">
                <input type="number" name="qty_groceries" id="qty_groceries" min="0" value="0">
                <label for="qty_groceries">Víveres</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="qty_proteins" id="qty_proteins" min="0" value="0">
                <label for="qty_proteins">Proteínas</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="qty_fruits" id="qty_fruits" min="0" value="0">
                <label for="qty_fruits">Frutas</label>
            </div>
            <div class="input-field col s3">
                <input type="number" name="qty_vegetables" id="qty_vegetables" min="0" value="0">
                <label for="qty_vegetables">Vegetales</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                <input type="text" name="receiver_signature" id="receiver_signature">
                <label for="receiver_signature">Firma del receptor (texto o código)</label>
            </div>
        </div>

        <div class="row right-align">
            <button type="submit" class="btn waves-effect waves-light">
                <i class="material-icons left">save</i> Guardar
            </button>
            <a href="<?= BASE_URL ?>deliveries" class="btn grey">
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
