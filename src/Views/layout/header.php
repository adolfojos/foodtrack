<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="msapplication-TileColor" content="#ffffff"/>
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png"/>
        <meta name="theme-color" content="#ffffff"/>
        <meta name="format-detection" content="telephone=no"/>
        <title><?php echo $title ?? 'foodtrack Support'; ?></title>
        <link rel="stylesheet" type="text/css" href="<?= VENDOR_PATH ?>material-icons/material-icons.css"/>
        <link rel="stylesheet" type="text/css" href="<?= VENDOR_PATH ?>materialize-src/sass/materialize.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?= VENDOR_PATH ?>materialize-datatables/css/dataTables.materialize.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_PATH ?>main.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_PATH ?>fonts.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_PATH ?>link-options.css">
    </head>
    <body>
        <header>
            <div id="main-menu" class="navbar-fixed">
                <nav>
                    <div class="nav-wrapper">
                        <a href="#" data-activates="mobile-menu" class="button-collapse mobile-menu">
                        <i class="material-icons">menu</i>
                        </a>
                        <div class="container2">
                            <ul class="left">
                                <li class="first active">
                                    <a href="<?= BASE_URL ?>">
                                    <i class="ico-dash_coin"></i>
                                    <span class="hide-on-small-only">foodtrack</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>institutions" title="Institutions">
                                    <i class="material-icons">account_balance</i>
                                    <span class="hide-on-small-only">Institutions</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>actors" title="Actors">
                                    <i class="material-icons">assignment_ind</i>
                                    <span class="hide-on-small-only">Actors</span>
                                    </a>
                                </li>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>receptions" title="Receptions">
                                    <i class="material-icons">assignment</i>
                                    <span class="hide-on-small-only">Receptions</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>deliveries" title="Deliveries">
                                    <i class="material-icons">local_shipping</i>
                                    <span class="hide-on-small-only">Deliveries</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>dailyreports" title="Daily Reports">
                                    <i class="material-icons">local_shipping</i>
                                    <span class="hide-on-small-only">Daily Reports</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>users" title="Users">
                                    <i class="material-icons">people</i>
                                    <span class="hide-on-small-only">Users</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                            <ul class="right">
                                <li style="line-height: 40px;">
                                    <a href="<?= BASE_URL ?>" style="line-height: 30px;margin-top: 5px;height: 40px;border-radius: 8px;">
                                    <i class="material-icons" style="line-height: 40px;height: 40px">notifications</i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-button-custom" href="javascript:void(0)" data-activates="dropdown1">
                                    <i class="material-icons left">perm_identity</i><i class="material-icons right">arrow_drop_down</i>
                                    </a>
                                </li>
                            </ul>
                            <ul id="dropdown1" class="dropdown-content">
                                <li><span><?=$_SESSION['username']?></span></li>
                                <li class="divider"></li>
                                <li>
                                    <a title="Profile" href="<?= BASE_URL ?>profile">Profile</a>
                                </li>
                                <li>
                                    <a title="Logout" href="<?= BASE_URL ?>auth/logout/">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="side-nav" id="mobile-menu">
                <ul id="actions-menu" class="collapsible collapsible-accordion">
                    <li class="menu-header">
                        <a href="#" onclick="$('.button-collapse').sideNav('hide');"><i class="material-icons">clear</i></a>
                        Home                
                    </li>
                    <li class="no-padding">
                        <a href="news.html" class="waves-effect waves-grey">
                        <i class="ico-noticias"></i>News
                        </a>
                    </li>
                    <li class="no-padding">
                        <a href="social-protection.html" class="waves-effect waves-grey">
                        <i class="ico-proteccion_social1"></i>Social Protection
                        </a>
                    </li>
                    <li class="no-padding">
                        <a href="militia.html" class="waves-effect waves-grey">
                        <i class="ico-fortalecimiento"></i> Enlistment
                        </a>
                    </li>
                    <li class="no-padding">
                        <a href="plan.html" class="waves-effect waves-grey">
                        <i class="ico-vuelta"></i> Return
                        </a>
                    </li>
                    <li class="no-padding active">
                        <a href="surveys.html" class="waves-effect waves-grey">
                        <i class="ico-encuesta"></i> Surveys
                        </a>
                    </li>
                </ul>
                <ul id="logout-menu">
                    <li>
                        <a href="notification">
                        <i class="material-icons">notifications</i> Notifications
                        </a>
                    </li>
                    <li>
                        <a href="logout">
                        <i class="material-icons left">perm_identity</i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </header>
        <!-- Main -->
        <main>
            <div class="inner-main">
                <?php
                // ---- INICIO DE MENSAJES FLASH ----//
                if (isset($_SESSION['flash_message'])):
                    $flash = $_SESSION['flash_message'];
                    // Definimos un estilo básico para los mensajes
                    ?>
                    <div class="flash-message flash-<?php echo htmlspecialchars($flash['type']); ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                    <?php
                    // ¡Importante! Borrar el mensaje después de mostrarlo
                    unset($_SESSION['flash_message']);
                endif;
                // ---- FIN DE MENSAJES FLASH ---//
                ?>

<!-- Mensajes flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash <?= $_SESSION['flash_message']['type'] ?>">
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>