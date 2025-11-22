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
                            <a href="<?= BASE_URL ?>actors/create" class="btn waves-effect waves-light">
                                <i class="material-icons left">add</i> btn
                            </a>
                        </div>
                    </div>
                </div> 
                <div class="row row-end">
                    <div class="col s12">
                        <form method="post" action="<?= BASE_URL ?>actors/save">
                            <?php if (!empty($actor->id)): ?>
                                <input type="hidden" name="id" value="<?= (int)$actor->id ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$actor->user_id ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="full_name" id="full_name" value="<?= $actor->full_name ?? '' ?>" required>
                                    <label for="full_name" class="required">Nombre completo:</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="national_id" id="national_id" value="<?= $actor->national_id ?? '' ?>" required>
                                    <label for="national_id" class="required">Cédula de identidad:</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="email" id="email" value="<?= $actor->email ?? '' ?>" required>
                                    <label for="email" class="required">Correo electrónico:</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="phone" id="phone" value="<?= $actor->phone ?? '' ?>" required>
                                    <label for="phone" class="required">Teléfono:</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" name="username" id="username" value="<?= $actor->user_name ?? '' ?>" required>
                                    <label for="username" class="required">Usuario:</label>
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" name="password" id="password">
                                    <label for="password" class="required">Contraseña:</label>
                                    <?php if (!empty($actor->id)): ?>
                                        <small>Deja en blanco para mantener la contraseña actual.</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <select id="role" name="role" required onchange="toggleInstitutionSelect()">
                                        <option value="">Selecciona un Rol</option>
                                            <?php foreach ($allowedRoles as $r): ?>
                                        <option value="<?= $r ?>"
                                            <?= (!empty($actor->role) && $actor->role === $r) ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_',' ', $r)) ?>
                                        </option>
                                            <?php endforeach; ?>
                                    </select>
                                    <label class="required" for="role">Rol:</label>
                                </div>
                                <div class="input-field col s6" id="institutionBlock">
                                    <select id="institutionDropdown" name="institution_ids[]">
                                        <option value="">Selecciona una institución</option>
                                            <?php foreach ($all_institutions as $inst): ?>
                                        <option value="<?= $inst->id ?>"
                                            <?= in_array($inst->id, $institution_ids ?? []) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($inst->name) ?>
                                        </option>
                                            <?php endforeach; ?>
                                    </select>
                                    <label class="required" for="institutionDropdown">Institución:</label>
                                    <small>Nota: Un director o vocero institucional solo puede estar asociado a una institución.</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12 m12 l12">
                                    <input type="checkbox" id="active" name="active" class="filled-in" value="1" <?= (!isset($actor->active) || $actor->active) ? 'checked' : '' ?>/>
                                    <label for="active">Institución activa</label>
                                </div>
                            </div>
                            <div class="row right-align">
                                <button type="submit" class="btn waves-effect waves-light"><i class="material-icons left">save</i> Guardar</button>
                                <a href="<?= BASE_URL ?>actors" class="btn grey"><i class="material-icons left">arrow_back</i> Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- card-panel -->
        </div> <!-- col -->
    </div> <!-- conten-body -->
</article>
<script>
function toggleInstitutionSelect() {
    const role = document.getElementById('role').value;
    const block = document.getElementById('institutionBlock');

    if (role === 'director' || role === 'vocero_institucional') {
        block.style.display = 'block';
    } else {
        block.style.display = 'none';
        document.getElementById('institutionDropdown').selectedIndex = 0;
    }
}

document.addEventListener('DOMContentLoaded', toggleInstitutionSelect);
</script>
