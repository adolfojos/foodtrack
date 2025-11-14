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
                <div class="card-title">
                    <div class="row">
                        <div class="header-title-left col s12 m6">
                            <h5><?= htmlspecialchars($title) ?></h5>
                        </div>
                        <div class="btn-action-title col s12 m6 align-right">

                        </div>
                    </div>
                </div>
                <form method="post" action="<?= BASE_URL ?>actors/save" class="col s12">
                    <?php if (!empty($actor)): ?>
                        <input type="hidden" name="id" value="<?= $actor->id ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="input-field col s6">
                            <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($actor ? $actor->full_name : '') ?>" required>
                            <label for="full_name" class="active">Nombre del actor</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="text" id="national_id" name="national_id" required="required" class="uppercase identification_letter_fix "value="<?= htmlspecialchars($actor ? $actor->national_id : '') ?>" autocomplete="off" />
                            <label class="required" for="national_id">Cédula (inicia con V o E)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select name="role" required>
                                <option value="" disabled selected>Seleccione rol</option>
                                <option value="admin" <?= (isset($actor->role) && $actor->role === 'admin') ? 'selected' : '' ?>>Administrador</option>
                                <option value="inspector" <?= (isset($actor->role) && $actor->role === 'inspector') ? 'selected' : '' ?>>Inspector</option>
                                <option value="vocero_parroquial" <?= (isset($actor->role) && $actor->role === 'vocero_parroquial') ? 'selected' : '' ?>>Vocero Parroquial</option>
                                <option value="vocero_institucional" <?= (isset($actor->role) && $actor->role === 'vocero_institucional') ? 'selected' : '' ?>>Vocero Institucional</option>
                                <option value="director" <?= (isset($actor->role) && $actor->role === 'director') ? 'selected' : '' ?>>Director</option>
                                <option value="cocinero" <?= (isset($actor->role) && $actor->role === 'cocinero') ? 'selected' : '' ?>>Cocinero</option>
                            </select>
                            <label>Rol</label>
                        </div>
                        <div class="input-field col s6">
                            <select name="institution_ids[]" multiple="multiple">
                                <option value="" disabled>Seleccione instituciones (Múltiple)</option>
                                <?php 
                                    // Prepara un array de IDs de instituciones ya asociadas para una fácil comprobación.
                                    // Asegúrate de que $actor_institutions contenga un array de IDs, e.g., [1, 5, 8].
                                    $selected_ids = !empty($actor_institutions) ? $actor_institutions : []; 
                                ?>
                                <?php foreach ($institutions as $i): ?>
                                    <option value="<?= $i->id ?>"
                                        <?= in_array($i->id, $selected_ids) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($i->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label>Instituciones vinculadas</label>
                            <span class="helper-text">Mantén Ctrl (Windows) / Cmd (Mac) para seleccionar múltiples.</span>
                        </div>
                        </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <select name="user_id">
                                <option value="">Sin usuario</option>
                                <?php foreach ($users as $u): ?>
                                <option value="<?= $u->id ?>"<?= (isset($actor->user_id) && $actor->user_id == $u->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u->username) ?> (<?= htmlspecialchars($u->role) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label>Seleccionar usuario existente</label>
                        </div>
                        <div class="input-field col s6">
                            </div>
                    </div>
                    <div class="row">
                        <h5>Registrar nuevo usuario (opcional)</h5>
                        <div class="input-field col s6">
                            <input type="text" name="new_username" id="new_username">
                            <label for="new_username">Nombre de usuario</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="password" name="new_password" id="new_password">
                            <label for="new_password">Contraseña</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m12 l12">
                            <input type="checkbox" id="active" name="active" class="filled-in" value="1" <?= (isset($actor->active) && $actor->active) ? 'checked' : '' ?> />
                            <label for="active">Actor activo</label>
                        </div>
                    </div>
                    <div class="row right-align">
                        <button type="submit" class="btn waves-effect waves-light">
                            <i class="material-icons left">save</i> Guardar
                        </button>
                        <a href="<?= BASE_URL ?>actors" class="btn grey">
                            <i class="material-icons left">arrow_back</i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>