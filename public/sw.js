/* =========================================================
   Service Worker — BIMO-tech Immo
   Objectif : rendre l'application installable (PWA)
   Stratégie : réseau pur, aucun cache géré ici.
   Cela évite tout bug lié aux ressources périmées.
   ========================================================= */

const SW_VERSION = 'v1';

/* --- Installation ----------------------------------------
   skipWaiting() active immédiatement ce service worker
   sans attendre la fermeture des onglets existants.
   --------------------------------------------------------- */
self.addEventListener('install', (event) => {
    console.log('[SW] Installé — version', SW_VERSION);
    self.skipWaiting();
});

/* --- Activation ------------------------------------------
   clients.claim() permet de contrôler les pages ouvertes
   sans attendre un rechargement.
   --------------------------------------------------------- */
self.addEventListener('activate', (event) => {
    console.log('[SW] Activé — version', SW_VERSION);
    event.waitUntil(self.clients.claim());
});

/* --- Requêtes réseau -------------------------------------
   On laisse passer toutes les requêtes vers le réseau,
   sans interception ni mise en cache.
   L'application se comporte exactement comme sans SW.
   --------------------------------------------------------- */
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});
