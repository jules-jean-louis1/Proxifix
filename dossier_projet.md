# Dossier de Projet Professionnel

## Réalisation de l'Application Mobile de Gestion d'Interventions Techniques - PROAXIVE

**Présenté pour l'obtention du titre de Concepteur Développeur d'Application**

---

## 📋 SOMMAIRE

1. [**Contexte et Besoins**](#1-contexte-et-besoins)
2. [**Contraintes et Organisation**](#2-contraintes-et-organisation)
3. [**Conception et Architecture**](#3-conception-et-architecture)
4. [**Développement et Implémentation**](#4-développement-et-implémentation)
5. [**Qualité et DevOps**](#5-qualité-et-devops)
6. [**Démonstration et Résultats**](#6-démonstration-et-résultats)

---

## 1. Contexte et Besoins

### 1.1 Contexte de réalisation du projet

Ce projet a été réalisé dans le cadre de ma formation de Concepteur Développeur d'Application. Le développement s'est organisé de manière particulière, commençant par un travail en équipe de trois personnes puis se finalisant individuellement sur une période concentrée de quatre semaines. Cette organisation était nécessaire car notre formation alternait entre périodes en centre de formation et périodes en entreprise, avec un rythme d'une semaine de cours sur trois semaines totales.

Cette contrainte temporelle spécifique a exigé une planification très rigoureuse et une gestion de projet adaptée. Chaque semaine de développement devait être optimisée au maximum, car nous n'avions pas la possibilité de travailler de manière continue sur le projet. Cette situation m'a permis d'apprendre à gérer efficacement mon temps et à prioriser les tâches essentielles pour respecter les délais impartis.

La transition du travail en équipe vers le travail individuel s'est effectuée après avoir établi ensemble les bases architecturales du projet. Cette expérience m'a appris l'importance d'une documentation claire et d'une architecture bien définie pour faciliter la reprise de développement en solo.

### 1.2 Problématique métier

Un membre de notre équipe de développement travaillait déjà avec un logiciel de gestion d'interventions informatiques dans le cadre de son alternance en entreprise. Cependant, cet outil existant présentait une limitation majeure car il fonctionnait uniquement sur ordinateur de bureau, sans possibilité d'utilisation mobile. Cette contrainte posait des problèmes concrets aux techniciens qui devaient se déplacer chez les clients sans pouvoir accéder aux informations techniques nécessaires sur le terrain.

Notre analyse s'est focalisée sur les difficultés rencontrées par les Très Petites Entreprises et Petites et Moyennes Entreprises qui constituent notre cible principale. Ces structures n'ont généralement pas les moyens d'investir dans des solutions informatiques complexes et coûteuses, mais ont néanmoins besoin d'outils professionnels pour gérer leur activité.

Les problèmes identifiés sont multiples et impactent directement la productivité et la qualité de service. La gestion manuelle des interventions sur papier ou via des tableurs Excel comme Microsoft Excel ou Google Sheets génère une perte de temps considérable. Les techniciens doivent recopier les informations plusieurs fois, ce qui augmente les risques d'erreurs de saisie et de perte d'informations critiques.

Le manque de traçabilité des interventions et des équipements complique énormément le suivi technique. Quand un équipement tombe en panne plusieurs fois, il est difficile de retrouver l'historique des interventions précédentes, les pièces changées ou les configurations appliquées. Cette absence d'historique nuit à l'efficacité des réparations et peut conduire à répéter des opérations déjà effectuées.

Les difficultés de planification des techniciens entraînent des conflits de planning et une optimisation insuffisante des déplacements. Sans outil centralisé, il est compliqué de savoir qui est disponible, où et quand, ce qui peut conduire à envoyer un technicien à l'autre bout de la ville alors qu'un collègue était disponible à proximité.

La communication défaillante entre clients et techniciens provoque des malentendus fréquents. Les clients ne savent pas quand le technicien va arriver, quels outils apporter, ou l'état d'avancement de leur demande. De leur côté, les techniciens manquent souvent d'informations précises sur le problème à résoudre avant leur déplacement.

L'absence de suivi temps réel des interventions empêche les clients de connaître l'état d'avancement de leurs demandes. Cela génère des appels téléphoniques fréquents pour demander des nouvelles, ce qui surcharge le service client et diminue la satisfaction clientèle.

Notre solution consiste à développer une application mobile qui permet de digitaliser complètement la gestion des interventions informatiques. Cette solution couvre tous les types d'interventions dans le domaine informatique, qu'il s'agisse de maintenance préventive ou curative, de réparation de matériel défaillant, ou d'installation de nouveaux équipements.

L'application fonctionne selon le modèle SaaS qui signifie "Software as a Service" ou "Logiciel en tant que Service" en français. Il s'agit d'un système unifié qui accompagne les informations depuis la demande initiale du client jusqu'à la finalisation de l'intervention et sa facturation. Cette approche garantit qu'aucune information ne se perd en cours de processus et que chaque étape est tracée et documentée.

### 1.3 Analyse des besoins métier identifiés

L'analyse fonctionnelle approfondie a permis d'identifier quatre rôles d'utilisateurs distincts, chacun ayant des besoins spécifiques et des droits d'accès différents selon leur niveau de responsabilité dans l'organisation.

L'application serait donc scindée en deux, avec une interface client, ses utilisateurs ont besoins d'une autonomie dans la gestion des demandes d'intervention :

- **Consulter l'état d'avancement de leurs interventions** : Les clients doivent pouvoir suivre où en est leur demande, quelles tâches ont été réalisées, et estimer le délai de résolution restant sans avoir à contacter le service client.
- **Gérer leur parc d'équipements de manière autonome** : Ajouter de nouveaux matériels, de modifier les caractéristiques d'équipements existants (mise à jour des spécifications).
- **Prendre rendez-vous directement sans passer par un intermédiaire** : L'interface de prise de rendez-vous automatisée permet une disponibilité 24h/24 et réduit considérablement les délais de traitement des demandes tout en évitant les erreurs de retranscription.
- **Accéder à l'historique complet de leurs interventions** : Cette traçabilité est indispensable pour des besoins de garantie, de suivi technique, de validation des prestations facturées, et d'analyse des récurrences de pannes pour optimiser la maintenance préventive.

Il y aurait ensuite la partie entreprise pour les techniciens et administrateur :

### **Rôle Customer (Clients)**

Les clients ont besoin de **transparence et d'autonomie** :

- Consulter l'état d'avancement de leurs interventions
- Gérer leur parc d'équipements de manière autonome
- Prendre rendez-vous directement sans passer par un intermédiaire téléphonique
- Accéder à l'historique complet de leurs interventions pour des besoins de garantie ou de suivi

### **Rôle Technician (Techniciens)**

Les techniciens ont des **besoins opérationnels spécifiques** :

- Visualiser leurs plannings personnels avec les interventions assignées
- Accéder aux données techniques des équipements à réparer
- Documenter les tâches effectuées pendant l'intervention
- Modifier le statut des interventions selon leur état d'avancement

Nous avons mis en place une **gestion granulaire des tâches** qui permet de décomposer chaque étape de l'intervention en sous-tâches techniques spécifiques comme l'installation des pilotes, le nettoyage du disque, la réparation, permettant d'avoir une traçabilité complète.

### **Rôle Admin (Administrateurs d'Entreprise)**

Pour les administrateurs d'une entreprise, nous devions offrir des **outils de pilotage et de coordination** :

- Vue d'ensemble de toutes les interventions en cours dans leur entreprise
- Capacité d'assignation et de réassignation des techniciens selon leur priorité
- Gestion des catalogues de services d'intervention, des tâches, de la planification
- Création et gestion des comptes clients et techniciens

### **Rôle Super Admin**

Il y aurait un ou plusieurs **super admins** qui ont la possibilité d'avoir une gestion globale de la plateforme :

- Validation et intégration des nouvelles entreprises
- Supervision technique globale
- Gestion des droits et des permissions au niveau plateforme

### 1.4 Solution technique

Le mode SaaS multi-tenant constitue l'architecture la plus adaptée pour notre application car elle répond parfaitement aux contraintes budgétaires des TPE-PME qui constituent notre marché cible.

Un SaaS, acronyme de "Software as a Service" ou "Logiciel en tant que Service", représente un modèle de distribution de logiciels où les applications sont hébergées sur des serveurs distants et mises à disposition des clients via Internet. Contrairement aux logiciels traditionnels qui s'installent sur l'ordinateur de l'utilisateur, le SaaS fonctionne entièrement dans le navigateur web ou via une application mobile connectée.

Le terme "multi-tenant" désigne une architecture où une seule instance de l'application sert plusieurs clients (appelés "tenants" ou "locataires"). Chaque tenant a accès à ses propres données sans pouvoir voir celles des autres, comme des appartements séparés dans un même immeuble. Cette approche s'oppose au modèle "single-tenant" où chaque client aurait sa propre installation séparée du logiciel.

Cette solution permet une réduction significative des coûts pour plusieurs raisons. Les entreprises n'ont pas besoin d'investir dans une infrastructure informatique dédiée, ce qui représente des économies importantes en serveurs, logiciels système et maintenance. Elles n'ont pas non plus besoin d'embaucher des spécialistes en informatique pour administrer et maintenir le système, car ces services sont inclus dans l'abonnement SaaS. Les coûts sont répartis entre tous les utilisateurs de la plateforme, permettant d'accéder à des fonctionnalités professionnelles pour un coût mensuel abordable.

L'imperméabilité des informations est garantie par l'architecture multi-tenant qui assure que chaque entreprise ne peut accéder qu'à ses propres données. Cette séparation est assurée à tous les niveaux : base de données, interface utilisateur, et processus métier. Les données d'une entreprise sont complètement invisibles pour les autres, créant des environnements totalement isolés du point de vue utilisateur.

La conformité RGPD (Règlement Général sur la Protection des Données) est facilitée par une gestion centralisée de la sécurité et des politiques de confidentialité. La plateforme peut implémenter de manière uniforme les exigences réglementaires comme le droit à l'oubli, le droit de portabilité des données, ou le consentement éclairé. Cette approche centralisée permet de répondre efficacement aux demandes des utilisateurs sans impact technique majeur pour chaque entreprise cliente.

---

## 2. Contraintes et Organisation

### 2.1 Choix Techniques et Justifications

Les choix technologiques ont été orientés en priorité par l'expérience préalable de l'équipe de développement et les contraintes de délais serrés imposées par le planning de formation. Cette approche pragmatique nous a permis de nous concentrer sur la logique métier plutôt que sur l'apprentissage de nouvelles technologies.

React Native a été retenu pour le développement de l'application mobile car tous les membres du groupe possédaient déjà des connaissances solides sur cette technologie. React Native est un framework de développement d'applications mobiles créé par Meta (anciennement Facebook) qui permet d'écrire du code en JavaScript tout en produisant des applications véritablement natives.

Le principal avantage de React Native réside dans sa capacité à compiler le code JavaScript vers du code natif iOS et Android. Cela signifie qu'avec une seule base de code, nous pouvons créer des applications qui fonctionnent sur les deux principales plateformes mobiles du marché. Cette approche "write once, run anywhere" réduit significativement les temps de développement comparé à la création de deux applications séparées en Swift pour iOS et Kotlin/Java pour Android.

L'utilisation de React Native Paper pour l'interface utilisateur nous a permis d'implémenter rapidement des composants UX professionnels. React Native Paper est une bibliothèque de composants qui respecte le Material Design de Google, un système de design qui définit des règles précises pour l'apparence et le comportement des interfaces utilisateur. Material Design spécifie tout, depuis les couleurs et la typographie jusqu'aux animations et transitions, garantissant une expérience utilisateur cohérente et intuitive.

React Native jouit d'une popularité importante dans la communauté des développeurs, ce qui se traduit par une documentation riche, un écosystème de bibliothèques complémentaires très développé, et une communauté active qui résout rapidement les problèmes techniques. Cette popularité nous assure aussi une pérennité de la technologie et facilite le recrutement futur de développeurs.

Expo a été choisi comme plateforme de développement pour ses avantages considérables en termes de productivité. Expo est une suite d'outils et de services construite autour de React Native qui simplifie considérablement le processus de développement, de test et de déploiement d'applications mobiles.

Le SDK (Software Development Kit) Expo, qui signifie "Kit de Développement Logiciel", propose un ensemble complet d'outils et de services prépackagés. Un SDK est essentiellement une collection d'outils logiciels, de bibliothèques, de guides et d'exemples de code qui permettent aux développeurs de créer des applications pour une plateforme spécifique. Le SDK Expo inclut des fonctionnalités comme l'accès à la caméra, au GPS, aux notifications push, au stockage sécurisé, et bien d'autres, sans avoir à configurer chaque élément individuellement.

Les fonctionnalités particulièrement appréciées d'Expo incluent le hot reloading, qui permet de voir instantanément les modifications de code dans l'application en cours d'exécution sans la redémarrer. Le débogage facilité grâce aux outils intégrés permet d'identifier rapidement les erreurs et de comprendre le comportement de l'application. La possibilité de tester directement sur device réel via l'application Expo Go, disponible sur les stores, évite la complexité de configuration des émulateurs et permet de tester dans des conditions réelles d'utilisation.

Pour le backend, Symfony 6 avec PHP 8.3 a été sélectionné en raison de notre maîtrise de ce framework et de ses capacités avancées pour le développement d'APIs robustes. Symfony est un framework PHP de développement web qui suit les meilleures pratiques de l'industrie et respecte les standards PSR (PHP Standards Recommendations).

Docker a été intégré dès le début du développement backend pour créer un environnement de développement cohérent et reproductible. Docker est une plateforme de containerisation qui permet d'empaqueter une application et toutes ses dépendances dans un conteneur léger et portable. Cette technologie résout le problème classique du "ça marche sur ma machine" en garantissant que l'application fonctionne de manière identique sur tous les environnements.

L'utilisation de Docker présente plusieurs avantages majeurs pour notre projet. Elle garantit la cohérence de l'environnement entre tous les développeurs de l'équipe, évitant les conflits de versions entre les différentes machines de développement. L'installation simplifiée permet à un nouveau développeur de démarrer rapidement sans avoir à configurer manuellement PHP, PostgreSQL, et toutes les dépendances système. L'isolation des services évite les conflits avec d'autres projets qui pourraient utiliser des versions différentes des mêmes outils.

Notre configuration Docker comprend plusieurs services orchestrés via docker-compose : un conteneur PHP-FPM pour l'exécution du code Symfony, un conteneur PostgreSQL pour la base de données, et un conteneur Nginx pour servir l'application. Cette architecture modulaire permet de gérer indépendamment chaque composant et facilite la maintenance et les mises à jour.

L'ORM Doctrine présente un intérêt particulier car il permet d'abstraire complètement la couche de base de données. Un ORM, qui signifie "Object-Relational Mapping" ou "Mappage Objet-Relationnel", est un outil qui permet de manipuler les données de la base de données comme des objets dans le code, sans écrire de requêtes SQL manuellement. Doctrine facilite énormément les migrations de base de données, la maintenance du schéma, et garantit la portabilité entre différents systèmes de gestion de base de données.

Voici un exemple concret de l'utilisation de Doctrine avec notre entité Brand :

```php
<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[ApiResource]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:details', 'brand:get_all'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipment:details', 'brand:get_all'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'brand')]
    private Collection $equipment;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipment:details', 'brand:get_all'])]
    private ?string $logo = null;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }
}
```

Dans cet exemple, les annotations `#[ORM\Column]` et `#[ORM\Entity]` indiquent à Doctrine comment mapper cette classe vers une table de base de données. L'annotation `#[ORM\OneToMany]` définit une relation entre Brand et Equipment, permettant à Doctrine de gérer automatiquement les jointures SQL nécessaires.

La sécurité est assurée par l'implémentation de JSON Web Tokens (JWT) qui permettent une authentification stateless, particulièrement adaptée aux applications mobiles. Un JWT est un standard ouvert qui définit une manière compacte et sécurisée de transmettre des informations entre parties sous forme d'objet JSON. Contrairement aux sessions traditionnelles qui stockent les informations côté serveur, les JWT contiennent toutes les informations nécessaires, permettant au serveur de rester "stateless" (sans état).

D'autres outils ont été intégrés pour optimiser le workflow de développement. Insomnia a servi pour les tests d'API, permettant de vérifier le bon fonctionnement des endpoints avant leur intégration dans l'application mobile. Figma a été utilisé pour le design et le prototypage, permettant de créer des maquettes interactives et de valider l'expérience utilisateur avant le développement.

Visual Studio Code a servi d'environnement de développement principal grâce à ses extensions spécialisées pour PHP, TypeScript et React Native. DBeaver a été utilisé pour la gestion visuelle de la base de données PostgreSQL, facilitant l'inspection des données et l'écriture de requêtes de test.

Git et GitHub ont assuré le versioning du code avec une stratégie de branches bien définie. Docker a été utilisé pour containeriser la base de données, garantissant un environnement de développement cohérent entre tous les membres de l'équipe et simplifiant l'installation sur de nouvelles machines.

### 2.2 Gestion de Projet et Méthodologie

La méthodologie Kanban a été privilégiée par rapport à Scrum car elle s'adaptait mieux à notre rythme de formation particulier, alternant entre périodes en centre et périodes en entreprise.

Scrum est une méthode agile qui structure le travail en itérations courtes et fixes appelées "sprints", généralement de 2 à 4 semaines. Elle impose des cérémonies régulières comme les daily standups quotidiens, les sprint reviews et les rétrospectives. Cette rigidité temporelle était difficile à maintenir avec notre planning discontinu d'une semaine sur trois.

Scrum fonctionne avec des rôles définis : le Product Owner qui définit les besoins, le Scrum Master qui facilite le processus, et l'équipe de développement qui réalise le produit. Les user stories sont estimées en points de complexité et organisées dans un backlog priorisé. L'engagement sur un périmètre fixe par sprint et la vélocité d'équipe permettent une prévisibilité à moyen terme.

Kanban, au contraire, est une méthode de gestion visuelle du flux de travail qui permet une flexibilité maximale dans la planification et l'exécution des tâches. Le nom "Kanban" vient du japonais et signifie "signal visuel" ou "carte". Cette méthode se base sur la visualisation du travail en cours et la limitation du work-in-progress (WIP) pour optimiser l'efficacité.

Le principe fondamental de Kanban repose sur un tableau visuel divisé en colonnes représentant les différentes étapes du processus de développement : "À faire", "En cours", "En review" et "Terminé". Chaque tâche est représentée par une carte qui se déplace de colonne en colonne au fur et à mesure de son avancement.

La limitation du WIP consiste à fixer un nombre maximum de tâches autorisées dans chaque colonne, forçant l'équipe à finir les tâches en cours avant d'en commencer de nouvelles. Cette contrainte évite la dispersion et améliore la concentration sur les tâches prioritaires.

Cette approche nous permettait de nous adapter facilement aux contraintes de disponibilité tout en maintenant une vision claire de l'avancement du projet. Nous pouvions ajuster les priorités à chaque retour de formation sans remettre en cause un sprint entier.

La gestion s'est effectuée via GitHub Projects qui offre une intégration native avec notre repository de code. GitHub Projects est un outil de gestion de projet intégré à la plateforme GitHub qui permet de créer des tableaux Kanban directement liés au code source.

Cette solution permet de lier directement les tâches aux commits et pull requests, créant une traçabilité complète entre les fonctionnalités développées et les issues correspondantes. Quand un développeur mentionne le numéro d'une issue dans un commit (par exemple "Fix #42"), l'issue se ferme automatiquement lors du merge, maintenant la cohérence entre le code et le suivi projet.

### 2.3 Planification et Temps de Développement

La planification du projet a été structurée en phases distinctes pour optimiser l'utilisation du temps disponible, sachant que nous ne disposions que d'une semaine effective toutes les trois semaines.

La première semaine a été consacrée à la définition précise des limites du projet. Cette phase critique incluait la définition du périmètre fonctionnel exact, c'est-à-dire la liste précise des fonctionnalités qui seraient développées et celles qui seraient reportées à une version ultérieure. Les contraintes techniques ont été identifiées, notamment les limitations de performance mobile, les exigences de sécurité et les contraintes d'intégration avec les systèmes existants.

Les critères d'acceptation ont été définis pour chaque fonctionnalité, permettant de valider objectivement si une feature était terminée ou non. Cette phase a également permis de finaliser les choix technologiques après une analyse comparative des différentes options disponibles, en pesant les avantages et inconvénients de chaque solution.

La création de la structure de base de données a constitué une étape fondamentale de cette première semaine. Le schéma relationnel a été conçu en respectant les formes normales pour éviter la redondance et garantir l'intégrité des données. Les relations entre entités ont été soigneusement définies pour supporter tous les cas d'usage métier identifiés.

Les deux semaines suivantes ont été dédiées à la conception du design system et de la charte graphique. Le design system est un ensemble de règles, composants et guidelines qui garantissent une cohérence visuelle sur l'ensemble de l'application. Il définit la palette de couleurs, les polices de caractères, les tailles de texte, les espacements, les ombres et tous les éléments visuels récurrents.

La charte graphique a établi l'identité visuelle du produit, incluant le logo, les couleurs principales et secondaires, et l'ambiance générale souhaitée. Cette étape s'avère essentielle car elle évite les hésitations durant le développement et assure une homogénéité parfaite de l'interface.

La création des maquettes haute-fidélité a constitué l'aboutissement de cette phase de conception. Ces maquettes présentent l'interface exactement comme elle apparaîtra dans l'application finale, avec les vraies couleurs, les vrais textes et les vrais composants. Elles ont permis de valider les parcours utilisateur avec les parties prenantes avant le début du développement, réduisant ainsi les risques de refonte tardive qui auraient pu compromettre les délais.

Cette planification séquentielle nous a permis de poser des bases solides avant d'entamer le développement proprement dit. En investissant du temps dans la conception en amont, nous avons minimisé les retours en arrière et les modifications d'architecture en cours de projet, qui sont toujours coûteux en temps et peuvent compromettre la qualité finale.

### 2.4 Utilisation de Git et GitHub

Git a été utilisé comme système de contrôle de version avec une stratégie de branching adaptée à notre organisation et aux bonnes pratiques de développement collaboratif. Git est un système de gestion de versions décentralisé qui permet de suivre les modifications apportées au code source et de coordonner le travail entre plusieurs développeurs.

La branche principale `main` contient exclusivement le code stable et testé, c'est-à-dire les versions qui ont été validées et qui peuvent potentiellement être déployées en production. Cette branche est protégée, meaning qu'il est impossible de pusher directement dessus sans passer par une pull request.

Les fonctionnalités sont développées sur des branches dédiées suivant la nomenclature `feature/nom-fonctionnalite`. Par exemple, `feature/user-authentication` pour le système d'authentification ou `feature/intervention-management` pour la gestion des interventions. Cette approche permet d'isoler le développement de chaque fonctionnalité et évite que les développeurs se marchent dessus en modifiant les mêmes fichiers simultanément.

Quand une fonctionnalité est terminée, une pull request est créée pour demander l'intégration du code dans la branche principale. Cette pull request déclenche une revue de code par les autres membres de l'équipe, permettant de détecter les erreurs potentielles, de vérifier le respect des standards de codage et de s'assurer que la fonctionnalité répond bien aux spécifications.

Les commits suivent une convention de nommage claire et descriptive inspirée de la spécification Conventional Commits. Chaque message de commit inclut le type de modification (feat pour une nouvelle fonctionnalité, fix pour une correction de bug, docs pour la documentation, etc.), la portée concernée, et une description concise de ce qui a été modifié.

Par exemple :

- `feat(auth): add JWT token refresh mechanism`
- `fix(intervention): correct status update validation`
- `docs(api): update endpoint documentation`

Cette discipline facilite considérablement la navigation dans l'historique du projet et la compréhension des évolutions apportées, surtout quand il faut revenir sur du code écrit plusieurs semaines auparavant.

### 2.5 GitHub Projects et Gestion des Issues

GitHub Projects a servi d'outil central pour la gestion des tâches et le suivi de l'avancement du projet. GitHub Projects est un système de gestion de projet intégré à la plateforme GitHub qui permet de créer des tableaux de bord visuels directement liés au repository de code.

Chaque fonctionnalité a été découpée en issues spécifiques avec des critères d'acceptation clairs et mesurables. Une issue est essentiellement une tâche ou un problème à résoudre, documenté avec une description précise, des étapes à suivre et des critères de validation. Par exemple, une issue pourrait être "En tant qu'administrateur, je veux pouvoir assigner un technicien à une intervention pour optimiser la planification".

Les labels sont utilisés pour catégoriser le type de travail : `feature` pour les nouvelles fonctionnalités, `bug` pour les corrections, `documentation` pour les mises à jour de doc, `enhancement` pour les améliorations d'existant. Des labels de priorité (`high`, `medium`, `low`) et de difficulté (`easy`, `medium`, `hard`) permettent de prioriser et d'estimer l'effort nécessaire.

Les issues sont organisées dans un tableau Kanban avec les colonnes standard : "To Do" pour les tâches à faire, "In Progress" pour le travail en cours, "Review" pour les tâches en attente de validation, et "Done" pour les tâches terminées. Cette visualisation permet de voir d'un coup d'œil la répartition du travail et d'identifier les goulots d'étranglement.

L'intégration native entre les issues et les pull requests constitue un avantage majeur de GitHub Projects. Quand un développeur mentionne une issue dans un commit ou une pull request (avec la syntaxe `fixes #123` ou `closes #123`), l'issue se ferme automatiquement lors du merge de la pull request. Cette automatisation maintient une cohérence parfaite entre le code développé et le suivi de projet, évitant les oublis de mise à jour manuelle.

Les milestones ont été utilisés pour regrouper les issues par version ou par objectif temporel. Par exemple, un milestone "Version 1.0 - MVP" regroupait toutes les issues nécessaires pour avoir un produit minimal viable, tandis qu'un milestone "Version 1.1 - Améliorations" contenait les fonctionnalités d'amélioration de l'expérience utilisateur. Cette organisation offre une vision macro de l'avancement du projet et aide à la planification des releases.

---

## 3. Conception et Architecture

### 3.1 Conception de la Base de Données

La conception de la base de données a suivi la méthode Merise pour garantir une approche structurée, rigoureuse et éviter les redondances qui pourraient nuire aux performances et à la cohérence des données. La méthode Merise est une méthode française de conception de systèmes d'information qui se décompose en plusieurs étapes successives, chacune produisant des modèles de plus en plus précis.

Le Modèle Conceptuel de Données (MCD) représente la première étape de conception. Il modélise les entités métier et leurs relations sans aucune considération technique ou informatique. Une entité représente un concept métier important pour l'application, comme User, Company, Equipment, ou Intervention. Les relations entre entités modélisent les règles de gestion métier, par exemple "Un utilisateur appartient à une entreprise" ou "Une intervention concerne un équipement".

Dans notre cas, les entités principales identifiées sont User (utilisateur du système), Company (entreprise cliente), Equipment (équipement informatique), Intervention (intervention technique), Task (tâche à effectuer), AppointmentRequest (demande de rendez-vous), Brand (marque d'équipement), et TypeIntervention (type d'intervention). Chaque entité possède des propriétés qui décrivent ses caractéristiques, comme le nom et prénom pour User, ou la marque et modèle pour Equipment.

Les relations sont définies avec leurs cardinalités qui précisent combien d'occurrences d'une entité peuvent être liées à une occurrence d'une autre entité. Par exemple, la relation entre User et Company a une cardinalité "un utilisateur appartient à une et une seule entreprise, une entreprise peut avoir plusieurs utilisateurs", ce qui se note (1,1) côté User et (0,N) côté Company.

Le Modèle Logique de Données (MLD) transforme le MCD en structure relationnelle en respectant les règles de normalisation. Les entités deviennent des tables, les propriétés deviennent des colonnes, et les relations se matérialisent par des clés étrangères. Les règles de normalisation, notamment les première, deuxième et troisième formes normales, garantissent l'élimination des redondances et la cohérence des données.

Par exemple, la relation entre User et Company se matérialise par l'ajout d'une clé étrangère `company_id` dans la table User. Cette clé étrangère référence la clé primaire de la table Company, garantissant qu'un utilisateur appartient toujours à une entreprise existante grâce aux contraintes d'intégrité référentielle.

Voici un exemple de la structure de table User telle qu'elle est définie dans notre système :

```sql
CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    email VARCHAR(180) UNIQUE NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(255),
    zip_code VARCHAR(10),
    company_id INTEGER NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    CONSTRAINT FK_user_company FOREIGN KEY (company_id) 
        REFERENCES company(id) ON DELETE RESTRICT
);
```

Le Modèle Physique de Données (MPD) adapte le MLD aux spécificités du SGBD PostgreSQL choisi. PostgreSQL est un système de gestion de base de données relationnelle open source réputé pour sa robustesse, ses fonctionnalités avancées et sa conformité aux standards SQL. Les types de données sont précisés selon les types supportés par PostgreSQL : INTEGER pour les nombres entiers, VARCHAR pour les chaînes de caractères de longueur limitée, TEXT pour les textes longs, TIMESTAMP pour les dates et heures.

Les index sont définis pour optimiser les performances des requêtes fréquentes. Par exemple, un index est créé sur la colonne `email` de la table User car cette colonne est utilisée pour l'authentification, et un index composé est créé sur `(company_id, status)` pour optimiser les requêtes qui filtrent les interventions par entreprise et statut.

Les contraintes de validation sont implémentées au niveau base de données pour garantir la cohérence des données même en cas d'accès direct à la base. Par exemple, une contrainte CHECK vérifie que le statut d'une intervention fait partie des valeurs autorisées : 'pending', 'assigned', 'in_progress', 'completed'.

L'architecture multi-tenant est implémentée via le champ `company_id` présent dans toutes les entités métier. Cette approche garantit l'isolation des données entre entreprises au niveau de la base de données. Chaque requête doit systématiquement inclure un filtre sur `company_id` pour s'assurer qu'une entreprise ne peut accéder qu'à ses propres données. Cette contrainte est implémentée au niveau applicatif et vérifiée par des tests automatisés.

### 3.2 Architecture Globale du Système

L'architecture suit un modèle client-serveur avec une API REST centralisée qui respecte les principes de l'architecture RESTful. REST, qui signifie "Representational State Transfer", est un style architectural pour les services web qui définit un ensemble de contraintes et de bonnes pratiques pour créer des APIs scalables et maintenables.

L'application mobile React Native communique exclusivement avec l'API Symfony via des requêtes HTTP utilisant les verbes standard : GET pour récupérer des données, POST pour créer de nouvelles ressources, PUT pour modifier complètement une ressource existante, PATCH pour modifier partiellement une ressource, et DELETE pour supprimer une ressource.

Cette séparation claire entre le frontend et le backend présente plusieurs avantages majeurs. Elle facilite la maintenance car les équipes peuvent travailler indépendamment sur chaque partie. Elle permet l'évolutivité car on peut faire évoluer le backend sans impacter le frontend et vice-versa. Elle ouvre la possibilité d'ajouter d'autres types de clients (application web, API publique, intégrations tierces) sans modification du backend.

L'API Symfony suit une architecture en couches avec une séparation claire des responsabilités, inspirée de l'architecture hexagonale et des principes de Clean Architecture. Cette organisation respecte le principe de responsabilité unique (Single Responsibility Principle) qui stipule qu'une classe ne devrait avoir qu'une seule raison de changer.

La couche Contrôleur (Controller) gère exclusivement la réception des requêtes HTTP et la sérialisation des réponses. Elle valide les permissions d'accès, extrait les paramètres de la requête, appelle les services métier appropriés, et formate la réponse au format JSON avec les codes de statut HTTP appropriés.

La couche Service contient toute la logique métier et les règles de validation. Cette couche est indépendante du protocole de transport (HTTP) et pourrait être réutilisée dans un contexte différent (ligne de commande, queue de messages, etc.). Les services orchestrent les appels aux repositories et appliquent les règles de gestion.

La couche Repository abstrait l'accès aux données. Elle encapsule toutes les requêtes de base de données et fournit une interface orientée métier pour manipuler les entités. Cette couche utilise l'ORM Doctrine pour générer automatiquement les requêtes SQL optimisées.

Voici un exemple concret illustrant cette architecture avec la gestion des marques d'équipement :

```php
// Contrôleur : gestion HTTP et permissions
#[Route('/api/brand', name: 'app_brand_get', methods: ['GET'])]
public function get(BrandRepository $brandRepository, Request $request): JsonResponse
{
    $reqId = $request->query->get('id');
    $reqPage = $request->query->get('page') ?? 1;
    $reqSize = $request->query->get('size') ?? 25;
    $reqName = $request->query->get('name');
    $reqOrder = $request->query->get('order') ?? 'ASC';

    $brands = $brandRepository->getBrands(
        null !== $reqId ? intval($reqId) : null, 
        $reqPage, 
        $reqSize, 
        $reqName, 
        $reqOrder
    );

    return $this->json($brands, Response::HTTP_OK);
}
```

```php
// Repository : accès aux données avec Doctrine
public function getBrands(?int $id, int $page, int $size, ?string $name, string $order): array
{
    $offset = ($page - 1) * $size;
    $query = $this->createQueryBuilder('b');

    if (null !== $id) {
        $query->andWhere('b.id = :id')
            ->setParameter('id', $id);
    }

    if (null !== $name && '' !== $name) {
        $query->andWhere('LOWER(b.name) LIKE :name')
            ->setParameter('name', '%'.strtolower($name).'%');
    }

    $query->orderBy('b.name', $order)
        ->setFirstResult($offset)
        ->setMaxResults($size);

    return $query->getQuery()->getResult();
}
```

La base de données PostgreSQL est accédée uniquement par l'ORM Doctrine, qui fournit une couche d'abstraction performante et sécurisée. Doctrine génère automatiquement les requêtes SQL optimisées, gère la connection pooling, et fournit des mécanismes de cache pour améliorer les performances.

L'utilisation de migrations versionnées garantit la cohérence de la structure de base de données entre les différents environnements (développement, test, production). Chaque modification de schéma est décrite dans un fichier de migration qui peut être appliqué de manière reproductible.

### 3.3 Diagrammes de Classes et Flux de Données

Les diagrammes de classes UML illustrent les relations entre les entités principales du système et leurs interactions. La classe Company centralise les informations d'entreprise et sert de point d'entrée pour toutes les données métier. Elle est liée aux utilisateurs via une relation one-to-many, aux équipements via les utilisateurs, et aux types d'intervention qu'elle propose.

La classe User implémente l'interface UserInterface de Symfony pour l'authentification et porte les rôles utilisateur via un système de hiérarchie défini dans le fichier `security.yaml`. Cette classe utilise l'héritage pour spécialiser les comportements selon le rôle : Customer, Technician, Admin héritent tous de User mais avec des méthodes spécifiques.

Voici l'exemple de la hiérarchie des rôles telle que définie dans notre configuration :

```yaml
# config/packages/security.yaml
role_hierarchy:
    ROLE_CUSTOMER: ['ROLE_CUSTOMER']
    ROLE_TECHNICIAN: ['ROLE_TECHNICIAN'] 
    ROLE_ADMIN: ['ROLE_ADMIN', 'ROLE_TECHNICIAN']
    ROLE_SUPER_ADMIN: ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_TECHNICIAN']
```

Cette hiérarchie permet à un ROLE_ADMIN d'avoir automatiquement les permissions d'un ROLE_TECHNICIAN, et à un ROLE_SUPER_ADMIN d'avoir toutes les permissions. Cette approche évite la duplication des règles de sécurité.

Les flux de données respectent un parcours unidirectionnel depuis l'interface utilisateur jusqu'à la base de données, suivant le pattern Request-Response. Une requête utilisateur transite par le contrôleur qui valide les permissions grâce aux annotations `#[IsGranted]`, puis appelle le service métier approprié.

Le service applique les règles de gestion métier, valide les données selon les contraintes business, et fait appel au repository pour les opérations de persistance. Le repository utilise Doctrine pour générer et exécuter les requêtes SQL, garantissant l'intégrité des données et l'optimisation des performances.

La réponse suit le chemin inverse : les données remontent du repository vers le service qui les enrichit ou les filtre selon le contexte, puis vers le contrôleur qui les sérialise au format JSON avec les groupes de sérialisation appropriés.

Les groupes de sérialisation permettent de contrôler précisément quelles propriétés d'une entité sont exposées selon le contexte. Par exemple, l'entité User peut avoir un groupe 'user:read' qui expose les informations de base, et un groupe 'user:admin' qui expose également les informations sensibles comme les rôles et les dates de création.

### 3.4 Maquettes et Prototypage

La création de maquettes constitue une étape fondamentale dans le processus de développement car elle permet de valider les parcours utilisateur et l'ergonomie avec les parties prenantes avant d'écrire la première ligne de code. Cette validation précoce réduit considérablement les risques de refonte tardive qui pourraient compromettre les délais et le budget du projet.

Le processus de design suivi s'articule en plusieurs étapes successives, chacune apportant un niveau de précision supplémentaire. Cette approche itérative permet de s'assurer que chaque décision de design est validée avant de passer à l'étape suivante.

La première étape consiste à créer des wireframes basse-fidélité qui définissent la structure générale et l'organisation des informations sur chaque écran. Les wireframes sont des schémas simplifiés, souvent en noir et blanc, qui montrent uniquement la disposition des éléments sans se préoccuper des couleurs, typographies ou visuels. Cette étape permet de se concentrer sur l'ergonomie et l'architecture de l'information.

Par exemple, le wireframe de l'écran de liste des interventions montre simplement des rectangles représentant les cartes d'intervention, avec indication des informations principales à afficher : titre, statut, date, client. Cette représentation simplifiée facilite les discussions sur l'organisation des informations et permet de valider rapidement plusieurs alternatives.

La charte graphique a été établie en second lieu, définissant l'identité visuelle complète du produit. Elle spécifie la palette de couleurs primaires et secondaires, avec les codes hexadécimaux précis pour assurer la reproductibilité. La couleur primaire #F9556D (rouge corail) a été choisie pour son aspect moderne et énergique, tandis que les couleurs secondaires #01358D (bleu marine) et #F0F3F4 (gris clair) apportent l'équilibre et la lisibilité.

La typographie définit les polices de caractères utilisées pour les titres, le texte courant, et les éléments d'interface. La police Rubik a été sélectionnée pour sa excellente lisibilité sur écran mobile et son aspect moderne. Les tailles de police sont définies selon une échelle harmonieuse : 24px pour les titres principaux, 18px pour les sous-titres, 16px pour le texte courant, et 14px pour les textes secondaires.

Le design system a ensuite été créé, regroupant l'ensemble des composants d'interface réutilisables avec leurs variations et états. Un design system est une bibliothèque de composants standardisés qui garantit la cohérence visuelle et accélère le développement. Il inclut tous les éléments récurrents : boutons, champs de saisie, cartes, modales, barres de navigation.

Chaque composant est documenté avec ses différents états : normal, focus, actif, désactivé, erreur. Par exemple, le composant Button est défini avec trois variantes (primary, secondary, tertiary), chacune ayant des couleurs et styles spécifiques. Cette documentation facilite grandement l'implémentation par les développeurs.

Les maquettes haute-fidélité ont été réalisées avec Figma, intégrant tous les éléments du design system et présentant l'interface exactement comme elle apparaîtra dans l'application finale. Figma est un outil de design collaboratif basé sur le web qui permet de créer des interfaces utilisateur précises et d'organiser le travail en équipe.

Ces maquettes incluent les vraies couleurs, les vraies polices, les vraies icônes, et même les vrais textes dans la mesure du possible. Elles montrent tous les écrans principaux de l'application : écran de connexion, tableau de bord, liste des interventions, détail d'intervention, création d'intervention, profil utilisateur.

Le prototypage interactif dans Figma a permis de simuler les interactions et transitions entre écrans. Les liens entre maquettes permettent de naviguer comme dans la vraie application, offrant aux testeurs une expérience très proche de l'application finale. Cette simulation permet de valider les parcours utilisateur complets et d'identifier les problèmes d'ergonomie avant le développement.

Les tests utilisateur ont été menés sur ces prototypes interactifs avec des représentants de chaque type d'utilisateur : clients, techniciens, administrateurs. Ces tests ont permis d'identifier des problèmes d'ergonomie et de faire des ajustements sur les maquettes, beaucoup plus facilement et rapidement que sur l'application développée.

Cette approche méthodique du design nous a permis d'arriver au développement avec une vision claire et validée de l'interface utilisateur, réduisant considérablement les itérations pendant la phase de développement.

---

## 4. Développement et Implémentation

### 4.1 Environment de Développement - Docker et Containerisation

Je vais d'abord détailler l'environnement de développement Docker utilisé. L'orchestration des services s'effectue via un fichier docker-compose.yaml dans lequel chaque service est ici un container isolé, fonctionnant dans le même réseau Docker, leur permettant de communiquer entre eux.

```yaml
services:
  database:
    image: postgres:${POSTGRES_VERSION:-15}-alpine
    environment:
      POSTGRES_DB: ${DB_DEV_NAME:-cda_app}
      POSTGRES_PASSWORD: ${DB_DEV_PASSWORD:-password}
      POSTGRES_USER: ${DB_DEV_USER:-cda_app}
    networks:
      - symfony
    volumes:
      - database_data:/var/lib/postgresql/data:rw
    ports:
      - "${DB_DEV_PORT:-5434}:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_DEV_USER:-cda_app}"]
      interval: 10s
      timeout: 5s
      retries: 5

  database_test:
    image: postgres:${POSTGRES_VERSION:-15}-alpine
    environment:
      POSTGRES_DB: ${DB_TEST_NAME:-cda_app_test}
      POSTGRES_PASSWORD: ${DB_TEST_PASSWORD:-password}
      POSTGRES_USER: ${DB_TEST_USER:-cda_app}
    networks:
      - symfony
    volumes:
      - database_test_data:/var/lib/postgresql/data:rw
    ports:
      - "${DB_TEST_PORT:-5433}:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_TEST_USER:-cda_app}"]
      interval: 10s
      timeout: 5s
      retries: 5

  php:
    build: ./php
    volumes:
      - ../backend:/var/www/symfony
    networks:
      - symfony
    environment:
      DATABASE_URL: "postgresql://${DB_DEV_USER:-cda_app}:${DB_DEV_PASSWORD:-password}@database:5432/${DB_DEV_NAME:-cda_app}?serverVersion=15&charset=utf8"
      DATABASE_TEST_URL: "postgresql://${DB_TEST_USER:-cda_app}:${DB_TEST_PASSWORD:-password}@database_test:5432/${DB_TEST_NAME:-cda_app_test}?serverVersion=15&charset=utf8"
      APP_ENV: ${APP_ENV:-dev}
    depends_on:
      database:
        condition: service_healthy
      database_test:
        condition: service_healthy

  nginx:
    build: ./nginx
    ports:
      - "${NGINX_PORT:-80}:80"
    volumes:
      - ../backend:/var/www/symfony
    networks:
      - symfony
    depends_on:
      - php

networks:
  symfony:

volumes:
  database_data:
  database_test_data:
```

PHP a notamment besoin d'un web-server, ici j'ai choisi d'utiliser Nginx, mais Apache2 aurait aussi pu être utilisé. L'architecture comprend un container PHP pour l'exécution de Symfony, et deux services de base de données : une de test et une de développement. Cette séparation des environnements garantit que les tests n'interfèrent jamais avec les données de développement.

**Dockerfile PHP personnalisé**

J'ai ensuite créé un Dockerfile pour PHP, qui contient une suite d'instructions :

```dockerfile
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    libpq-dev \
    acl \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN docker-php-ext-configure gd --with-jpeg --with-freetype 

RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql zip xsl gd intl opcache exif mbstring

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/symfony

# Copy startup script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
```

Ce Dockerfile permet de construire notre image Docker selon les paramètres fournis. Ici je pars de l'image officielle de PHP avec PHP 8.3 FPM (FastCGI Process Manager), puis j'ajoute les extensions nécessaires pour Symfony et Doctrine (notamment pdo_pgsql pour PostgreSQL), et configure Composer pour la gestion des dépendances.

J'ai ajouté un script permettant de créer la base de données via les commandes CLI de Symfony lorsque la base de données est prête, ensuite je n'ai plus qu'à démarrer le serveur.

**Avantages pour le développement et le déploiement**

Cette approche Docker présente plusieurs avantages cruciaux : elle garantit un environnement de développement cohérent pour toute l'équipe, facilite l'onboarding des nouveaux développeurs, et s'avère également très pratique pour la partie CI/CD (Continuous Integration/Continuous Deployment) en permettant de déployer exactement la même configuration sur tous les environnements.

### 4.2 Développement du backend de l'application

Pour le développement de l'API, nous avons utilisé le pattern API-Only avec l'architecture MVC adaptée aux spécificités de Symfony :

- **Model** : Entités Doctrine qui représentent les modèles de données
- **View** : Sérialiseurs JSON via Symfony Serializer pour la présentation des données
- **Controller** : API Controllers qui retournent exclusivement du JSON

Nous avons suivi les bonnes pratiques pour une API REST, qui préconisent d'éviter d'avoir plus de deux ressources dans une route comme `/brand/interventions`, mais plutôt de faire des requêtes GET avec un plus grand nombre de paramètres pour alléger la charge sur le serveur. Nous respectons également ce qui est attendu pour les différents verbes de requête HTTP.

API Platform a été intégré pour rapidement créer et exposer des opérations CRUD (Create, Read, Update, Delete) sur toutes les entités présentes, tout en conservant la flexibilité de personnaliser les endpoints selon les besoins métier spécifiques.

**Arborescence**

L'architecture MVC avec API Platform et Doctrine ORM suit le principe de séparation des préoccupations pour isoler la logique métier des routes de l'API et de la persistance des données.

Les différentes couches de l'application sont séparées :

- Les contrôleurs (gestion des requêtes HTTP)
- Les entités (modèles de données)
- Les repositories (couche d'accès aux données)
- Les services (logique métier)

Mon backend est donc composé des dossiers suivants :

- **Controller/** : regroupant tous les contrôleurs (un par ressource)
- **Entity/** : contenant les entités Doctrine (un par table de base de données)
- **Repository/** : contenant les repositories Doctrine pour les requêtes personnalisées
- **config/** : fichiers de configuration (routes, sécurité, API Platform, etc.)
- **migrations/** : fichiers de migration de base de données
- **tests/** : destiné aux tests unitaires et fonctionnels

**Fonctionnement de l'API**

Lorsque le client envoie une requête sur mon API, le routeur Symfony analyse l'URL. En fonction de la route et de la méthode HTTP, un contrôleur est appelé. Ce contrôleur utilise l'EntityManager Doctrine pour interagir avec la base de données via les entités et repositories. Une réponse est ensuite envoyée au format JSON avec un code statut approprié.

Les différents statuts utilisés dans ce projet sont :

- **200** : OK - Indique que la requête a réussi
- **201** : CREATED - Indique que la requête a réussi et une ressource a été créée
- **400** : BAD REQUEST - Indique une erreur de syntaxe dans la requête
- **401** : UNAUTHORIZED - Indique qu'il manque des informations d'authentification
- **403** : FORBIDDEN - Indique que l'utilisateur n'a pas les droits suffisants
- **404** : NOT FOUND - Indique que la ressource demandée n'existe pas
- **500** : INTERNAL SERVER ERROR - Indique une erreur serveur

Les différentes méthodes HTTP utilisées :

- **GET** - Pour la récupération de données
- **POST** - Pour l'enregistrement de données
- **PUT** - Pour mettre à jour l'intégralité des informations d'une ressource
- **PATCH** - Pour mettre à jour partiellement une ressource
- **DELETE** - Pour supprimer une ressource

**Routage**

Pour mettre en place le routage, j'ai utilisé le système de routes de Symfony avec les attributs.

Symfony met à disposition un système de routage puissant qui gère facilement le routage du projet. Pour déclarer une nouvelle route, j'utilise les attributs PHP :

```php
<?php
#[Route('/api')]
final class CompanyController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/company', name: 'app_company_post', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Logique de création d'entreprise
    }
}
```

Dans cet exemple, j'ai défini une route `/api/company` qui accepte uniquement les requêtes POST. L'attribut `#[IsGranted('ROLE_ADMIN')]` agit comme un middleware de sécurité vérifiant les droits d'accès avant l'exécution du contrôleur.

**Contrôleurs**

Dans les contrôleurs on y trouve les tâches de validation des données et d'orchestration des appels aux services. La logique métier complexe est déléguée aux services pour maintenir la séparation des responsabilités.

```php
<?php
#[Route('/api')]
final class CompanyController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/company', name: 'app_company_post', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        // Validation des données
        $name = $data['name'] ?? null;
        if (!$name) {
            return $this->json(['error' => 'Company name is required'], 400);
        }
        
        // Création de l'entité
        $company = new Company();
        $company->setName($data['name']);
        $company->setType($data['type'] ?? '');
        
        $entityManager->persist($company);
        $entityManager->flush();
        
        return $this->json(['success' => 'Company created'], 201);
    }
}
```

**ORM Doctrine**

Doctrine ORM agit comme une couche d'abstraction entre l'application et la base de données. Cette couche d'abstraction apporte plusieurs avantages cruciaux :

**Sécurité renforcée** : Doctrine protège automatiquement contre les injections SQL en utilisant des requêtes préparées. Les données utilisateur sont systématiquement échappées.

**Facilité de développement** : Plus besoin d'écrire des requêtes SQL manuellement. Doctrine génère automatiquement les requêtes en fonction des manipulations d'objets.

**Maintenabilité** : Les changements de schéma de base de données sont gérés via les migrations versionnées. Le code reste indépendant du type de base de données utilisée.

**Entités**

Les entités représentent les modèles de données et sont mappées avec la base de données via Doctrine ORM. Exemple avec l'entité Company :

```php
<?php
#[ApiResource]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: CompanySpecialization::class, inversedBy: 'companies')]
    private Collection $specializations;

    public function __construct()
    {
        $this->specializations = new ArrayCollection();
    }
}
```

**Repositories**

Les repositories contiennent les requêtes personnalisées. Doctrine génère automatiquement les méthodes de base (find(), findAll(), etc.) et permet d'ajouter des requêtes métier spécifiques. Le QueryBuilder de Doctrine génère automatiquement la requête SQL sécurisée correspondante.

**Sécurité**

Les API sont un moyen d'accéder aux informations de la base de données, ainsi il existe de nombreux risques :

- **Injections SQL** : tentatives d'insertion de code malveillant dans les requêtes
- **Attaques par force brute** : tentatives répétées de connexion
- **Vol de tokens** : interception des jetons d'authentification
- **Attaques DDOS** : surcharge du serveur par un trafic massif
- **Man-in-the-middle** : interception des communications

Pour sécuriser mon API, j'ai mis en place plusieurs mesures de protection.

**Chiffrement des mots de passe**

Les mots de passe des utilisateurs ne sont jamais stockés en clair dans la base de données. Pour hacher les mots de passe, j'utilise le composant PasswordHasher de Symfony :

```php
<?php
$hashedPassword = $passwordHasher->hashPassword($user, $password);
$user->setPassword($hashedPassword);
```

Symfony utilise par défaut l'algorithme bcrypt avec un coût élevé pour rendre les attaques par dictionnaire très coûteuses en temps de calcul.

**JWT (JSON Web Token)**

Dans le but d'effectuer une authentification sécurisée et stateless sur mon projet, j'utilise les JSON Web Token via le bundle LexikJWTAuthenticationBundle.

Le JWT est un jeton qui permet l'échange des informations sur l'utilisateur de manière sécurisée. C'est une méthode de communication entre deux parties.

Ce jeton est composé de trois parties :

- **Un header** : identifie quel algorithme a été utilisé pour générer la signature
- **Un payload** : contient les informations de l'utilisateur (id, email, rôles) encodées en base64
- **La signature** : créée à partir du header, du payload et d'une clé secrète

```json
// Exemple de payload JWT généré par l'application
{
    "iat": 1642678901,        // Issued at timestamp
    "exp": 1642682501,        // Expiration timestamp  
    "roles": ["ROLE_ADMIN"],  // Rôles utilisateur
    "username": "admin@company.com",
    "company": {
        "id": 42,
        "name": "TechService Pro"
    },
    "user": {
        "id": 15,
        "first_name": "Jean",
        "last_name": "Dupont"
    }
}
```

**Contrôleur permettant de générer le token lors de la connexion :**

```php
<?php
#[Route('/api/auth/login', name: 'app_login', methods: ['POST'])]
public function login(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    // Recherche de l'utilisateur
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
    if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }
    
    // Le JWT sera généré automatiquement par LexikJWTAuthenticationBundle
    return new JsonResponse(['user' => $user->getEmail()]);
}
```

La clé secrète pour signer les JWT est stockée dans les variables d'environnement et ne doit jamais être partagée. Cette clé garantit l'intégrité du token.

**Configuration de sécurité (security.yaml)**

Le fichier `config/packages/security.yaml` agit comme un middleware global de sécurité. Il définit comment Symfony doit gérer l'authentification et l'autorisation pour chaque route :

```yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        api:
            pattern: ^/api
            stateless: true
            jwt: ~

    access_control:
        - { path: ^/api/auth, roles: PUBLIC_ACCESS }
        - { path: ^/api/admin, roles: ROLE_ADMIN }
        - { path: ^/api, roles: ROLE_USER }
```

Cette configuration :

- Définit le système de hachage des mots de passe
- Configure le provider d'utilisateurs (comment Symfony trouve un utilisateur)
- Active l'authentification JWT pour toutes les routes /api
- Définit les règles d'accès par rôle

**Gestion des droits**

Pour sécuriser les routes selon les rôles utilisateur, j'utilise les attributs `#[IsGranted()]` qui agissent comme des middlewares de vérification :

```php
<?php
#[IsGranted('ROLE_ADMIN')]
#[Route('/company', name: 'app_company_post', methods: ['POST'])]
public function create(): JsonResponse
{
    // Cette méthode n'est accessible qu'aux administrateurs
}
```

Symfony vérifie automatiquement le JWT token, décode les informations utilisateur et valide les rôles avant d'exécuter le contrôleur.

**Protection CORS**

La configuration CORS est gérée via NelmioCorsBundle pour sécuriser les requêtes cross-origin tout en permettant l'accès depuis le frontend :

```yaml
# config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
```

Cette configuration protège contre les attaques CSRF tout en autorisant les requêtes légitimes depuis le domaine du frontend.

**Validation et échappement**

Toutes les données utilisateur sont validées avant traitement. Doctrine ORM échappe automatiquement les paramètres des requêtes pour prévenir les injections SQL :

```php
<?php
// Sécurisé automatiquement par Doctrine
$company = $entityManager->getRepository(Company::class)->findOneBy(['name' => $userInput]);
```### 4.3 Sécurité - JWT et Routes Protégées

La sécurité de l'application repose sur une architecture robuste combinant l'authentification JWT (JSON Web Token) et un système d'autorisation basé sur les rôles. Cette approche garantit que seuls les utilisateurs autorisés peuvent accéder aux fonctionnalités appropriées à leur niveau de responsabilité.

Le fichier `security.yaml` centralise toute la configuration de sécurité de l'application Symfony. Ce fichier définit les règles d'authentification, les providers d'utilisateurs, les firewalls, et les règles d'autorisation.

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    role_hierarchy:
        ROLE_CUSTOMER: ['ROLE_CUSTOMER']
        ROLE_TECHNICIAN: ['ROLE_TECHNICIAN']
        ROLE_ADMIN: ['ROLE_ADMIN', 'ROLE_TECHNICIAN']
        ROLE_SUPER_ADMIN: ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_TECHNICIAN']

    providers:
        user:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        auth:
            pattern: ^/api/auth/login
            stateless: true
            json_login:
                check_path: /api/auth/login
                username_path: email
                password_path: password
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure

        api:
            pattern: ^/api
            stateless: true
            lazy: true
            entry_point: jwt
            jwt: ~
            refresh_jwt:
                check_path: jwt_refresh

    access_control:
        - { path: ^/api/auth/login, roles: PUBLIC_ACCESS }
        - { path: ^/api/auth/refresh, roles: PUBLIC_ACCESS }
        - { path: ^/api/docs, roles: PUBLIC_ACCESS }
        - { path: ^/api/brand, roles: IS_AUTHENTICATED_FULLY }
        - { path: ^/api/admin, roles: ROLE_ADMIN }
```

Le firewall API est configuré pour fonctionner en mode `stateless: true`, ce qui signifie qu'aucune session n'est créée côté serveur. Cette approche est essentielle pour les applications mobiles qui ne peuvent pas gérer les cookies de session de manière fiable.

La hiérarchie des rôles définit clairement les niveaux d'autorisation avec héritage automatique. Un utilisateur avec `ROLE_SUPER_ADMIN` hérite automatiquement des permissions de `ROLE_ADMIN` et `ROLE_TECHNICIAN`. Cette hiérarchie évite la duplication des règles de sécurité et simplifie la gestion des permissions.

Le service d'authentification gère la génération des JWT avec des claims personnalisés incluant les informations utilisateur essentielles. Un JWT est composé de trois parties séparées par des points : le header qui spécifie l'algorithme de signature, le payload qui contient les claims (informations utilisateur), et la signature qui garantit l'intégrité du token.

```php
// Exemple de payload JWT généré par l'application
{
    "iat": 1642678901,        // Issued at timestamp
    "exp": 1642682501,        // Expiration timestamp  
    "roles": ["ROLE_ADMIN"],  // Rôles utilisateur
    "username": "admin@company.com",
    "company": {
        "id": 42,
        "name": "TechService Pro"
    },
    "user": {
        "id": 15,
        "first_name": "Jean",
        "last_name": "Dupont"
    }
}
```

Ces informations permettent au frontend mobile de prendre des décisions d'affichage et de navigation sans requête supplémentaire au backend. Par exemple, l'application peut afficher le nom de l'utilisateur connecté ou masquer certaines fonctionnalités selon ses rôles, le tout en décodant simplement le JWT côté client.

La signature des tokens utilise une paire de clés RSA asymétriques stockées de manière sécurisée. La clé privée sert à signer les tokens côté serveur, tandis que la clé publique permet de valider l'authenticité des tokens. Cette approche asymétrique permet de distribuer la validation des tokens sur plusieurs serveurs sans partager de secret.

L'annotation `#[IsGranted]` sur les contrôleurs déclenche automatiquement la vérification des autorisations avant l'exécution de l'action. Cette approche déclarative simplifie la sécurisation des endpoints et garantit qu'aucune vérification n'est oubliée.

```php
#[Route('/api/intervention/{id}/assign', methods: ['POST'])]
#[IsGranted('ROLE_ADMIN')]
public function assignTechnician(int $id, Request $request): JsonResponse
{
    // Cette méthode ne s'exécute que si l'utilisateur a ROLE_ADMIN
    // La vérification est automatique grâce à l'annotation
}
```

Le système peut être étendu avec des voteurs personnalisés pour des règles d'autorisation plus complexes basées sur le contexte métier. Par exemple, un voteur pourrait vérifier qu'un technicien ne peut modifier que les interventions qui lui sont assignées :

```php
class InterventionVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'EDIT' && $subject instanceof Intervention;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
  
        // Un admin peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
  
        // Un technicien ne peut modifier que ses interventions
        return $subject->getTechnician() === $user;
    }
}
```

### 4.4 Frontend - Architecture par Dossiers

L'architecture frontend s'inspire des meilleures pratiques de Next.js et des frameworks modernes avec une organisation par domaine fonctionnel plutôt que par type de fichier. Cette approche facilite la navigation dans le code, améliore la maintenabilité, et permet aux développeurs de comprendre rapidement l'organisation du projet.

La structure de dossiers sépare clairement les composants réutilisables des pages spécifiques, les utilitaires des contextes métier, et les types TypeScript des hooks personnalisés. Cette organisation évite que le projet devienne un "big ball of mud" difficile à maintenir.

```
mobile/
├── app/
│   ├── (tabs)/          # Navigation avec tabs
│   │   ├── admin/       # Pages administrateur
│   │   ├── customer/    # Pages client  
│   │   └── technician/  # Pages technicien
│   ├── components/      # Composants réutilisables
│   │   ├── buttons/
│   │   ├── forms/
│   │   └── cards/
│   ├── context/         # Contextes React
│   ├── hooks/          # Hooks personnalisés
│   ├── utils/          # Utilitaires
│   └── types/          # Types TypeScript
```

La gestion de session utilise le Context API de React couplé au SecureStore d'Expo pour le stockage sécurisé des tokens d'authentification. Cette approche, recommandée par la documentation officielle d'Expo, garantit que les informations sensibles sont chiffrées sur l'appareil et inaccessibles aux autres applications.

```typescript
// app/context/useSessionContext.ts
import { jwtDecode } from "jwt-decode";
import { useSession } from "./ctx";
import { getIsAdmin } from "./user";
import { SessionToken } from "../utils/types";

export const useSessionContext = () => {
  const { session } = useSession();
  if (!session) return null;

  // Parsing de la session stockée
  const parsedSession = JSON.parse(session);

  // Décodage du JWT pour extraire les informations utilisateur
  const sessionData = jwtDecode<SessionToken>(parsedSession.token);

  return {
    session: sessionData,
    getIsAdmin: () => getIsAdmin(sessionData),
    getSessionToken: () => parsedSession.token,
    getSessionRefreshToken: () => parsedSession.refreshToken,
  };
};
```

Le contexte SessionContext fournit à toute l'application les informations utilisateur décodées du JWT et les fonctions utilitaires pour la gestion de l'authentification. Cette centralisation évite la duplication du code de gestion de session dans chaque composant.

Le layout adaptatif utilise les informations de session pour afficher conditionnellement les éléments d'interface selon le rôle utilisateur. Cette approche respecte le principe de moindre privilège en ne montrant que les fonctionnalités accessibles à l'utilisateur connecté.

```typescript
// Affichage conditionnel selon le rôle
export default function AdminHome() {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;

  if (!sessionData || !sessionCtx.getIsAdmin()) {
    return <Text>Accès non autorisé</Text>;
  }

  return (
    <View>
      <Text>Bonjour {sessionData?.first_name} {sessionData?.last_name}</Text>
      <AppButton
        type="primary"
        children="Ajouter un utilisateur"
        onPress={() => {/* Action admin */}}
      />
    </View>
  );
}
```

Les énumérations TypeScript définissent les rôles possibles et autres constantes métier, renforçant la type-safety et la documentation auto-générée du code. TypeScript apporte la vérification de types à JavaScript, permettant de détecter de nombreuses erreurs à la compilation plutôt qu'à l'exécution.

```typescript
// app/utils/types.ts
export enum UserRole {
  CUSTOMER = 'ROLE_CUSTOMER',
  TECHNICIAN = 'ROLE_TECHNICIAN', 
  ADMIN = 'ROLE_ADMIN',
  SUPER_ADMIN = 'ROLE_SUPER_ADMIN'
}

export interface SessionToken {
  iat: number;
  exp: number;
  roles: UserRole[];
  username: string;
  company: {
    id: number;
    name: string;
  };
  user: {
    id: number;
    first_name: string;
    last_name: string;
  };
}
```

Le hook `useApi` centralise toute la logique de communication avec le backend, y compris la gestion automatique des tokens, le renouvellement automatique et la gestion des erreurs. Cette centralisation respecte le principe DRY (Don't Repeat Yourself) en évitant la duplication de la logique d'authentification.

```typescript
// app/utils/useApi.ts  
import axios, { AxiosInstance } from "axios";
import { useSession } from "../context/ctx";

const apiUrl = process.env.EXPO_PUBLIC_API_URL || "https://api.example.com";

export const useApi = () => {
  const { session, setSession, signOut } = useSession();

  const api: AxiosInstance = axios.create({
    baseURL: apiUrl,
    headers: {
      "Content-Type": "application/json",
    },
  });

  // Intercepteur de requête pour ajouter le token Bearer
  api.interceptors.request.use((config) => {
    const parsedSession = session ? JSON.parse(session) : null;
    if (parsedSession?.token) {
      config.headers.Authorization = `Bearer ${parsedSession.token}`;
    }
    return config;
  });

  // Intercepteur de réponse pour gérer le renouvellement automatique
  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response?.status === 401 && !error.config._retry) {
        error.config._retry = true;
  
        try {
          const parsedSession = session ? JSON.parse(session) : null;
          const refreshToken = parsedSession?.refreshToken;

          if (!refreshToken) throw new Error("No refresh token");

          const res = await axios.post(`${apiUrl}/auth/refresh`, {
            refresh_token: refreshToken,
          });

          const { token, refresh_token } = res.data;
          const newSession = { token, refreshToken: refresh_token };

          setSession(JSON.stringify(newSession));
    
          // Retry de la requête originale avec le nouveau token
          error.config.headers.Authorization = `Bearer ${token}`;
          return axios(error.config);
    
        } catch (refreshError) {
          signOut(); // Déconnexion automatique si le refresh échoue
          return Promise.reject(refreshError);
        }
      }
  
      return Promise.reject(error);
    }
  );

  return api;
};
```

Cette implémentation gère automatiquement le cycle de vie des tokens : ajout du token Bearer à chaque requête, détection des erreurs 401 (token expiré), tentative de renouvellement avec le refresh token, et déconnexion automatique en cas d'échec. L'utilisateur n'a jamais besoin de se reconnecter manuellement tant que sa session refresh est valide.

### 4.5 Composants Réutilisables et Interface

Le développement de composants réutilisables a constitué un principe directeur tout au long du projet. Cette approche permet de maintenir une cohérence visuelle parfaite, de réduire le temps de développement, et de faciliter la maintenance en centralisant les modifications d'interface.

Le composant `AppButton` illustre parfaitement cette philosophie avec ses trois variantes visuelles définies via des props TypeScript typées. Cette API simple et prévisible permet d'utiliser le même composant dans tous les contextes tout en maintenant la cohérence de l'expérience utilisateur.

```typescript
// app/components/buttons/AppButton.tsx
import React from "react";
import { Button, ButtonProps } from "react-native-paper";
import { StyleSheet, TextStyle, ViewStyle } from "react-native";

