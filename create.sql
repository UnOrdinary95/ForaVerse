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

-- Update
ALTER TABLE Utilisateur
ALTER COLUMN chemin_photo
SET DEFAULT 'images/pp_user/default.jpeg';

ALTER TABLE communaute RENAME COLUMN visibilité TO visibilite;

ALTER TABLE Communaute
ALTER COLUMN chemin_photo
SET DEFAULT 'images/pp_commu/default.png';

ALTER TABLE Abonne
DROP CONSTRAINT fk_abo_user,
ADD CONSTRAINT fk_abo_user
FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE Abonne
DROP CONSTRAINT fk_abo_follower,
ADD CONSTRAINT fk_abo_follower
FOREIGN KEY(idAbonne) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE Role
DROP CONSTRAINT fk_role_user,
ADD CONSTRAINT fk_role_user
FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE Role
DROP CONSTRAINT fk_role_commu,
ADD CONSTRAINT fk_role_commu
FOREIGN KEY(idCommunaute) REFERENCES Communaute(idCommunaute)
ON DELETE CASCADE;

-- Si 'accepté' alors, user devient membre de la communauté (L'implémentation était facultatif, on pouvait gérer ça au niveau du backend)
CREATE OR REPLACE FUNCTION addMembre()
RETURNS TRIGGER AS $$
    BEGIN
       IF NEW.statut = 'acceptée' THEN
           INSERT INTO Role (idUtilisateur, idCommunaute) VALUES (NEW.idUtilisateur, NEW.idCommunaute);
        END IF;
        RETURN NEW;
    END;
    $$ LANGUAGE plpgsql;

CREATE TRIGGER after_acceptAdhesion
    AFTER UPDATE ON DemandeAdhesion
    FOR EACH ROW
    EXECUTE FUNCTION addMembre();

ALTER TABLE DemandeAdhesion
DROP CONSTRAINT  fk_adh_user,
ADD CONSTRAINT fk_adh_user
FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE DemandeAdhesion
DROP CONSTRAINT fk_adh_commu,
ADD CONSTRAINT fk_adh_commu
FOREIGN KEY (idCommunaute) REFERENCES Communaute(idCommunaute)
ON DELETE CASCADE;

ALTER TABLE Moderation
DROP CONSTRAINT fk_mod_mod,
ADD CONSTRAINT fk_mod_mod
FOREIGN KEY (idModerateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE Moderation
DROP CONSTRAINT fk_targeted_user,
ADD CONSTRAINT fk_targeted_user
FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE Moderation
DROP CONSTRAINT fk_ban_commu,
ADD CONSTRAINT fk_ban_commu
FOREIGN KEY (idCommunaute) REFERENCES Communaute(idCommunaute)
ON DELETE CASCADE;

-- Je viens de me rendre compte que c'est un héritage de colonne et de type (donc sans les contraintes
-- et cas particulier comme les 'INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY')
-- Je vais devoir ALTER TABLE et ajouter ça à la main.
ALTER TABLE avertissement ADD PRIMARY KEY (idmoderation);

ALTER TABLE avertissement
    ALTER COLUMN idmoderation ADD GENERATED ALWAYS AS IDENTITY;

ALTER TABLE avertissement
ALTER COLUMN idModerateur
SET NOT NULL;

ALTER TABLE avertissement
ALTER COLUMN idUtilisateur
SET NOT NULL;

ALTER TABLE avertissement
ADD CONSTRAINT fk_mod_mod_a
FOREIGN KEY (idModerateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE avertissement
ADD CONSTRAINT fk_targeted_user_a
FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE avertissement
ADD CONSTRAINT fk_ban_commu_a
FOREIGN KEY (idCommunaute) REFERENCES Communaute(idCommunaute)
ON DELETE CASCADE;

ALTER TABLE bannissement ADD PRIMARY KEY (idmoderation);

ALTER TABLE bannissement
    ALTER COLUMN idmoderation ADD GENERATED ALWAYS AS IDENTITY;

ALTER TABLE bannissement
ALTER COLUMN idModerateur
SET NOT NULL;

ALTER TABLE bannissement
ALTER COLUMN idUtilisateur
SET NOT NULL;

ALTER TABLE bannissement
ADD CONSTRAINT fk_mod_mod_b
FOREIGN KEY (idModerateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE bannissement
ADD CONSTRAINT fk_targeted_user_b
FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
ON DELETE CASCADE;

ALTER TABLE bannissement
ADD CONSTRAINT fk_ban_commu_b
FOREIGN KEY (idCommunaute) REFERENCES Communaute(idCommunaute)
ON DELETE CASCADE;

CREATE OR REPLACE FUNCTION banMembre()
RETURNS TRIGGER AS $$
    DECLARE
    compteur INT;
    BEGIN
       SELECT COUNT(*) INTO compteur FROM Avertissement
        WHERE idUtilisateur = NEW.idUtilisateur
        AND idCommunaute = NEW.idCommunaute;

       IF compteur = 3 THEN
           IF NOT EXISTS (
               SELECT 1 FROM Bannissement
               WHERE idUtilisateur = NEW.idUtilisateur
               AND idCommunaute = NEW.idCommunaute
               ) THEN
                INSERT INTO Bannissement (idModerateur, idUtilisateur, idCommunaute, date_fin, raison)
            VALUES (NEW.idModerateur, NEW.idUtilisateur, NEW.idCommunaute, NOW() + INTERVAL '1 month', 'Banni automatiquement après dépassement de ' || compteur || ' avertissements.');
           ELSE
               DELETE FROM Bannissement WHERE idUtilisateur = NEW.idUtilisateur
               AND idCommunaute = NEW.idCommunaute;
               INSERT INTO Bannissement (idModerateur, idUtilisateur, idCommunaute, date_fin, raison)
            VALUES (NEW.idModerateur, NEW.idUtilisateur, NEW.idCommunaute, NOW() + INTERVAL '1 month', 'Banni automatiquement après dépassement de ' || compteur || ' avertissements.');
           END IF;
       END IF;

       IF compteur = 5 THEN
           IF NOT EXISTS (
               SELECT 1 FROM Bannissement
               WHERE idUtilisateur = NEW.idUtilisateur
               AND idCommunaute = NEW.idCommunaute
               ) THEN
                INSERT INTO Bannissement (idModerateur, idUtilisateur, idCommunaute, date_fin, raison)
            VALUES (NEW.idModerateur, NEW.idUtilisateur, NEW.idCommunaute, null, 'Banni automatiquement après dépassement de ' || compteur || ' avertissements.');
           ELSE
               DELETE FROM Bannissement WHERE idUtilisateur = NEW.idUtilisateur
               AND idCommunaute = NEW.idCommunaute;
               INSERT INTO Bannissement (idModerateur, idUtilisateur, idCommunaute, date_fin, raison)
            VALUES (NEW.idModerateur, NEW.idUtilisateur, NEW.idCommunaute, null, 'Banni automatiquement après dépassement de ' || compteur || ' avertissements.');
           END IF;
       END IF;
       RETURN NEW;
    END;

    $$ LANGUAGE plpgsql;

CREATE TRIGGER after_addAvertissement
    AFTER INSERT ON Avertissement
    FOR EACH ROW
    EXECUTE FUNCTION banMembre();

-- Problème : Je me retrouve avec deux Moderation avec la même ID (encore une conséquence de l'héritage)
-- Pour régler ça soit je génère des UUID (peu de chance de tomber sur les mêmes)
-- Ou soit je crée une séquence et je partage cette séquence sur les trois tables (je vais partir sur ça)
ALTER TABLE Moderation
    ALTER COLUMN idModeration DROP IDENTITY;

ALTER TABLE Avertissement
    ALTER COLUMN idModeration DROP IDENTITY;

ALTER TABLE Bannissement
    ALTER COLUMN idModeration DROP IDENTITY;

CREATE SEQUENCE moderation_id_seq START WITH 1;

ALTER TABLE Moderation
    ALTER COLUMN idModeration SET DEFAULT nextval('moderation_id_seq');

ALTER TABLE Avertissement
    ALTER COLUMN idModeration SET DEFAULT nextval('moderation_id_seq');

ALTER TABLE Bannissement
    ALTER COLUMN idModeration SET DEFAULT nextval('moderation_id_seq');

-- J'automatise la suppression de ban avec pg_cron (tous les jours à minuit psql va lancer la fonction ci-dessus)
CREATE OR REPLACE FUNCTION deleteBan()
RETURNS VOID AS $$
    BEGIN
        DELETE FROM Bannissement WHERE date_fin <= NOW();
    END;
    $$ LANGUAGE plpgsql;

-- Active l'extension pg_cron
CREATE EXTENSION pg_cron;

-- Planification de la tâche
SELECT cron.schedule(
    'suppression-bans-expires',
    '0 0 * * *',                -- Tous les jours à minuit (selon Europe/Paris)
    'SELECT deleteBan();'
);

-- Vérification
SELECT * FROM cron.job;