<?php
// api.php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactive l'affichage des erreurs dans la réponse HTTP
ini_set('log_errors', 1); // Active le log des erreurs
ini_set('error_log', __DIR__ . '/php-error.log'); // Définit le fichier de log
header('Content-Type: application/json; charset=utf-8');

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Interface pour tous les types de dés
interface DiceInterface {
    public function roll();
    public function rollMultiple($count);
    public function getSides();
    public function getName();
}

// Interface pour tous les types de dés
interface DiceFabrique {
    public function fabrique() : DiceInterface;
}

// Classe de base abstraite pour les dés
abstract class BaseDice implements DiceInterface {
    protected $sides;
    protected $name;
    
    public function __construct($sides, $name) {
        $this->sides = $sides;
        $this->name = $name;
    }
    
    public function roll() {
        return rand(1, $this->sides);
    }
    
    public function rollMultiple($count) {
        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->roll();
        }
        return $results;
    }
    
    public function getSides() {
        return $this->sides;
    }
    
    public function getName() {
        return $this->name;
    }
}

// Classes spécifiques pour chaque type de dé
class D4 extends BaseDice {
    public function __construct() {
        parent::__construct(4, 'd4');
    }
}

class D6 extends BaseDice {     
    public function __construct() {
        parent::__construct(6, 'd6');
    }
}

class D8 extends BaseDice {
    public function __construct() {
        parent::__construct(8, 'd8');
    }
}

class D10 extends BaseDice {
    public function __construct() {
        parent::__construct(10, 'd10');
    }
}

class D12 extends BaseDice {
    public function __construct() {
        parent::__construct(12, 'd12');
    }
}

class D20 extends BaseDice {
    public function __construct() {
        parent::__construct(20, 'd20');
    }
    
    // Méthode spéciale pour les critiques au D20
    public function isCriticalHit($result) {
        return $result === 20;
    }
    
    public function isCriticalFail($result) {
        return $result === 1;
    }
}

class D100 extends BaseDice {
    public function __construct() {
        parent::__construct(100, 'd100');
    }
}

// Classe pour gérer la fabrique de dés
class D4Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D4();
    }
}

class D6Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D6();
    }
}

class D8Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D8();
    }
}

class D10Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D10();
    }
}

class D12Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D12();
    }
}

class D20Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D20();
    }
}

class D100Factory implements DiceFabrique {
    public function fabrique() : DiceInterface {
        return new D100();
    }
}

// Classe pour gérer les résultats de lancers avec modificateurs
class DiceRollResult {
    private $dice;
    private $count;
    private $results;
    private $total;
    private $modifier;
    private $showBestWorst;
    private $advantageMode;
    
    public function __construct(DiceInterface $dice, $count, array $results, $modifier = 0, $showBestWorst = false, $advantageMode = false) {
        $this->dice = $dice;
        $this->count = $count;
        $this->results = $results;
        $this->modifier = $modifier;
        $this->showBestWorst = $showBestWorst;
        $this->advantageMode = $advantageMode;
        
        // Calcul du total selon le mode
        if ($advantageMode && $dice->getName() === 'd20' && count($results) === 2) {
            // Mode avantage : prendre le meilleur dé
            $this->total = max($results);
        } else {
            // Mode normal : somme de tous les dés
            $this->total = array_sum($results);
        }
    }
    
    public function toArray() {
        $data = [
            'dice' => $this->dice->getName(),
            'count' => $this->count,
            'total' => $this->total,
            'modifier' => $this->modifier,
            'show_best_worst' => $this->showBestWorst,
            'advantage_mode' => $this->advantageMode
        ];
        
        if ($this->count === 1) {
            $data['result'] = $this->results[0];
        } else {
            $data['results'] = $this->results;
        }
        
        // Ajout d'informations spéciales pour le D20
        if ($this->dice instanceof D20) {
            // Pour le mode avantage, utiliser le meilleur résultat
            $effectiveResult = ($this->advantageMode && count($this->results) === 2) 
                ? max($this->results) 
                : $this->results[0];
                
            if ($this->count === 1 || ($this->advantageMode && count($this->results) === 2)) {
                $data['is_critical_hit'] = $this->dice->isCriticalHit($effectiveResult);
                $data['is_critical_fail'] = $this->dice->isCriticalFail($effectiveResult);
            }
        }
        
        // Ajouter les informations pour l'historique
        if ($this->showBestWorst && count($this->results) > 1) {
            $data['best_result'] = max($this->results);
            $data['worst_result'] = min($this->results);
        }
        
        return $data;
    }
    
    public function getTotal() {
        return $this->total;
    }
    
    public function getResults() {
        return $this->results;
    }
    
    public function getDice() {
        return $this->dice;
    }
    
    public function getCount() {
        return $this->count;
    }
    
    public function getModifier() {
        return $this->modifier;
    }
}

