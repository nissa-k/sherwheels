<?php
$pageTitle = 'SherWheels Festival — Accueil';
include __DIR__ . '/includes/header.php';
?>

<section class="hero hero--home" aria-label="Bannière principale">
  <div class="hero-overlay" aria-hidden="true"></div>

  <div class="container hero-inner">
    <h1 class="hero-title">
      Faites le plein de connaissances autour de votre passion auto / moto
    </h1>

    <div class="hero-actions" role="group" aria-label="Actions principales">
      <a class="btn" href="map.php">Comment venir ?</a>
      <a class="btn" href="billetterie.php" target="_blank" rel="noopener noreferrer">Je prends mes billets</a>
      <a class="btn" href="programme.php" target="_blank" rel="noopener noreferrer">Le programme</a>
    </div>
  </div>

  <a class="floating-ticket" href="billetterie.php" target="_blank" rel="noopener noreferrer" aria-label="Prends ton billet (ouvre la billetterie)">
    Prends<br>ton<br>billet !
  </a>
</section>

<section class="countdown-band" aria-label="Compte à rebours">
  <div class="container countdown-band-inner">
    <h2 class="countdown-title">Rendez-vous dans</h2>

    <div class="countdown-box" data-countdown data-date="2027-05-08T00:00:00">
      <div class="cd-item" aria-label="Jours">
        <span data-days>0</span><small>J</small>
      </div>
      <div class="cd-item" aria-label="Heures">
        <span data-hours>0</span><small>H</small>
      </div>
      <div class="cd-item" aria-label="Minutes">
        <span data-minutes>0</span><small>M</small>
      </div>
      <div class="cd-item" aria-label="Secondes">
        <span data-seconds>0</span><small>S</small>
      </div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="invite-title">
  <div class="container">
    <div class="section-head">
      <h2 id="invite-title">Intervenants</h2>
    </div>

    <div class="carousel" data-carousel>
      <div class="carousel-track" data-carousel-track style="overflow-x:auto; scroll-behavior:smooth;">
        <!-- Salim -->
        <article class="guest-card" style="width:500px;">
          <img class="guest-thumb"
                src="/assets/img/intervenants/salim.png"
                alt="Salim Karrouache"
                style="object-position: center 20%;">

          <div class="guest-meta">
            <p class="guest-name">Salim KARROUACHE</p>

            <p class="guest-role muted">
              Champion de drift 2025 à la Viking Cup
            </p>

            <p class="guest-description">
              Pilote reconnu dans l’univers du drift, Salim partagera son expérience en compétition.
            </p>
          </div>
        </article>

        <!-- Franck -->
        <article class="guest-card" style="width:500px;">
          <img class="guest-thumb"
               src="/assets/img/intervenants/franck.png"
               alt="Franck Galiègue">

          <div class="guest-meta">
            <p class="guest-name">Franck GALIÈGUE</p>

            <p class="guest-role muted">
              Directeur du musée Movie Cars Central
            </p>

            <p class="guest-description">
              Passionné de cinéma et d’automobile, Franck présentera des véhicules cultes.
            </p>
          </div>
        </article>

        <!-- Noémie -->
        <article class="guest-card">
          <img class="guest-thumb"
               src="/assets/img/intervenants/noemie.png"
               alt="Noémie Marmorat">

          <div class="guest-meta">
            <p class="guest-name">Noémie MARMORAT</p>

            <p class="guest-role muted">
              Illustratrice automobile & moto
            </p>

            <p class="guest-description">
              Artiste spécialisée dans l’illustration auto/moto sur mesure.
            </p>
          </div>
        </article>

        <!-- Mederic -->
        <article class="guest-card">
          <img class="guest-thumb"
               src="/assets/img/intervenants/mederic.png"
               alt="Mederic Isker">

          <div class="guest-meta">
            <p class="guest-name">Mederic ISKER</p>

            <p class="guest-role muted">
              Directeur de Shiftech
            </p>

            <p class="guest-description">
              Expert en préparation moteur et performance automobile.
            </p>
          </div>
        </article>

      </div>

      <div class="carousel-controls" aria-label="Contrôles du carrousel">
        <button class="icon-btn" type="button" data-carousel-prev aria-label="Précédent">‹</button>
        <button class="icon-btn" type="button" data-carousel-next aria-label="Suivant">›</button>
      </div>
    </div>
  </div>
</section>

<section class="banner" aria-label="Infos festival">
  <div class="container banner-inner">
    <p class="banner-eyebrow">SherWheels Festival 2027</p>
    <p class="banner-date">08/05/2027</p>

    <dl class="stats" aria-label="Chiffres clés">
      <div class="stat">
        <dt>Voitures</dt>
        <dd>200+</dd>
      </div>
      <div class="stat">
        <dt>Stands pro</dt>
        <dd>20+</dd>
      </div>
      <div class="stat">
        <dt>Visiteurs</dt>
        <dd>2000</dd>
      </div>
    </dl>
  </div>
</section>
<section class="explication" aria-labelledby="explanation-inFest">
  <div class="container">
    <div class="section-head">
      
      <h2 id="explanation-title">Ateliers</h2>
      <p class="muted">Participez à nos ateliers interactifs et immersifs,
         conçus pour apprendre par la pratique. 
         Encadrés par des professionnels passionnés, 
         ces ateliers participatifs favorisent l’échange, 
         l’expérimentation et le développement de nouvelles compétences dans une ambiance conviviale. 
      </p>
      <h2 id="explanation-title">stands pro</h2>
      <p class="muted">Découvrez nos stands professionnels et échangez directement
         avec des experts et acteurs du secteur.
          Une occasion privilégiée de rencontrer des professionnels,
          de découvrir leurs services,
          leurs innovations et de créer de nouvelles opportunités de collaboration. 
      </p>
      <h2 id="explanation-title">masterclass guest</h2>
        <p class="muted">Venez rencontrer nos invités lors de masterclass
         exclusives et profitez de leur expérience et de leur expertise dans leurs domaines
         respectifs. Des personnalités reconnues comme Julien Fébreau, 
         Fabio Quartararo et bien d’autres partagent leur savoir,
          leurs parcours et leurs conseils lors de moments uniques d’échange et d’inspiration.
      </p>
    </div>
  </div>
</section>
  <script>
  document.addEventListener('DOMContentLoaded', () => {

    const track = document.querySelector('[data-carousel-track]');
    const prev = document.querySelector('[data-carousel-prev]');
    const next = document.querySelector('[data-carousel-next]');

    if(track && prev && next){

      next.addEventListener('click', () => {
        track.scrollBy({
          left: 350,
          behavior: 'smooth'
        });
      });

      prev.addEventListener('click', () => {
        track.scrollBy({
          left: -350,
          behavior: 'smooth'
        });
      });

    }

  });
  </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
