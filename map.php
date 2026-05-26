<?php
$pageTitle = 'Carte — SherWheels Festival';
include __DIR__ . '/includes/header.php';
?>

<section class="section" aria-labelledby="page-title">
  <div class="container">

    <h1 id="page-title">Carte</h1>

    <p class="muted">
      Retrouvez toutes les informations pour vous rendre au SherWheels Festival.
    </p>

    <p>
      L’aérodrome de Meaux - Esbly est un aérodrome civil,
      ouvert à la circulation aérienne publique,
      situé sur la commune d'Isles-lès-Villenoy, à 5 km au sud-ouest de Meaux,
      en Seine-et-Marne. Il est utilisé pour la pratique d’activités de loisirs et de tourisme.
    </p>

    <p class="muted">
      Adresse : Aérodrome de Meaux - Esbly, 77450 Isles-lès-Villenoy
    </p>

    <!-- IMAGES -->
    <div style="display: flex; flex-direction: column; gap: 20px; margin-top: 20px;">

      <!-- Ligne avec 2 images -->
      <div style="display: flex; gap: 20px; flex-wrap: wrap;">

        <img 
          src="../assets/img/lieu/meauxdezoom.png" 
          alt="Dezoom Meaux"
          style="width: 48%;"
        >

        <img 
          src="../assets/img/lieu/aerodrome.png" 
          alt="Plan de l'aérodrome"
          style="width: 48%;"
        >

      </div>
    </div>
  <p class="muted"> Des places de parking seront disponibles sur place, avec un accès facilité pour les personnes à mobilité réduite.</p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>