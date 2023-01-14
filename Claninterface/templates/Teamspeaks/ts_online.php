<?php
/**
 * @var AppView $this
 * @var array $online
 * @var int $permissionLevel
 */

use App\View\AppView;

?>
<?= $this->element('TeamspeakNav', ['site' => "tsOnline"]) ?>
<h1>Teamspeak Admin</h1>
<table class="table table-sm table-striped DataTable">
    <thead>
    <tr>
        <th>Teamspeak</th>
        <th>Ingame</th>
        <th>WoT-Online</th>
        <th>Channel</th>
        <?php if($user->can("Admin",$auth)): ?><th>XXX</th> <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($online as $row): ?>
        <tr>
            <td><?= $row["teamspeak"] ?></td>
            <td><?= $row["ingame"] ?></td>
            <?php if ($row["online"]) { ?>
                <td data-order='online' data-search='online'><i class='text-success bi bi-check2-circle'></i></td>
            <?php } else { ?>
                <td data-order='offline' data-search='offline'><i
                        class='text-danger bi bi-exclamation-diamond-fill'></i></td>

            <?php } ?>
            <td><?= $row["channel"] ?></td>
            <?php if($user->can("Admin",$auth)): ?>
            <td>
                <?php
                if (!$row["admin"]) {
                    echo $this->Form->postLink("kicken", ["action" => "kick", bin2hex($row["teamspeakUID"])], ["class" => "btn btn-sm btn-warning", 'confirm' => __('Spieler wirklich kicken')])."&nbsp;";
                    echo $this->Form->postLink("bannen", ["action" => "ban", bin2hex($row["teamspeakUID"])], ["class" => "btn btn-sm btn-danger",'confirm' => __('Spieler wirklich für einen Tag  bannen')])."&nbsp;";
                }
                ?>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if($user->can("Admin",$auth)): ?>
<hr>
<h4>Nachricht an alle</h4>
<?= $this->Form->create(null,["action" => "pokeAll"]) ?>
<?= $this->Form->control('Nachricht'); ?>
<?= $this->Form->button(__('Senden'),["class" => "btn btn-success"]) ?>
<?= $this->Form->end() ?>
<?php endif; ?>




<?= $this->element('DataTables', ['orderCol' => 4, 'order' => 'asc']) ?>
