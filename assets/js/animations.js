/* =====================================================
   ZakaAI Dashboard — Modern Motion Layer
   Purely additive: does not touch existing app.js logic.
   ===================================================== */

document.addEventListener('DOMContentLoaded', function () {

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) return;

  /* ---------- Stagger the riwayat & insight list on load ---------- */
  function staggerChildren(selector, delayStep) {
    document.querySelectorAll(selector).forEach(function (el, i) {
      el.style.opacity = '0';
      el.style.transform = 'translateY(12px)';
      el.style.transition = 'opacity .5s cubic-bezier(.22,1,.36,1), transform .5s cubic-bezier(.22,1,.36,1)';
      el.style.transitionDelay = (i * delayStep) + 'ms';
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        });
      });
    });
  }
  staggerChildren('.riwayat-item', 60);
  staggerChildren('.insight-item', 90);

  /* ---------- Ripple feedback on primary buttons ---------- */
  document.querySelectorAll('.btn-primary, .btn-upgrade').forEach(function (btn) {
    btn.style.position = btn.style.position || 'relative';
    btn.style.overflow = 'hidden';
    btn.addEventListener('click', function (e) {
      var rect = btn.getBoundingClientRect();
      var ripple = document.createElement('span');
      var size = Math.max(rect.width, rect.height);
      ripple.style.position = 'absolute';
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      ripple.style.borderRadius = '50%';
      ripple.style.background = 'rgba(255,255,255,0.35)';
      ripple.style.pointerEvents = 'none';
      ripple.style.transform = 'scale(0)';
      ripple.style.opacity = '1';
      ripple.style.transition = 'transform .6s cubic-bezier(.22,1,.36,1), opacity .6s ease';
      ripple.style.zIndex = '0';
      btn.appendChild(ripple);
      requestAnimationFrame(function () {
        ripple.style.transform = 'scale(1.6)';
        ripple.style.opacity = '0';
      });
      setTimeout(function () { ripple.remove(); }, 650);
    });
  });

});
