{{-- Wordmark SIGNATURE « Bimmo » (système de marque Bimmo — variante A).
     Script Allura doré, majuscule initiale uniquement. Porte l'émotion de la marque.
     Usage : écran de connexion / accueil, en-têtes de documents, emails, vitrine.
     Taille minimale ~28px (illisible en dessous). Ne PAS utiliser en sidebar,
     header quotidien, favicon, bouton ou badge (→ x-wordmark).
     Nécessite la police Allura chargée dans le layout. --}}
<span {{ $attributes->merge(['class' => 'wordmark-signature']) }}>Bimmo</span>
