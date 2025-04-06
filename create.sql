CREATE TABLE Utilisateur(
    idUtilisateur INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pseudo VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    motdepasse VARCHAR(256),
    chemin_photo VARCHAR(256), -- Prévoir un DEFAULT
    bio VARCHAR(256) DEFAULT 'Pas de bio.',
    date_inscription DATE DEFAULT CURRENT_DATE,
    est_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE Communaute(
    idCommunaute INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(1024) DEFAULT 'Pas de description.',
    chemin_photo VARCHAR(256), -- Prévoir un DEFAULT
    visibilité BOOLEAN DEFAULT TRUE
);

CREATE TABLE Notification(
    idNotification INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    idUtilisateur INT NOT NULL,
    type_notification VARCHAR(50)
       CHECK (type_notification IN ('votes', 'publications', 'abonnement', 'abonne', 'demande')),
    contenu VARCHAR(256) NOT NULL,
    datetime_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    est_lu BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_notif_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
);

CREATE TABLE Moderation (
    idModeration INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    idModerateur INT NOT NULL,
    idUtilisateur INT NOT NULL,
    idCommunaute INT,
    date_debut DATE DEFAULT CURRENT_DATE,
    raison VARCHAR(256),
    CONSTRAINT fk_mod_mod FOREIGN KEY(idModerateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_targeted_user FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_ban_commu FOREIGN KEY(idCommunaute) REFERENCES Communaute(idCommunaute),
    CHECK (idModerateur != idUtilisateur)
);

CREATE TABLE Bannissement(
    date_fin DATE CHECK (date_fin IS NULL OR date_fin > date_debut),
    est_global BOOLEAN DEFAULT FALSE
) INHERITS (Moderation);

CREATE TABLE Avertissement(
) INHERITS (Moderation);

-- Pour ajouter NOT NULL à la colonne héritée 'idCommunaute'
ALTER TABLE Avertissement
ALTER COLUMN idCommunaute SET NOT NULL;

CREATE TABLE Publication(
    idPublication INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    idCommunaute INT NOT NULL,
    idUtilisateur INT NOT NULL,
    contenu VARCHAR(2048),
    datetime_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    score INT DEFAULT 1,
    est_epingle BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_post_commu FOREIGN KEY(idCommunaute) REFERENCES Communaute(idCommunaute),
    CONSTRAINT fk_post_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
);

CREATE TABLE Discussion(
    titre VARCHAR(50) NOT NULL,
    CONSTRAINT pk_discussion PRIMARY KEY (idPublication)
) INHERITS (Publication);

CREATE TABLE Commentaire(
    idDiscussion INT NOT NULL,
    CONSTRAINT pk_commentaire PRIMARY KEY (idPublication),
    CONSTRAINT fk_comm_disc FOREIGN KEY(idDiscussion) REFERENCES Discussion(idPublication)
) INHERITS (Publication);

CREATE TABLE Role(
    idUtilisateur INT NOT NULL,
    idCommunaute INT NOT NULL,
    role VARCHAR(20) CHECK(role IN ('membre', 'modérateur', 'propriétaire')) DEFAULT 'membre',
    CONSTRAINT pk_role PRIMARY KEY(idUtilisateur, idCommunaute),
    CONSTRAINT fk_role_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_role_commu FOREIGN KEY(idCommunaute) REFERENCES Communaute(idCommunaute)
);

CREATE TABLE Abonne(
    idUtilisateur INT NOT NULL,
    idAbonne INT NOT NULL,
    datetime_abonnement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_abonne PRIMARY KEY(idUtilisateur, idAbonne),
    CONSTRAINT fk_abo_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_abo_follower FOREIGN KEY(idAbonne) REFERENCES Utilisateur(idUtilisateur),
    CHECK (idUtilisateur != idAbonne)
);

CREATE TABLE SousCommentaire (
    idCommentaireParent INT NOT NULL,
    idCommentaireEnfant INT NOT NULL,
    CONSTRAINT pk_nestedcomm PRIMARY KEY(idCommentaireParent, idCommentaireEnfant),
    CONSTRAINT fk_comm_parent FOREIGN KEY(idCommentaireParent) REFERENCES Commentaire(idPublication),
    CONSTRAINT fk_comm_child FOREIGN KEY(idCommentaireEnfant) REFERENCES Commentaire(idPublication),
    CHECK (idCommentaireParent != idCommentaireEnfant)
);

CREATE TABLE DemandeAdhesion(
    idUtilisateur INT NOT NULL,
    idCommunaute INT NOT NULL,
    datetime_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) CHECK(statut IN ('en attente', 'acceptée', 'refusée')) DEFAULT 'en attente',
    CONSTRAINT pk_adh PRIMARY KEY(idUtilisateur, idCommunaute),
    CONSTRAINT fk_adh_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_adh_commu FOREIGN KEY(idCommunaute) REFERENCES Communaute(idCommunaute)
);

CREATE TABLE Favoris(
    idUtilisateur INT NOT NULL,
    idPublication INT NOT NULL,
    datetime_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_fav PRIMARY KEY(idUtilisateur, idPublication),
    CONSTRAINT fk_fav_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_fav_post FOREIGN KEY(idPublication) REFERENCES Publication(idPublication)
);

CREATE TABLE Vote(
    idUtilisateur INT NOT NULL,
    idPublication INT NOT NULL,
    resultat SMALLINT CHECK (resultat IN (-1, 0, 1)) DEFAULT 1,
    CONSTRAINT pk_vote PRIMARY KEY(idUtilisateur, idPublication),
    CONSTRAINT fk_vote_user FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
    CONSTRAINT fk_vote_post FOREIGN KEY(idPublication) REFERENCES Publication(idPublication)
);
