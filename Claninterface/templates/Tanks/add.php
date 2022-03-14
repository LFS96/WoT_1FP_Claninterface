<?php
/**
 * @var AppView $this
 * @var Tank $tank
 */

use App\Model\Entity\Tank;
use App\View\AppView;

?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Tanks'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Tank Types'), ['controller' => 'Tanktypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Tank Type'), ['controller' => 'Tanktypes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Statistics'), ['controller' => 'Statistics', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Statistic'), ['controller' => 'Statistics', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tanks form large-9 medium-8 columns content">
    <?= $this->Form->create($tank) ?>
    <fieldset>
        <legend><?= __('Add Tank') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('tier');
            echo $this->Form->control('tankType_id', ['options' => $tankTypes]);
            echo $this->Form->control('expDef');
            echo $this->Form->control('expFrag');
            echo $this->Form->control('expSpot');
            echo $this->Form->control('expDamage');
            echo $this->Form->control('expWinRate');
            echo $this->Form->control('nation');
            echo $this->Form->control('premium');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
