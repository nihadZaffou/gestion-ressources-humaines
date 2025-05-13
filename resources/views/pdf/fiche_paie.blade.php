<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fiche de Paie</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #000; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Fiche de Paie - {{ $mois }}/{{ $annee }}</h2>

    <p><strong>Nom :</strong> {{ $employe->nom }}</p>
    <p><strong>Prénom :</strong> {{ $employe->prenom }}</p>
    <p><strong>Date d'entrée :</strong> {{ $employe->date_entree }}</p>

    <table>
        <tr>
            <th>Salaire Net</th>
            <td>{{ number_format($salaire_net, 2) }} DH</td>
        </tr>
    </table>

    <p style="margin-top: 30px;">Ceci est une fiche générée automatiquement. Merci.</p>
</body>
</html>
