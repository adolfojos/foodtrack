<!-- Menú lateral izquierdo (plantilla base) -->
<aside id="left-side-menu">
    <ul class="collapsible collapsible-accordion">
        <li class="no-padding">
            <a href="RUTA_1.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Name Section 1
            </a>
        </li>
        <li class="no-padding">
            <a href="RUTA_2.html" class="waves-effect waves-grey">
                <i class="material-icons">menu</i>Name Section 2
            </a>
        </li>
    </ul>
</aside>
<?php 
    $is_editing = isset($user) && $user !== null; 
    $action_title = $is_editing ? 'Edit' : 'Create';
?>
<article>
    <div class="conten-body">
        <div class="card-panel">
            <!-- Título -->
            <div class="card-title">
                <div class="row">
                    <div class="header-title-left col s12">
                        <h5><?php echo $action_title; ?> User</h5>
                    </div>
                </div>
            </div>          
            <!-- Formulario -->
            <form action="<?=BASE_URL?>users/guardar" method="POST">
                <?php if ($is_editing): ?>
                    <input type="hidden" name="id" value="<?php echo $user->id; ?>">
                <?php endif; ?>
                <div id="user-form">
                    <!-- User Name: -->
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="text" id="username" name="username" required value="<?php echo $is_editing ? htmlspecialchars($user->username) : ''; ?>" />
                            <label class="required" for="username">User Name:</label>
                        </div>
                    </div>
                    <!-- Contraseña -->
                    <div class="row">
                        <div class="input-field col s12">   
                            <input type="password" id="password" name="password" <?php echo $is_editing ? '' : 'required'; ?> />
                            <label class="required" for="password">Password<?php echo $is_editing ? ' (Dejar vacío para no cambiar)' : ' *'; ?>:</label>
                        </div>
                    </div>
                    <!-- Departamento -->
                    <div class="row">
                        <div class="input-field col s12">
                            <select id="role" name="role" required>
                                <option value="" disabled selected>Select Role</option>
                                <?php
                                $current_rol = $is_editing ? $user->role : ''; 
                                if (isset($allowedRoles) && is_array($allowedRoles)):
                                    foreach ($allowedRoles as $role):
                                        $selected = ($current_rol === $role) ? 'selected' : '';
                                        ?>
                                    <option value="<?php echo $role; ?>" <?php echo $selected; ?>>
                                        <?php echo ucfirst($role); ?>
                                    </option>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                            <?php if (empty($allowedRoles)): ?>
                                <p style="color: red;">
                                    Warning! There is no User Role.
                                    <a href="<?=BASE_URL?>users/create">Create one first</a>.
                                </p>
                            <?php endif; ?>
                            <label class="required" for="role">User Role:</label>
                        </div>
                    </div>


                <!-- Botones -->
                <div class="row btn-actions">
                    <div class="col l12">
                        <button type="submit" name="action" class="btn waves-effect waves-light btn-first">
                            <?php echo $is_editing ? 'Actualizar' : 'Create'; ?> User
                        </button>
                        <a href="<?=BASE_URL?>users" class="btn grey" title="Go back">Go back</a>
                    </div>
                </div>
            </form>
        </div> <!-- card-panel -->
    </div> <!-- conten-body -->
</article>
