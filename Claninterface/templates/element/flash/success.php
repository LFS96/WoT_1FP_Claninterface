<?php
/**
 * @var AppView $this
 * @var array $params
 * @var string $message
 */

use App\View\AppView;

if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="message success" onclick="this.classList.add('hidden')"><?= $message ?></div>
