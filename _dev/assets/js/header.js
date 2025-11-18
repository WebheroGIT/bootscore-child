/* nav-submenu-guard.js
 * Flip/contain dei sottomenu annidati (depth_1 / depth_2…) per evitare overflow viewport.
 * Dipendenze: nessuna (funziona con Bootstrap 5, Popper attivo per il primo livello).
 */

(function () {
    'use strict';
  
    var NAV_SELECTOR = '#bootscore-navbar';
    var PAD = 8; // margine di sicurezza dai bordi viewport
  
    function positionSubmenu(li, subMenu) {
      // reset soft
      subMenu.classList.remove('submenu-left', 'auto-scroll');
      subMenu.style.left = '100%';
      subMenu.style.right = 'auto';
      subMenu.style.top = '0px';
      subMenu.style.maxHeight = '';
  
      // forza layout per avere misure attendibili
      var rect = subMenu.getBoundingClientRect();
      var vw = window.innerWidth;
      var vh = window.innerHeight;
  
      // Flip orizzontale se sborda a destra
      if (rect.right > (vw - PAD)) {
        subMenu.classList.add('submenu-left');
      }
  
      // ricalcola dopo il flip
      var rect2 = subMenu.getBoundingClientRect();
  
      // Contenimento verticale se sborda in basso
      if (rect2.bottom > (vh - PAD)) {
        var deltaBottom = rect2.bottom - (vh - PAD);
        var currentTop = parseFloat(subMenu.style.top || '0');
        var newTop = Math.max(currentTop - deltaBottom, -(rect2.height - 40)); // lascia ~40px
        subMenu.style.top = newTop + 'px';
  
        var maxH = Math.min(rect2.height, vh - (PAD * 2));
        subMenu.style.maxHeight = maxH + 'px';
        subMenu.classList.add('auto-scroll');
      }
  
      // Se tocca il bordo alto, spingilo giù
      var rect3 = subMenu.getBoundingClientRect();
      if (rect3.top < PAD) {
        var adjustDown = PAD - rect3.top;
        var currentTop2 = parseFloat(subMenu.style.top || '0');
        subMenu.style.top = (currentTop2 + adjustDown) + 'px';
      }
    }
  
    function bindSubmenus(root) {
      // Consideriamo solo LI che hanno submenu annidato immediato
      var items = root.querySelectorAll('.depth_0 .depth_1 > li');
      items.forEach(function (li) {
        var sub = li.querySelector(':scope > .dropdown-menu');
        if (!sub) return;
  
        li.addEventListener('mouseenter', function () {
          // Rendilo misurabile
          sub.classList.add('show');
          sub.style.display = 'block';
          positionSubmenu(li, sub);
        });
  
        li.addEventListener('mouseleave', function () {
          // Cleanup (non tocchiamo i top-level)
          sub.classList.remove('show', 'submenu-left', 'auto-scroll');
          sub.style.display = '';
          sub.style.top = '';
          sub.style.left = '';
          sub.style.right = '';
          sub.style.maxHeight = '';
        });
      });
  
      // Recompute su resize
      window.addEventListener('resize', function () {
        root.querySelectorAll('.depth_0 .depth_1 > li > .dropdown-menu.show').forEach(function (sub) {
          var li = sub.parentElement;
          positionSubmenu(li, sub);
        });
      });
    }
  
    function onReady(fn) {
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
      } else {
        fn();
      }
    }
  
    onReady(function () {
      var nav = document.querySelector(NAV_SELECTOR);
      if (!nav) return;
  
      // Suggerito: evita chiusure automatiche di Popper sui dropdown top-level mentre gestiamo i figli
      nav.querySelectorAll('.dropdown-toggle').forEach(function (t) {
        t.setAttribute('data-bs-display', 'static');
      });
  
      bindSubmenus(nav);
    });
  })();
  