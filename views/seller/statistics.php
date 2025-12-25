<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">📊 Statistiques</h1>

    <?php
    // Calcule les statistiques du vendeur
    $productRepo = new \App\Repositories\ProductRepository();
    $orderRepo = new \App\Repositories\OrderRepository();
    
    $sellerId = $user['id'];
    $products = $productRepo->getBySeller($sellerId);
    $activeProducts = count(array_filter($products, function($p) { return $p['is_active'] == 1; }));
    
    // Total des vues
    $totalViews = $productRepo->getTotalViewsBySeller($sellerId);
    
    // Statistiques du mois en cours
    $allOrders = $orderRepo->getAll();
    $currentMonth = date('Y-m');
    $salesThisMonth = 0;
    $revenueThisMonth = 0;
    
    // Données pour les graphiques (30 derniers jours)
    $salesByDay = array_fill(0, 30, 0);
    $revenueByDay = array_fill(0, 30, 0);
    $productSales = [];
    
    foreach ($allOrders as $order) {
        if ($order['payment_status'] === 'paid') {
            $items = $orderRepo->getOrderItems($order['id']);
            
            foreach ($items as $item) {
                if ($item['seller_id'] == $sellerId) {
                    $orderDate = date('Y-m', strtotime($order['created_at']));
                    $revenue = $item['price'] * $item['quantity'];
                    
                    // Stats du mois
                    if ($orderDate === $currentMonth) {
                        $salesThisMonth++;
                        $revenueThisMonth += $revenue;
                    }
                    
                    // Stats des 30 derniers jours
                    $daysAgo = floor((time() - strtotime($order['created_at'])) / 86400);
                    if ($daysAgo >= 0 && $daysAgo < 30) {
                        $dayIndex = 29 - $daysAgo;
                        $salesByDay[$dayIndex]++;
                        $revenueByDay[$dayIndex] += $revenue;
                    }
                    
                    // Top produits
                    $productId = $item['product_id'];
                    if (!isset($productSales[$productId])) {
                        $productSales[$productId] = [
                            'title' => $item['title'],
                            'sales' => 0,
                            'revenue' => 0
                        ];
                    }
                    $productSales[$productId]['sales']++;
                    $productSales[$productId]['revenue'] += $revenue;
                }
            }
        }
    }
    
    // Trie les produits par ventes
    uasort($productSales, function($a, $b) {
        return $b['sales'] - $a['sales'];
    });
    $topProducts = array_slice($productSales, 0, 5, true);
    ?>

    <!-- Cartes de statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $salesThisMonth; ?></h3>
            <p style="opacity: 0.9;">Ventes ce mois</p>
        </div>

        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;">$<?php echo number_format($revenueThisMonth, 2); ?></h3>
            <p style="opacity: 0.9;">Revenus ce mois</p>
        </div>

        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $activeProducts; ?></h3>
            <p style="opacity: 0.9;">Produits actifs</p>
        </div>

        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo number_format($totalViews); ?></h3>
            <p style="opacity: 0.9;">Total vues</p>
        </div>
    </div>

    <!-- Graphiques -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
        <!-- Graphique des ventes -->
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">Ventes des 30 derniers jours</h2>
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Graphique des revenus -->
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">Revenus des 30 derniers jours</h2>
            <canvas id="revenueChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Top produits -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 40px;">
        <h2 style="margin-bottom: 20px;">Top 5 Produits</h2>
        <?php if (!empty($topProducts)): ?>
            <canvas id="topProductsChart" style="max-height: 300px;"></canvas>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px 0;">Aucune vente pour le moment</p>
        <?php endif; ?>
    </div>

    <!-- Tableau détaillé -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;">Performance par produit</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th style="padding: 15px; text-align: left;">Produit</th>
                    <th style="padding: 15px; text-align: center;">Vues</th>
                    <th style="padding: 15px; text-align: center;">Ventes</th>
                    <th style="padding: 15px; text-align: center;">Taux conversion</th>
                    <th style="padding: 15px; text-align: right;">Revenus</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productSales)): ?>
                    <?php foreach($productSales as $productId => $stats): ?>
                    <?php
                    // Trouve le produit pour afficher ses vues
                    $prod = array_filter($products, function($p) use ($productId) {
                        return $p['id'] == $productId;
                    });
                    $prod = reset($prod);
                    $views = $prod ? ($prod['views'] ?? 0) : 0;
                    $conversionRate = $views > 0 ? ($stats['sales'] / $views * 100) : 0;
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><?php echo htmlspecialchars($stats['title']); ?></td>
                        <td style="padding: 15px; text-align: center;"><?php echo number_format($views); ?></td>
                        <td style="padding: 15px; text-align: center;"><?php echo $stats['sales']; ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <?php echo $views > 0 ? number_format($conversionRate, 1) . '%' : '-'; ?>
                        </td>
                        <td style="padding: 15px; text-align: right;">$<?php echo number_format($stats['revenue'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                            Aucune donnée disponible pour le moment
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';

    // Génère les labels des 30 derniers jours
    const last30Days = <?php echo json_encode(array_map(function($i) {
        $date = new DateTime();
        $date->modify('-' . (29 - $i) . ' days');
        return $date->format('d/m');
    }, range(0, 29))); ?>;

    // Données réelles des ventes
    const salesData = <?php echo json_encode($salesByDay); ?>;
    const revenueData = <?php echo json_encode($revenueByDay); ?>;

    // Graphique des ventes
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: last30Days,
            datasets: [{
                label: 'Ventes',
                data: salesData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Graphique des revenus
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: last30Days,
            datasets: [{
                label: 'Revenus ($)',
                data: revenueData,
                backgroundColor: 'rgba(240, 147, 251, 0.8)',
                borderColor: '#f093fb',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    <?php if (!empty($topProducts)): ?>
    // Graphique top produits
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($topProducts, 'title')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($topProducts, 'sales')); ?>,
                backgroundColor: [
                    '#667eea',
                    '#f093fb',
                    '#4facfe',
                    '#43e97b',
                    '#fa709a'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    <?php endif; ?>
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>