<?php
/**
 * @var \App\Model\Entity\Meeting[] $Meetings
 * @var array $Players
 * @var \App\Model\Entity\Clan $Clan
 */

use App\Logic\Helper\MeetingsHelper;

?>
<?= $this->Html->link(__('<i class="bi bi-chevron-left"></i> zurück'), ['action' => 'index'], ["class" => "btn btn-dark btn-sm", "escape" => false]) ?>&nbsp;

<h4>Teilname Zusammenfassung für <?= $Clan->short ?></h4>

<i class="text-secondary far fa-times-circle"></i> Nicht im TS3-Server
 | <i class='text-danger bi bi-exclamation-diamond-fill'></i> Am TS3-Server aber nicht im Spiel
 | <i class="text-warning bi bi-check-circle-fill"></i> Am TS3-Server und im Spiel, aber nicht im Kampfraum
 | <i class="text-success bi bi-check-circle-fill"></i> Am TS3-Server und im Spiel

<table class="table table-striped table-sm">
    <thead>
    <tr>
        <th></th>
        <?php foreach($Meetings as $meeting): ?>
            <th><?= $meeting->name ?> (<?= $meeting->date->format("d.m.y")?>)</th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($Players as $nick => $m): ?>
    <tr>
        <th><?= $nick ?></th>
        <?php
        /**
         * @var \App\Model\Entity\Meeting $meeting
         */
        foreach($m as $meeting): ?>
            <td>
                <?php
                    if($meeting == null){
                        echo '<i class="text-secondary far fa-times-circle"></i>';
                    }else{
                        if($meeting->wot){
                            $color = MeetingsHelper::joinTsBattleRoom($meeting->channel)?"text-success":"text-warning";
                            echo  "<i class=\"$color bi bi-check-circle-fill\"></i>";
                        }else{
                            echo "<i class='text-danger bi bi-exclamation-diamond-fill'></i>";
                        }
                    }
                ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br />
<br />

