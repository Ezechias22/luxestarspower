<?php ob_start(); ?>

<style>
.pricing-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
}

.pricing-hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
}

.pricing-hero p {
    font-size: 1.3rem;
    opacity: 0.9;
}

.pricing-container {
    max-width: 1200px;
    margin: -50px auto 80px;
    padding: 0 20px;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.pricing-card {
    background: white;
    border-radius: 15px;
    padding: 40px 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.pricing-card.popular {
    border: 3px solid #667eea;
    transform: scale(1.05);
}

.pricing-card.popular::before {
    content: "⭐ POPULAIRE";
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: #667eea;
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.85rem;
}

.plan-name {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.plan-price {
    font-size: 3rem;
    font-weight: bold;
    color: #667eea;
    margin: 20px 0;
}

.plan-price .currency {
    font-size: 1.5rem;
    vertical-align: super;
}

.plan-price .period {
    font-size: 1rem;
    color: #999;
    font-weight: normal;
}

.plan-savings {
    background: #d4edda;
    color: #155724;
    padding: 8px 15px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: inline-block;
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 30px 0;
}

.plan-features li {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.plan-features li:last-child {
    border-bottom: none;
}

.plan-features .check {
    color: #28a745;
    font-weight: bold;
}

.plan-features .cross {
    color: #dc3545;
    font-weight: bold;
}

.plan-cta {
    display: block;
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1rem;
    transition: transform 0.3s, box-shadow 0.3s;
}

.plan-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.plan-cta.secondary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.faq-section {
    max-width: 800px;
    margin: 80px auto;
    padding: 0 20px;
}

.faq-item {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.faq-question {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.faq-answer {
    color: #666;
    line-height: 1.6;
}
</style>

<div class="pricing-hero">
    <h1>💎 Choisissez Votre Plan</h1>
    <p>Commencez gratuitement, passez à la vitesse supérieure quand vous êtes prêt</p>
</div>

<div class="pricing-container">
    <div class="pricing-grid">
        
        <!-- PLAN GRATUIT -->
        <div class="pricing-card">
            <div class="plan-name">🆓 Essai Gratuit</div>
            <div class="plan-price">
                <span class="currency">$</span>0
                <span class="period">/14 jours</span>
            </div>
            
            <ul class="plan-features">
                <li><span class="check">✓</span> Boutique personnalisée</li>
                <li><span class="check">✓</span> 3 produits maximum</li>
                <li><span class="check">✓</span> Commission 15%</li>
                <li><span class="check">✓</span> Support email</li>
                <li><span class="cross">✗</span> Badge Premium</li>
                <li><span class="cross">✗</span> Mise en avant</li>
            </ul>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/abonnement/essai" class="plan-cta secondary">Commencer l'essai gratuit</a>
            <?php else: ?>
                <a href="/inscription" class="plan-cta secondary">Créer un compte</a>
            <?php endif; ?>
        </div>
        
        <!-- PLAN MENSUEL -->
        <div class="pricing-card popular">
            <div class="plan-name">💎 Plan Mensuel</div>
            <div class="plan-price">
                <span class="currency">$</span>19.99
                <span class="period">/mois</span>
            </div>
            
            <ul class="plan-features">
                <li><span class="check">✓</span> Produits illimités</li>
                <li><span class="check">✓</span> Badge Vendeur Premium</li>
                <li><span class="check">✓</span> Commission 10%</li>
                <li><span class="check">✓</span> Mise en avant</li>
                <li><span class="check">✓</span> Statistiques avancées</li>
                <li><span class="check">✓</span> Support prioritaire</li>
            </ul>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/abonnement/paiement/monthly" class="plan-cta">Choisir ce plan</a>
            <?php else: ?>
                <a href="/inscription" class="plan-cta">Créer un compte</a>
            <?php endif; ?>
        </div>
        
        <!-- PLAN ANNUEL -->
        <div class="pricing-card">
            <div class="plan-name">🔥 Plan Annuel</div>
            <div class="plan-savings">💰 Économisez $40/an !</div>
            <div class="plan-price">
                <span class="currency">$</span>199
                <span class="period">/an</span>
            </div>
            
            <ul class="plan-features">
                <li><span class="check">✓</span> Tout du plan mensuel</li>
                <li><span class="check">✓</span> Badge Vendeur Elite</li>
                <li><span class="check">✓</span> Commission 5% seulement!</li>
                <li><span class="check">✓</span> Produit en vedette 1x/mois</li>
                <li><span class="check">✓</span> Formation marketing gratuite</li>
                <li><span class="check">✓</span> Support VIP 24/7</li>
            </ul>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/abonnement/paiement/yearly" class="plan-cta">Choisir ce plan</a>
            <?php else: ?>
                <a href="/inscription" class="plan-cta">Créer un compte</a>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<!-- FAQ -->
<div class="faq-section">
    <h2 style="text-align: center; margin-bottom: 40px; font-size: 2.5rem;">❓ Questions Fréquentes</h2>
    
    <div class="faq-item">
        <div class="faq-question">Comment fonctionne l'essai gratuit ?</div>
        <div class="faq-answer">Vous pouvez tester la plateforme pendant 14 jours gratuitement avec jusqu'à 3 produits. Aucune carte bancaire requise !</div>
    </div>
    
    <div class="faq-item">
        <div class="faq-question">Puis-je annuler à tout moment ?</div>
        <div class="faq-answer">Oui ! Vous pouvez annuler votre abonnement à tout moment. Vous continuerez à avoir accès jusqu'à la fin de votre période payée.</div>
    </div>
    
    <div class="faq-item">
        <div class="faq-question">Comment fonctionne la commission ?</div>
        <div class="faq-answer">Nous prenons un pourcentage sur chaque vente : 15% (essai), 10% (mensuel), ou 5% (annuel). Le reste vous revient à 100% !</div>
    </div>
    
    <div class="faq-item">
        <div class="faq-question">Puis-je changer de plan plus tard ?</div>
        <div class="faq-answer">Absolument ! Vous pouvez upgrader ou downgrader votre plan à tout moment depuis votre tableau de bord vendeur.</div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>