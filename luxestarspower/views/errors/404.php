<?php ob_start(); ?>

<div class="error-page">
    <div class="container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>La page que vous recherchez n'existe pas.</p>
        <a href="/" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</div>

<style>
.error-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.error-page h1 {
    font-size: 6rem;
    color: var(--primary);
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
