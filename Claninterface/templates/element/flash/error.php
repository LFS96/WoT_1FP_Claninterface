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
<div class="message error" onclick="this.classList.add('hidden');"><?= $message ?></div>
