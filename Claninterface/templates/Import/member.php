<?php

use App\View\AppView;

/**
 * @var AppView $this
 * @var array $response
 */

echo "Import Clan Statistics...<br />";
echo "----------------------------------------------------<br />";
echo "Count of Clans: " . count($response) . "<br />";
echo "----------------------------------------------------<br />";
echo "Finished Import Clan Statistics.<br />";

echo "<table class='table table-sm table-striped'>";
echo "<tr><th>Clan ID</th><th>Clan</th><th>Members</th></tr>";
foreach ($response as $clanId =>  $clan) {
    echo "<tr>";
    echo "<td>" . $clanId . "</td>";
    echo "<td>" . $clan["name"] . "</td>";
    echo "<td>" . $clan["member"] . "</td>";
    echo "</tr>";
}
echo "</table>";

