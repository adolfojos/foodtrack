<!-- Views/institutions/form.php -->
<main class="container">
    <h4><?= htmlspecialchars($title) ?></h4>

    <form method="post" action="<?= BASE_URL ?>institutions/save" class="col s12">
        <?php if (!empty($institution)): ?>
            <input type="hidden" name="id" value="<?= $institution->id ?>">
        <?php endif; ?>

        <div class="row">
            <div class="input-field col s6">
                <input type="text" name="name" id="name" 
                       value="<?= $institution->name ?? '' ?>" required>
                <label for="name" class="active">Nombre de la institución</label>
            </div>

            <div class="input-field col s6">
                <input type="text" name="parish" id="parish" 
                       value="<?= $institution->parish ?? '' ?>">
                <label for="parish" class="active">Parroquia</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6">
                <input type="text" name="director" id="director" 
                       value="<?= $institution->director ?? '' ?>">
                <label for="director" class="active">Responsable</label>
            </div>

            <div class="input-field col s6">
                <input type="text" name="phone" id="phone" 
                       value="<?= $institution->phone ?? '' ?>">
                <label for="phone" class="active">Teléfono</label>
            </div>
        </div>

        <div class="row">
            <label>
                <input type="checkbox" name="active" value="1" 
                       <?= (isset($institution->active) && $institution->active) ? 'checked' : '' ?>>
                <span>Institución activa</span>
            </label>
        </div>

        <div class="row right-align">
            <button type="submit" class="btn waves-effect waves-light">
                <i class="material-icons left">save</i> Guardar
            </button>
            <a href="<?= BASE_URL ?>institutions" class="btn grey">
                <i class="material-icons left">arrow_back</i> Cancelar
            </a>
        </div>
    </form>
</main>