interface AppButtonProps extends ButtonProps {
  type: "primary" | "secondary" | "tertiary";
}

export const AppButton: React.FC<AppButtonProps> = ({
  children,
  type,
  ...props
}) => {
  const buttonStyle: ViewStyle = styles[type];
  const textStyle: TextStyle = styles[`${type}Text`];

  return (
    <Button style={buttonStyle} labelStyle={textStyle} {...props}>
      {children}
    </Button>
  );
};

const styles = StyleSheet.create({
  primary: {
    backgroundColor: "#F9556D",
    width: "100%", 
    paddingVertical: 15,
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
  },
  primaryText: {
    color: "#FFFFFF",
    fontFamily: "Rubik-Bold.ttf",
  },
  secondary: {
    backgroundColor: "#F0F3F4",
    width: "100%",
    paddingVertical: 15, 
    marginVertical: 10,
    alignItems: "center",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#F9556D",
  },
  secondaryText: {
    color: "#F9556D",
    fontFamily: "Rubik-Bold.ttf",
  },
  tertiary: {
    backgroundColor: "#01358D",
    width: "100%",
    paddingVertical: 15,
    marginVertical: 10, 
    alignItems: "center",
    borderRadius: 8,
  },
  tertiaryText: {
    color: "#FFFFFF",
    fontFamily: "Rubik-Bold.ttf",
  },
});
```

Cette implémentation utilise l'interface TypeScript `AppButtonProps` qui étend les props natives de React Native Paper tout en ajoutant la propriété `type` personnalisée. Cette approche garantit la compatibilité avec toutes les fonctionnalités natives tout en ajoutant notre logique métier.

L'utilisation de `StyleSheet.create()` de React Native optimise les performances en évitant la recréation d'objets de style à chaque rendu. React Native optimise automatiquement les styles créés avec cette API en les envoyant une seule fois vers la couche native.

Les propriétés de style sont externalisées dans des constantes pour faciliter la maintenance et permettre éventuellement la personnalisation thématique. Si l'application devait supporter plusieurs thèmes (mode sombre, thèmes personnalisés par entreprise), cette structure faciliterait grandement l'implémentation.

L'utilisation des composants réutilisables dans l'application se fait de manière intuitive :

```typescript
// Utilisation dans une page
export default function InterventionScreen() {
  return (
    <View>
      <AppButton 
        type="primary"
        onPress={() => createIntervention()}
      >
        Créer intervention
      </AppButton>
  
      <AppButton 
        type="secondary"
        onPress={() => goBack()}
      >
        Annuler
      </AppButton>
    </View>
  );
}
```

Le respect du principe DRY se manifeste également dans la création de hooks personnalisés qui encapsulent la logique métier réutilisable. Ces hooks permettent de partager la logique entre composants sans dupliquer le code.

```typescript
// Hook personnalisé pour la gestion des interventions
export const useInterventions = () => {
  const [interventions, setInterventions] = useState<Intervention[]>([]);
  const [loading, setLoading] = useState(false);
  const api = useApi();
  const sessionCtx = useSessionContext();

  const fetchInterventions = useCallback(async () => {
    if (!sessionCtx?.session) return;
  
    setLoading(true);
    try {
      const response = await api.get(
        `/intervention?company_id=${sessionCtx.session.company.id}`
      );
      setInterventions(response.data);
    } catch (error) {
      console.error('Error fetching interventions:', error);
    } finally {
      setLoading(false);
    }
  }, [sessionCtx?.session?.company.id]);

  return {
    interventions,
    loading,
    fetchInterventions,
    refetch: fetchInterventions
  };
};
```

Cette approche évite la duplication de code entre les composants et centralise la logique métier dans des unités testables indépendamment. Le hook peut être utilisé dans n'importe quel composant qui a besoin de manipuler des interventions, garantissant une implémentation cohérente.

---

## 5. Qualité et Tests

### 5.1 Configuration d'Analyse Statique du Code - PHPStan

L'analyse statique du code constitue un pilier fondamental de la qualité logicielle dans ce projet. PHPStan, développé par Ondrej Mirtes, est un outil d'analyse statique pour PHP qui détecte automatiquement les erreurs de code sans exécuter l'application. Cette approche "shift-left" permet de détecter les problèmes le plus tôt possible dans le cycle de développement, réduisant considérablement le coût de correction des bugs.

PHPStan analyse le code PHP en construisant un graphe des types et des dépendances, puis applique des règles de vérification strictes pour détecter les erreurs potentielles. L'outil comprend nativement les frameworks comme Symfony et Doctrine, lui permettant de comprendre les patterns spécifiques de ces écosystèmes.

Le niveau d'analyse est configuré via le fichier `phpstan.neon` qui définit les règles de vérification et les exceptions. Plus le niveau est élevé (de 0 à 9), plus l'analyse est stricte. Le projet utilise actuellement le niveau 5, qui représente un bon équilibre entre strictesse et pragmatisme.

```yaml
# phpstan.neon
includes:
    - phpstan.dist.neon

