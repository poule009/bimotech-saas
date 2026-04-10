<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">

    <h2 style="color: #1a56db;">Nouvelle demande de démo</h2>

    <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
        <tr><td style="padding: 8px; font-weight: bold; width: 140px;">Prénom</td><td style="padding: 8px;">{{ $data['prenom'] }}</td></tr>
        <tr style="background:#f9f9f9"><td style="padding: 8px; font-weight: bold;">Nom</td><td style="padding: 8px;">{{ $data['nom'] }}</td></tr>
        <tr><td style="padding: 8px; font-weight: bold;">Agence</td><td style="padding: 8px;">{{ $data['agence'] }}</td></tr>
        <tr style="background:#f9f9f9"><td style="padding: 8px; font-weight: bold;">Email</td><td style="padding: 8px;">{{ $data['email'] }}</td></tr>
        <tr><td style="padding: 8px; font-weight: bold;">Téléphone</td><td style="padding: 8px;">{{ $data['telephone'] }}</td></tr>
        <tr style="background:#f9f9f9"><td style="padding: 8px; font-weight: bold;">Nb biens</td><td style="padding: 8px;">{{ $data['nb_biens'] ?? 'Non renseigné' }}</td></tr>
        <tr><td style="padding: 8px; font-weight: bold;">Ville</td><td style="padding: 8px;">{{ $data['ville'] ?? 'Non renseignée' }}</td></tr>
    </table>

    <p style="margin-top: 24px; font-size: 12px; color: #888;">Message reçu le {{ now()->format('d/m/Y à H:i') }}</p>

</body>
</html>
