<?php
/** Affichage de la communauté
 * 
 * @var Communaute $communaute
 * @var Role $role
 * @var int $nbr_membres
 * @var array $erreurs_addmod
 * @var array $erreurs_rename
 * @var array $mods
 * @var string $proprio
 * @var array $membres
 * @var ?Adhesion $adhesion
 * @var array $liste_refus
 * @var array $liste_attentes
 * @var array $liste_warns
 * @var array $liste_bans
 * @var array $discussions
 * @var array $erreurs
*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($communaute->getNom()) ?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <?php include_once 'components/header.php'; ?>
    <main class="flex auto_w">
        <?php include_once 'components/left_sidebar.php'; ?>

        <div id="communaute_container1">
            <div class="flex justify-center items-center auto_w margin3 bg-border">
                <div style="width: 80%" class="flex">
                    <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 100px; border-radius: 50%">
                    <div class="flex items-end">
                        <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap3 margin3 margin-right1">
                <?php if (isset($_SESSION['Pseudo'])): ?>
                    <?php if($role): ?>
                        <button id="btnCreerDiscussion" class="flex items-center gap2">
                            <svg class="svg_white"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80z"/></svg>
                            Créer
                        </button>
                    <?php endif; ?>

                    <?php if ($communaute->getVisibilite()):?>
                        <?php if (!$role): ?>
                            <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Rejoindre</button>
                        <?php elseif ($role->estMembreOuModerateur()): ?>
                            <button id="btnAdhesion" class="flex items-center gap2" data-communaute_id="<?= $communaute->getId() ?>">
                                <svg class="svg_white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M680-160v-400H313l144 144-56 57-241-241 240-240 57 57-144 143h447v480z"/></svg>
                                Quitter
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!$role && !isset($adhesion)):?>
                            <button id="btnAdhesionPrivee" class="flex items-center gap2" data-communaute_id="<?= $communaute->getId() ?>">
                                Demander à rejoindre
                            </button>
                        <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'en attente'): ?>
                            <button id="btnAdhesionPrivee" class="flex items-center gap2" data-communaute_id="<?= $communaute->getId() ?>">
                                Demande en attente
                            </button>
                        <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'refusée'): ?>
                            <button id="btnAdhesionPrivee" class="flex items-center gap2" data-communaute_id="<?= $communaute->getId() ?>">
                                Demande refusé
                            </button>
                        <?php elseif ($role->estMembreOuModerateur()): ?>
                            <button id="btnAdhesionPrivee" class="flex items-center gap2" data-communaute_id="<?= $communaute->getId() ?>">
                                Quitter
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($role) && $role->peutGererCommunaute()): ?>
                        <button id="btnGestion" class="flex items-center gap2">
                            <svg class="svg_white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M420-680q0-33 23.5-56.5T500-760t56.5 23.5T580-680t-23.5 56.5T500-600t-56.5-23.5T420-680M500 0 320-180l60-80-60-80 60-85v-47q-54-32-87-86.5T260-680q0-100 70-170t170-70 170 70 70 170q0 67-33 121.5T620-472v352zM340-680q0 56 34 98.5t86 56.5v125l-41 58 61 82-55 71 75 75 40-40v-371q52-14 86-56.5t34-98.5q0-66-47-113t-113-47-113 47-47 113"/></svg>    
                            Gérer
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($_SESSION['Pseudo']) && $role && $role->peutModerer()): ?>
                    <button id="btnModeration" class="flex items-center gap2" style="margin-right: 3px;">
                        <svg class="svg_white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5-2-31.5-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266zm42-180q58 0 99-41t41-99-41-99-99-41q-59 0-99.5 41T342-480t40.5 99 99.5 41m-2-140"/></svg>
                        Modération
                    </button>
                <?php endif; ?>
            </div>
            
            <div id="creerDiscussionContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Créer une discussion</h2>
                        <svg id="closeDiscussionContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#creerDiscussionContainer" novalidate>
                        <input type="text" name="titreDiscussion" placeholder="Titre de la discussion"><br><br>
                        <?php if (!empty($erreurs['titreDiscussion'])): ?>
                            <span class="error"><?= $erreurs['titreDiscussion'] ?></span><br>
                        <?php endif; ?>
                        <textarea name="contenuDiscussion" class="no_resize" rows="20" cols="100" placeholder="Contenu de la discussion"></textarea><br><br>
                        <?php if (!empty($erreurs['contenuDiscussion'])): ?>
                            <span class="error"><?= $erreurs['contenuDiscussion'] ?></span><br>
                        <?php endif; ?>
                        <button type="submit">Créer</button>
                    </form>
                </div>
            </div>

            <div id="modContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Modération</h2>
                        <svg id="closeModContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <div id="param_moderation">
                        <div class="card pointer" id="gestionadhesion">
                            <h3>Gestion des adhésions</h3>
                        </div>
                        
                        <div class="card pointer" id="gestionaverti">
                            <h3>Liste des utilisateurs avertis</h3>
                        </div>
                        
                        <div class="card pointer" id="gestionbanni">
                            <h3>Liste des utilisateurs bannis</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div id="gestionAdhesionContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Gestion des demandes d'adhésion</h2>
                        <svg id="closeGestionAdhesionContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <div id="adhesionList" style="display: block; border: 1px solid black;">
                        <div>
                            <h2 id="demandeadh" style="text-decoration: underline; text-decoration-color: red; color: red; cursor: pointer;">• Liste des demandes d'adhésion</h2>
                            <h2 id="refusadh">• Liste des refus</h2>
                        </div>
                        <div id="demandeblock" style="display: block;">
                            <?php if (count($liste_attentes) > 0): ?>
                                <?php foreach ($liste_attentes as $pseudo_adhesion => $adhesion): ?>
                                    <div class="card">
                                        <a href="./?action=profil&utilisateur=<?=$pseudo_adhesion?>"><span><?= htmlspecialchars($pseudo_adhesion) . " - le " . $adhesion['datedemande'] ?></span></a>
                                        <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#gestionAdhesion" style="display:inline">
                                            <input type="hidden" name="idAdhesion" value="<?= $adhesion['id'] ?>">
                                            <button type="submit" name="validerAdhesion">✔️</button>
                                            <button type="submit" name="refuserAdhesion">❌</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune demande d'adhésion pour le moment.</p>
                            <?php endif; ?>
                        </div>

                        <div id="refusblock" style="display: none;">
                            <?php if (count($liste_refus) > 0): ?>
                                <?php foreach ($liste_refus as $pseudo_adhesion => $id_adhesion): ?>
                                    <div class="card">
                                        <a href="./?action=profil&utilisateur=<?=$pseudo_adhesion?>"><span><?= htmlspecialchars($pseudo_adhesion) ?></span></a>
                                        <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#gestionAdhesion" style="display:inline">
                                            <input type="hidden" name="idAdhesion" value="<?= $id_adhesion ?>">
                                            <button type="submit" name="annulerAdhesion">➖</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucun refus pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="listeAvertiContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Liste des utilisateurs avertis</h2>
                        <svg id="closeListeAvertiContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <div id="avertiList">
                        <?php if (count($liste_warns) > 0): ?>
                            <?php foreach ($liste_warns as $avertissement): ?>
                                <div class="card">
                                    <a href="./?action=profil&utilisateur=<?=$avertissement->getUtilisateur()->getPseudo()?>"><span><?= htmlspecialchars($avertissement->getUtilisateur()->getPseudo()) . " - le " . $avertissement->getDateDebut()?></span></a>
                                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#listeAvertiContainer" style="display:inline">
                                        <input type="hidden" name="idAvertissement" value="<?= $avertissement->getId() ?>">
                                        <button type="submit" name="annulerAvertissement">➖</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucun utilisateur averti pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="listeBanniContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Liste des utilisateurs bannis</h2>
                        <svg id="closeListeBanniContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <div id="banniList">
                        <?php if (count($liste_bans) > 0): ?>
                            <?php foreach ($liste_bans as $bannissement): ?>
                                <div class="card">
                                    <a href="./?action=profil&utilisateur=<?=$bannissement->getUtilisateur()->getPseudo()?>">
                                        <span>
                                            <?= htmlspecialchars($bannissement->getUtilisateur()->getPseudo()) ?> - du 
                                            <?= $bannissement->getDateDebut() ?>
                                            <?php if ($bannissement->getDateFin()): ?>
                                                au <?= $bannissement->getDateFin() ?>
                                            <?php else: ?>
                                                (Permanent)
                                            <?php endif; ?>
                                        </span>
                                    </a>
                                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#listeBanniContainer" style="display:inline">
                                        <input type="hidden" name="idBannissement" value="<?= $bannissement->getId() ?>">
                                        <button type="submit" name="annulerBannissement">➖</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucun utilisateur banni pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="ParamCommuContainer" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Paramètres de la communauté</h2>
                        <svg id="closeParamCommuContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <div id="parametres">
                        <div class="card pointer" id="btnMod">
                                <h2>Gestion des modérateurs</h2>
                        </div>
                        <div class="card pointer" id="btnRename">
                            <h2>Renommer la communauté</h2>
                            <p><?=htmlspecialchars($communaute->getNom())?></p>
                        </div>
                        <div class="card pointer" id="btnDelete">
                            <h2>Supprimer la communauté</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="modalMod">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Paramètres de la communauté</h2>
                        <svg id="closeModalMod" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalMod" novalidate>
                        <h3>Ajouter un modérateur</h3>
                        <input type="text" name="pseudoMembre" placeholder="Pseudo du membre"><br><br>
                        <button type="submit">Ajouter comme modérateur</button>
                        <?php if (!empty($erreurs_addmod['pseudoMembre'])): ?>
                            <span style="color: red"><?= $erreurs_addmod['pseudoMembre'] ?></span><br>
                        <?php endif; ?>
                    </form>
                    <hr class="margin4">
                    <h3>Liste des modérateurs actuels</h3>
                    <div>
                        <?php if(isset($mods) && count($mods) > 0): ?>
                            <?php foreach($mods as $mod): ?>
                                <div class="flex items-center gap3 card">
                                    <span><?= htmlspecialchars($mod['pseudo']) ?></span>
                                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalMod">
                                        <input type="hidden" name="deleteMod" value="<?= $mod['pseudo']?>">
                                        <button type="submit">➖</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucun modérateur pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="modal" id="modalRename">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Renommer la communauté</h2>
                        <svg id="closeModalRename" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalRename" novalidate>
                        <input type="text" name="nomCommu" placeholder="Nouveau nom"><br><br>
                        <?php if (!empty($erreurs_rename['nomCommu'])): ?>
                            <span class="error"><?= $erreurs_rename['nomCommu'] ?></span><br>
                        <?php endif; ?>
                        <button type="submit">Renommer</button>
                    </form>
                </div>
            </div>

            <div class="modal" id="modalDelete">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Supprimer la communauté</h2>
                        <svg id="closeModalDelete" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>

                    <p>Êtes-vous sûr de vouloir supprimer cette communauté ? Cette action est irréversible.</p>
                    <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                        <a href="./?action=accueil"><button>Non, annuler</button></a>
                        
                        <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>" novalidate>
                            <input type="hidden" name="supprimerCommunaute" value="1">
                            <button type="submit">Oui, supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap3 margin-left5">
                <p>Trié par :</p>
                <form method="POST" class="flex gap3" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>">
                    <select name="tri" id="tri">
                        <option value="select">Sélectionner un tri</option>
                        <option value="recents">Récents (Par défaut)</option>
                        <option value="anciens">Anciens</option>
                        <option value="upvotes">Upvotes (⬆️)</option>
                        <option value="downvotes">Downvotes (⬇️)</option>
                    </select>
                    <button type="submit" id="triButton">✅</button>
                </form>
            </div>

            <div id="discussionContainer">
                <?php if (isset($discussions) && count($discussions) > 0): ?>
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="flex justify-center items-center auto_w margin4">
                            <div class="discussion card" style="border: 1px solid silver; width: 90%;">
                                <?php if (isset($role) && $role->peutModerer() || isset($session_user) && $discussion->getIdUtilisateur() == $session_user->getId()): ?>
                                    <form class="flex justify-end" style="margin: 0; padding: 0;" method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>">
                                        <input type="hidden" name="deleteDiscussion" value="<?= $discussion->getIdPublication() ?>">
                                        <svg onclick="this.closest('form').submit();" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                                    </form>
                                <?php endif; ?>

                                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none; display: flex; align-items: center;">
                                    <p><?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>, le <?= $discussion->getDateCreationFormatee()?> à <?= $discussion->getHeureCreationFormatee() ?></p>
                                    <?php if ($discussion->estEpingle()): ?>
                                        <p>📌</p>
                                    <?php endif; ?>
                                </a>
                                <a href="./?action=publication&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>&idPublication=<?= htmlspecialchars($discussion->getIdPublication()) ?>" style="text-decoration: none;">
                                    <h2 class="text-gradient"><?= htmlspecialchars($discussion->getTitre()) ?></h2>
                                </a>
                                <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container card bg-border" data-publication-id=<?=$discussion->getIdPublication()?>>
                                    <?php if(isset($_SESSION['Pseudo'])){
                                        switch($discussion->getVoteUtilisateurCourant()){
                                            case 1:
                                                print '
                                                <svg class="vote-up active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                                break;
                                            case -1:
                                                print '
                                                <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                                break;
                                            default:
                                                print '
                                                <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                        }
                                        print '
                                        <a href="./?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($discussion->getIdPublication()).'">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                        </a>
                                        <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($communaute->getNom()).'">
                                            <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">
                                            <input type="hidden" name="Favoris" value="1">';
                                        if ($discussion->estFavoris()){
                                            print '<svg onclick="this.closest(\'form\').submit();" class="svg_red pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120zm400-600H280v520h400zm-400 0v520z"/>Favoris</svg>';
                                        }
                                        else{
                                            print '<svg onclick="this.closest(\'form\').submit();" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m480-120-58-52q-101-91-167-157T150-447.5 95.5-544 80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5 705-329 538-172zm0-108q96-86 158-147.5t98-107 50-81 14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81 98 107T480-228m0-273"/>Favoris</svg>';
                                        }
                                        print '</form>';
                                        
                                        if (isset($role) && $role->peutModerer()){
                                            print '
                                            <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($communaute->getNom()).'">
                                                <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">
                                                <input type="hidden" name="epinglerPublication" value="1">';
                                            if ($discussion->estEpingle()){
                                                print '<svg
                                                        onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        >
                                                        <path d="M12 17v5" />
                                                        <path d="M15 9.34V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H7.89" />
                                                        <path d="m2 2 20 20" />
                                                        <path d="M9 9v1.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h11" />
                                                        </svg>
                                                        ';
                                            }
                                            else{
                                                print '<svg
                                                        onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        >
                                                        <path d="M12 17v5" />
                                                        <path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z" />
                                                        </svg>
                                                        ';
                                            }
                                            print '</form>';
                                        }
                                    }
                                    else{                                        
                                        print '
                                        <a href="./?action=connexion">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                        </a>
                                        <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                        <a href="./?action=connexion">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>
                                        </a>
                                        <a href="./?action=connexion">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                        </a>
                                        </span>
                                        <br>';
                                    }?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune discussion pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    
        <?php include_once 'components/right_sidebar.php'; ?>
    </main>

    <script>
        if (window.location.hash === "creerCommu"){
            window.history.replaceState("", document.title, window.location.pathname + window.location.search);
        }
    </script>
    <script src="../../public/scripts/creer_commu.js"></script>
    <script src="../../public/scripts/gestion_adhesion.js"></script>
    <script src="../../public/scripts/image_cropper_commu.js"></script>
    <script src="../../public/scripts/communaute_settings.js"></script>
    <script src="../../public/scripts/commu_moderation_settings.js"></script>
    <script src="../../public/scripts/communaute_discussion.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>