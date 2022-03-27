<?php
/**
 * @var AppView $this
 * @var bool $UserIsAdmin
 * @var Player[] $Players
 * @var \App\Logic\Helper\RightsHelper|null $rights
 * @var array $registrations
 * @var User $auth
 */

use App\Logic\Helper\MeetingRegistrationHelper;
use App\Model\Entity\Player;
use App\Model\Entity\User;
use App\View\AppView;
use Cake\Core\Configure;

$addAccountUrl = "https://" . Configure::read('Wargaming.server') . "/wot/auth/login/?application_id=" . Configure::read('Wargaming.authkey') . "&display=page&redirect_uri=" . $this->Url->build(["controller" => "Tokens", "action" => "receive"], ['escape' => false, 'fullBase' => true]);

?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Berechtigungen</h1>
        <span class="lead">
            <?php if ($rights == null) { ?>
               Berechtigung: UNBEKANNT
            <?php } else { ?>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Berechtigung</th><th>Ihre Rechte</th></tr></thead>
                    <tbody>
                        <tr><td>Clangruppen-Mitglied</td><td><?= $user?->canResult("Member", $auth)->getStatus() ? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                        <tr><td>Clangruppen-Offizier</td><td><?= $user?->canResult("Officer", $auth)->getStatus() ? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                        <tr><td>Clangruppen-Personaloffizier</td><td><?= $user?->canResult("Personal", $auth)->getStatus() ? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                        <tr><td>Clangruppen-Feldkommandant</td><td><?= $user?->canResult("FieldCommander", $auth)->getStatus()? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                        <tr><td>Clangruppen-Commandant</td><td><?= $user?->canResult("Commander", $auth)->getStatus() ? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                        <tr><td>Administrator</td><td><?= $user?->canResult("Admin", $auth)->getStatus() ? "<i class='bi bi-check-circle-fill'></i> JA" : "<i class='bi bi-slash-circle'></i> NEIN" ?></td></tr>
                    </tbody>
                </table>
            <?php } ?>
        </span>
    </div>
</div>

<h3>Verbundene WoT-Accounts</h3>
Das Claninterface benutzt den Rang der verbundenen WoT-Accounts, um deine Berechtigungen du im Claninterface festzulegen.
Wir verwenden das Wargaming-OpenID verfahren, bei diesem Bestätigt Wargaming, dass du der Besitzer des WoT-Accounts bis.
<b>Wir erhalten weder deine E-Mail-Adresse noch dein
    Passwort</b>, alle 14 Tage muss die Verbindung erneut bestätigt werden.<br/>
<u>Folgende Daten erhalten und nutzen wir:</u>
<ul>
    <li>Benutzername</li>
    <li>vertrauliche Claninformationen</li>
    <li>Liste der Spielerfahrzeuge, inklusive Mietfahrzeuge</li>
    <li>Erweiterte Spieler-Statistiken</li>
</ul>
<?= $this->Html->link("<i class='bi bi-plus-circle-dotted'></i> WoT-Account verbinden", $addAccountUrl, ["class" => "btn btn-success btn-sm", "escape" => false]) ?>
<i class="toggle-explain-btn bi bi-question-circle" data-toggle="tooltip" data-placement="top"
   title="Anleitung anzeigen"></i>
<br/> <br/>
<div class="toggle-explain">


    <strong>Anleitung</strong>
    <ol>
        <li>Auf den oberen <?= $this->Html->link("Link", $addAccountUrl) ?> klicken</li>
        <li>Bei <?= $this->Html->link("Wargaming", "https://eu.wargaming.net/", ["target" => "_blank"]) ?> einloggen
        </li>
        <li>Zugriff bestätigen</li>
        <li><b>Wenn weitere Accounts hinzugefügt werden sollen:</b>
            Bei <?= $this->Html->link("Wargaming", "https://eu.wargaming.net/", ["target" => "_blank"]) ?> ausloggen,
            bei 1. beginnen.
        </li>
    </ol>
</div>

<?php if (!$Players->count()) {
    echo "keine Accounts verbunden";
} else { ?>
    <div class="row">
        <?php foreach ($Players as $player): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="connetedPlayers">
                    <span class="player-clan">[<?= $player->clanName ?>]</span>
                    <span
                        class="player">  <?= $this->Html->link($this->Html->image("ranks/" . $player->rankIcon . ".png", ["height" => 35]) . " " . h($player->nick), ["controller" => "Players", "action" => "view", $player->id], ["escape" => false, 'data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Statistik anzeigen"]) ?></span>

                    <span class="timeout"><?= $player->expires->format("d.m.Y H:i") ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php } ?>
<?php if (!empty($registrations)): ?>
    <br/>
    <h3>An Events teilnehmen</h3>
    Bitte teile uns mit an welchen Events du teilnehmen willst?
    <table class="table table-sm table-hover">
        <thead>
        <tr>
            <th>Account</th>
            <th>Event</th>
            <th>Zeitraum</th>
            <th>Teilnahme?</th>
            <th>Nimmst du Teil?</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $registration): ?>
            <tr>
                <td><?= $registration["player"]->nick ?></td>
                <td><?= $registration["meeting"]->name ?> <small>[<?= $registration["meeting"]->clan->short ?>]</small>
                </td>
                <td><?= $registration["meeting"]->date->format("d.m.Y") ?> <?= $registration["meeting"]->start->format("H:i") ?>
                    - <?= $registration["meeting"]->end->format("H:i") ?> </td>
                <td><span
                        class="badge bg-<?= MeetingRegistrationHelper::$status[$registration["status"]]["class"] ?>"><?= MeetingRegistrationHelper::$status[$registration["status"]]["icon"] ?> <?= MeetingRegistrationHelper::$status[$registration["status"]]["display"] ?></span>
                </td>
                <td>
                    <?php foreach (MeetingRegistrationHelper::$status as $key => $status):
                        if ($status["isButton"]):
                            ?>

                            <?= $this->Form->postLink(
                            $status["icon"] . " " . $status["button"],
                            ["controller" => "Meetingregistrations", "action" => "setRegistrations", $registration["player"]->id, $registration["meeting"]->id, $key],
                            ["class" => "btn btn-sm btn-{$status["class"]}", "escape" => false]); ?>
                        <?php
                        endif;
                    endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<script>
    $(document).ready(function () {
        $(".toggle-explain").hide();
        $(".toggle-explain-btn").click(function () {
            $(".toggle-explain").slideToggle();
        });
    });
</script>
