<?php
/**
 * @var AppView $this
 *
 * @var array $MembersOnline
 * @var Teamspeak[] $OfflineRecords
 * @var Clans[] $ClansTimeout
 */

use App\Logic\Helper\ClanRuleHelper;
use App\Logic\Helper\TimeHelper;
use App\Model\Entity\Teamspeak;
use App\View\AppView;

?>
<?= $this->element('TeamspeakNav', ['site' => "players"]) ?>
<div class="teamspeaks index large-9 medium-8 columns content">


    <h1>Zusammenfassung der Verstöße nach Spielern</h1>
    <table class="DataTable table table-sm table-striped">
        <thead>
        <tr>
            <th>Clan</th>
            <th>Spieler</th>
            <th>Sekunden</th>
            <th>Anzahl</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($OfflineRecords as $ts) {
            $ts = (object)$ts;
            ?>
            <tr>
                <td><?= $ts->short ?></td>
                <td><?= $ts->nick ?></td>
                <td data-order="<?= $ts->sum ?>"><?= TimeHelper::secondsToTime($ts->sum) ?></td>
                <td><?= $ts->count ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

<?= $this->element('DataTables', ['orderCol' => 3, 'order' => 'desc']) ?>