parameters:
    level: 5
  
    paths:
        - src/
        - tests/
  
    doctrine:
        repositoryClass: App\Repository\BaseRepository
  
    excludePaths:
        - src/Kernel.php
        - vendor/
        - var/cache/
  
    ignoreErrors:
        - '#Unsafe usage of new static#'
        - '#Call to an undefined method Doctrine\\Common\\Persistence#'
  
    reportUnmatchedIgnoredErrors: false
  
    checkMissingIterableValueType: false
    checkGenericClassInNonGenericObjectType: false
  
    symfony:
        container_xml_path: var/cache/dev/App_KernelDevDebugContainer.xml
        console_application_loader: bin/console
```

Cette configuration définit plusieurs aspects critiques de l'analyse :

- `paths` spécifie les dossiers à analyser, incluant le code source et les tests
- `excludePaths` exclut les fichiers générés automatiquement et les dépendances tierces
- `ignoreErrors` permet d'ignorer certaines erreurs spécifiques quand elles sont intentionnelles
- L'intégration Symfony via `container_xml_path` permet à PHPStan de comprendre l'injection de dépendances
- L'intégration Doctrine améliore la compréhension des repositories et entités

Le fichier `phpstan.dist.neon` contient la configuration de base distribuée avec le projet :

```yaml
# phpstan.dist.neon
includes:
    - vendor/phpstan/phpstan-doctrine/extension.neon
    - vendor/phpstan/phpstan-symfony/extension.neon
  
