<aside id="left-side-menu">
    <ul class="collapsible collapsible-accordion">
        <li class="no-padding">
            <a href="ROUTE_1.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Section Name 1
            </a>
        </li>
        <li class="no-padding">
            <a href="ROUTE_2.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Section Name 2
            </a>
        </li>
    </ul>
</aside>
<article>
    <div class="conten-body">
        <div class="col s12 m12 l12">
            <div class="card-panel">
                <!-- Título y botón -->
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5><?= htmlspecialchars($title) ?></h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">
                            <a href="<?= BASE_URL ?>institutions/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add</i> Nueva Institución
                            </a>
                        </div>
                    </div>
                </div> 
                <!-- Tabla de institutions -->
                <div class="row row-end">
                    <div class="col s12">
                        <form method="post" action="<?= BASE_URL ?>institutions/save" class="col s12">
                        <?php if (!empty($institution)): ?>
                        <input type="hidden" name="id" value="<?= $institution->id ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" name="name" id="name" value="<?= $institution->name ?? '' ?>" required>
                                <label for="name" class="active">Nombre de la institución</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="director_ids[]" id="director_ids" multiple>
                                    <option value="" disabled>Selecciona un director</option>
                                    <?php if (!empty($allDirectors)): ?>
                                        <?php foreach ($allDirectors as $director): ?>
                                        <?php 
                                        // Verifica si el ID del director actual está en la lista de IDs ya asociados
                                        $selected = in_array($director->id, $currentDirectorIds ?? []) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $director->id ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($director->full_name) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="director_ids" class="active">Director Asignado</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" name="campus_code" id="campus_code" value="<?= $institution->campus_code ?? '' ?>">
                                <label for="campus_code" class="active">Código Plantel</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="sica_code" id="sica_code" value="<?= $institution->sica_code ?? '' ?>">
                                <label for="sica_code" class="active">Código SICA</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" name="phone" id="phone" value="<?= $institution->phone ?? '' ?>">
                                <label for="phone" class="active">Teléfono</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="email" name="email" id="email" value="">
                                <label for="email" class="active">Correo</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="text" name="municipality" id="municipality" value="<?= $institution->municipality ?? '' ?>">
                                <label for="municipality" class="active">Municipio</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="text" name="parish" id="parish" value="<?= $institution->parish ?? '' ?>">
                                <label for="parish" class="active">Parroquia</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="number" name="total_enrollment" id="total_enrollment" value="<?= $institution->total_enrollment ?? '' ?>">
                                <label for="total_enrollment" class="active">Matrícula Total</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="checkbox" id="active" name="active" class="filled-in" value="1" <?= (isset($institution->active) && $institution->active) ? 'checked' : '' ?> />
                                <label for="active">Institución activa</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m12 l12">

                            </div>
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
                    </div>
                </div>
            </div> <!-- card-panel -->
        </div> <!-- col -->
    </div> <!-- conten-body -->
</article>