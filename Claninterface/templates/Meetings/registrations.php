<?php
/**
 * @var Meeting $meeting
 * @var Meetingregistration[] $regs_clan
 * @var Meetingregistration[] $regs_group
 */

use App\Logic\Helper\MeetingRegistrationHelper;
use App\Logic\Helper\RanksHelper;
use App\Model\Entity\Meeting;
use App\Model\Entity\Meetingregistration;


$group = [
    "clan" =>[
        1 => 0,
        2 => 0,
        3 => 0,
    ],
    "group" =>[
        1 => 0,
        2 => 0,
        3 => 0,
    ]
]
?>

<?= $this->Html->link(__('<i class="bi bi-chevron-left"></i> zurück'), ['action' => 'index'], ["class" => "btn btn-dark btn-sm", "escape" => false]) ?>&nbsp;

<h3>Anmeldung für <?= $meeting->name ?>  <small><i>(<?= $meeting->date->format("d.m.Y") ?> <?= $meeting->start->format("H:i") ?>)</i></small></h3>

<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th>Clan</th><th>Spieler</th><th>Status</th><th>Rang</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($regs_clan as $reg):
        $group['clan'][RanksHelper::rankId2Pos($reg->player->rank_id)] += $reg->status == 1 ? 1 :  0 ; ?>
        <tr>
            <td><?= $reg->player->clan->short ?></td>
            <td><?= $reg->player->nick ?></td>
            <td><span class="badge bg-<?= MeetingRegistrationHelper::$status[$reg->status]["class"] ?>"><?= MeetingRegistrationHelper::$status[$reg->status]["icon"] ?> <?= MeetingRegistrationHelper::$status[$reg->status]["display"] ?></span></td>
            <td><?= $this->Html->image("ranks/". $reg->player->rank->name.".png",["height"=>"35"])?><?= $reg->player->rank->speekName ?></td>
        </tr>
    <?php endforeach; ?>
    <?php foreach ($regs_group as $reg):
        $group['group'][RanksHelper::rankId2Pos($reg->player->rank_id)] += $reg->status == 1 ? 1 :  0 ;?>
        <tr>
            <td><?= $reg->player->clan->short ?></td>
            <td><?= $reg->player->nick ?></td>
            <td><span class="badge bg-<?= MeetingRegistrationHelper::$status[$reg->status]["class"] ?>"><?= MeetingRegistrationHelper::$status[$reg->status]["icon"] ?> <?= MeetingRegistrationHelper::$status[$reg->status]["display"] ?></span></td>
            <td><?= $this->Html->image("ranks/". $reg->player->rank->name.".png",["height"=>"35"])?><?= $reg->player->rank->speekName ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="row">
    <div class="col-6">
        <?php  $ranks = [1 =>"Kommando", 2 => "Kampf-Offizier", 3 => "Mitglied"]; ?>
        <b><u>Zusammenfassung Clan</u></b><br />
        <?php foreach ($group["clan"] as $key => $item): ?>
             <?= $ranks[$key] ?>: <?= $item ?> <br />
        <?php endforeach;?>
    </div>
    <div class="col-6">
        <b><u>Zusammenfassung Clangruppe</u></b><br />
        <?php foreach ($group["group"] as $key => $item): ?>
            <?= $ranks[$key] ?>: <?= $item ?> <br />
        <?php endforeach;?>
    </div>
</div>
