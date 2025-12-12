<?php ob_start(); ?>

<div class="admin-users">
    <div class="container">
        <h1>Gestion des utilisateurs</h1>
        
        <div class="filters">
            <form method="GET" action="/admin/users" class="filter-form">
                <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Status</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><span class="badge badge-<?= $user->role ?>"><?= $user->role ?></span></td>
                    <td>
                        <?php if($user->is_active): ?>
                            <span class="badge badge-active">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($user->created_at)) ?></td>
                    <td>
                        <a href="/admin/user/<?= $user->id ?>/edit" class="btn btn-sm">Modifier</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.filters { margin: 2rem 0; }
.filter-form { display: flex; gap: 1rem; }
.filter-form input { flex: 1; max-width: 400px; }
.badge-buyer { background: #dbeafe; color: #1e40af; }
.badge-seller { background: #fef3c7; color: #92400e; }
.badge-admin { background: #fce7f3; color: #9f1239; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
