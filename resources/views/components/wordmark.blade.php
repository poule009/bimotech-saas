{{-- Wordmark FONCTIONNEL « bimmo » (système de marque Bimmo — variante B).
     Fraunces light, tout en minuscule, double-m marqué d'un trait doré.
     Usage : sidebar, header d'app, contextes réduits où la lisibilité prime.
     Ne PAS utiliser pour la connexion, les PDF, les emails ou la vitrine (→ x-wordmark-signature).
     Nécessite la police Fraunces (déjà chargée dans les layouts app). --}}
<span {{ $attributes->merge(['class' => 'wordmark']) }}>bi<span class="wm-mm">mm</span>o</span>
