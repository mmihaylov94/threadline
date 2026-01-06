<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-8">

        <div class="card mb-3">
            <div class="card-body">
                <h1 class="h4 mb-2">Welcome to the Forum</h1>
                <p class="mb-0">
                    This is a sample view rendered using a shared layout.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-0 text-muted">
                    Layout rendering is working correctly.
                </p>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
