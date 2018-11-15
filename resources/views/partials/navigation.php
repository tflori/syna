<?php /** @var Syna\View $v */ /** @var callable $e */ ?>
<ul class="nav">
    <?php foreach ($items as $item) : ?>
        <li>
            <a href="<?= $e($item['target']) ?>">
                <i class="material-icons"><?= $item['icon'] ?></i> <?= $e($item['title']) ?>
            </a>
            <?= $item['subitems'] ? $v->fetch('partials/navigation', ['items' => $item['subitems']]) : '' ?>
        </li>
    <?php endforeach; ?>
</ul>
