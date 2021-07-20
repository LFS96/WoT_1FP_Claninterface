<?php
/**
 * @var \App\View\AppView $this
 * @var bool $UserIsAdmin
 * @var Player[] $Players
 * @var int $permissionLevel
 */

use App\Model\Entity\Player;
use Cake\Core\Configure;

$addAccountUrl = "https://".Configure::read('Wargaming.server')."/wot/auth/login/?application_id=". Configure::read('Wargaming.authkey')."&display=page&redirect_uri=".$this->Url->build(["controller" => "Tokens","action" => "receive"], ['escape' => false,'fullBase' => true]);

?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Berechtigungen</h1>

        <p class="lead">
            <?php if($permissionLevel >= 3):?>
            <i class="bi bi-check-circle-fill"></i>
            <?php endif; ?>
            Sie sind
            <?php if($permissionLevel ==-1):?>
                UNBEKANNT
            <?php endif; ?>
            <?php if($permissionLevel == 0):?>
                Externer oder ohne WoT-Account
            <?php endif; ?>
            <?php if($permissionLevel ==3):?>
                Clangruppen Mitglied
            <?php endif; ?>
            <?php if($permissionLevel ==5):?>
                Clangruppen-Feldkommandant
            <?php endif; ?>
            <?php if($permissionLevel ==8):?>
                Clangruppen Kommandant
            <?php endif; ?>
            <?php if($permissionLevel ==10):?>
                Claninterface Administrator
            <?php endif; ?>
            </p>


        <?= $this->Html->link("WoT-Account mit dem Claninterface verbinden",$addAccountUrl) ?> <i class="toggle-explain-btn bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Anleitung anzeigen"></i>

        <div class="toggle-explain">
            <br/>
            <br/>
            <strong>Anleitung</strong>
            <ol>
                <li>Auf den oberen Link klicken</li>
                <li>Bei Wargaming einloggen</li>
                <li>Zugriff bestätigen</li>
                <li><b>Wenn weitere Accounts hinzugefügt werden sollen:</b> Bei Wargaming ausloggen, bei 1. beginnen.</li>
            </ol>
            </div>
    </div>
</div>

Folgende Accounts sind mit deinem Konto verbunden:<br />
<?php if(!$Players->count()){
    echo "keine Accounts mit diesem Konto Verbunden";
}else{?>
    <table class="table">
        <thead>
        <tr>
            <th>Clan</th>
            <th>Nick</th>
            <th>Rang</th>
            <th>Verbunden bis:</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($Players as $player): ?>
            <tr>
                <td><?= $player->clanName ?></td>
                <td><?= $this->Html->link($player->nick,["controller"=>"Players","action"=>"view",$player->id]) ?></td>
                <td><?=  $this->Html->image("ranks/" . $player->rankIcon . ".png", ["height" => 25]) ?> <?= $player->rank ?></td>
                <td><?= $player->expires->format("d.m.Y H:i") ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php } ?>
<script>
    $(document).ready(function (){
        $(".toggle-explain").hide();
        $(".toggle-explain-btn").click(function (){
            $(".toggle-explain").slideToggle();
        });
    });
</script>
