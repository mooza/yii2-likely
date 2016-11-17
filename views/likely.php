<?php

/**
 * @var array $options
 * @var Yii\web\View $this
 */

?>

<div class="likely <?= $options['colorClass'] ?> <?= $options['sizeClass'] ?>" <?= $options['url'] ?> <?=$options['title'] ?>>
    <?php foreach ($options['items'] as $item) { ?>
        <div class="<?= $item['class'] ?>" <?= $item['via'] ?> <?= $item['text'] ?> <?=$item['media'] ?>><?= $item['title'] ?></div>
    <?php } ?>
</div>