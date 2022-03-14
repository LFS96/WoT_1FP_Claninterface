<?php
/**
 * @var AppView $this
 * @var User[] $users
 * @var User[] $wgAccounts
 */

use App\Model\Entity\User;
use App\View\AppView;

?>
<h1> Benutzerverwaltung</h1>
<?= $this->Html->link(__('<i class="fas fa-user-plus"></i> Neuen Benutzer anlegen'), ['action' => 'add'],["escape" => false, 'class'=> "btn btn-success btn-sm"]) ?>
<div class="users index large-9 medium-8 columns content">
    <h3>Benutzer</h3>
    <table class="table table-sm  DataTable table-striped ">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Admin</th>
            <th>Erstellt</th>
            <th>Bearbeitet</th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $this->Number->format($user->id) ?></td>
                <td><?= h($user->name) ?></td>
                <td><?= h($user->email) ?></td>

                <td><?= $user->admin ? "Ja" : "Nein" ?></td>
                <td><?= h($user->created->format("d.m.Y")) ?></td>
                <td><?= h($user->modified->format("d.m.Y")) ?></td>
                <td class="actions">
                    <?= $this->Html->link("<i class='fas fa-eye'></i>",['action' => 'view', $user->id],["escape"=>false, "class"=>"btn btn-info btn-sm"]) ?>
                    <?= $this->Form->postLink("<i class='fas fa-key'></i>", ['action' => 'adminPwReset', $user->id], ['confirm' => __('Neues Passwort an {0} senden?', $user->email), "escape" => false, "class" => "btn btn-primary btn-sm"]) ?>
                    <?= $this->Form->postLink("<i class='fas fa-crown'></i>", ['action' => 'toggleAdmin', $user->id], ['confirm' => __('Toggle admin status?', $user->id), "escape" => false, "class" => "btn btn-sm " . ($user->admin ? "btn-secondary" : "btn-warning")]) ?>
                    <?= $this->Form->postLink("<i class='fas fa-trash'></i>", ['action' => 'delete', $user->id], ['confirm' => __('Konto mit folgender Email löschen? {0}', $user->email), "escape" => false, "class" => "btn btn-danger btn-sm"]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="users index large-9 medium-8 columns content">
    <h3>Wargaming Accounts</h3>
    <table class="table table-sm  DataTable table-striped ">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Admin</th>
            <th>Erstellt</th>
            <th>Bearbeitet</th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($wgAccounts as $user): ?>
            <tr>
                <td><?= $this->Number->format($user->id) ?></td>
                <td><?= h($user->name) ?></td>
                <td><?= h($user->email) ?></td>

                <td><?= $user->admin ? "Ja" : "Nein" ?></td>
                <td><?= h($user->created->format("d.m.Y")) ?></td>
                <td><?= h($user->modified->format("d.m.Y")) ?></td>
                <td class="actions">
                    <?= $this->Html->link("<i class='fas fa-eye'></i>",['action' => 'view', $user->id],["escape"=>false, "class"=>"btn btn-info btn-sm"]) ?>
                    <?= $this->Form->postLink("<i class='fas fa-trash'></i>", ['action' => 'delete', $user->id], ['confirm' => __('Konto mit folgender Email löschen? {0}', $user->email), "escape" => false, "class" => "btn btn-danger btn-sm"]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->element('DataTables', ['orderCol' => 1, 'order' => 'asc']) ?>
