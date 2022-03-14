<?php
/**
 * @var AppView $this
 * @var Tanktype $tanktype
 */

use App\Model\Entity\Tanktype;
use App\View\AppView;

?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tanktype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tanktype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Tanktypes'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="tanktypes form large-9 medium-8 columns content">
    <?= $this->Form->create($tanktype) ?>
    <fieldset>
        <legend><?= __('Edit Tanktype') ?></legend>
        <?php
            echo $this->Form->control('name');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
