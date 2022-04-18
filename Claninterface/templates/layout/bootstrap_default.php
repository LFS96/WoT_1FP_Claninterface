<?php
/**
 * Created by PhpStorm.
 * User: fHarmsen
 * Date: 20.02.2019
 * Time: 09:36
 *
 * @var AppView $this
 * @var RightsHelper|null $rights
 * @var User|null $auth
 */


use App\Logic\Helper\RightsHelper;
use App\Model\Entity\User;
use App\View\AppView;
use Cake\Core\Configure;

$lang = Configure::read('App.language');

/**
 * Default `flash` block.
 */
if (!$this->fetch('tb_flash')) {
    $this->start('tb_flash');
    if (isset($this->Flash))
        echo $this->Flash->render();
    $this->end();
}
if (!$this->fetch('tb_breadcrumb')) {
    $this->start('tb_breadcrumb');
    if (isset($this->Breadcrumbs)) {
        echo "<div class='mps-bread'>";
        echo $this->Breadcrumbs->render();
        echo "</div>";
    }
    $this->end();
}


/**
 * Prepend `meta` block with `author` and `favicon`.
 */
$this->prepend('meta', $this->Html->meta('author', null, ['name' => 'author', 'content' => Configure::read('App.author')]));
$this->prepend('meta', $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']));
$this->prepend('meta', $this->Html->meta(array('name' => 'robots', 'content' => 'noindex, nofollow'), null, array('inline' => false)));


$isAuth = ($auth != null);

$isOnlyPlayer = false;
$user = $this->request->getAttribute('identity');

$container_class = "container";
if (isset($container)) {
    $container_class = $container;
}

?>
<!DOCTYPE html>
<head lang="<?= $lang ?>" class="no-js">
    <?= $this->Html->charset() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1">
    <title><?= $this->fetch('title') ?></title>
    <?= $this->fetch('meta') ?>

    <?= $this->Html->css('bootstrap4/bootstrap.min.css'); ?>
    <?= $this->Html->css('jquery-ui.min.css'); ?>
    <?= $this->Html->css("bootstrap-icons.min.css"); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <?= $this->Html->css("default.css"); ?>
    <?= $this->Html->css("font_awesome/all.css"); ?>
    <?= $this->fetch('css'); ?>

    <?= $this->Html->script('jquery-3.5.1.min.js') ?>
    <?= $this->Html->script('popper.min.js') ?>
    <?= $this->Html->script('bootstrap4/bootstrap.min.js') ?>
    <?= $this->Html->script('font_awesome/all.min.js') ?>
    <script src='https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>var _tooltip = jQuery.fn.tooltip;</script>
    <?= $this->Html->script('jquery-ui.min.js') ?>
    <script>jQuery.fn.tooltip = _tooltip;</script>
    <?= $this->fetch('js'); ?>
<body>
<nav class="navbar navbar-dark bg-dark navbar-expand-md">
    <div class="container">
        <?php if ($rights == null): ?>
            <?= $this->Html->link("Clan Interface", ['controller' => 'Inactives', 'action' => 'add', 'home'],["class"=>"navbar-brand"]) ?>
        <?php else:?>
            <?= $this->Html->link("Clan Interface", ['controller' => 'Users', 'action' => 'Dashboard'],["class"=>"navbar-brand"]) ?>
        <?php endif; ?>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($rights == null): ?>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <?= $this->Html->link("Auszeit nehmen", ['controller' => 'Inactives', 'action' => 'add', 'home'], ["class" => "nav-link"]) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link("TS3 Rang", ['controller' => 'Players', 'action' => 'tsRank'], ["class" => "nav-link"]) ?>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <?= $this->Html->link("Login", ['controller' => 'Users', 'action' => 'login'], ["class" => "nav-link"]) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link("Über & Rechtliches und Impressum", ['controller' => 'pages', "action" => "display", "impressum"], ["class" => "nav-link"]) ?>
                </li>
            </ul>

            <?php endif; ?>
            <?php if ($rights != null): ?>
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <?= $this->Html->link("Clans", ['controller' => 'Clans', 'action' => 'index'], ["class" => "nav-link"]) ?>
                    </li>
                    <?php if ($user?->canResult("Personal", $auth)->getStatus()): ?>
                        <li class="nav-item">
                            <?= $this->Html->link("Teamspeak", ['controller' => 'Teamspeaks', 'action' => 'index'], ["class" => "nav-link"]) ?>
                        </li>
                        <li class="nav-item">
                            <?= $this->Html->link("Abmeldungen", ['controller' => 'Inactives', 'action' => 'index'], ["class" => "nav-link"]) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($user?->canResult("FieldCommander", $auth)->getStatus()): ?>
                        <li class="nav-item">
                            <?= $this->Html->link("Veranstaltung", ['controller' => 'Meetings', 'action' => 'index'], ["class" => "nav-link"]) ?>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Mehr
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <?= $this->Html->link("Panzer", ['controller' => 'Tanks', 'action' => 'index'], ["class" => "dropdown-item"]) ?>
                            <?php if ($user?->canResult("Commander", $auth)->getStatus()): ?>
                                <?= $this->Html->link("Ränge", ['controller' => 'Ranks', 'action' => 'index'], ["class" => "dropdown-item"]) ?>
                            <?php endif; ?>
                            <?= $this->Html->link("TS3 Rang", ['controller' => 'Players', 'action' => 'tsRank'], ["class" => "dropdown-item"]) ?>
                            <?= $this->Html->link("Auszeit nehmen", ['controller' => 'Inactives', 'action' => 'add', 'home'], ["class" => "dropdown-item"]) ?>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= $auth->name ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <?= $this->Html->link("Mein Konto", ['controller' => 'Users', 'action' => 'Dashboard'], ["class" => "dropdown-item"]) ?>
                            <?= $this->Html->link("Passwort ändern", ['controller' => 'Users', 'action' => 'newpass'], ["class" => "dropdown-item"]) ?>
                            <?= $this->Html->link("Abmelden", ['controller' => 'Users', 'action' => 'logout'], ["class" => "dropdown-item"]) ?>

                            <?php if ($user?->canResult("Admin", $auth)->getStatus()): ?>
                                <hr/>
                                <?= $this->Html->link("Nutzerverwaltung", ['controller' => 'Users', 'action' => 'index'], ["class" => "dropdown-item"]) ?>
                            <?php endif; ?>
                            <hr/>
                            <?= $this->Html->link("Über & Rechtliches und Impressum", ['controller' => 'pages', "action" => "display", "impressum"], ["class" => "dropdown-item"]) ?>
                        </div>
                    </li>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</nav>
<div class="<?= $container_class ?>">
    <br/>
    <?= $this->fetch('tb_breadcrumb'); ?>

    <?= $this->fetch('tb_flash'); ?>

    <?= $this->fetch('content'); ?>
    <br/>
    <?= $this->fetch('tb_footer'); ?>
</div>
<?php if (Configure::read('footer.enable') === true): ?>
    <br/><br/><br/><br/>
    <div class="container-fluid">
        <footer class="text-center text-lg-start bg-dark text-light fixed-bottom">
            <div class="row">
                <div class="col-12">
                    <?= Configure::read('footer.text') ?> <br/>
                    <?= $this->Html->link(Configure::read('footer.link.text'), Configure::read('footer.link.url'), ["target" => Configure::read('footer.link.target')]) ?>
                </div>
            </div>
            <span class="copyright-lfs96 ">by LFS96
        <?= $this->Html->link('<i class="bi bi-github"></i>', 'https://github.com/LFS96/WoT_1FP_Claninterface', ["escape" => false, "target" => "_blank"]) ?>
                <?= $this->Html->link('<i class="bi bi-telegram"></i>', 'https://t.me/FabiGothic', ["escape" => false, "target" => "_blank"]) ?>
                <?= $this->Html->link('<i class="bi bi-instagram"></i>', 'https://instagram.com/fabigothic/', ["escape" => false, "target" => "_blank"]) ?><br/>
        <a rel="license" href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank"><img
                alt="GNU GENERAL PUBLIC LICENSE" style="border-width:0"
                src="https://upload.wikimedia.org/wikipedia/commons/9/93/GPLv3_Logo.svg"
                title="Dieses Werk ist lizenziert unter einer GNU GENERAL PUBLIC LICENSE"/></a>
    </span>
        </footer>
    </div>
<?php endif; ?>
<?php if (Configure::read('footer.enable') !== true): ?>
    <span class="copyright-lfs96">by LFS96
        <?= $this->Html->link('<i class="bi bi-github"></i>', 'https://github.com/LFS96/WoT_1FP_Claninterface', ["escape" => false, "target" => "_blank"]) ?>
        <?= $this->Html->link('<i class="bi bi-telegram"></i>', 'https://t.me/FabiGothic', ["escape" => false, "target" => "_blank"]) ?>
        <?= $this->Html->link('<i class="bi bi-instagram"></i>', 'https://instagram.com/fabigothic/', ["escape" => false, "target" => "_blank"]) ?><br/>
        <a rel="license" href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank"><img
                alt="GNU GENERAL PUBLIC LICENSE" style="border-width:0"
                src="https://upload.wikimedia.org/wikipedia/commons/9/93/GPLv3_Logo.svg"
                title="Dieses Werk ist lizenziert unter einer GNU GENERAL PUBLIC LICENSE"/></a>
    </span>
<?php endif; ?>
<?= $this->fetch('scriptBottom'); ?>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
</body>
</html>
