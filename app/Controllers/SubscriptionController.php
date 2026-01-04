<?php
namespace App\Controllers;

use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Database;

class SubscriptionController {
    private $subscriptionRepo;
    private $userRepo;
    private $db;
    
    public function __construct() {
        $this->subscriptionRepo = new SubscriptionRepository();
        $this->userRepo = new UserRepository();
        $this->db = Database::getInstance();
    }
    
    /**
     * Page tarifs (publique)
     */
    public function pricing() {
        $plans = $this->subscriptionRepo->getActivePlans();
        
        view('front/pricing', [
            'plans' => $plans,
            'title' => 'Nos Tarifs - Luxe Stars Power',
            'seo' => [
                'title' => 'Tarifs et Plans d\'Abonnement - Luxe Stars Power',
                'description' => 'Choisissez le plan qui vous convient : Essai gratuit 14 jours, Plan Mensuel à $19.99/mois ou Plan Annuel à $199/an.',
                'keywords' => 'tarifs, prix, abonnement, essai gratuit, plan mensuel, plan annuel, marketplace',
                'url' => 'https://luxestarspower.com/tarifs'
            ]
        ]);
    }
    
    /**
     * Abonnement actuel de l'utilisateur
     */
    public function current() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
            return;
        }
        
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        $plans = $this->subscriptionRepo->getActivePlans();
        
        view('subscription/current', [
            'subscription' => $subscription,
            'plans' => $plans,
            'title' => 'Mon Abonnement'
        ]);
    }
    
    /**
     * Démarrer l'essai gratuit
     */
    public function startTrial() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour commencer l'essai gratuit.";
            redirect('/connexion');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Vérifie si l'utilisateur a déjà un abonnement
        $existingSubscription = $this->subscriptionRepo->getUserActiveSubscription($userId);
        
        if ($existingSubscription) {
            $_SESSION['error'] = "Vous avez déjà un abonnement actif.";
            redirect('/abonnement');
            return;
        }
        
        try {
            // Crée l'abonnement essai gratuit
            $subscriptionId = $this->subscriptionRepo->createTrialSubscription($userId);
            
            // Met à jour le rôle de l'utilisateur en seller s'il ne l'est pas déjà
            $user = $this->userRepo->find($userId);
            if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
                $this->db->query(
                    "UPDATE users SET role = 'seller' WHERE id = ?",
                    [$userId]
                );
                $_SESSION['user_role'] = 'seller';
            }
            
            $_SESSION['success'] = "🎉 Votre essai gratuit de 14 jours a commencé ! Vous pouvez maintenant ajouter jusqu'à 3 produits.";
            redirect('/vendeur/tableau-de-bord');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la création de l'abonnement : " . $e->getMessage();
            redirect('/tarifs');
        }
    }
    
    /**
     * Page de checkout pour un plan payant
     */
    public function checkout($params) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour vous abonner.";
            redirect('/connexion');
            return;
        }
        
        $planSlug = $params['plan'] ?? null;
        
        if (!$planSlug) {
            redirect('/tarifs');
            return;
        }
        
        $plan = $this->subscriptionRepo->getPlanBySlug($planSlug);
        
        if (!$plan) {
            $_SESSION['error'] = "Plan introuvable.";
            redirect('/tarifs');
            return;
        }
        
        // Vérifie si l'utilisateur a déjà un abonnement
        $currentSubscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        
        view('subscription/checkout', [
            'plan' => $plan,
            'currentSubscription' => $currentSubscription,
            'title' => 'Paiement - ' . $plan['name'],
            'stripePublicKey' => env('STRIPE_PUBLIC_KEY')
        ]);
    }
    
    /**
     * Traiter le paiement Stripe
     */
    public function processPayment() {
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'error' => 'Non authentifié'], 401);
        }
        
        $planId = $_POST['plan_id'] ?? null;
        $paymentMethodId = $_POST['payment_method_id'] ?? null;
        
        if (!$planId || !$paymentMethodId) {
            json_response(['success' => false, 'error' => 'Données manquantes'], 400);
        }
        
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            
            $userId = $_SESSION['user_id'];
            $user = $this->userRepo->find($userId);
            $plan = $this->db->fetchOne("SELECT * FROM subscription_plans WHERE id = ?", [$planId]);
            
            if (!$plan) {
                json_response(['success' => false, 'error' => 'Plan introuvable'], 404);
            }
            
            // Crée ou récupère le client Stripe
            $stripeCustomerId = $user['stripe_customer_id'] ?? null;
            
            if (!$stripeCustomerId) {
                $customer = \Stripe\Customer::create([
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'payment_method' => $paymentMethodId,
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId,
                    ],
                ]);
                
                $stripeCustomerId = $customer->id;
                
                // Sauvegarde l'ID client Stripe
                $this->db->query(
                    "UPDATE users SET stripe_customer_id = ? WHERE id = ?",
                    [$stripeCustomerId, $userId]
                );
            } else {
                // Attache la méthode de paiement au client existant
                \Stripe\PaymentMethod::retrieve($paymentMethodId)->attach([
                    'customer' => $stripeCustomerId,
                ]);
                
                \Stripe\Customer::update($stripeCustomerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId,
                    ],
                ]);
            }
            
            // Crée l'abonnement Stripe
            $priceId = $this->getOrCreateStripePriceId($plan);
            
            $subscription = \Stripe\Subscription::create([
                'customer' => $stripeCustomerId,
                'items' => [['price' => $priceId]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            
            // Crée l'abonnement dans notre base de données
            $periodEnd = date('Y-m-d H:i:s', $subscription->current_period_end);
            $periodStart = date('Y-m-d H:i:s', $subscription->current_period_start);
            
            $subscriptionId = $this->db->insert(
                "INSERT INTO user_subscriptions 
                 (user_id, plan_id, status, current_period_start, current_period_end, stripe_subscription_id, stripe_customer_id)
                 VALUES (?, ?, 'active', ?, ?, ?, ?)",
                [$userId, $planId, $periodStart, $periodEnd, $subscription->id, $stripeCustomerId]
            );
            
            // Met à jour l'utilisateur
            $this->db->query(
                "UPDATE users SET current_subscription_id = ?, role = 'seller' WHERE id = ?",
                [$subscriptionId, $userId]
            );
            
            $_SESSION['user_role'] = 'seller';
            
            json_response([
                'success' => true,
                'message' => 'Abonnement créé avec succès !',
                'redirect' => '/vendeur/tableau-de-bord'
            ]);
            
        } catch (\Exception $e) {
            error_log("Stripe subscription error: " . $e->getMessage());
            json_response([
                'success' => false,
                'error' => 'Erreur lors du paiement : ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Annuler l'abonnement
     */
    public function cancel() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
            return;
        }
        
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        
        if (!$subscription) {
            $_SESSION['error'] = "Aucun abonnement actif trouvé.";
            redirect('/abonnement');
            return;
        }
        
        try {
            // Annule à la fin de la période
            $this->subscriptionRepo->cancelAtPeriodEnd($subscription['id']);
            
            // Si c'est un abonnement Stripe, annule aussi sur Stripe
            if ($subscription['stripe_subscription_id']) {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                \Stripe\Subscription::update($subscription['stripe_subscription_id'], [
                    'cancel_at_period_end' => true
                ]);
            }
            
            $_SESSION['success'] = "Votre abonnement sera annulé à la fin de la période en cours.";
            redirect('/abonnement');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'annulation : " . $e->getMessage();
            redirect('/abonnement');
        }
    }
    
    /**
     * Réactiver un abonnement annulé
     */
    public function resume() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
            return;
        }
        
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        
        if (!$subscription) {
            $_SESSION['error'] = "Aucun abonnement trouvé.";
            redirect('/abonnement');
            return;
        }
        
        try {
            // Réactive l'abonnement
            $this->db->query(
                "UPDATE user_subscriptions SET cancel_at_period_end = 0, cancelled_at = NULL WHERE id = ?",
                [$subscription['id']]
            );
            
            // Si c'est un abonnement Stripe, réactive aussi sur Stripe
            if ($subscription['stripe_subscription_id']) {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                \Stripe\Subscription::update($subscription['stripe_subscription_id'], [
                    'cancel_at_period_end' => false
                ]);
            }
            
            $_SESSION['success'] = "Votre abonnement a été réactivé avec succès !";
            redirect('/abonnement');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la réactivation : " . $e->getMessage();
            redirect('/abonnement');
        }
    }
    
    /**
     * Changer de plan
     */
    public function change($params) {
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
            return;
        }
        
        $newPlanSlug = $params['plan'] ?? null;
        
        if (!$newPlanSlug) {
            redirect('/tarifs');
            return;
        }
        
        $newPlan = $this->subscriptionRepo->getPlanBySlug($newPlanSlug);
        $currentSubscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        
        if (!$newPlan || !$currentSubscription) {
            $_SESSION['error'] = "Impossible de changer de plan.";
            redirect('/abonnement');
            return;
        }
        
        try {
            if ($currentSubscription['stripe_subscription_id']) {
                // Changement via Stripe
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                
                $priceId = $this->getOrCreateStripePriceId($newPlan);
                
                $subscription = \Stripe\Subscription::retrieve($currentSubscription['stripe_subscription_id']);
                
                \Stripe\Subscription::update($currentSubscription['stripe_subscription_id'], [
                    'items' => [
                        [
                            'id' => $subscription->items->data[0]->id,
                            'price' => $priceId,
                        ],
                    ],
                    'proration_behavior' => 'create_prorations',
                ]);
            }
            
            // Met à jour dans notre base
            $this->db->query(
                "UPDATE user_subscriptions SET plan_id = ? WHERE id = ?",
                [$newPlan['id'], $currentSubscription['id']]
            );
            
            $_SESSION['success'] = "Votre plan a été changé avec succès vers : " . $newPlan['name'];
            redirect('/abonnement');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors du changement de plan : " . $e->getMessage();
            redirect('/abonnement');
        }
    }
    
    /**
     * Page abonnement vendeur (dans le dashboard)
     */
    public function sellerSubscription() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/connexion');
            return;
        }
        
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($_SESSION['user_id']);
        $plans = $this->subscriptionRepo->getActivePlans();
        
        view('seller/subscription', [
            'subscription' => $subscription,
            'plans' => $plans,
            'title' => 'Mon Abonnement'
        ]);
    }
    
    /**
     * Helper: Créer ou récupérer le Price ID Stripe pour un plan
     */
    private function getOrCreateStripePriceId($plan) {
        // Si tu as déjà créé les prix sur Stripe Dashboard, retourne l'ID
        // Sinon, crée-le dynamiquement
        
        if (!empty($plan['stripe_price_id'])) {
            return $plan['stripe_price_id'];
        }
        
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        
        // Crée le produit Stripe si nécessaire
        $product = \Stripe\Product::create([
            'name' => $plan['name'],
            'description' => 'Abonnement ' . $plan['name'] . ' - Luxe Stars Power',
        ]);
        
        // Crée le prix
        $interval = $plan['billing_period'] === 'monthly' ? 'month' : 'year';
        
        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => intval($plan['price'] * 100), // Stripe utilise les centimes
            'currency' => strtolower($plan['currency']),
            'recurring' => ['interval' => $interval],
        ]);
        
        // Sauvegarde le price ID dans la base (optionnel)
        $this->db->query(
            "UPDATE subscription_plans SET stripe_price_id = ?, stripe_product_id = ? WHERE id = ?",
            [$price->id, $product->id, $plan['id']]
        );
        
        return $price->id;
    }
}