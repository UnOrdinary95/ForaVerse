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
    <title>Communauté - <?= htmlspecialchars($communaute->getNom()) ?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div style="display:flex; min-height: 100vh;">
        <div style="width: 80vw; border: 3px solid black;">
            <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
            <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 50px; height: 50px; border-radius: 50%">
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <?php if (isset($_SESSION['Pseudo'])): ?>
                <?php if($role): ?>
                    <button id="btnCreerDiscussion">➕Créer</button>
                <?php endif; ?>

                <?php if ($communaute->getVisibilite()):?>
                    <?php if (!$role): ?>
                        <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Rejoindre</button>
                    <?php elseif ($role->estMembreOuModerateur()): ?>
                        <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Quitter</button>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!$role && !isset($adhesion)):?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">➡️Demander à rejoindre</button>
                    <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'en attente'): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">🔄️Demande en attente</button>
                    <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'refusée'): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">⛔Demande refusé</button>
                    <?php elseif ($role->estMembreOuModerateur()): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">🚪Quitter</button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($role) && $role->estProprietaire()): ?>
                    <button id="btnGestion">🔑Gérer</button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['Pseudo']) && $role && $role->peutModerer()): ?>
                <button id="btnModeration">🔧Modération</button>
            <?php endif; ?>

            <div id="creerDiscussionContainer" class="modal">
                <div class="modal-content">
                    <h1>Créer une discussion</h1><h1 id="closeDiscussionContainer" style="cursor: pointer;">❌</h1>
                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#creerDiscussionContainer" novalidate>
                        <input type="text" name="titreDiscussion" placeholder="Titre de la discussion"><br><br>
                        <?php if (!empty($erreurs['titreDiscussion'])): ?>
                            <span style="color: red"><?= $erreurs['titreDiscussion'] ?></span><br>
                        <?php endif; ?>
                        <textarea name="contenuDiscussion" style="resize: none;" rows="20" cols="100" placeholder="Contenu de la discussion"></textarea><br><br>
                        <?php if (!empty($erreurs['contenuDiscussion'])): ?>
                            <span style="color: red"><?= $erreurs['contenuDiscussion'] ?></span><br>
                        <?php endif; ?>
                        <button type="submit">Créer</button>
                    </form>
                </div>
            </div>

            <div id="modContainer" class="modal">
                <div class="modal-content">
                    <h1>Modération</h1><h1 id="closeModContainer" style="cursor: pointer;">❌</h1>
                    <div id="param_moderation" style="display: block; border: 1px solid black;">
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="gestionadhesion">
                            <h2>Gestion des adhésions</h2>
                        </div>
                        
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="gestionaverti">
                            <h2>Liste des utilisateurs avertis</h2>
                        </div>
                        
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="gestionbanni">
                            <h2>Liste des utilisateurs bannis</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div id="gestionAdhesionContainer" class="modal">
                <div class="modal-content">
                    <h1>Gestion des demandes d'adhésion</h1><h1 id="closeGestionAdhesionContainer" style="cursor: pointer;">❌</h1>
                    <div id="adhesionList" style="display: block; border: 1px solid black;">
                        <div>
                            <h2 id="demandeadh" style="text-decoration: underline; text-decoration-color: red; color: red; cursor: pointer;">Liste des demandes d'adhésion</h2>
                            <h2 id="refusadh" style="text-decoration: none; text-decoration-color: none; color: black; cursor: pointer;">Liste des refus</h2>
                        </div>
                        <div id="demandeblock" style="display: block;">
                            <?php if (count($liste_attentes) > 0): ?>
                                <?php foreach ($liste_attentes as $pseudo_adhesion => $adhesion): ?>
                                    <div>
                                        <a href="./?action=profil&utilisateur=<?=$pseudo_adhesion?>"><span><?= htmlspecialchars($pseudo_adhesion) . " - le " . (new DateTime($adhesion['datedemande']))->format('d/m/Y') ?></span></a>
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
                                    <div>
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
                    <h1>Liste des utilisateurs avertis</h1><h1 id="closeListeAvertiContainer" style="cursor: pointer;">❌</h1>
                    <div id="avertiList" style="display: block; border: 1px solid black;">
                        <?php if (count($liste_warns) > 0): ?>
                            <?php foreach ($liste_warns as $avertissement): ?>
                                <div>
                                    <a href="./?action=profil&utilisateur=<?=$avertissement->getUtilisateur()->getPseudo()?>"><span><?= htmlspecialchars($avertissement->getUtilisateur()->getPseudo()) . " - le " . (new DateTime($avertissement->getDateDebut()))->format('d/m/Y')?></span></a>
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
                    <h1>Liste des utilisateurs bannis</h1><h1 id="closeListeBanniContainer" style="cursor: pointer;">❌</h1>
                    <div id="banniList" style="display: block; border: 1px solid black;">
                        <?php if (count($liste_bans) > 0): ?>
                            <?php foreach ($liste_bans as $bannissement): ?>
                                <div>
                                    <a href="./?action=profil&utilisateur=<?=$bannissement->getUtilisateur()->getPseudo()?>">
                                        <span>
                                            <?= htmlspecialchars($bannissement->getUtilisateur()->getPseudo()) ?> - du 
                                            <?= (new DateTime($bannissement->getDateDebut()))->format('d/m/Y') ?>
                                            <?php if ($bannissement->getDateFin()): ?>
                                                au <?= (new DateTime($bannissement->getDateFin()))->format('d/m/Y') ?>
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
                    <h1>Paramètres de la communauté</h1><h1 id="closeParamCommuContainer" style="cursor: pointer;">❌</h1>
                    <div id="parametres" style="display: block; border: 1px solid black;">
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnMod">
                                <h2>Gestion des modérateurs</h2>
                        </div>
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnRename">
                                <h2>Renommer la communauté</h2>
                                <p><?=htmlspecialchars($communaute->getNom())?></p>
                        </div>
                        <div style="cursor: pointer;" id="btnDelete">
                            <h2>Supprimer la communauté</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="modalMod">
                <div class="modal-content">
                    <h1>Gestion des modérateurs</h1><h1 id="closeModalMod" style="cursor: pointer;">❌</h1>
                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalMod" novalidate>
                        <h3>Ajouter un modérateur</h3>
                        <input type="text" name="pseudoMembre" placeholder="Pseudo du membre"><br><br>
                        <button type="submit">Ajouter comme modérateur</button>
                        <?php if (!empty($erreurs_addmod['pseudoMembre'])): ?>
                            <span style="color: red"><?= $erreurs_addmod['pseudoMembre'] ?></span><br>
                        <?php endif; ?>
                    </form>
                    <hr>
                    <h3>Liste des modérateurs actuels</h3>
                    <div>
                        <?php if(isset($pseudos_mod) && count($pseudos_mod) > 0): ?>
                            <?php foreach($pseudos_mod as $pseudo_mod): ?>
                                <span><?= htmlspecialchars($pseudo_mod) ?></span>
                                <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalMod" style="display:inline">
                                    <input type="hidden" name="deleteMod" value="<?= $pseudo_mod ?>">
                                    <button type="submit">➖</button>
                                </form>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucun modérateur pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="modal" id="modalRename">
                <div class="modal-content">
                    <h1>Renommer la communauté</h1><h1 id="closeModalRename" style="cursor: pointer;">❌</h1>
                    <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalRename" novalidate>
                        <input type="text" name="nomCommu" placeholder="Nouveau nom"><br><br>
                        <?php if (!empty($erreurs_rename['nomCommu'])): ?>
                            <span style="color: red"><?= $erreurs_rename['nomCommu'] ?></span><br>
                        <?php endif; ?>
                        <button type="submit">Renommer</button>
                    </form>
                </div>
            </div>

            <div class="modal" id="modalDelete">
                <div class="modal-content">
                    <h1>Supprimer la communauté</h1><h1 id="closeModalDelete" style="cursor: pointer;">❌</h1>
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
            
            <hr>
                <div id="discussionContainer">
                    <?php if (isset($discussions) && count($discussions) > 0): ?>
                        <?php foreach ($discussions as $discussion): ?>
                            <div class="discussion">
                                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none;">
                                    <p><?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>, le <?= (new DateTime($discussion->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($discussion->getDateCreation()))->format('H:i') ?></p>
                                </a>
                                <h2><?= htmlspecialchars($discussion->getTitre()) ?></h2>
                                    <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container" data-publication-id=<?=$discussion->getIdPublication()?>>
                                        <?php if(isset($_SESSION['Pseudo'])){
                                            switch($discussion->getVoteUtilisateurCourant()){
                                                case 1:
                                                    print '
                                                    <button class="vote-up active">⬆️</button>
                                                    <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                    <button class="vote-down">⬇️</button>';
                                                    break;
                                                case -1:
                                                    print '
                                                    <button class="vote-up">⬆️</button>
                                                    <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                    <button class="vote-down">⬇️</button>;';
                                                    break;
                                                default:
                                                    print '
                                                    <button class="vote-up">⬆️</button>
                                                    <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                    <button class="vote-down">⬇️</button>';
                                            }
                                            print '
                                            <button>🗨️Commentaire</button>
                                            <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($communaute->getNom()).'">
                                                <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">';
                                            if ($discussion->estFavoris()){
                                                print '<button type="submit" name="Favoris">❌Favoris</button>';
                                            }
                                            else{
                                                print '<button type="submit" name="Favoris">⭐Favoris</button>';
                                            }
                                            print '</form>';
                                            
                                            if (isset($role) && $role->peutModerer()){
                                                print '
                                                <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($communaute->getNom()).'">
                                                    <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">';
                                                if ($discussion->estEpingle()){
                                                    print '<button type="submit" name="epinglerPublication">❌Désépingler</button>';
                                                }
                                                else{
                                                    print '<button type="submit" name="epinglerPublication">📌Epingler</button>';
                                                }
                                                print '</form>';
                                            }
                                        }
                                        else{
                                            print '
                                            <a href="./?action=connexion"><button>⬆️</button></a>
                                            <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                            <a href="./?action=connexion"><button>⬇️</button></a>
                                            <a href="./?action=connexion"><button>🗨️Commentaire</button></a>';
                                        }?>
                                    </span>
                                <hr>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune discussion pour le moment.</p>
                    <?php endif; ?>
                </div>
        </div>

        <div style="width: 20vw;border: 3px solid black;">
            <img id="communauteImage" src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 75px; height: 75px; border-radius: 50%; cursor: pointer;" 
                <?php if (isset($_SESSION['Pseudo'])): ?>
                    onclick="document.getElementById('imageInput').click();"
                <?php endif; ?>
            >
            
            <input type="file" id="imageInput" accept="image/*" style="display: none;">
            <div id="cropperContainer" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 0; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 1000; width: 40vw; max-width: 100vw; display: none;">
                <img id="imagePreview" src="" alt="Preview">
                <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
                <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
            </div>
            
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <p id="description"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            <p>Visibilité : <?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></p>
            <p id="compteurMembres"><?= htmlspecialchars($nbr_membres) . " Membres"?></p>

            <h2>Propriétaire</h2>
            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($proprio['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                <img src="../../public/<?= htmlspecialchars($proprio['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                <span style="font-size: 18px;"><?= htmlspecialchars($proprio['pseudo']) ?></span>
            </a>

            <h2>Modérateurs</h2>
            <div>
                <?php if(isset($mods) && count($mods) > 0): ?>
                    <?php foreach($mods as $mod): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($mod['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                            <img src="../../public/<?= htmlspecialchars($mod['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                            <span style="font-size: 18px;"><?= htmlspecialchars($mod['pseudo']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun modérateur pour le moment.</p>
                <?php endif; ?>
            </div>

            <h2>Membres</h2>
            <div>
                <?php if(isset($membres) && count($membres) > 0): ?>
                    <?php foreach($membres as $membre): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($membre['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                            <img src="../../public/<?= htmlspecialchars($membre['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                            <span style="font-size: 18px;"><?= htmlspecialchars($membre['pseudo']) ?></span>
                            <?php if($membre['admin']): ?>
                                <span style="color: red; font-weight: bold;">{Admin}</span>
                            <?php endif; ?>
                            <?php if($membre['banglobal']): ?>
                                <span style="color: green; font-weight: bold;">{Ban global}</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun membre pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        if (window.location.hash === "creerCommu"){
            window.history.replaceState("", document.title, window.location.pathname + window.location.search);
        }
    </script>
    <script src="../../public/scripts/gestion_adhesion.js"></script>
    <script src="../../public/scripts/image_cropper_commu.js"></script>
    <script src="../../public/scripts/communaute_settings.js"></script>
    <script src="../../public/scripts/commu_moderation_settings.js"></script>
    <script src="../../public/scripts/communaute_discussion.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>