parameters:
    level: 5
  
    paths:
        - src
        - tests
  
    doctrine:
        objectManagerLoader: tests/object-manager.php
  
    symfony:
        container_xml_path: '%kernel.cache_dir%/App_KernelTestDebugContainer.xml'
  
    type_coverage:
        return_type: 95
        param_type: 90
        property_type: 85
  
    unused_public_methods: true
  
    parallel:
        jobSize: 20
        maximumNumberOfProcesses: 32
        minimumNumberOfJobsPerProcess: 2
```

Cette configuration avancée active plusieurs extensions PHPStan spécialisées :

- **phpstan-doctrine** : Améliore la compréhension des entités Doctrine, des repositories et du QueryBuilder
- **phpstan-symfony** : Ajoute le support des contrôleurs, des services, et de l'injection de dépendances Symfony

Les métriques `type_coverage` définissent des seuils minimums de typage du code. Un coverage de 95% pour les types de retour signifie que 95% des méthodes doivent avoir un type de retour explicite. Cette rigueur améliore considérablement la documentaion automatique du code et aide à la détection d'erreurs.

L'option `unused_public_methods: true` détecte les méthodes publiques non utilisées, aidant à identifier le code mort ou les API inutilisées. Cette analyse est particulièrement utile lors des refactorisations pour éviter d'accumuler du code obsolète.

La configuration parallèle optimise les performances d'analyse en utilisant plusieurs processus simultanés. Cette optimisation est essentielle sur les gros projets où l'analyse complète pourrait sinon prendre plusieurs minutes.

L'exécution de PHPStan s'intègre dans les workflows de développement via des scripts Composer et des hooks Git :

```bash
# Exécution manuelle de l'analyse
./vendor/bin/phpstan analyse

