<?php
/**
 * @var AppView $this
 * @var User $user
 */

use App\Model\Entity\User;
use App\View\AppView;

?>
<?= $this->Html->link(__('<i class="bi bi-chevron-left"></i> zurück'), ['action' => 'index'], ["class" => "btn btn-sm btn-dark", "escape" => false]) ?>
<br/><br/>
<div class="users view large-9 medium-8 columns content">
    <h1><?= h($user->name) ?></h1>
    <h3>Zusammenfassung</h3>
    <table class="table table-sm">
        <tr>
            <th scope="row"><?= __('Benutzer ID') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Nickname') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= $this->Text->autoLink($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Berechtigung') ?></th>
            <td><?= $user->admin ? '<i class="fas fa-crown"></i> Administrator' : '<i class="far fa-user"></i> Benutzer' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Abgeleitet von WoT-Account') ?></th>
            <td><?= $user->has('player') ? $this->Html->link($user->player->nick, ['controller' => 'Players', 'action' => 'view', $user->player->id]) : '' ?></td>
        </tr>
    </table>
    <?php if (!empty($user->tokens)):  ?>
        <h3>Tokens</h3>
        <table class="table table-sm DataTable">
            <thead>
            <tr>
                <th>#</th><th>Clan</th><th>Spieler</th><th>Token</th><th>Erstellt</th><th>Auslauf</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($user->tokens as $token): ?>
            <tr>
                <td><?= $token->id ?></td>
                <td><?= $this->Html->link($token->player->clan->short,['controller' => 'Players', 'action' => 'view', $token->player->clan->id])?></td>
                <td><?= $this->Html->link(h($token->nickname),['controller' => 'Players', 'action' => 'view', $token->player_id]) ?></td>
                <td><code><?= $token->token ?></code></td>
                <td><?= $token->created->format("d.m.Y H:i") ?></td>
                <td><?= $token->expires->format("d.m.Y H:i") ?></td>
                <td><?= $this->Form->postLink("<i class='fa fa-trash'></i>",['controller' => 'Tokens', 'action' => 'delete', $token->id],["class"=> "btn btn-danger btn-sm", "escape"=>false]) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
    <br/>
    <i><small><b>Erstellt:</b> <?= h($user->created->format("d.m.Y H:i")) ?></small></i><br/>
    <i><small><b>Bearbeitet:</b> <?= h($user->modified->format("d.m.Y H:i")) ?> </small></i><br/>
</div>
<?= $this->element('DataTables', ['orderCol' => 5, 'order' => 'desc']) ?>
