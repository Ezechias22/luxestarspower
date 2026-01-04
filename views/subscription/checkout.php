<?php ob_start(); ?>

<style>
.checkout-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
}

.plan-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.plan-summary h2 {
    margin: 0 0 10px;
    font-size: 2rem;
}

.plan-summary .price {
    font-size: 3rem;
    font-weight: bold;
    margin: 10px 0;
}

.payment-form {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

#card-element {
    border: 2px solid #e0e0e0;
    padding: 15px;
    border-radius: 8px;
    background: #f9f9f9;
}

.submit-btn {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.3s;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error-message {
    background: #fee;
    color: #c00;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.features-list li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.features-list li::before {
    content: "✓";
    font-weight: bold;
    font-size: 1.2rem;
}
</style>

<div class="checkout-container">
    <h1 style="text-align: center; margin-bottom: 40px;">💳 Finaliser votre abonnement</h1>
    
    <!-- Résumé du plan -->
    <div class="plan-summary">
        <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
        <div class="price">
            $<?php echo number_format($plan['price'], 2); ?>
            <span style="font-size: 1rem;">/<?php echo $plan['billing_period'] === 'monthly' ? 'mois' : 'an'; ?></span>
        </div>
        
        <?php 
        $features = json_decode($plan['features'], true);
        if ($features): 
        ?>
        <ul class="features-list">
            <?php foreach ($features as $feature): ?>
                <li><?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    
    <!-- Formulaire de paiement -->
    <div class="payment-form">
        <h3 style="margin-bottom: 20px;">Informations de paiement</h3>
        
        <div id="error-message" class="error-message"></div>
        
        <form id="payment-form">
            <div class="form-group">
                <label>Carte bancaire</label>
                <div id="card-element"></div>
            </div>
            
            <button type="submit" id="submit-btn" class="submit-btn">
                🔒 Payer $<?php echo number_format($plan['price'], 2); ?>
            </button>
        </form>
        
        <p style="text-align: center; color: #999; font-size: 0.9rem; margin-top: 20px;">
            🔒 Paiement 100% sécurisé par Stripe
        </p>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
// Initialise Stripe
const stripe = Stripe('<?php echo $stripePublicKey; ?>');
const elements = stripe.elements();

// Crée l'élément de carte
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#32325d',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            '::placeholder': {
                color: '#aab7c4'
            }
        }
    }
});

cardElement.mount('#card-element');

// Gère la soumission du formulaire
const form = document.getElementById('payment-form');
const submitBtn = document.getElementById('submit-btn');
const errorDiv = document.getElementById('error-message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Traitement en cours...';
    errorDiv.style.display = 'none';
    
    try {
        // Crée la méthode de paiement
        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });
        
        if (error) {
            throw new Error(error.message);
        }
        
        // Envoie au serveur
        const response = await fetch('/abonnement/paiement', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                plan_id: '<?php echo $plan['id']; ?>',
                payment_method_id: paymentMethod.id
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = result.redirect || '/vendeur/tableau-de-bord';
        } else {
            throw new Error(result.error || 'Erreur lors du paiement');
        }
        
    } catch (err) {
        errorDiv.textContent = err.message;
        errorDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = '🔒 Payer $<?php echo number_format($plan['price'], 2); ?>';
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>