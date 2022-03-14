<?php
/**
 * @var AppView $this
 * @var array $params
 * @var string $message
 */

use App\View\AppView;

$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="<?= h($class) ?>" onclick="this.classList.add('hidden');"><?= $message ?></div>