// Service pour gérer les lancers de dés avec nouvelles fonctionnalités
class DiceRollService {
    public static function rollDice($diceType, $count = 1, $save = true, $saveHistory = false, $modifier = 0, $showBestWorst = false, $advantageMode = false) {
        // Validation du nombre de dés
        $count = max(1, min(20, (int)$count));
        $modifier = max(-50, min(50, (int)$modifier)); // Limiter les modificateurs
    
        try {
            $dice = null;
        
            // Création du dé via la fabrique
            switch ($diceType){
                case 'd4':
                    $dice = (new D4Factory())->fabrique();
                    break;
                case 'd6':
                    $dice = (new D6Factory())->fabrique();
                    break;
                case 'd8':
                    $dice = (new D8Factory())->fabrique();
                    break;
                case 'd10':
                    $dice = (new D10Factory())->fabrique();
                    break;
                case 'd12':
                    $dice = (new D12Factory())->fabrique();
                    break;
                case 'd20':
                    $dice = (new D20Factory())->fabrique();
                    break;
                case 'd100':
                    $dice = (new D100Factory())->fabrique();
                    break;
                default:
                    throw new InvalidArgumentException("Type de dé non supporté: " . $diceType);
            }
            
            // Lancer des dés
            $results = $dice->rollMultiple($count);
        
            // Création du résultat avec les nouvelles options
            $rollResult = new DiceRollResult($dice, $count, $results, $modifier, $showBestWorst, $advantageMode);
        
            // Sauvegarde conditionnelle
            if ($save) {
                $manager = new DataManager();
                $manager->saveRollResult($rollResult, $saveHistory);
            }
        
            return $rollResult;
        
        } catch (InvalidArgumentException $e) {
            throw new Exception("Erreur lors du lancer: " . $e->getMessage());
        }
    }
}

// Gestionnaire de données avec historique amélioré
class DataManager {
    private $file = 'simple_dice_data.json';
    private $historyFile = 'dice_history.json';
    
    public function saveRollResult(DiceRollResult $rollResult, $saveHistory = false) {
        $data = $rollResult->toArray();
        
        // Sauvegarde du dernier résultat (comme avant)
        $saved = file_put_contents($this->file, json_encode($data)) !== false;
        
        // Sauvegarde dans l'historique si demandé
        if ($saveHistory) {
            $this->addToHistory($data);
        }
        
        return $saved;
    }
    
    private function addToHistory($data) {
        // Ajouter timestamp
        $data['timestamp'] = date('Y-m-d H:i:s');
        
        // Lire l'historique existant
        $history = $this->getHistory();
        
        // Ajouter le nouveau résultat au début
        array_unshift($history, $data);
        
        // Limiter à 100 entrées max
        if (count($history) > 100) {
            $history = array_slice($history, 0, 100);
        }
        
        // Sauvegarder l'historique
        return file_put_contents($this->historyFile, json_encode($history, JSON_PRETTY_PRINT)) !== false;
    }
    
    public function getResult() {
        return file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : null;
    }
    
    public function getHistory() {
        return file_exists($this->historyFile) ? json_decode(file_get_contents($this->historyFile), true) : [];
    }
    
    public function clearHistory() {
        return file_exists($this->historyFile) ? unlink($this->historyFile) : true;
    }
}

// === TRAITEMENT DES REQUÊTES ===

$manager = new DataManager();

if (($_POST['action'] ?? '') === 'roll') {
    try {
        $diceType = $_POST['dice'] ?? 'd20';
        $diceCount = $_POST['count'] ?? 1;
        $saveToFile = $_POST['save'] ?? true;
        $saveHistory = $_POST['history'] ?? false;
        
        // Nouveaux paramètres
        $modifier = (int)($_POST['modifier'] ?? 0);
        $showBestWorst = ($_POST['show_best_worst'] ?? '0') === '1';
        $advantageMode = ($_POST['advantage_mode'] ?? '0') === '1';
        
        // Utilisation du service avec les nouvelles options
        $rollResult = DiceRollService::rollDice(
            $diceType, 
            $diceCount, 
            $saveToFile, 
            $saveHistory, 
            $modifier, 
            $showBestWorst, 
            $advantageMode
        );
        
        // Réponse JSON
        $response = $rollResult->toArray();
        $response['success'] = true;
        $response['saved'] = $saveToFile;
        $response['history_saved'] = $saveHistory;
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

// Pour récupération périodique
if (($_GET['action'] ?? '') === 'get') {
    echo json_encode($manager->getResult() ?? []);
    exit;
}

// Route pour récupérer l'historique
if (($_GET['action'] ?? '') === 'history') {
    echo json_encode($manager->getHistory());
    exit;
}

// Route pour vider l'historique
if (($_POST['action'] ?? '') === 'clear_history') {
    $cleared = $manager->clearHistory();
    echo json_encode(['success' => $cleared]);
    exit;
}