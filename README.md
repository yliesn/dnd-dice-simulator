# D – API de Lancer de Dés

## Présentation

Ce dossier contient une API PHP permettant de simuler des lancers de dés (D4, D6, D8, D10, D12, D20, D100) avec gestion d’historique, modificateurs, modes spéciaux (avantage/désavantage), et sauvegarde des résultats.  
L’API est pensée pour être utilisée côté front-end via des requêtes AJAX.

---

## Fonctionnalités

- Lancer un ou plusieurs dés de différents types.
- Appliquer un modificateur au résultat.
- Mode « avantage » (pour D20) : garde le meilleur de deux lancers.
- Affichage du meilleur/pire résultat sur une série.
- Sauvegarde du dernier résultat et de l’historique (100 derniers lancers).
- Récupération et suppression de l’historique via l’API.

---

## Installation & Utilisation

1. **Placer le dossier `D` dans `develop/`** de votre projet web PHP.
2. **Vérifier les droits d’écriture** sur le dossier :  
   L’API crée automatiquement les fichiers `simple_dice_data.json` et `dice_history.json` si besoin.
3. **Appeler l’API** via des requêtes POST ou GET :

### Exemples de requêtes

- **Lancer un dé :**
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

- **Récupérer le dernier résultat :**
  ```http
  GET /develop/D/api.php?action=get
  ```

- **Récupérer l’historique :**
  ```http
  GET /develop/D/api.php?action=history
  ```

- **Vider l’historique :**
  ```http
  POST /develop/D/api.php
  action=clear_history
  ```

---

## Structure des fichiers

- `api.php` : point d’entrée unique, contient toute la logique (fabriques, services, gestion des fichiers JSON, routes API).
- `simple_dice_data.json` : dernier résultat sauvegardé.
- `dice_history.json` : historique des 100 derniers lancers.

---

## Patrons de conception utilisés

### 1. **Factory (Fabrique)**
- **Utilisation :** Chaque type de dé possède une fabrique dédiée (`D4Factory`, `D6Factory`, etc.) pour instancier le bon objet.
- **Intérêt :** Permet d’ajouter facilement de nouveaux types de dés sans modifier la logique principale.

### 2. **Strategy (Stratégie)**
- **Utilisation :** Tous les dés implémentent une interface commune (`DiceInterface`), chaque classe de dé définit son propre comportement de lancer.
- **Intérêt :** Permet de traiter tous les types de dés de façon uniforme dans le service.

### 3. **Service**
- **Utilisation :** La classe `DiceRollService` centralise la logique métier du lancer de dés.
- **Intérêt :** Sépare la logique métier de la gestion des données et de la présentation, facilitant la maintenance.

### 4. **Singleton (optionnel)**
- **Utilisation :** La classe `DataManager` pourrait être utilisée en singleton pour garantir une seule instance de gestion des fichiers.
- **Intérêt :** Centralise la gestion des accès aux fichiers JSON.

---

## Sécurité & Bonnes pratiques

- Les fichiers JSON sont créés automatiquement s’ils n’existent pas.
- Les entrées utilisateur sont validées et limitées (nombre de dés, modificateur).
- Les fichiers d’historique sont limités à 100 entrées pour éviter l’explosion de la taille.

---

## Personnalisation

- Pour ajouter un nouveau type de dé, créer une nouvelle classe et une fabrique correspondante.
- Adapter les limites (nombre de dés, taille historique) dans `api.php` si besoin.

---

## Auteur

© 2024 – Projet D (développé par Nejara Ylies)