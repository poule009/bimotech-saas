/* =========================================================================
   Vitrine publique par agence — interactions minimales (vanilla, sans Alpine).
   1. Galerie : cliquer une miniature remplace l'image principale.
   2. Copier le lien du bien (presse-papier) avec repli si l'API est absente.
   Aucun handler inline (compatible CSP stricte / nonce).
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
  // ── Galerie : swap de l'image principale ────────────────────────────────
  const mainImg = document.getElementById('gallery-main-img');
  if (mainImg) {
    document.querySelectorAll('.gallery-thumb[data-full]').forEach((thumb) => {
      thumb.addEventListener('click', () => {
        mainImg.src = thumb.dataset.full;
        document.querySelectorAll('.gallery-thumb').forEach((t) => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
  }

  // ── Copier le lien du bien ──────────────────────────────────────────────
  const copyBtn = document.getElementById('copy-link-btn');
  if (copyBtn) {
    const feedback = document.getElementById('copy-feedback');
    const url = copyBtn.dataset.url || window.location.href;
    const label = copyBtn.querySelector('.copy-label');
    const originalLabel = label ? label.textContent : '';

    const done = () => {
      if (feedback) feedback.textContent = 'Lien copié ✓';
      if (label) label.textContent = 'Lien copié ✓';
      setTimeout(() => {
        if (feedback) feedback.textContent = '';
        if (label) label.textContent = originalLabel;
      }, 2500);
    };

    copyBtn.addEventListener('click', async () => {
      try {
        if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(url);
        } else {
          const tmp = document.createElement('textarea');
          tmp.value = url;
          tmp.style.position = 'fixed';
          tmp.style.opacity = '0';
          document.body.appendChild(tmp);
          tmp.select();
          document.execCommand('copy');
          document.body.removeChild(tmp);
        }
        done();
      } catch (e) {
        if (feedback) feedback.textContent = url;
      }
    });
  }
});
