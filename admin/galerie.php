<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Galerie';
$msg = '';

// Suppression photo
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM galerie WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();

    if ($photo) {
        $filepath = __DIR__ . '/../assets/img/' . $photo['image'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $pdo->prepare("DELETE FROM galerie WHERE id = ?")->execute([$id]);
        $msg = ['type' => 'success', 'text' => 'Photo supprimée.'];
    }
}

// Upload photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $msg = ['type' => 'error', 'text' => 'Erreur lors de l\'upload.'];
    } elseif (!in_array($file['type'], $allowed)) {
        $msg = ['type' => 'error', 'text' => 'Format non autorisé (jpg, png, webp, gif).'];
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $msg = ['type' => 'error', 'text' => 'Fichier trop lourd (max 5Mo).'];
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'galerie_' . uniqid() . '.' . strtolower($ext);
        $dest = __DIR__ . '/../assets/img/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $pdo->prepare("INSERT INTO galerie (image) VALUES (?)")->execute([$filename]);
            $msg = ['type' => 'success', 'text' => 'Photo ajoutée avec succès.'];
        } else {
            $msg = ['type' => 'error', 'text' => 'Impossible de déplacer le fichier.'];
        }
    }
}

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();
$photos = $pdo->query("SELECT * FROM galerie ORDER BY created_at DESC")->fetchAll();

require_once __DIR__.'/layout.php';
?>

<?php if ($msg): ?>
  <div class="alert <?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Upload -->
<div class="section-title">Ajouter une photo</div>
<form method="POST" enctype="multipart/form-data" class="admin-form" style="margin-bottom:32px;">
  <label>Fichier image (jpg, png, webp — max 5Mo)</label>
  <input type="file" name="photo" accept="image/*" required>
  <button type="submit" class="btn-admin primary">Uploader</button>
</form>

<!-- Galerie -->
<div class="section-title">Photos publiées (<?= count($photos) ?>)</div>

<?php if (empty($photos)): ?>
  <p style="color:var(--admin-muted);">Aucune photo dans la galerie.</p>
<?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;">
    <?php foreach ($photos as $photo): ?>
    <div style="position:relative;background:var(--admin-surface);border:1px solid var(--admin-border);overflow:hidden;">
      <img src="/assets/img/<?= htmlspecialchars($photo['image']) ?>"
           style="width:100%;height:160px;object-fit:cover;display:block;">
      <div style="padding:8px;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:.7rem;color:var(--admin-muted);"><?= date('d/m/Y', strtotime($photo['created_at'])) ?></span>
        <a href="?delete=<?= $photo['id'] ?>"
           class="btn-admin danger"
           style="padding:4px 8px;font-size:.7rem;"
           onclick="return confirm('Supprimer cette photo ?')">🗑</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__.'/layout_end.php'; ?>