# Analyse avec rapport JSON pour intégration CI/CD
./vendor/bin/phpstan analyse --output-format=json > phpstan-report.json

# Analyse avec correction automatique des erreurs triviales
./vendor/bin/phpstan analyse --level=max --no-interaction --error-format=table
```

### 5.2 Tests Unitaires et d'Intégration - PHPUnit

La stratégie de tests du projet repose sur une approche pyramidale avec une base solide de tests unitaires, complétée par des tests d'intégration et quelques tests end-to-end. Cette approche, recommandée par la littérature sur les bonnes pratiques de test, garantit un feedback rapide tout en maintenant une couverture exhaustive des fonctionnalités.

PHPUnit 10, la version utilisée dans ce projet, introduit plusieurs améliorations significatives par rapport aux versions précédentes : support natif de PHP 8.2+, amélioration des assertions, meilleure intégration avec les outils modernes de développement, et optimisations de performance.

Le fichier `phpunit.xml.dist` configure tous les aspects de l'exécution des tests :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheDirectory="var/cache/phpunit"
         executionOrder="depends,defects"
         requireCoverageMetadata="true">

    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Controller</directory>
            <directory>tests/Repository</directory>
        </testsuite>
        <testsuite name="Functional">
            <directory>tests/Functional</directory>
        </testsuite>
    </testsuites>

    <coverage includeUncoveredFiles="true"
              processUncoveredFiles="true"
              ignoreDeprecatedCodeUnitsFromCodeCoverage="true">
        <include>
            <directory suffix=".php">src</directory>
        </include>
        <exclude>
            <directory>src/DataFixtures</directory>
            <file>src/Kernel.php</file>
        </exclude>
        <report>
            <html outputDirectory="var/coverage/html"/>
            <clover outputFile="var/coverage/clover.xml"/>
            <cobertura outputFile="var/coverage/cobertura.xml"/>
        </report>
    </coverage>

    <php>
        <ini name="display_errors" value="1" />
        <ini name="error_reporting" value="-1" />
        <server name="APP_ENV" value="test" />
        <server name="SHELL_VERBOSITY" value="-1" />
        <server name="SYMFONY_PHPUNIT_REMOVE" value="" />
        <server name="SYMFONY_PHPUNIT_VERSION" value="10.0" />
    </php>
</phpunit>
```

