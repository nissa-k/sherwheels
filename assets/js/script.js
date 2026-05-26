/*
  SherWheels Festival — scripts
  - Menu mobile accessible (ARIA, ESC, clic extérieur)
  - Compte à rebours configurable via data-date
  - Mini carrousel (scroll snap) pour la section "Invités"
*/

console.log('SherWheels Festival scripts loaded');

function $(selector, root = document){
  return root.querySelector(selector);
}

function $all(selector, root = document){
  return Array.from(root.querySelectorAll(selector));
}

document.addEventListener('DOMContentLoaded', () => {
  initMenu();
  initCountdown();
  initCarousel();
});

function initMenu(){
  const btn = $('#menuBtn');
  const menu = $('#mainMenu');
  if (!btn || !menu) return;

  const header = document.querySelector('[data-header]');

  const openMenu = () => {
    menu.hidden = false;

    // force reflow pour la transition
    void menu.offsetHeight;

    menu.classList.add('open');
    btn.setAttribute('aria-expanded', 'true');

    document.addEventListener('keydown', onKeyDown);
    document.addEventListener('click', onDocClick);
  };

  const closeMenu = () => {
    menu.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');

    document.removeEventListener('keydown', onKeyDown);
    document.removeEventListener('click', onDocClick);

    window.setTimeout(() => {
      menu.hidden = true;
    }, 220);
  };

  const toggle = () => {
    const isOpen = btn.getAttribute('aria-expanded') === 'true';

    if (isOpen){
      closeMenu();
    } else {
      openMenu();
    }
  };

  const onKeyDown = (e) => {
    if (e.key === 'Escape'){
      closeMenu();
    }
  };

  const onDocClick = (e) => {

  if (!menu.contains(e.target) && !btn.contains(e.target)) {
    closeMenu();
  }

};

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggle();
  });
}

function initCountdown(){
  const box = document.querySelector('[data-countdown]');
  if (!box) return;

  const dateStr = box.getAttribute('data-date');
  const target = dateStr ? new Date(dateStr) : null;

  if (!(target instanceof Date) || isNaN(target.getTime())) return;

  const $days = $('[data-days]', box);
  const $hours = $('[data-hours]', box);
  const $minutes = $('[data-minutes]', box);
  const $seconds = $('[data-seconds]', box);

  const pad2 = (n) => String(n).padStart(2, '0');

  const tick = () => {
    const now = Date.now();
    const dist = target.getTime() - now;

    if (dist <= 0){
      box.textContent = 'Le festival commence maintenant !';
      box.setAttribute('role', 'status');
      box.setAttribute('aria-live', 'polite');
      return false;
    }

    const days = Math.floor(dist / (1000 * 60 * 60 * 24));
    const hours = Math.floor((dist / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((dist / (1000 * 60)) % 60);
    const seconds = Math.floor((dist / 1000) % 60);

    if ($days) $days.textContent = String(days);
    if ($hours) $hours.textContent = pad2(hours);
    if ($minutes) $minutes.textContent = pad2(minutes);
    if ($seconds) $seconds.textContent = pad2(seconds);

    return true;
  };

  tick();

  const id = window.setInterval(() => {
    const keep = tick();

    if (!keep){
      window.clearInterval(id);
    }
  }, 1000);
}

function initCarousel(){
  const root = document.querySelector('[data-carousel]');
  if (!root) return;

  const track = $('[data-carousel-track]', root);
  const prev = $('[data-carousel-prev]', root);
  const next = $('[data-carousel-next]', root);

  if (!track || !prev || !next) return;

  const scrollByCard = (dir) => {
    const firstCard = track.querySelector('.guest-card');
    const gap = 16;

    const amount = firstCard
      ? (firstCard.getBoundingClientRect().width + gap)
      : 280;

    track.scrollBy({
      left: dir * amount,
      behavior: 'smooth'
    });
  };

  prev.addEventListener('click', () => scrollByCard(-1));
  next.addEventListener('click', () => scrollByCard(1));
}