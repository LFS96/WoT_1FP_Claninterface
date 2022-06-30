<?php
/**
 * @var AppView $this
 * @var array $tree
 */
use App\View\AppView;
?>

<?= $this->Html->link("Zum Dashboard",['controller' => 'Users', 'action' => 'dashboard']) ?>

<h1>Bestenliste der Baumfäller</h1>
<span class="text-danger">
    <b>Hinweis:</b> Bäume zu fällen ist mitunter negativ für die Tarnung. Diese Bestenliste ist nicht ernst gemeint.
</span>
<table class="table table-sm DataTable">
    <thead>
    <tr><th>Nickname</th><th>Bäume je Gefecht</th><th>Bäume je Jahr</th><th>Bäume gesamt</th></tr>
    </thead>
    <tbody>
    <?php foreach ($tree as $player){
        echo "<tr>";
        foreach ($player as $col){
            if(is_numeric($col)){
                echo "<td class='text-right' data-sort='$col'>".number_format($col,3,",",".")."</td>";
            }else {
                echo "<td>$col</td>";
            }
        }
        echo "</tr>";

    } ?>
    </tbody>
</table>
<?= $this->element('DataTables', ['orderCol' => 1, 'order' => 'desc']) ?>
