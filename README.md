# 🎲 D – API de Lancer de Dés

## 📌 Présentation

Ce projet propose une **API PHP complète** permettant de simuler des lancers de dés courants (D4, D6, D8, D10, D12, D20, D100), avec des fonctionnalités avancées telles que :

- Application de modificateurs
- Modes spéciaux (avantage / désavantage)
- Affichage du meilleur et du pire résultat
- Gestion d’un historique persistant des lancers

L’API est conçue pour être utilisée facilement côté front-end via des requêtes **AJAX**.

---

## ⚙️ Fonctionnalités

- 🎲 Lancer un ou plusieurs dés parmi les types pris en charge.
- ➕ Appliquer un **modificateur** au total du lancer.
- 🆚 Mode **avantage** ou **désavantage** (D20) : conserve le meilleur ou le pire des deux lancers.
- 🔝 Affichage optionnel du **meilleur** et du **pire** résultat dans une série.
- 💾 Sauvegarde automatique du **dernier lancer** et de l’**historique** (jusqu’à 100 entrées).
- 🔄 Endpoints pour consulter ou **vider l’historique**.

---

## 🚀 Installation & Utilisation

1. **Assurez-vous que le serveur a les droits d’écriture** :
   > Les fichiers `simple_dice_data.json` (dernier résultat) et `dice_history.json` (historique) seront créés automatiquement.
2. **Effectuez des appels POST/GET** vers `api.php`.

### 📬 Exemples d’appels

#### Lancer un dé
```http
POST /develop/D/api.php
action=roll
dice=d20
count=2
modifier=1
show_best_worst=1
advantage_mode=1
save=1
history=1
```

#### Obtenir le dernier résultat
```http
GET /develop/D/api.php?action=get
```

#### Récupérer l’historique
```http
GET /develop/D/api.php?action=history
```

#### Vider l’historique
```http
POST /develop/D/api.php
action=clear_history
```

---

## 📁 Structure des fichiers

| Fichier                     | Description                                                       |
|-----------------------------|-------------------------------------------------------------------|
| `api.php`                  | Point d’entrée principal. Contient la logique de traitement, les classes, les routes API |
| `simple_dice_data.json`    | Dernier résultat enregistré                                      |
| `dice_history.json`        | Historique des 100 derniers lancers                             |
| `index.php`                | Exemple de front pour utiliser l'api                             |


---

## 🧠 Patrons de conception utilisés

### 🏭 1. **Factory Method**
- Chaque type de dé possède une fabrique dédiée (`D6Factory`, `D20Factory`, etc.).
- ➕ Permet d’ajouter facilement de nouveaux dés sans impacter la logique principale.

### 🧠 2. **Strategy**
- Tous les dés implémentent une interface (`DiceInterface`) avec des comportements spécifiques (`roll()`).
- ➕ Uniformise le traitement des types de dés.

### 🧰 3. **Service**
- `DiceRollService` centralise la logique métier liée aux lancers.
- ➕ Séparation nette entre les responsabilités (logique, données, interface).

### 🔒 4. **Singleton**
- `DataManager` suit un schéma singleton strict.
- ➕ Garantit une unique instance pour la gestion des fichiers JSON.

---

## 🛡️ Sécurité & Bonnes pratiques

- ✅ Les fichiers sont créés automatiquement si absents.
- ✅ Entrées utilisateur filtrées et limitées (modificateur de -50 à +50, max 20 dés).
- ✅ Historique limité à 100 entrées pour éviter les débordements mémoire.

---

## 🧱 Principes SOLID appliqués

Le code de cette API suit autant que possible les **principes SOLID**, garantissant une architecture propre, maintenable et extensible.

### 📌 S — Single Responsibility Principle (Responsabilité unique)
Chaque classe a une responsabilité bien définie :
- `DiceRollService` gère la logique métier des lancers,
- `DataManager` s’occupe uniquement de la lecture/écriture des fichiers,
- Chaque classe de dé (`D4`, `D6`, etc.) gère son propre comportement de lancer.

🔧 Cela facilite les modifications sans effet de bord.

---

### 📌 O — Open/Closed Principle (Ouvert/Fermé)
Les classes de dés sont **ouvertes à l’extension**, mais **fermées à la modification** :
- Pour ajouter un nouveau type de dé, il suffit de créer une nouvelle classe + fabrique.
- Aucun besoin de modifier les classes existantes ou le cœur de la logique (`DiceRollService`).

---

### 📌 L — Liskov Substitution Principle (Substitution de Liskov)
Toutes les classes de dés héritent de `BaseDice` et respectent le contrat défini par `DiceInterface`.  
✅ N’importe quelle sous-classe peut être utilisée en lieu et place de `DiceInterface` sans casser le fonctionnement du code.

---

### 📌 I — Interface Segregation Principle (Segregation d’interface)
L’interface `DiceInterface` reste simple et ciblée (`roll`, `rollMultiple`, etc.), évitant d’imposer des méthodes inutiles aux implémentations.  
✅ Chaque classe de dé n’implémente que ce qu’elle utilise réellement.

---

### 📌 D — Dependency Inversion Principle (Inversion des dépendances)
Le service `DiceRollService` et le reste du code dépendent d’abstractions (`DiceInterface`, `DiceFabrique`) et non de classes concrètes.  
✅ Cela permet de découpler les composants et facilite les tests ou les évolutions (ex: mock, nouveaux types de dés, etc.).

---


## 📏 Autres principes de développement appliqués

Au-delà de SOLID, plusieurs bonnes pratiques de développement ont été respectées dans cette API.

### 💡 KISS – *Keep It Simple, Stupid*
Le code reste simple et lisible :
- Pas de surcharge inutile,
- Une seule fonction = une seule tâche claire,
- Pas de complexité algorithmique superflue.

👉 Le code peut être compris et modifié facilement, même sans connaître tout le projet.

---

### 🔁 DRY – *Don’t Repeat Yourself*
Les comportements partagés (comme `rollMultiple()` ou la structure JSON des résultats) sont **centralisés** :
- Dans `BaseDice` pour les lancers communs,
- Dans `DiceRollResult` pour la structure de sortie,
- Dans `DiceRollService` pour la logique métier.

✅ Évite les répétitions, limite les erreurs et facilite la maintenance.

---

### 📝 WET – *Write Everything Twice* (évité ici)
Ce principe **n’est pas appliqué** : aucune duplication volontaire.  
Le code suit **DRY** au maximum.

---

### 🚫 YAGNI – *You Ain’t Gonna Need It*
Aucune fonctionnalité n’est codée **par anticipation** :
- L’implémentation reste proche des besoins réels (pas de surcharge technique inutile),
- Les extensions (comme un nouveau dé) ne sont ajoutées **que si besoin**.

✅ L’API reste légère, rapide à charger et facile à maintenir.

---
