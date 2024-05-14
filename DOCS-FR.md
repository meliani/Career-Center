# Guide d'utilisation de la plateforme carrières

## Informations générales

Le code source de la plateforme carrières est mis à disposition en open source pour tout opération d’audit de sécurité.

La plateforme est sous licence personnalisée: droits d’utilisation pour des fin non commerciales est permise avec l’autorisation du propriétaire. Le tout est le fichier readme sur l’entrepôt GitHub.com/MELIANI/career-center

## Guide d’utilisation Administrateur / Professeur

### Activation du compte

Se rendre sur la page d'accueil de la plateforme, rendez sur l’espace désiré et suivre la procédure mot de passe oublié, les comptes sont déjà créés par l’administration avec l'adresse e-mail institutionnelle en attendant qu'on met en plaçe un système d'authentification unique (SSO) au niveau de l'institut.

## List des fonctionnalités

### Tableau de bord

Le tableur de bord contient des graphes pour avoir une vue d’ensemble du déroulement des stages PFE, technique et ouvrier et leurs statistiques relatif.

### logique du déroulement du processus des stages sur la plateforme

#### 1 - Lancement d'une campagne pour la recherche des stages (PFE / Technique / Ouvrier)

l'école/institut lance une campagne mailing pour la recherche des stages, il peut définir les dates de début et de fin de la campagne, les filières concernées, les types de stages (PFE, Technique, Ouvrier), les villes concernées, les entreprises concernées, les critères de recherche des stages.

la base de donnée des emails est alimentée, purifiée periodiquement graçe a un système automatisé de traitement des retours email (bounce) et des désabonnements.

la base de donnée des emails est composée des entreprises, des anciens encadrants, des professeurs, des anciens étudiants, des anciens encadrants, des partenaires de l’université.

#### 2 - Annonce des stages par les entreprises

les entreprises intéressées par la campagne envoient leurs offres de stages sur la plateforme, les offres sont validées par la DASER avant d’être publiées sur la plateforme.

#### 3 - déclaration de stage

l’étudiant dans cette étape rempli un formulaire pour renseigner les informations relatives à la génération de sa convention.
Une fois c’est fait l’étudiant génère sa convention, le status de sa déclaration va passé du status “brouillon” au status “déclaré”.

#### 2 - validation du coordonateur du département

la validation est faite soit par e-mail ou sur la plateforme ou il a accès aux étudiants relatifs à sa filière.

#### 3 - suivi par l'école/institut

l'école/institut assure le suivi des convention sur la plateforme en renseignant l’état actuel des conventions sur la plateforme (“achevée”, “signée”)

#### 4 - conversion des convention de stage signés en projets de fin d’études

le projet peut contenir des binômes ou trinômes donc plusieurs étudiants et plusieurs conventions dans le cas échéant.

#### 5 - affectation des encadrants aux projets

l’affectation peux se faire directement par les CF et/ou CD puis validé par l’administration de l'école pour un contrôle minutieux sur les encadrements ajoutés par les collègues.
l’affectation peux aussi se faire en mass en injectant un fichier d’un tableur (excel ou libre office calc).

#### 6 - Notification des étudiants

les étudiants sont notifiés par e-mail de l’affectation de leurs encadrants.

#### 7 - Notification des encadrants

les encadrants sont notifiés par e-mail de l’affectation des étudiants.

#### 8 - Affectation des examinateurs

la même procédure que l’étape 5, 6 et 7 est répétée pour l’affectation des examinateurs.

#### 9 - plannings des soutenances

##### paramètrage des intervalles des soutenances

l'école/institut défini les intervalles quand les soutenances vont se passer date de début et de fin de l'intervalle, heure de début de la journée et heure de fin, durée des pauses, durée des soutenances.

##### Parametrage des salles

création des salles prévus pour les soutenances.

##### Generation des slots

avec un algorithme taillé sur mesure, la plateforme génère les slots horaires pour les soutenances en fonction des intervalles définis, exlua les jours fériés et les weekends programmatiquement.

##### Affectation des slots

avec un autre algorithme la plateforme va distribuer les projets sur les créneaux horaires en fonction des contraintes de planification tout en évitant le chevauchement des resources (professeurs ou salles).

##### Choix des algorithmes

les algorithmes de planification sont adaptable en fonction des besoins.
    - Favoriser les soutenances par date de fin de stage minimisant le temps de d'attente
    - Favoriser les soutenances par minimisation des déplacements des professeurs
    - Favoriser les soutenances par minimisation de l'intervalle des soutenances

#### 10 - Notification des étudiants

les étudiants sont notifiés par e-mail de l’affectation de leurs dates de soutenances.

#### 11 - Notification des encadrants / examinateurs

les encadrants et les examinateurs sont notifiés par e-mail de l’affectation des dates de soutenances.
