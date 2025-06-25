<?php
$data = file_exists('simple_dice_data.json') ? json_decode(file_get_contents('simple_dice_data.json'), true) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dés D&D Améliorés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&family=Uncial+Antiqua&display=swap');

        :root {
            --primary-color: #2c1810;
            --secondary-color: #d4b483;
            --accent-color: #8b4513;
            --text-light: #e8d5b5;
            --text-dark: #1a0f0a;
            --background-main: #1a0f0a;
            --background-light: #f5e6d3;
            --border-radius: 8px;
            --box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            --transition: all 0.3s ease;
            --font-main: 'MedievalSharp', cursive;
            --font-title: 'Uncial Antiqua', cursive;
            --font-accent: 'Cinzel', serif;
            --success-color: #28a745;
            --fail-color: #dc3545;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--background-main);
            color: var(--text-light);
            padding: 2rem;
            display: flex;
            justify-content: center;
            margin: 0;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background-color: var(--background-light);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 2px solid var(--accent-color);
            position: relative;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
        }

        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .header-container h1 {
            margin: 0;
            font-family: var(--font-accent);
            color: var(--text-dark);
            text-transform: uppercase;
            font-size: 2rem;
        }

        #Dice20 {
            width: 130px;
            height: 90px;
            margin-left: 1rem;
            background-color: var(--background-light);
        }

        #Dice20 canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        .dice-selector {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .dice-btn {
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: var(--text-light);
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            font-family: var(--font-accent);
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .dice-btn:hover,
        .dice-btn.active {
            background-color: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }

        .controls-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .input-group {
            text-align: center;
            font-weight: bold;
            color: var(--text-dark);
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        #diceCount {
            width: 60px;
            padding: 0.4rem;
            border-radius: var(--border-radius);
            border: 2px solid var(--accent-color);
            background-color: var(--background-light);
            color: var(--text-dark);
            font-family: var(--font-main);
            font-size: 1rem;
            text-align: center;
        }

        .modifiers-group {
            text-align: center;
        }

        .modifier-buttons {
            display: flex;
            justify-content: center;
            gap: 0.3rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .modifier-btn {
            padding: 0.3rem 0.6rem;
            background-color: var(--secondary-color);
            color: var(--text-dark);
            border: 1px solid var(--accent-color);
            border-radius: 4px;
            font-family: var(--font-accent);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: bold;
        }

        .modifier-btn:hover {
            background-color: var(--accent-color);
            color: var(--text-light);
            transform: translateY(-1px);
        }

        .modifier-display {
            margin-top: 0.5rem;
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .advantage-section {
            grid-column: 1 / -1;
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            background-color: rgba(139, 69, 19, 0.1);
            border-radius: var(--border-radius);
            border: 1px solid var(--accent-color);
        }

        .advantage-options {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 0.5rem;
        }

        .advantage-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: var(--transition);
        }

        .advantage-option:hover {
            background-color: rgba(139, 69, 19, 0.2);
        }

        .advantage-option input[type="checkbox"] {
            transform: scale(1.2);
            accent-color: var(--accent-color);
        }

        .advantage-option label {
            font-family: var(--font-accent);
            font-weight: bold;
            color: var(--text-dark);
            cursor: pointer;
        }

        .roll-btn {
            display: block;
            width: 100%;
            padding: 0.7rem;
            background-color: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: var(--border-radius);
            font-family: var(--font-accent);
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .roll-btn:hover {
            background-color: var(--accent-color);
            box-shadow: var(--box-shadow);
            transform: translateY(-1px);
        }

        #result {
            text-align: center;
            background-color: #fff6e9;
            color: var(--text-dark);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 2px solid var(--accent-color);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
        }

        #result h2 {
            font-size: 2rem;
            color: var(--primary-color);
            font-family: var(--font-accent);
            margin-bottom: 0.5rem;
        }

        #result p {
            font-family: var(--font-main);
            font-size: 1rem;
            margin: 0.3rem 0;
        }

        .dice-results {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .dice-result {
            display: inline-block;
            padding: 0.5rem 0.8rem;
            background-color: var(--background-light);
            border: 2px solid var(--accent-color);
            border-radius: 50%;
            font-family: var(--font-accent);
            font-weight: bold;
            font-size: 1.1rem;
            min-width: 2.5rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .dice-result.best {
            background-color: var(--success-color);
            color: white;
            border-color: var(--success-color);
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .dice-result.worst {
            background-color: var(--fail-color);
            color: white;
            border-color: var(--fail-color);
            transform: scale(0.9);
            opacity: 0.7;
        }

        .final-result {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
            font-family: var(--font-accent);
        }

        .modifier-applied {
            font-size: 1rem;
            color: #666;
            font-style: italic;
        }

        .critical-success {
            color: var(--success-color);
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        .critical-fail {
            color: var(--fail-color);
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        /* === STYLES POUR L'HISTORIQUE === */
        .history-section {
            margin-top: 2rem;
            background-color: var(--background-light);
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .history-header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-header h2 {
            margin: 0;
            font-family: var(--font-accent);
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .history-controls {
            display: flex;
            gap: 0.5rem;
        }

        .history-btn, .clear-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: var(--border-radius);
            font-family: var(--font-main);
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            font-weight: bold;
        }

        .history-btn {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        .history-btn:hover {
            background-color: #c9a876;
            transform: translateY(-1px);
        }

        .clear-btn {
            background-color: #8b0000;
            color: var(--text-light);
        }

        .clear-btn:hover {
            background-color: #a00000;
            transform: translateY(-1px);
        }

        .history-container {
            max-height: 400px;
            overflow-y: auto;
            background-color: #f9f1e6;
        }

        .history-list {
            padding: 1rem;
        }

        .history-item {
            background-color: white;
            border: 1px solid var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .history-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .history-header-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .history-dice {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: var(--font-accent);
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .history-time {
            color: #666;
            font-size: 0.8rem;
            font-family: var(--font-accent);
        }

        .history-result {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .history-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            font-family: var(--font-accent);
        }

        .history-single {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--primary-color);
            font-family: var(--font-accent);
        }

        .history-detail {
            color: #555;
            font-size: 0.9rem;
            font-family: var(--font-main);
        }

        .critical-text {
            margin-top: 0.5rem;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
            text-align: center;
        }

        .critical-success {
            background-color: #d4edda;
            border-color: #28a745;
        }

        .critical-success .critical-text {
            background-color: #28a745;
            color: white;
        }

        .critical-fail {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        .critical-fail .critical-text {
            background-color: #dc3545;
            color: white;
        }

        .no-history {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
            font-family: var(--font-main);
        }

        @media (max-width: 768px) {
            body { padding: 1rem; }
            .container { padding: 1.5rem; }
            .header-container { flex-direction: column; gap: 1rem; text-align: center; }
            .header-container h1 { font-size: 1.5rem; }
            #Dice20 { width: 100px; height: 60px; margin: 0; }
            .controls-section { grid-template-columns: 1fr; gap: 1rem; }
            .advantage-options { flex-direction: column; gap: 1rem; }
            .modifier-buttons { justify-content: center; }
            .history-header { flex-direction: column; gap: 1rem; text-align: center; }
            .history-controls { justify-content: center; }
            .history-container { max-height: 300px; }
            .history-header-item { flex-direction: column; gap: 0.3rem; text-align: center; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h1>Dés D&D</h1>
            <div id="Dice20"></div>
        </div>

        <div class="dice-selector">
            <?php foreach (['d4','d6','d8','d10','d12','d20','d100'] as $dice): ?>
                <button class="dice-btn<?= $dice === 'd4' ? ' active' : '' ?>" onclick="selectDice('<?= $dice ?>', this)"><?= strtoupper($dice) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="controls-section">
            <div class="input-group">
                <label for="diceCount">Nombre de dés :</label>
                <input type="number" id="diceCount" value="1" min="1" max="20">
            </div>

            <div class="modifiers-group">
                <label>Modificateurs :</label>
                <div class="modifier-buttons">
                    <button class="modifier-btn" onclick="addModifier(-5)">-5</button>
                    <button class="modifier-btn" onclick="addModifier(-2)">-2</button>
                    <button class="modifier-btn" onclick="addModifier(-1)">-1</button>
                    <button class="modifier-btn" onclick="addModifier(1)">+1</button>
                    <button class="modifier-btn" onclick="addModifier(2)">+2</button>
                    <button class="modifier-btn" onclick="addModifier(5)">+5</button>
                </div>
                <div class="modifier-display">
                    Modificateur : <span id="currentModifier">+0</span>
                    <button class="modifier-btn" onclick="resetModifier()" style="margin-left: 0.5rem;">Reset</button>
                </div>
            </div>

            <div class="advantage-section">
                <label>Options D&D :</label>
                <div class="advantage-options">
                    <div class="advantage-option">
                        <input type="checkbox" id="showBestWorst" />
                        <label for="showBestWorst">Montrer Meilleur/Pire</label>
                    </div>
                    <div class="advantage-option">
                        <input type="checkbox" id="advantageMode" />
                        <label for="advantageMode">Mode Avantage (D20)</label>
                    </div>
                </div>
            </div>
        </div>

        <button class="roll-btn" onclick="rollDice()">Lancer les Dés</button>

        <div id="result">
            <?php if ($data): ?>
                <?php if (isset($data['results']) && is_array($data['results'])): ?>
                    <h2><?= htmlspecialchars($data['total']) ?></h2>
                    <p>Total : <?= htmlspecialchars($data['count']) ?><?= htmlspecialchars(strtoupper($data['dice'])) ?></p>
                    <p>Détail : [<?= htmlspecialchars(implode(', ', $data['results'])) ?>]</p>
                <?php else: ?>
                    <h2><?= htmlspecialchars($data['result']) ?></h2>
                    <p>Dé : <?= htmlspecialchars(strtoupper($data['dice'])) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <h2>?</h2>
                <p>Aucun lancer</p>
            <?php endif; ?>
        </div>

        <!-- Section Historique -->
        <div class="history-section">
            <div class="history-header">
                <h2>Historique des Lancers</h2>
                <div class="history-controls">
                    <button class="history-btn" onclick="toggleHistory()">
                        <span id="historyToggleText">Afficher</span>
                    </button>
                    <button class="clear-btn" onclick="clearHistory()">Vider</button>
                </div>
            </div>
            
            <div id="historyContainer" class="history-container" style="display: none;">
                <div id="historyList" class="history-list">
                    <!-- L'historique sera chargé ici -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDice = 'd4';
        let currentModifier = 0;
        let historyVisible = false;

        function selectDice(dice, btn) {
            document.querySelectorAll('.dice-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentDice = dice;
        }

        function addModifier(value) {
            currentModifier += value;
            updateModifierDisplay();
        }

        function resetModifier() {
            currentModifier = 0;
            updateModifierDisplay();
        }

        function updateModifierDisplay() {  
            const display = document.getElementById('currentModifier');
            const sign = currentModifier >= 0 ? '+' : '';
            display.textContent = sign + currentModifier;
        }

        function rollDice(saveResult = true) {
            const diceCount = parseInt(document.getElementById('diceCount').value);
            const showBestWorst = document.getElementById('showBestWorst').checked;
            const advantageMode = document.getElementById('advantageMode').checked;
            const saveHistory = true;

            // Si mode avantage activé ET c'est un D20, forcer 2 dés
            let finalDiceCount = diceCount;
            if (advantageMode && currentDice === 'd20') {
                finalDiceCount = 2;
            }

            fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=roll&dice=${currentDice}&count=${finalDiceCount}&save=${saveResult ? '1' : '0'}&history=${saveHistory ? '1' : '0'}&modifier=${currentModifier}&show_best_worst=${showBestWorst ? '1' : '0'}&advantage_mode=${advantageMode ? '1' : '0'}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateResultWithEnhancements(data, showBestWorst, advantageMode);
                    if (data.history_saved && historyVisible) {
                        loadHistory();
                    }
                }
            });
        }

        function updateResultWithEnhancements(data, showBestWorst, advantageMode) {
            const resultDiv = document.getElementById('result');
            
            // Si c'est du mode avantage D20, traitement spécial
            if (advantageMode && currentDice === 'd20' && data.results && data.results.length === 2) {
                const bestRoll = Math.max(...data.results);
                const finalTotal = bestRoll + currentModifier;
                
                let html = '<div class="dice-results">';
                data.results.forEach((result, index) => {
                    const isMax = result === bestRoll;
                    const cssClass = isMax ? 'dice-result best' : 'dice-result worst';
                    html += `<div class="${cssClass}">${result}</div>`;
                });
                html += '</div>';
                
                html += `<div class="final-result">Avantage : ${finalTotal}</div>`;
                if (currentModifier !== 0) {
                    html += `<div class="modifier-applied">Meilleur dé (${bestRoll}) + modificateur (${currentModifier >= 0 ? '+' : ''}${currentModifier})</div>`;
                }
                
                // Critiques basés sur le meilleur dé
                if (bestRoll === 20) {
                    html += '<p class="critical-success">Critique Réussi !</p>';
                } else if (bestRoll === 1) {
                    html += '<p class="critical-fail">Échec Critique !</p>';
                }
                
                resultDiv.innerHTML = html;
                return;
            }
            
            // Traitement normal avec améliorations visuelles
            let html = '';
            
            // Affichage des dés individuels si multiple ou si show_best_worst activé
            if ((data.results && data.results.length > 1) || showBestWorst) {
                html += '<div class="dice-results">';
                if (data.results) {
                    const maxVal = Math.max(...data.results);
                    const minVal = Math.min(...data.results);
                    const maxIndex = data.results.indexOf(maxVal);
                    const minIndex = data.results.indexOf(minVal);
                    
                    data.results.forEach((result, index) => {
                        let cssClass = 'dice-result';
                        if (showBestWorst && data.results.length > 1) {
                            if (index === maxIndex) cssClass += ' best';
                            if (index === minIndex) cssClass += ' worst';
                        }
                        html += `<div class="${cssClass}">${result}</div>`;
                    });
                }
                html += '</div>';
            }
            
            // Calcul du total avec modificateur
            const rawTotal = data.total || data.result;
            const finalTotal = rawTotal + currentModifier;
            
            html += `<div class="final-result">${finalTotal}</div>`;
            
            if (data.results && data.results.length > 1) {
                html += `<p>Total : ${data.count}${data.dice.toUpperCase()}</p>`;
                html += `<p>Détail : [${data.results.join(', ')}]</p>`;
            } else {
                html += `<p>Dé : ${data.dice.toUpperCase()}</p>`;
            }
            
            if (currentModifier !== 0) {
                html += `<div class="modifier-applied">Total brut (${rawTotal}) + modificateur (${currentModifier >= 0 ? '+' : ''}${currentModifier})</div>`;
            }
            
            // Ajout des informations critiques pour le D20
            if (data.is_critical_hit) {
                html += '<p class="critical-success">Critique Réussi !</p>';
            } else if (data.is_critical_fail) {
                html += '<p class="critical-fail">Échec Critique !</p>';
            }

            resultDiv.innerHTML = html;
        }

        // Fonction pour basculer l'affichage de l'historique
        function toggleHistory() {
            const container = document.getElementById('historyContainer');
            const toggleText = document.getElementById('historyToggleText');
            
            if (historyVisible) {
                container.style.display = 'none';
                toggleText.textContent = 'Afficher';
                historyVisible = false;
            } else {
                container.style.display = 'block';
                toggleText.textContent = 'Masquer';
                historyVisible = true;
                loadHistory();
            }
        }

        // Fonction pour charger l'historique
        function loadHistory() {
            fetch('api.php?action=history')
            .then(res => res.json())
            .then(history => {
                displayHistory(history);
            })
            .catch(err => {
                console.error('Erreur lors du chargement de l\'historique:', err);
            });
        }

        // Fonction pour afficher l'historique
        function displayHistory(history) {
            const historyList = document.getElementById('historyList');
            
            if (!history || history.length === 0) {
                historyList.innerHTML = '<p class="no-history">Aucun historique disponible</p>';
                return;
            }

            let html = '';
            history.forEach((entry, index) => {
                const isMultiple = entry.results && Array.isArray(entry.results);
                const criticalClass = getCriticalClass(entry);
                
                html += `
                    <div class="history-item ${criticalClass}">
                        <div class="history-header-item">
                            <span class="history-dice">${entry.dice.toUpperCase()}</span>
                            <span class="history-time">${formatTimestamp(entry.timestamp)}</span>
                        </div>
                        <div class="history-result">
                            ${isMultiple ? 
                                `<span class="history-total">Total: ${entry.total}</span>
                                 <span class="history-detail">[${entry.results.join(', ')}]</span>` :
                                `<span class="history-single">${entry.result || entry.total}</span>`
                            }
                        </div>
                        ${getCriticalText(entry)}
                    </div>
                `;
            });
            
            historyList.innerHTML = html;
        }

        // Fonction pour déterminer la classe CSS selon le résultat critique
        function getCriticalClass(entry) {
            if (entry.dice === 'd20' && entry.count === 1) {
                if (entry.is_critical_hit) return 'critical-success';
                if (entry.is_critical_fail) return 'critical-fail';
            }
            return '';
        }

        // Fonction pour afficher le texte critique
        function getCriticalText(entry) {
            if (entry.dice === 'd20' && entry.count === 1) {
                if (entry.is_critical_hit) return '<div class="critical-text">Critique Réussi !</div>';
                if (entry.is_critical_fail) return '<div class="critical-text">Échec Critique !</div>';
            }
            return '';
        }

        // Fonction pour formater le timestamp
        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            // Si c'est aujourd'hui, afficher seulement l'heure
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
            
            // Sinon afficher la date et l'heure
            return date.toLocaleString('fr-FR', { 
                day: '2-digit', 
                month: '2-digit', 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }

        // Fonction pour vider l'historique
        function clearHistory() {
            if (!confirm('Êtes-vous sûr de vouloir vider l\'historique ?')) {
                return;
            }

            fetch('api.php?action=clear_history', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=clear_history'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (historyVisible) {
                        loadHistory(); // Recharger l'historique vide
                    }
                    console.log('Historique vidé');
                }
            })
            .catch(err => {
                console.error('Erreur lors de la suppression:', err);
            });
        }

        // Fonction pour mettre à jour l'affichage depuis les données JSON (synchronisation)
        function updateResult(data) {
            const resultDiv = document.getElementById('result');
            
            // Récupération des états actuels des cases à cocher
            const showBestWorst = document.getElementById('showBestWorst').checked;
            const advantageMode = document.getElementById('advantageMode').checked;
            
            let html = '';
            
            // Gestion du mode avantage D20 depuis les données JSON
            if (data.advantage_mode && data.dice === 'd20' && data.results && data.results.length === 2) {
                const bestRoll = Math.max(...data.results);
                const finalTotal = bestRoll + (data.modifier || currentModifier);
                
                html += '<div class="dice-results">';
                data.results.forEach((result, index) => {
                    const isMax = result === bestRoll;
                    const cssClass = isMax ? 'dice-result best' : 'dice-result worst';
                    html += `<div class="${cssClass}">${result}</div>`;
                });
                html += '</div>';
                
                html += `<div class="final-result">Avantage : ${finalTotal}</div>`;
                if (data.modifier || currentModifier !== 0) {
                    const mod = data.modifier || currentModifier;
                    html += `<div class="modifier-applied">Meilleur dé (${bestRoll}) + modificateur (${mod >= 0 ? '+' : ''}${mod})</div>`;
                }
                
                // Critiques basés sur le meilleur dé
                if (bestRoll === 20) {
                    html += '<p class="critical-success">Critique Réussi !</p>';
                } else if (bestRoll === 1) {
                    html += '<p class="critical-fail">Échec Critique !</p>';
                }
            }
            // Affichage normal avec option meilleur/pire
            else {
                // Affichage des dés individuels si multiple ou si show_best_worst est activé
                if ((data.results && data.results.length > 1) || (showBestWorst && data.results)) {
                    html += '<div class="dice-results">';
                    if (data.results) {
                        const maxVal = Math.max(...data.results);
                        const minVal = Math.min(...data.results);
                        const maxIndex = data.results.indexOf(maxVal);
                        const minIndex = data.results.indexOf(minVal);
                        
                        data.results.forEach((result, index) => {
                            let cssClass = 'dice-result';
                            if ((showBestWorst || data.show_best_worst) && data.results.length > 1) {
                                if (index === maxIndex) cssClass += ' best';
                                if (index === minIndex) cssClass += ' worst';
                            }
                            html += `<div class="${cssClass}">${result}</div>`;
                        });
                    }
                    html += '</div>';
                }
                
                // Calcul du total avec modificateur
                const rawTotal = data.total || data.result;
                const appliedModifier = data.modifier || currentModifier;
                const finalTotal = rawTotal + appliedModifier;
                
                html += `<div class="final-result">${finalTotal}</div>`;
                
                if (data.results && data.results.length > 1) {
                    html += `<p>Total : ${data.count}${data.dice.toUpperCase()}</p>`;
                    html += `<p>Détail : [${data.results.join(', ')}]</p>`;
                } else {
                    html += `<p>Dé : ${data.dice.toUpperCase()}</p>`;
                }
                
                if (appliedModifier !== 0) {
                    html += `<div class="modifier-applied">Total brut (${rawTotal}) + modificateur (${appliedModifier >= 0 ? '+' : ''}${appliedModifier})</div>`;
                }
                
                // Ajout des informations critiques pour le D20
                if (data.is_critical_hit) {
                    html += '<p class="critical-success">Critique Réussi !</p>';
                } else if (data.is_critical_fail) {
                    html += '<p class="critical-fail">Échec Critique !</p>';
                }
            }

            resultDiv.innerHTML = html;
        }

        // Synchronisation périodique avec le serveur (optimisée)
        let lastSyncTime = Date.now();
        setInterval(() => {
            fetch('api.php?action=get')
            .then(r => r.json())
            .then(data => {
                if (data && (data.result || data.total)) {
                    // Ne mettre à jour que si nécessaire
                    updateResult(data);
                }
            });
            
            // Recharger l'historique seulement s'il est visible et moins fréquemment
            if (historyVisible && (Date.now() - lastSyncTime > 2000)) {
                loadHistory();
                lastSyncTime = Date.now();
            }
        }, 1000); // Réduit à 1 seconde

        // === THREE.JS POUR LE DÉ D20 ===
        let scene, camera, renderer, dice;

        function init() {
            scene = new THREE.Scene();

            const container = document.getElementById('Dice20');

            camera = new THREE.PerspectiveCamera(75, 120/80, 0.1, 1000);
            camera.position.z = 4;

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setClearColor(0x000000, 0);
            renderer.setSize(120, 80);
            container.appendChild(renderer.domElement);

            const geometry = new THREE.IcosahedronGeometry(2, 0);
            const material = new THREE.MeshLambertMaterial({ color: 0x6d4c41 });

            const ambientLight = new THREE.AmbientLight(0x404040, 0.6);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);

            dice = new THREE.Mesh(geometry, material);
            scene.add(dice);

            animate();
        }

        function animate() {
            requestAnimationFrame(animate);
            dice.rotation.x += 0.01;
            dice.rotation.y += 0.02;
            renderer.render(scene, camera);
        }

        // Initialisation
        updateModifierDisplay();
        init();
    </script>
</body>
</html>