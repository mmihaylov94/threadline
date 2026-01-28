<?php

// show 2 pages on each side of current (2 + current + 2 = 5)
$pager->setSurroundCount(2);

$currentPage = $pager->getCurrentPageNumber();
$pageCount   = $pager->getPageCount();

// array of ['uri' => string, 'title' => int, 'active' => bool]
$links = $pager->links();

if ($pageCount <= 1) {
    return;
}
?>

<nav aria-label="Thread pagination">
    <ul class="pagination forum-pagination-custom">

        <!-- Previous page -->
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasPreviousPage()): ?>
                <a class="page-link" href="<?= esc($pager->getPreviousPage()) ?>" aria-label="Previous page">
                    <span aria-hidden="true">&lt;</span>
                </a>
            <?php else: ?>
                <span class="page-link">
                    <span aria-hidden="true">&lt;</span>
                </span>
            <?php endif; ?>
        </li>

        <!-- Page numbers -->
        <?php foreach ($links as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= esc($link['uri']) ?>">
                    <?= esc($link['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>

        <!-- Next page -->
        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasNextPage()): ?>
                <a class="page-link" href="<?= esc($pager->getNextPage()) ?>" aria-label="Next page">
                    <span aria-hidden="true">&gt;</span>
                </a>
            <?php else: ?>
                <span class="page-link">
                    <span aria-hidden="true">&gt;</span>
                </span>
            <?php endif; ?>
        </li>

    </ul>
</nav>