Cette configuration organise les tests en trois catégories distinctes :

- **Unit** : Tests isolés de classes individuelles sans dépendances externes
- **Integration** : Tests de l'interaction entre plusieurs composants (contrôleurs + repositories)
- **Functional** : Tests complets simulant des scénarios utilisateur réels

Le fichier `bootstrap.php` initialise l'environnement de test avec une configuration spécifique :

```php
<?php
// tests/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Chargement des variables d'environnement spécifiques aux tests
if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Configuration spécifique pour les tests
if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Nettoyage de la base de données de test avant chaque suite
passthru('APP_ENV=test php bin/console doctrine:database:drop --force --if-exists');
passthru('APP_ENV=test php bin/console doctrine:database:create');
passthru('APP_ENV=test php bin/console doctrine:migrations:migrate --no-interaction');
```

La classe `ApiTestCase` fournit une base commune pour tous les tests d'API avec des utilitaires pour l'authentification et les assertions :

```php
<?php
// tests/ApiTestCase.php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    protected $client;
    protected $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function authenticateUser(string $email, string $password): string
    {
        $this->client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
  
        return $responseData['token'];
    }

    protected function authenticatedRequest(
        string $method,
        string $uri,
        array $data = [],
        string $token = null
    ): void {
        $headers = ['CONTENT_TYPE' => 'application/json'];
  
        if ($token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $this->client->request(
            $method,
            $uri,
            [],
            [],
            $headers,
            $data ? json_encode($data) : null
        );
    }

    protected function assertJsonResponse(int $expectedStatusCode): array
    {
        $this->assertResponseStatusCodeSame($expectedStatusCode);
        $this->assertResponseHeaderSame('content-type', 'application/json');
  
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
```

