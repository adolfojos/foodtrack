<?php
// Detectar modo Edición
$isEdit = isset($delivery);
$actionUrl = $isEdit ? BASE_URL . "deliveries/update/{$delivery->id}" : BASE_URL . "deliveries/save";
$headerTitle = $isEdit ? "Editar Entrega #{$delivery->id}" : "Nueva Entrega";
?>

<main class="container">
    <div class="card-panel">
        <h4><?= htmlspecialchars($headerTitle) ?></h4>
        <form method="post" action="<?= $actionUrl ?>" autocomplete="off">
            
            <div class="row">
                <div class="input-field col s12 m6">
                    <input type="date" name="date" id="date" 
                           value="<?= $isEdit ? $delivery->date : date('Y-m-d') ?>" required>
                    <label for="date" class="active">Fecha de Entrega</label>
                </div>
                <div class="input-field col s12 m6">
                    <select name="receiver_id" required>
                        <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>¿Quién recibe?</option>
                        <?php foreach ($receivers as $r): ?>
                            <option value="<?= $r->id ?>" 
                                <?= ($isEdit && $delivery->receiver_id == $r->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r->full_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Persona que recibe</label>
                </div>
            </div>

            <div class="card-panel grey lighten-5" style="border: 1px solid #e0e0e0;">
                <h6><i class="material-icons tiny">sync</i> Origen y Destino</h6>
                <div class="row">
                    <div class="input-field col s12 m6">
                        <select name="institution_id" id="institution_sel" onchange="calculateAllocation()" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Seleccione Institución</option>
                            <?php foreach ($institutions as $inst): ?>
                                <option value="<?= $inst->id ?>" 
                                        data-enrollment="<?= $inst->total_enrollment ?>"
                                        <?= ($isEdit && $delivery->institution_id == $inst->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($inst->name) ?> (Mat: <?= $inst->total_enrollment ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label>Institución</label>
                    </div>
                    
                    <div class="input-field col s12 m6">
                        <select name="reception_id" id="reception_sel" onchange="calculateAllocation()" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Seleccione Fuente</option>
                            <?php foreach ($receptions as $rec): ?>
                                <?php 
                                    $unitLabel = ($rec->reception_type === 'CLAP') ? 'Bolsas' : 'Kg';
                                    $displayDate = date('d/m', strtotime($rec->date));
                                    $displayText = "$displayDate - {$rec->reception_type} (Total: " . (float)$rec->summary_quantity . " $unitLabel)";
                                    $selected = ($isEdit && $delivery->reception_id == $rec->id) ? 'selected' : '';
                                ?>
                                <option value="<?= $rec->id ?>" data-type="<?= $rec->reception_type ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($displayText) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label>Fuente de Alimentos</label>
                    </div>
                </div>

                <div class="row" id="suggestion-box" style="display:none;">
                    <div class="col s12">
                        <div class="card-panel blue lighten-5 blue-text text-darken-4" style="padding: 10px;">
                            <i class="material-icons left">info_outline</i>
                            <span>Sugerencia según matrícula: <strong id="txt_result">0</strong> <span id="txt_unit"></span>.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12" id="container_clap" style="display:none;">
                    <i class="material-icons prefix">shopping_basket</i>
                    <input type="number" name="qty_groceries" id="qty_groceries" min="0"
                           value="<?= $isEdit ? (int)$delivery->qty_groceries : 0 ?>">
                    <label for="qty_groceries">Cantidad Bolsas CLAP a Entregar</label>
                </div>

                <div class="input-field col s12" id="container_proteina" style="display:none;">
                    <i class="material-icons prefix">restaurant</i>
                    <input type="number" name="qty_proteins" id="qty_proteins" step="0.01" min="0"
                           value="<?= $isEdit ? (float)$delivery->qty_proteins : 0 ?>">
                    <label for="qty_proteins">Total Kilos Proteína a Entregar</label>
                </div>

                <div class="input-field col s12" id="container_fruvert" style="display:none;">
                    <i class="material-icons prefix">eco</i>
                    <input type="number" name="qty_fruits" id="qty_fruits" step="0.01" min="0"
                           value="<?= $isEdit ? (float)$delivery->qty_fruits : 0 ?>">
                    <label for="qty_fruits">Total Kilos Fruver a Entregar</label>
                </div>
            </div>

            <div class="row center-align" style="margin-top: 2rem;">
                <button type="submit" class="btn green darken-1 btn-large waves-effect">
                    <i class="material-icons left">save</i> <?= $isEdit ? 'Actualizar Entrega' : 'Registrar Entrega' ?>
                </button>
                <a href="<?= BASE_URL ?>deliveries" class="btn grey btn-large">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.FormSelect.init(document.querySelectorAll('select'));
        
        // Lógica para MODO EDICIÓN: Inicializar el campo correcto
        <?php if($isEdit): ?>
             // Forzamos la detección del tipo basado en el select de recepción actual
             const recepSelect = document.getElementById('reception_sel');
             const selectedOption = recepSelect.options[recepSelect.selectedIndex];
             const type = selectedOption.getAttribute('data-type');
             
             // Valores actuales para no perderlos
             const currentVal = {
                 clap: <?= (int)$delivery->qty_groceries ?>,
                 prot: <?= (float)$delivery->qty_proteins ?>,
                 fruv: <?= (float)$delivery->qty_fruits ?>
             };

             // Mostramos el input correcto
             toggleInputs(type, 0); // Pasamos 0 porque ya los inputs tienen el value por PHP
             M.updateTextFields();
        <?php endif; ?>
    });

    // ... (Mantén aquí la función calculateAllocation() del ejemplo anterior) ...

    function toggleInputs(type, suggestedValue) {
        const divClap = document.getElementById('container_clap');
        const divProt = document.getElementById('container_proteina');
        const divFruv = document.getElementById('container_fruvert');
        
        [divClap, divProt, divFruv].forEach(div => div.style.display = 'none');

        if (type === 'CLAP') {
            divClap.style.display = 'block';
            if(suggestedValue > 0) document.getElementById('qty_groceries').value = suggestedValue;
        } else if (type === 'PROTEINA') {
            divProt.style.display = 'block';
            if(suggestedValue > 0) document.getElementById('qty_proteins').value = suggestedValue;
        } else if (type === 'FRUVERT') {
            divFruv.style.display = 'block';
            if(suggestedValue > 0) document.getElementById('qty_fruits').value = suggestedValue;
        }
    }
</script>