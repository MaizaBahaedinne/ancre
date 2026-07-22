<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Recu Paiement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.4;
        }

        .header {
            margin-bottom: 22px;
            border-bottom: 2px solid #0ea5e9;
            padding-bottom: 10px;
        }

        .title {
            margin: 0;
            font-size: 22px;
            color: #0f172a;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #475569;
        }

        .meta {
            margin-top: 10px;
            color: #334155;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }

        th {
            width: 34%;
            background: #f8fafc;
            color: #0f172a;
        }

        .footer {
            margin-top: 24px;
            font-size: 11px;
            color: #64748b;
        }

        .amount {
            font-size: 18px;
            font-weight: 700;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Recu de Paiement</h1>
        <p class="subtitle">Garderie Ancre Des Elites</p>
        <p class="meta">Reference: {{ $paiement->reference ?: ('PAY-' . str_pad((string) $paiement->id, 6, '0', STR_PAD_LEFT)) }}</p>
        <p class="meta">Genere le: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <tr>
            <th>Enfant</th>
            <td>{{ $paiement->enfant?->nom }} {{ $paiement->enfant?->prenom }}</td>
        </tr>
        <tr>
            <th>Parent</th>
            <td>{{ $paiement->enfant?->parent?->nom }} {{ $paiement->enfant?->parent?->prenom }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>{{ $paiement->mois }}/{{ $paiement->annee }}</td>
        </tr>
        <tr>
            <th>Date de paiement</th>
            <td>{{ optional($paiement->date_paiement)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Mode de paiement</th>
            <td>{{ $paiement->mode_paiement }}</td>
        </tr>
        <tr>
            <th>Statut</th>
            <td>{{ $paiement->statut }}</td>
        </tr>
        <tr>
            <th>Montant</th>
            <td class="amount">{{ number_format((float) $paiement->montant, 2, ',', ' ') }} TND</td>
        </tr>
        <tr>
            <th>Commentaire</th>
            <td>{{ $paiement->commentaire ?: '-' }}</td>
        </tr>
    </table>

    <p class="footer">Ce document est genere automatiquement par le systeme de gestion de la garderie.</p>
</body>
</html>
