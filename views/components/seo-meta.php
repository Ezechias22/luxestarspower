<?php
// Valeurs par défaut
$defaults = [
    'title' => 'Luxe Stars Power - Marketplace Premium de Produits Numériques',
    'description' => 'Achetez et vendez des ebooks, formations, vidéos et produits numériques de qualité. Marketplace sécurisée avec paiement instantané.',
    'keywords' => 'marketplace, produits numériques, ebooks, formations en ligne, vidéos, acheter en ligne, vendre en ligne',
    'image' => 'https://luxestarspower.com/assets/images/og-image.jpg',
    'url' => 'https://luxestarspower.com' . $_SERVER['REQUEST_URI'],
    'type' => 'website',
    'locale' => \App\I18n::getLocale() ?? 'fr',
    'siteName' => 'Luxe Stars Power'
];

// Fusionne avec les valeurs fournies
$seo = array_merge($defaults, $seo ?? []);

// Génère le titre complet
$fullTitle = $seo['title'];
if (!str_contains($fullTitle, 'Luxe Stars Power')) {
    $fullTitle .= ' | Luxe Stars Power';
}
?>

<!-- Primary Meta Tags -->
<title><?php echo htmlspecialchars($fullTitle); ?></title>
<meta name="title" content="<?php echo htmlspecialchars($fullTitle); ?>">
<meta name="description" content="<?php echo htmlspecialchars($seo['description']); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($seo['keywords']); ?>">
<meta name="author" content="Luxe Stars Power">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo htmlspecialchars($seo['url']); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo htmlspecialchars($seo['type']); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($seo['url']); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($fullTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seo['description']); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($seo['image']); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($seo['siteName']); ?>">
<meta property="og:locale" content="<?php echo htmlspecialchars($seo['locale']); ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo htmlspecialchars($seo['url']); ?>">
<meta name="twitter:title" content="<?php echo htmlspecialchars($fullTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($seo['description']); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($seo['image']); ?>">

<!-- Additional SEO -->
<meta name="language" content="<?php echo htmlspecialchars(\App\I18n::getLocale() ?? 'fr'); ?>">
<meta name="revisit-after" content="7 days">
<meta name="theme-color" content="#667eea">

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Luxe Stars Power",
  "url": "https://luxestarspower.com",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://luxestarspower.com/produits?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

<?php if (isset($seo['product'])): ?>
<!-- Product Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "<?php echo htmlspecialchars($seo['product']['name']); ?>",
  "description": "<?php echo htmlspecialchars($seo['product']['description']); ?>",
  "image": "<?php echo htmlspecialchars($seo['product']['image']); ?>",
  "offers": {
    "@type": "Offer",
    "price": "<?php echo $seo['product']['price']; ?>",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock",
    "url": "<?php echo htmlspecialchars($seo['url']); ?>"
  }
  <?php if (isset($seo['product']['seller'])): ?>
  ,
  "brand": {
    "@type": "Brand",
    "name": "<?php echo htmlspecialchars($seo['product']['seller']); ?>"
  }
  <?php endif; ?>
}
</script>
<?php endif; ?>

<?php if (isset($seo['shop'])): ?>
<!-- Shop/Organization Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Store",
  "name": "<?php echo htmlspecialchars($seo['shop']['name']); ?>",
  "description": "<?php echo htmlspecialchars($seo['shop']['description']); ?>",
  "url": "<?php echo htmlspecialchars($seo['url']); ?>",
  "image": "<?php echo htmlspecialchars($seo['shop']['logo'] ?? $seo['image']); ?>"
}
</script>
<?php endif; ?>