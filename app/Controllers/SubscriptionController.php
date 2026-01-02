<?php
namespace App\Controllers;

use App\Repositories\SubscriptionRepository;

class SubscriptionController {
    private $subscriptionRepo;
    
    public function __construct() {
        $this->subscriptionRepo = new SubscriptionRepository();
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
                'description' => 'Choisissez le plan qui vous convient : Essai gratuit 14 jours, Plan Mensuel à $19.99/mois ou Plan Annuel à $199/an. Commencez à vendre vos produits numériques dès aujourd\'hui !',
                'keywords' => 'tarifs, prix, abonnement, essai gratuit, plan mensuel, plan annuel, marketplace',
                'url' => 'https://luxestarspower.com/tarifs'
            ]
        ]);
    }
}