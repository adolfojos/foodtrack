<?php
// Detectar si es edición o creación
$isEdit = isset($reception);
$actionUrl = $isEdit ? BASE_URL . "receptions/update/{$reception->id}" : BASE_URL . "receptions/save";
$headerTitle = $isEdit ? "Editar Recepción #{$reception->id}" : "Nueva Recepción";
?>

<main class="container">
    <div class="card-panel">
        <h4><?= htmlspecialchars($headerTitle) ?></h4>

        <form method="post" action="<?= $actionUrl ?>" autocomplete="off">
            
            <div class="row">
                <div class="input-field col s12 m6">
                    <input type="date" name="date" id="date" required 
                           value="<?= $isEdit ? $reception->date : date('Y-m-d') ?>">
                    <label for="date" class="active">Fecha de recepción</label>
                </div>

                <div class="input-field col s12 m6">
                    <select name="reception_type" id="reception_type" required onchange="toggleHint()">
                        <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Seleccione tipo</option>
                        <?php foreach ($receptionTypes as $type): ?>
                            <option value="<?= $type ?>" 
                                <?= ($isEdit && $reception->reception_type == $type) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Tipo de recepción</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input type="number" name="summary_quantity" id="summary_quantity" min="0" 
                           value="<?= $isEdit ? $reception->summary_quantity : '' ?>">
                    <label for="summary_quantity" id="lbl_summary">Cantidad Total / N° Bolsas</label>
                    <span class="helper-text" id="helper_summary"></span>
                </div>

                <div class="input-field col s12 m6">
                    <select name="inspector_id" required>
                        <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Inspector responsable</option>
                        <?php foreach ($inspectors as $i): ?>
                            <option value="<?= $i->id ?>" 
                                <?= ($isEdit && $reception->inspector_id == $i->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($i->full_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Inspector</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <select name="vocero_parroquial_id" required>
                        <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Vocero Parroquial</option>
                        <?php foreach ($spokespersons as $p): ?>
                            <option value="<?= $p->id ?>" 
                                <?= ($isEdit && $reception->vocero_parroquial_id == $p->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p->full_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Vocero</label>
                </div>

                <div class="input-field col s12 m6">
                    <textarea name="notes" id="notes" class="materialize-textarea"><?= $isEdit ? htmlspecialchars($reception->notes) : '' ?></textarea>
                    <label for="notes">Observaciones</label>
                </div>
            </div>

            <datalist id="products-list-options">
                <?php if (!empty($productsList)): ?>
                    <?php foreach ($productsList as $prodName): ?>
                        <option value="<?= htmlspecialchars($prodName) ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
            </datalist>

            <hr>
            <h5>Contenido de la Recepción</h5>
            <div class="card-panel grey lighten-5">
                <p id="instruction-text" class="blue-text text-darken-2">
                    <i class="material-icons tiny">info</i> Ingrese los productos recibidos.
                </p>

                <div class="row row-header hide-on-small-only">
                    <div class="col s4"><strong>Producto</strong></div>
                    <div class="col s3"><strong id="qty-header">Cantidad</strong></div>
                    <div class="col s3"><strong>Unidad</strong></div>
                    <div class="col s2 center-align"><strong>Acción</strong></div>
                </div>
                
                <div id="items-container">
                    <?php 
                    // LOGICA DE RENDERIZADO DE ITEMS (PARA EDICION)
                    // Si estamos editando, hacemos un bucle. Si no, mostramos una fila vacía.
                    $initialItems = $isEdit ? $items : [null]; 
                    $currentIndex = 0;
                    ?>

                    <?php foreach ($initialItems as $item): ?>
                        <div class="row item-row valign-wrapper" data-index="<?= $currentIndex ?>">
                            <div class="input-field col s4">
                                <input type="text" 
                                       name="items[<?= $currentIndex ?>][product_name]" 
                                       list="products-list-options" 
                                       placeholder="Escriba para buscar..." 
                                       value="<?= $item ? htmlspecialchars($item->product_name) : '' ?>"
                                       required>
                            </div>
                            <div class="input-field col s3">
                                <input type="number" name="items[<?= $currentIndex ?>][quantity]" 
                                       placeholder="0" step="0.01" min="0.01" 
                                       value="<?= $item ? floatval($item->quantity) : '' ?>"
                                       required>
                            </div>
                            <div class="input-field col s3">
                                <input type="text" name="items[<?= $currentIndex ?>][unit]" 
                                       placeholder="Kg" 
                                       value="<?= $item ? htmlspecialchars($item->unit) : '' ?>"
                                       required>
                            </div>
                            <div class="col s2 center-align">
                                <button type="button" class="btn-flat red-text remove-item" onclick="removeItem(this)">
                                    <i class="material-icons">delete</i>
                                </button>
                            </div>
                        </div>
                        <?php $currentIndex++; ?>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn blue lighten-1" onclick="addItem()">
                    <i class="material-icons left">add</i> Agregar Producto
                </button>
            </div>

            <div class="row center-align mt-3" style="margin-top: 2rem;">
                <button type="submit" class="btn waves-effect waves-light green darken-1 btn-large">
                    <i class="material-icons left">save</i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= BASE_URL ?>receptions" class="btn grey waves-effect waves-light btn-large">
                    <i class="material-icons left">arrow_back</i> Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<script>
    // Inicializamos el índice en base a cuántos items pintamos con PHP
    let itemIndex = <?= $currentIndex ?>;

    document.addEventListener('DOMContentLoaded', function() {
        M.FormSelect.init(document.querySelectorAll('select'));
        M.updateTextFields(); // Importante para que las labels no se solapen con el texto en modo edición
        updateRemoveButtonVisibility();
        toggleHint(); // Ejecutar al inicio para poner los textos correctos
    });

    function toggleHint() {
        const type = document.getElementById('reception_type').value;
        const lblSummary = document.getElementById('lbl_summary');
        const helperSummary = document.getElementById('helper_summary');
        const instruction = document.getElementById('instruction-text');
        const qtyHeader = document.getElementById('qty-header');

        if (type === 'CLAP') {
            lblSummary.innerText = "N° de Bolsas/Combos recibidos";
            helperSummary.innerText = "El sistema multiplicará el contenido por este número.";
            instruction.innerHTML = '<i class="material-icons tiny">warning</i> <b>MODO CLAP:</b> Edite el contenido de <b>UNA SOLA BOLSA</b>.';
            qtyHeader.innerText = "Cant. por Bolsa";
        } else {
            lblSummary.innerText = "Cantidad Resumen (Opcional)";
            helperSummary.innerText = "Total de bultos/cestas (solo informativo).";
            instruction.innerHTML = '<i class="material-icons tiny">info</i> Edite el total de productos recibidos.';
            qtyHeader.innerText = "Cantidad Total";
        }
    }

    function addItem() {
        const container = document.getElementById('items-container');
        const row = document.createElement('div');
        row.classList.add('row', 'item-row', 'valign-wrapper');
        row.setAttribute('data-index', itemIndex);

        row.innerHTML = `
            <div class="input-field col s4">
                <input type="text" name="items[${itemIndex}][product_name]" list="products-list-options" placeholder="Escriba para buscar..." required>
            </div>
            <div class="input-field col s3">
                <input type="number" name="items[${itemIndex}][quantity]" placeholder="0" step="0.01" min="0.01" required>
            </div>
            <div class="input-field col s3">
                <input type="text" name="items[${itemIndex}][unit]" placeholder="Kg" required>
            </div>
            <div class="col s2 center-align">
                <button type="button" class="btn-flat red-text remove-item" onclick="removeItem(this)">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
        updateRemoveButtonVisibility();
    }

    function removeItem(btn) {
        const row = btn.closest('.item-row');
        row.remove();
        reindexItems();
        updateRemoveButtonVisibility();
    }

    function reindexItems() {
        const rows = document.querySelectorAll('.item-row');
        let newIndex = 0;
        rows.forEach(row => {
            row.setAttribute('data-index', newIndex);
            row.querySelectorAll('input').forEach(input => {
                const nameParts = input.name.split(/\[\d+\]/); 
                if (nameParts.length > 1) {
                    input.name = `items[${newIndex}]${nameParts[1]}`;
                }
            });
            newIndex++;
        });
        itemIndex = newIndex;
    }

    function updateRemoveButtonVisibility() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-item');
            if(btn) btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }
</script>

<style>
    .item-row { border-bottom: 1px solid #f1f1f1; margin-bottom: 0px; }
    .row-header { background-color: #f5f5f5; padding: 10px 0; border-bottom: 2px solid #e0e0e0; }
    input[list] { margin-bottom: 0; }
</style>