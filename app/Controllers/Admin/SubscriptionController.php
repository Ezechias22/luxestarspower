<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\SubscriptionRepository;
use App\Database;

class SubscriptionController {
    private $auth;
    private $subscriptionRepo;
    private $db;

    public function __construct() {
        $this->auth = new AuthService();
        $this->subscriptionRepo = new SubscriptionRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Liste de tous les abonnements
     */
    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit - Administrateur uniquement');
        }

        // Filtres
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';

        // Query de base
        $query = "SELECT 
            us.*,
            u.name as user_name,
            u.email as user_email,
            sp.name as plan_name,
            sp.slug as plan_slug,
            sp.price as plan_price,
            sp.billing_period
        FROM user_subscriptions us
        JOIN users u ON us.user_id = u.id
        JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE 1=1";

        $params = [];

        // Filtre par status
        if ($status !== 'all') {
            $query .= " AND us.status = ?";
            $params[] = $status;
        }

        // Recherche par nom/email
        if (!empty($search)) {
            $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY us.created_at DESC";

        $subscriptions = $this->db->fetchAll($query, $params);

        // Statistiques
        $stats = $this->getStats();

        view('admin/subscriptions/index', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'stats' => $stats,
            'currentStatus' => $status,
            'searchQuery' => $search
        ]);
    }

    /**
     * Détails d'un abonnement
     */
    public function show($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "ID d'abonnement invalide.";
            redirect('/admin/abonnements');
            return;
        }

        // Récupère l'abonnement avec détails
        $subscription = $this->db->fetchOne("
            SELECT 
                us.*,
                u.id as user_id,
                u.name as user_name,
                u.email as user_email,
                u.role as user_role,
                sp.name as plan_name,
                sp.slug as plan_slug,
                sp.price as plan_price,
                sp.billing_period,
                sp.max_products,
                sp.commission_rate
            FROM user_subscriptions us
            JOIN users u ON us.user_id = u.id
            JOIN subscription_plans sp ON us.plan_id = sp.id
            WHERE us.id = ?
        ", [$id]);

        if (!$subscription) {
            $_SESSION['error'] = "Abonnement introuvable.";
            redirect('/admin/abonnements');
            return;
        }

        // Récupère l'historique des paiements
        $payments = $this->db->fetchAll("
            SELECT * FROM subscription_payments
            WHERE subscription_id = ?
            ORDER BY created_at DESC
        ", [$id]);

        // Récupère tous les plans disponibles
        $plans = $this->subscriptionRepo->getActivePlans();

        // Compte les produits du vendeur
        $productsCount = $this->db->fetchOne("
            SELECT COUNT(*) as count FROM products 
            WHERE seller_id = ? AND deleted_at IS NULL
        ", [$subscription['user_id']]);

        view('admin/subscriptions/show', [
            'user' => $user,
            'subscription' => $subscription,
            'payments' => $payments,
            'plans' => $plans,
            'productsCount' => $productsCount['count'] ?? 0
        ]);
    }

    /**
     * Changer le plan d'un utilisateur (SANS PAIEMENT - Admin override)
     */
    public function changePlan() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $subscriptionId = $_POST['subscription_id'] ?? null;
        $newPlanId = $_POST['new_plan_id'] ?? null;

        if (!$subscriptionId || !$newPlanId) {
            $_SESSION['error'] = "Données manquantes.";
            redirect('/admin/abonnements');
            return;
        }

        try {
            // Récupère l'abonnement
            $subscription = $this->db->fetchOne(
                "SELECT * FROM user_subscriptions WHERE id = ?",
                [$subscriptionId]
            );

            if (!$subscription) {
                throw new \Exception("Abonnement introuvable.");
            }

            // Récupère le nouveau plan
            $newPlan = $this->db->fetchOne(
                "SELECT * FROM subscription_plans WHERE id = ?",
                [$newPlanId]
            );

            if (!$newPlan) {
                throw new \Exception("Plan introuvable.");
            }

            // Met à jour l'abonnement
            $this->db->query("
                UPDATE user_subscriptions 
                SET plan_id = ?, 
                    status = 'active',
                    cancel_at_period_end = 0
                WHERE id = ?
            ", [$newPlanId, $subscriptionId]);

            $_SESSION['success'] = "✅ Plan changé avec succès vers : " . $newPlan['name'];

        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }

        redirect('/admin/abonnements/' . $subscriptionId);
    }

    /**
     * Prolonger la période d'abonnement
     */
    public function extend() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $subscriptionId = $_POST['subscription_id'] ?? null;
        $days = intval($_POST['days'] ?? 0);

        if (!$subscriptionId || $days <= 0) {
            $_SESSION['error'] = "Données invalides.";
            redirect('/admin/abonnements');
            return;
        }

        try {
            $subscription = $this->db->fetchOne(
                "SELECT * FROM user_subscriptions WHERE id = ?",
                [$subscriptionId]
            );

            if (!$subscription) {
                throw new \Exception("Abonnement introuvable.");
            }

            // Prolonge la période
            $currentEnd = $subscription['current_period_end'];
            $newEnd = date('Y-m-d H:i:s', strtotime($currentEnd . " +$days days"));

            $this->db->query("
                UPDATE user_subscriptions 
                SET current_period_end = ?
                WHERE id = ?
            ", [$newEnd, $subscriptionId]);

            // Si c'est un essai, prolonge aussi trial_ends_at
            if ($subscription['status'] === 'trial') {
                $currentTrialEnd = $subscription['trial_ends_at'];
                $newTrialEnd = date('Y-m-d H:i:s', strtotime($currentTrialEnd . " +$days days"));
                
                $this->db->query("
                    UPDATE user_subscriptions 
                    SET trial_ends_at = ?
                    WHERE id = ?
                ", [$newTrialEnd, $subscriptionId]);
            }

            $_SESSION['success'] = "✅ Période prolongée de $days jours avec succès !";

        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }

        redirect('/admin/abonnements/' . $subscriptionId);
    }

    /**
     * Annuler un abonnement
     */
    public function cancel() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $subscriptionId = $_POST['subscription_id'] ?? null;

        if (!$subscriptionId) {
            $_SESSION['error'] = "ID d'abonnement manquant.";
            redirect('/admin/abonnements');
            return;
        }

        try {
            $this->db->query("
                UPDATE user_subscriptions 
                SET status = 'cancelled', 
                    cancelled_at = NOW()
                WHERE id = ?
            ", [$subscriptionId]);

            $_SESSION['success'] = "✅ Abonnement annulé avec succès.";

        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }

        redirect('/admin/abonnements/' . $subscriptionId);
    }

    /**
     * Réactiver un abonnement annulé
     */
    public function reactivate() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $subscriptionId = $_POST['subscription_id'] ?? null;

        if (!$subscriptionId) {
            $_SESSION['error'] = "ID d'abonnement manquant.";
            redirect('/admin/abonnements');
            return;
        }

        try {
            $this->db->query("
                UPDATE user_subscriptions 
                SET status = 'active', 
                    cancelled_at = NULL,
                    cancel_at_period_end = 0
                WHERE id = ?
            ", [$subscriptionId]);

            $_SESSION['success'] = "✅ Abonnement réactivé avec succès.";

        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }

        redirect('/admin/abonnements/' . $subscriptionId);
    }

    /**
     * Page des statistiques
     */
    public function stats() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $stats = $this->getStats();

        // Revenus par mois (6 derniers mois)
        $monthlyRevenue = $this->db->fetchAll("
            SELECT 
                DATE_FORMAT(paid_at, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as payments
            FROM subscription_payments
            WHERE status = 'succeeded'
                AND paid_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month DESC
        ");

        view('admin/subscriptions/stats', [
            'user' => $user,
            'stats' => $stats,
            'monthlyRevenue' => $monthlyRevenue
        ]);
    }

    /**
     * Récupère les statistiques globales
     */
    private function getStats() {
        $total = $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions");
        $active = $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'active'");
        $trial = $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'trial'");
        $cancelled = $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'cancelled'");

        // Revenus total
        $revenue = $this->db->fetchOne("
            SELECT SUM(amount) as total 
            FROM subscription_payments 
            WHERE status = 'succeeded'
        ");

        // Revenus ce mois
        $monthRevenue = $this->db->fetchOne("
            SELECT SUM(amount) as total 
            FROM subscription_payments 
            WHERE status = 'succeeded'
                AND MONTH(paid_at) = MONTH(NOW())
                AND YEAR(paid_at) = YEAR(NOW())
        ");

        // Taux de conversion (trial -> payant)
        $conversionRate = 0;
        if ($trial['count'] > 0) {
            $converted = $this->db->fetchOne("
                SELECT COUNT(*) as count 
                FROM user_subscriptions 
                WHERE status = 'active' 
                    AND stripe_subscription_id IS NOT NULL
            ");
            $conversionRate = ($converted['count'] / $trial['count']) * 100;
        }

        return [
            'total' => $total['count'] ?? 0,
            'active' => $active['count'] ?? 0,
            'trial' => $trial['count'] ?? 0,
            'cancelled' => $cancelled['count'] ?? 0,
            'total_revenue' => $revenue['total'] ?? 0,
            'month_revenue' => $monthRevenue['total'] ?? 0,
            'conversion_rate' => round($conversionRate, 1)
        ];
    }
}