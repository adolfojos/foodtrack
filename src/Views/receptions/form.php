<!-- Views/receptions/form.php -->
<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>receptions/save" class="col s12">
        <div class="row">
            <!-- Fecha (Mejora: Fecha actual por defecto) -->
            <div class="input-field col s6">
                <!-- Se establece la fecha actual usando PHP para conveniencia del usuario -->
                <input type="date" name="date" id="date" required value="<?= date('Y-m-d') ?>">
                <label for="date" class="active">Fecha de recepción</label>
            </div>

            <!-- Tipo de recepción -->
            <div class="input-field col s6">
                <select name="reception_type" required>
                    <option value="" disabled selected>Seleccione tipo</option>
                    <?php foreach ($receptionTypes as $type): ?>
                        <option value="<?= $type ?>"><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Tipo de recepción</label>
            </div>
        </div>

        <div class="row">
            <!-- Cantidad resumen -->
            <div class="input-field col s6">
                <input type="number" name="summary_quantity" id="summary_quantity" min="0">
                <label for="summary_quantity">Cantidad resumen (ej. N° de bolsas)</label>
            </div>

            <!-- Inspector -->
            <div class="input-field col s6">
                <select name="inspector_id" required>
                    <option value="" disabled selected>Seleccione inspector</option>
                    <?php foreach ($inspectors as $i): ?>
                        <option value="<?= $i->id ?>"><?= htmlspecialchars($i->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Inspector responsable</label>
            </div>
        </div>

        <div class="row">
            <!-- Vocero parroquial -->
            <div class="input-field col s6">
                <select name="vocero_parroquial_id" required>
                    <option value="" disabled selected>Seleccione vocero</option>
                    <?php foreach ($spokespersons as $p): ?>
                        <option value="<?= $p->id ?>"><?= htmlspecialchars($p->full_name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Vocero parroquial</label>
            </div>

            <!-- Observaciones -->
            <div class="input-field col s6">
                <textarea name="notes" id="notes" class="materialize-textarea"></textarea>
                <label for="notes">Observaciones</label>
            </div>
        </div>

        <!-- Ítems -->
        <h5>Productos recibidos</h5>
        <div class="row grey lighten-4 p-2 mb-2">
            <div class="col s3">**Producto**</div>
            <div class="col s3">**Cantidad**</div>
            <div class="col s3">**Unidad**</div>
            <div class="col s3 center-align">**Acción**</div>
        </div>
        
        <div id="items-container">
            <!-- Primer ítem (necesita un botón de eliminar si se permite que sea opcional) -->
            <div class="row item-row" data-index="0">
                <div class="input-field col s3">
                    <input type="text" name="items[0][product_name]" placeholder="Nombre del producto" required>
                </div>
                <div class="input-field col s3">
                    <input type="number" name="items[0][quantity]" placeholder="0" min="1" required>
                </div>
                <div class="input-field col s3">
                    <input type="text" name="items[0][unit]" placeholder="Kg, unidad, lt" required>
                </div>
                <!-- Botón de eliminar (visible solo para el primer ítem, si hay más de uno) -->
                <div class="input-field col s3 center-align">
                    <button type="button" class="btn-flat red-text remove-item" onclick="removeItem(this)">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <button type="button" class="btn blue" onclick="addItem()">
                <i class="material-icons left">add</i> Añadir producto
            </button>
        </div>

        <div class="row right-align">
            <button type="submit" class="btn waves-effect waves-light green darken-1">
                <i class="material-icons left">save</i> Guardar Recepción
            </button>
            <a href="<?= BASE_URL ?>receptions" class="btn grey waves-effect waves-light">
                <i class="material-icons left">arrow_back</i> Cancelar
            </a>
        </div>
    </form>
</main>

<script>
    let itemIndex = 1; // El índice inicial es 1, ya que el primer elemento es 0.

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar selectores de Materialize
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
        
        // Función para verificar si el primer ítem debe tener el botón de eliminar visible
        updateRemoveButtonVisibility();
    });

    /**
     * Añade una nueva fila de producto al formulario.
     */
    function addItem() {
        const container = document.getElementById('items-container');
        const row = document.createElement('div');
        
        row.classList.add('row', 'item-row');
        row.setAttribute('data-index', itemIndex);

        row.innerHTML = `
            <div class="input-field col s3">
                <input type="text" name="items[${itemIndex}][product_name]" placeholder="Nombre del producto" required>
            </div>
            <div class="input-field col s3">
                <input type="number" name="items[${itemIndex}][quantity]" placeholder="0" min="1" required>
            </div>
            <div class="input-field col s3">
                <input type="text" name="items[${itemIndex}][unit]" placeholder="Kg, unidad, lt" required>
            </div>
            <div class="input-field col s3 center-align">
                <button type="button" class="btn-flat red-text remove-item" onclick="removeItem(this)">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        `;
        container.appendChild(row);
        itemIndex++; // Incrementa el índice para el próximo elemento
        
        // Asegura que los botones de eliminar se muestren/oculten correctamente
        updateRemoveButtonVisibility();
    }

    /**
     * Elimina la fila de producto al que pertenece el botón pulsado.
     */
    function removeItem(buttonElement) {
        const itemRow = buttonElement.closest('.item-row');
        if (itemRow) {
            itemRow.remove();
        }
        
        // Reindexar los elementos para evitar huecos en el array POST
        reindexItems();
        
        // Asegura que al menos un ítem no tenga el botón de eliminar
        updateRemoveButtonVisibility();
    }
    
    /**
     * Reindexa los nombres de los campos de input después de eliminar una fila.
     * Esto asegura que PHP reciba un array secuencial correcto.
     */
    function reindexItems() {
        const container = document.getElementById('items-container');
        const rows = container.querySelectorAll('.item-row');
        
        itemIndex = 0; // Reiniciamos el índice de JS
        
        rows.forEach((row, newIndex) => {
            row.setAttribute('data-index', newIndex);
            // Actualiza los nombres de los inputs
            row.querySelectorAll('input').forEach(input => {
                const oldName = input.name;
                // Usa expresiones regulares para reemplazar el índice numérico
                input.name = oldName.replace(/items\[\d+\]/, `items[${newIndex}]`);
            });
            itemIndex = newIndex + 1; // Establece el nuevo índice para el próximo addItem
        });
    }

    /**
     * Muestra el botón de eliminar en todas las filas EXCEPTO si solo queda una.
     * Esto asegura que al menos un producto se envíe en el formulario.
     */
    function updateRemoveButtonVisibility() {
        const container = document.getElementById('items-container');
        const rows = container.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const removeButton = row.querySelector('.remove-item');
            if (removeButton) {
                // Si solo queda una fila, deshabilitar el botón de eliminar
                removeButton.style.display = rows.length > 1 ? '' : 'none';
            }
        });
    }
</script>
<style>
    /* Estilos opcionales para mejorar el aspecto de los ítems dinámicos */
    .item-row {
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }
    .item-row:last-child {
        border-bottom: none;
    }
    .remove-item {
        /* Para que el botón de eliminar se alinee verticalmente con los campos */
        margin-top: 1.5rem; 
    }
</style>