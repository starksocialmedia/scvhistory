/* Review is local only.
 *
 * The decisions store answers 403 anywhere but a dev environment, so on staging
 * these screens can show the work but must not look as though they record it.
 * This asks the store once and, if it refuses, puts a banner at the top of the
 * page. window.reviewReadOnly is a promise of true or false; a screen that
 * saves anything awaits it and stops saving when it is true.
 *
 * Only a 403 means read-only. A store that does not answer at all is a fault on
 * a dev machine and each screen already says so in its own words. */
window.reviewReadOnly = fetch('/actions/reviewstore/decisions/all', { headers: { Accept: 'application/json' } })
  .then(r => r.status === 403, () => false)
  .then(ro => {
    if (!ro) return false;
    const show = () => {
      const b = document.createElement('div');
      b.setAttribute('role', 'status');
      b.style.cssText = 'background:#fff4d6;color:#4a3500;border-bottom:1px solid #d9b24c;'
        + 'padding:10px 16px;font:600 14px/1.4 system-ui,sans-serif;';
      b.textContent = 'Read-only. Review happens on the local machine; this copy shows the '
        + 'decisions as last committed, and nothing done on this page is saved.';
      document.body.prepend(b);
    };
    if (document.body) show(); else document.addEventListener('DOMContentLoaded', show);
    return true;
  });
