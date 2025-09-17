# Plugin WordPress - Intégration Twitch

Un plugin WordPress complet pour intégrer Twitch sur votre site avec des shortcodes et des widgets Elementor.

## Fonctionnalités

- **Indicateur de statut** : Affiche si le streamer est en direct ou hors ligne
- **Lecteur principal** : Lecteur intégré qui affiche le live ou le dernier replay
- **Grille des replays** : Liste des derniers replays avec miniatures
- **Widgets Elementor** : Intégration complète avec Elementor
- **Mise en cache** : Optimisation des performances avec cache des données API

## Installation

1. Téléchargez le plugin dans le dossier `/wp-content/plugins/`
2. Activez le plugin dans l'administration WordPress
3. Allez dans **Réglages > Intégration Twitch** pour configurer vos identifiants API

### Pour les utilisateurs

1.  Téléchargez la dernière version du plugin sur la page des "Releases" : https://github.com/eberess/wordpress/releases.
2.  Dans votre tableau de bord WordPress, allez dans **Extensions > Ajouter**.
3.  Cliquez sur **Téléverser une extension**, puis choisissez le fichier .zip téléchargé.
4.  Activez le plugin.


## Configuration

### Identifiants API Twitch

1. Créez une application sur [Twitch Developers](https://dev.twitch.tv/console)
2. Récupérez votre **Client ID** et **Client Secret**
3. Configurez ces informations dans les réglages du plugin

### Paramètres requis

- **Nom de la chaîne Twitch** : Le nom d'utilisateur du streamer
- **Client ID** : Identifiant de votre application Twitch
- **Client Secret** : Clé secrète de votre application Twitch

## Utilisation

### Shortcodes

#### Indicateur de statut
```
[twitch_status_indicateur]
[twitch_status_indicateur rafraichir="oui"]
```

#### Lecteur principal
```
[twitch_lecteur_principal]
[twitch_lecteur_principal rafraichir="oui"]
```

#### Grille des replays
```
[twitch_derniers_replays]
[twitch_derniers_replays rafraichir="oui"]
```

### Widgets Elementor

Le plugin ajoute une catégorie **Twitch** dans Elementor avec six widgets :

1. **Indicateur de Statut Twitch**
   - Couleurs personnalisables (live/offline)
   - Tailles multiples (petite/normale/grande)
   - Styles variés (défaut/minimal/badge)
   - Option d'affichage du texte LIVE/OFFLINE

2. **Lecteur Principal Twitch**
   - Hauteur personnalisable
   - Mode debug pour les administrateurs

3. **Grille des Replays Twitch**
   - Nombre de replays configurable
   - Colonnes personnalisables
   - Taille d'images variable
   - Styles de cartes (défaut/ombre/bordure)
   - Options d'affichage (durée, type)
   - Couleurs personnalisables

4. **Compteur de Replays Twitch**
   - Texte personnalisable
   - Styles multiples (défaut/badge/encadré)
   - Couleurs configurables
   - Alignement responsive

5. **Statistiques Twitch**
   - Éléments sélectionnables (tout/live/replays/activité)
   - Styles d'affichage (liste/carte)
   - Couleurs et espacement personnalisables

6. **Dernier Replay Twitch**
   - Styles d'affichage (carte/minimal)
   - Tailles d'images configurables
   - Largeur maximale ajustable
   - Options de durée

Chaque widget dispose d'options avancées de personnalisation dans l'éditeur Elementor.

## Fonctionnalités techniques

- **Cache intelligent** : Les données API sont mises en cache pendant 5 minutes
- **Gestion d'erreurs** : Logs détaillés pour le débogage
- **Responsive** : Interface adaptée à tous les écrans
- **Performance** : Optimisé pour minimiser les appels API

## Support

Pour toute question ou problème, consultez les logs WordPress ou contactez moi.

## Changelog

### Version 1.0
- Version initiale
- Shortcodes de base
- Widgets Elementor
- Page de configuration