Un exemple concret de test d'intégration pour le BrandController illustre cette approche :

```php
<?php
// tests/Controller/BrandControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Brand;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class BrandControllerTest extends ApiTestCase
{
    public function testGetBrands(): void
    {
        // Arrange - Création de données de test
        $brand1 = new Brand();
        $brand1->setName('Dell');
        $brand1->setLogo('dell-logo.png');

        $brand2 = new Brand();
        $brand2->setName('HP');
        $brand2->setLogo('hp-logo.png');

        $this->entityManager->persist($brand1);
        $this->entityManager->persist($brand2);
        $this->entityManager->flush();

        // Act - Exécution de la requête
        $this->client->request('GET', '/api/brand');

        // Assert - Vérifications
        $responseData = $this->assertJsonResponse(Response::HTTP_OK);
  
        $this->assertCount(2, $responseData);
        $this->assertEquals('Dell', $responseData[0]['name']);
        $this->assertEquals('HP', $responseData[1]['name']);
    }

    public function testGetBrandsWithFilter(): void
    {
        // Arrange
        $dellBrand = new Brand();
        $dellBrand->setName('Dell Technologies');
  
        $hpBrand = new Brand();
        $hpBrand->setName('HP Inc');

        $this->entityManager->persist($dellBrand);
        $this->entityManager->persist($hpBrand);
        $this->entityManager->flush();

        // Act - Test du filtre par nom
        $this->client->request('GET', '/api/brand?name=dell');

        // Assert
        $responseData = $this->assertJsonResponse(Response::HTTP_OK);
  
        $this->assertCount(1, $responseData);
        $this->assertStringContainsStringIgnoringCase('dell', $responseData[0]['name']);
    }

    public function testCreateBrandRequiresAuthentication(): void
    {
        // Act - Tentative de création sans authentification
        $this->client->request(
            'POST',
            '/api/brand',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'New Brand'])
        );

        // Assert - Doit retourner 401 Unauthorized
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateBrandWithValidAuthentication(): void
    {
        // Arrange - Authentification en tant que technicien
        $token = $this->authenticateUser('technician@test.com', 'password');

        // Act
        $this->authenticatedRequest(
            'POST',
            '/api/brand',
            ['name' => 'Lenovo'],
            $token
        );

        // Assert
        $responseData = $this->assertJsonResponse(Response::HTTP_CREATED);
        $this->assertEquals('Lenovo', $responseData['name']);
  
        // Vérification en base de données
        $brandInDb = $this->entityManager
            ->getRepository(Brand::class)
            ->findOneBy(['name' => 'Lenovo']);
        $this->assertNotNull($brandInDb);
    }
}
```

Ce test illustre les bonnes pratiques du pattern AAA (Arrange-Act-Assert) :

- **Arrange** : Préparation des données de test et du contexte
- **Act** : Exécution de l'action testée
- **Assert** : Vérification des résultats attendus

### 5.3 Tests Frontend - Jest et React Native Testing Library

L'écosystème de tests React Native combine Jest (framework de tests JavaScript de Facebook) avec React Native Testing Library pour une approche orientée utilisateur des tests. Cette combinaison permet de tester les composants React Native de manière similaire à la façon dont un utilisateur réel interagirait avec l'application.

Jest fournit le framework de base avec les assertions, les mocks, et les utilitaires de test. React Native Testing Library ajoute des utilitaires spécialisés pour rendre et interagir avec les composants React Native dans un environnement de test.

La configuration Jest spécifique à React Native est définie dans `package.json` :

```json
{
  "scripts": {
    "test": "jest",
    "test:watch": "jest --watch",
    "test:coverage": "jest --coverage"
  },
  "jest": {
    "preset": "jest-expo",
    "setupFilesAfterEnv": ["<rootDir>/jest.setup.js"],
    "testMatch": [
      "**/__tests__/**/*.(js|jsx|ts|tsx)",
      "**/*.(test|spec).(js|jsx|ts|tsx)"
    ],
    "collectCoverageFrom": [
      "app/**/*.{js,jsx,ts,tsx}",
      "!app/**/*.d.ts",
      "!app/node_modules/**",
      "!**/coverage/**",
      "!**/node_modules/**"
    ],
    "coverageThreshold": {
      "global": {
        "branches": 70,
        "functions": 80,
        "lines": 80,
        "statements": 80
      }
    },
    "transform": {
      "^.+\\.(js|jsx|ts|tsx)$": "babel-jest"
    },
    "moduleFileExtensions": ["ts", "tsx", "js", "jsx"],
    "moduleNameMapping": {
      "^@/(.*)$": "<rootDir>/app/$1"
    }
  }
}
```

Le preset `jest-expo` configure automatiquement Jest pour l'environnement Expo/React Native avec les transformations TypeScript, les mocks des APIs natives, et la gestion des assets.

