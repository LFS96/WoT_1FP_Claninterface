<?php
/**
 * @var AppView $this
 * @var array $response
 */

use App\View\AppView;

echo "Extracting data from Teamspeak...<br />";
echo "----------------------------------------------------<br />";
echo "Count of Teamspeak users: " . count($response) . "<br />";
echo "----------------------------------------------------<br />";
echo "Finished extracting data from Teamspeak.<br />";
