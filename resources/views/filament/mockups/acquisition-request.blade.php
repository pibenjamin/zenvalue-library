<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'acquisition</title>
    <style>
        .mockup {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .header {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .field {
            margin-bottom: 1rem;
        }
        .label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            min-height: 100px;
        }
        .button {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="mockup">
        <div class="section">
            <div class="header">
                Demande d'acquisition de livre 📚
            </div>

            <div class="grid">
                <div class="field">
                    <label class="label">Titre du livre *</label>
                    <input type="text" class="input" placeholder="Le titre du livre">
                </div>

                <div class="field">
                    <label class="label">Auteur *</label>
                    <input type="text" class="input" placeholder="Nom de l'auteur">
                </div>
            </div>

            <div class="field">
                <label class="label">Description de la demande</label>
                <textarea class="textarea" placeholder="Pourquoi souhaitez-vous ce livre ?"></textarea>
            </div>

            <div class="grid">
                <div class="field">
                    <label class="label">ISBN</label>
                    <input type="text" class="input" placeholder="978-2-1234-5678-9">
                </div>

                <div class="field">
                    <label class="label">Prix estimé</label>
                    <input type="text" class="input" placeholder="19.99 €">
                </div>
            </div>

            <div class="field">
                <label class="label">Lien vers le livre</label>
                <input type="url" class="input" placeholder="https://...">
            </div>

            <div class="field">
                <label class="label">Image de couverture</label>
                <input type="file" accept="image/*" class="input">
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="button">
                    Soumettre la demande
                </button>
            </div>
        </div>
    </div>
</body>
</html> 