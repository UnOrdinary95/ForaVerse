<?php
/** Affichage de la communauté
 * 
 * @var Communaute $communaute
 * @var Role $role
 * @var int $nbr_membres
 * @var array $erreurs_addmod
 * @var array $erreurs_rename
 * @var array $moderateurs
 * @var Role $proprio
 * @var ?Adhesion $adhesion
 * @var array $liste_refus
 * @var array $liste_attentes
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
                <?php if ($communaute->getVisibilite()):?>
                    <?php if (!$role): ?>
                        <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Rejoindre</button>
                    <?php elseif ($role->estMembreOuModerateur()): ?>
                        <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Quitter</button>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!$role && !isset($adhesion)):?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">Demander à rejoindre</button>
                    <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'en attente'): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">Demande en attente</button>
                    <?php elseif (!$role && isset($adhesion) && $adhesion->getStatut() == 'refusée'): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">Demande refusé</button>
                    <?php elseif ($role->estMembreOuModerateur()): ?>
                        <button id="btnAdhesionPrivee" data-communaute_id="<?= $communaute->getId() ?>">Quitter</button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($role) && $role->estProprietaire()): ?>
                    <button id="btnGestion">Gérer</button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['Pseudo']) && $role && $role->peutModerer()): ?>
                <button id="btnModeration">Modération</button>
            <?php endif; ?>
                <div id="modContainer" class="modal">
                <div class="modal-content">
                    <h1>Modération</h1><h1 id="closeModContainer" style="cursor: pointer;">❌</h1>
                    <div id="param_moderation" style="display: block; border: 1px solid black;">
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="gestionadhesion">
                            <h2>Gestion des adhésions</h2>
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
                                <?php foreach ($liste_attentes as $adhesion): ?>
                                    <div>
                                        <span><?= htmlspecialchars($adhesion->getPseudo()) ?></span>
                                        <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#gestionAdhesion" style="display:inline">
                                            <input type="hidden" name="idAdhesion" value="<?= $adhesion->getId() ?>">
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
                                <?php foreach ($liste_refus as $adhesion): ?>
                                    <div>
                                        <span><?= htmlspecialchars($adhesion->getPseudo()) ?></span>
                                        <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#gestionAdhesion" style="display:inline">
                                            <input type="hidden" name="idAdhesion" value="<?= $adhesion->getId() ?>">
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
                        <?php if(isset($moderateurs) && count($moderateurs) > 0): ?>
                            <?php foreach($moderateurs as $mod): ?>
                                <span><?= htmlspecialchars($mod->getUtilisateur()->getPseudo()) ?></span>
                                <form method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>#modalMod" style="display:inline">
                                    <input type="hidden" name="deleteMod" value="<?= $mod->getUtilisateurId() ?>">
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
            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($proprio->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none;">
                <span><?= htmlspecialchars($proprio->getUtilisateur()->getPseudo()) ?></span>
            </a>

            <h2>Modérateurs</h2>
            <div>
                <?php if(isset($moderateurs) && count($moderateurs) > 0): ?>
                    <?php foreach($moderateurs as $mod): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($mod->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none;">
                            <span><?= htmlspecialchars($mod->getUtilisateur()->getPseudo()) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun modérateur pour le moment.</p>
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
</body>
</html>