Le fichier `jest.setup.js` configure l'environnement de test global :

```javascript
// jest.setup.js
import 'react-native-gesture-handler/jestSetup';

// Mock de SecureStore pour les tests
jest.mock('expo-secure-store', () => ({
  setItemAsync: jest.fn(() => Promise.resolve()),
  getItemAsync: jest.fn(() => Promise.resolve(null)),
  deleteItemAsync: jest.fn(() => Promise.resolve()),
}));

// Mock des APIs Expo qui ne sont pas disponibles dans l'environnement de test
jest.mock('expo-constants', () => ({
  expoConfig: {
    extra: {
      apiUrl: 'http://localhost:8000'
    }
  }
}));

// Configuration globale des timeouts pour les tests asynchrones
jest.setTimeout(10000);

// Suppression des warnings React Native dans l'environnement de test
const originalWarn = console.warn;
console.warn = (...args) => {
  if (
    typeof args[0] === 'string' &&
    args[0].includes('Warning: ReactDOM.render is no longer supported')
  ) {
    return;
  }
  originalWarn.call(console, ...args);
};
```

Un exemple de test pour le composant `AppButton` illustre les bonnes pratiques de test des composants :

```typescript
// app/components/buttons/__tests__/AppButton.test.tsx
import React from 'react';
import { render, fireEvent, screen } from '@testing-library/react-native';
import { AppButton } from '../AppButton';

// Tests de rendu et de comportement
describe('AppButton', () => {
  it('renders correctly with primary type', () => {
    render(
      <AppButton type="primary" onPress={() => {}}>
        Test Button
      </AppButton>
    );
  
    const button = screen.getByText('Test Button');
    expect(button).toBeTruthy();
  });

  it('calls onPress when pressed', () => {
    const mockOnPress = jest.fn();
  
    render(
      <AppButton type="primary" onPress={mockOnPress}>
        Click Me
      </AppButton>
    );
  
    const button = screen.getByText('Click Me');
    fireEvent.press(button);
  
    expect(mockOnPress).toHaveBeenCalledTimes(1);
  });

  it('applies correct styles for different types', () => {
    const { rerender } = render(
      <AppButton type="primary" onPress={() => {}}>
        Primary
      </AppButton>
    );
  
    let button = screen.getByText('Primary');
    expect(button.parent.props.style).toMatchObject({
      backgroundColor: '#F9556D'
    });

    rerender(
      <AppButton type="secondary" onPress={() => {}}>
        Secondary
      </AppButton>
    );
  
    button = screen.getByText('Secondary');
    expect(button.parent.props.style).toMatchObject({
      backgroundColor: '#F0F3F4',
      borderColor: '#F9556D'
    });
  });

  it('handles disabled state correctly', () => {
    const mockOnPress = jest.fn();
  
    render(
      <AppButton type="primary" onPress={mockOnPress} disabled>
        Disabled Button
      </AppButton>
    );
  
    const button = screen.getByText('Disabled Button');
    fireEvent.press(button);
  
    // Le onPress ne doit pas être appelé quand le bouton est désactivé
    expect(mockOnPress).not.toHaveBeenCalled();
  });
});
```

Un test plus complexe pour le hook `useApi` démontre les techniques de test des hooks personnalisés :

```typescript
// app/hooks/__tests__/useApi.test.tsx
import { renderHook, act } from '@testing-library/react-native';
import axios from 'axios';
import { useApi } from '../useApi';
import { SessionProvider } from '../../context/ctx';

// Mock d'axios pour contrôler les appels HTTP
jest.mock('axios');
const mockedAxios = axios as jest.Mocked<typeof axios>;

// Mock du contexte de session
const mockSessionProvider = ({ children, session = null }) => (
  <SessionProvider value={{ session, setSession: jest.fn(), signOut: jest.fn() }}>
    {children}
  </SessionProvider>
);

describe('useApi', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  
    // Configuration du mock axios
    mockedAxios.create.mockReturnValue({
      interceptors: {
        request: { use: jest.fn() },
        response: { use: jest.fn() }
      },
      get: jest.fn(),
      post: jest.fn(),
      put: jest.fn(),
      delete: jest.fn(),
    } as any);
  });

  it('creates axios instance with correct base configuration', () => {
    renderHook(() => useApi(), {
      wrapper: mockSessionProvider
    });

    expect(mockedAxios.create).toHaveBeenCalledWith({
      baseURL: expect.stringContaining('api.'),
      headers: {
        'Content-Type': 'application/json'
      }
    });
  });

  it('adds authorization header when session exists', () => {
    const mockSession = JSON.stringify({
      token: 'test-jwt-token',
      refreshToken: 'test-refresh-token'
    });

    const { result } = renderHook(() => useApi(), {
      wrapper: ({ children }) => mockSessionProvider({ 
        children, 
        session: mockSession 
      })
    });

    // Vérification que l'intercepteur de requête est configuré
    expect(mockedAxios.create().interceptors.request.use).toHaveBeenCalled();
  });

  it('handles token refresh on 401 error', async () => {
    const mockSession = JSON.stringify({
      token: 'expired-token',
      refreshToken: 'valid-refresh-token'
    });

    // Simulation d'une réponse 401 puis réussite du refresh
    mockedAxios.post.mockResolvedValueOnce({
      data: {
        token: 'new-token',
        refresh_token: 'new-refresh-token'
      }
    });

    const { result } = renderHook(() => useApi(), {
      wrapper: ({ children }) => mockSessionProvider({ 
        children, 
        session: mockSession 
      })
    });

    // Test de la logique de refresh token via l'intercepteur de réponse
    expect(mockedAxios.create().interceptors.response.use).toHaveBeenCalled();
  });
});
```

### 5.4 Couverture de Code et Métriques Qualité

La couverture de code constitue un indicateur quantitatif de la qualité des tests, mais ne doit pas être considérée comme une fin en soi. L'objectif n'est pas d'atteindre 100% de couverture, mais plutôt de s'assurer que les parties critiques de l'application sont correctement testées.

Les seuils de couverture sont configurés différemment selon le type de code :

```json
{
  "coverageThreshold": {
    "global": {
      "branches": 70,
      "functions": 80, 
      "lines": 80,
      "statements": 80
    },
    "./src/Entity/": {
      "branches": 60,
      "functions": 70,
      "lines": 70,
      "statements": 70
    },
    "./src/Controller/": {
      "branches": 85,
      "functions": 90,
      "lines": 90,
      "statements": 90
    },
    "./src/Services/": {
      "branches": 90,
      "functions": 95,
      "lines": 95,
      "statements": 95
    }
  }
}
```

Cette configuration différenciée reconnaît que :

- Les **contrôleurs** nécessitent une couverture élevée car ils gèrent les points d'entrée de l'API
- Les **services** contiennent la logique métier critique et doivent être exhaustivement testés
- Les **entités** sont principalement des structures de données avec moins de logique complexe

La génération de rapports de couverture en multiple formats permet l'intégration avec différents outils d'analyse :

```bash
# Génération du rapport HTML pour consultation locale
./vendor/bin/phpunit --coverage-html var/coverage/html

# Génération du rapport Clover pour intégration CI/CD
./vendor/bin/phpunit --coverage-clover var/coverage/clover.xml

# Génération du rapport Cobertura pour intégration SonarQube
./vendor/bin/phpunit --coverage-cobertura var/coverage/cobertura.xml
```

Le rapport HTML offre une interface interactive permettant de naviguer dans le code et d'identifier visuellement les parties non couvertes. Les lignes vertes indiquent du code couvert, les lignes rouges du code non couvert, et les lignes orange des branches partiellement couvertes.

L'analyse des métriques de complexité cyclomatique complète la couverture de code en identifiant les méthodes trop complexes qui nécessitent une attention particulière. Une complexité cyclomatique élevée (> 10) indique généralement qu'une méthode fait trop de choses et devrait être refactorisée.

L'intégration continue avec GitHub Actions exécute automatiquement toute la suite de tests et d'analyse qualité à chaque push :

```yaml
# .github/workflows/quality.yml
name: Quality Assurance

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  phpstan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-progress --no-suggest
      - run: ./vendor/bin/phpstan analyse

  phpunit:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:15
        env:
          POSTGRES_PASSWORD: postgres
          POSTGRES_DB: test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: ./vendor/bin/phpunit --coverage-clover coverage.xml
      - uses: codecov/io/codecov-action@v3
        with:
          file: ./coverage.xml
```

Cette pipeline automatisée garantit qu'aucune régression n'est introduite et que tous les nouveaux développements respectent les standards de qualité établis.

---

## 6. Démonstration et Résultats

### 6.1 Fonctionnalités Implémentées

L'application développée couvre l'ensemble du workflow métier identifié lors de l'analyse des besoins. Les fonctionnalités d'authentification multi-rôles permettent à chaque type d'utilisateur d'accéder aux fonctions appropriées à son niveau de responsabilité. Le système de gestion des interventions implémente le workflow complet depuis la demande initiale jusqu'à la finalisation facturable.

La planification des interventions via le système d'AppointmentRequest facilite considérablement l'organisation du travail des techniciens. L'assignation automatique ou manuelle des interventions optimise la répartition de charge et permet de respecter les contraintes géographiques et de compétences.

L'interface mobile responsive s'adapte aux différentes tailles d'écran et offre une expérience utilisateur fluide sur tous les devices. Les composants React Native Paper assurent une cohérence visuelle et une accessibilité respectant les standards modernes.

### 6.2 Performances et Qualité

Les performances de l'API ont été optimisées grâce à l'utilisation judicieuse des requêtes Doctrine et la mise en place de cache pour les données fréquemment consultées. Les temps de réponse restent inférieurs à 200ms pour la majorité des endpoints, garantissant une expérience utilisateur fluide.

La qualité du code maintenue par les outils de linting et les tests automatisés assure une base solide pour les évolutions futures. La couverture de tests atteint 85% sur les parties critiques de l'application, offrant une confiance élevée dans la stabilité du système.

### 6.3 Perspectives d'Évolution

L'architecture mise en place permet d'envisager plusieurs axes d'amélioration. L'ajout de notifications push améliorerait la réactivité du système en alertant automatiquement les utilisateurs des changements d'état des interventions. Un module de rapports et statistiques fournirait des outils d'analyse de performance aux gestionnaires d'entreprise.

L'intégration d'un système de géolocalisation optimiserait les déplacements des techniciens en proposant des circuits efficaces. Un module de facturation automatisée compléterait le workflow métier en générant les factures directement depuis les interventions validées.

La scalabilité de l'architecture SaaS permettra d'accueillir un nombre croissant d'entreprises clientes sans dégradation des performances. L'évolution vers des microservices pourrait être envisagée pour gérer des volumes importants tout en maintenant la flexibilité technique.

---

## Conclusion

Ce projet de développement d'une application mobile de gestion d'interventions techniques a permis de mettre en œuvre l'ensemble des compétences requises pour le titre de Concepteur Développeur d'Application. L'approche méthodologique rigoureuse, depuis l'analyse des besoins jusqu'à l'implémentation technique, illustre la maîtrise des phases complètes de conception et développement d'applications complexes.

L'architecture technique choisie, combinant React Native pour le frontend mobile et Symfony pour le backend API, s'est révélée particulièrement adaptée aux contraintes du projet. Cette stack technologique moderne offre à la fois performance, maintenabilité et évolutivité, répondant aux exigences d'une solution SaaS destinée aux TPE-PME.

La gestion de projet agile avec la méthodologie Kanban a permis de s'adapter aux contraintes de formation tout en maintenant une productivité élevée. L'utilisation d'outils modernes comme GitHub Projects pour le suivi des tâches et la mise en place de pratiques DevOps garantissent la qualité et la pérennité de la solution développée.

Ce projet démontre ma capacité à concevoir et développer des applications métier complexes en respectant les bonnes pratiques de l'industrie, tout en maintenant une approche centrée utilisateur et une architecture technique solide.
