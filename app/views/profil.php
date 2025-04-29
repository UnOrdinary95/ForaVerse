<?php
/**
 * @var Utilisateur $utilisateur
 * @var Utilisateur $session_user
 * @var array $liste_commu_moderation
 * @var ?Bannissement $est_banni_global
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?= $utilisateur->getPseudo()?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <script src="../../public/scripts/lien_to_pressepapier.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>

<body>
    <?php include_once 'components/header.php'; ?>
    <main class="flex auto_w">
        <?php include_once 'components/left_sidebar.php'; ?>
        <div class="flex flex-col items-center test" style="flex-grow: 1;">
            <div class="card flex flex-col items-center bg-border margin6" style="width: 30%;">
                <?php if (isset($_SESSION['Pseudo']) && $utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto())?>" style="width: 100px; height: 100px; border-radius: 30%; cursor: pointer;"
                    id="profileImage" alt="Profil"
                    <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                        onclick="document.getElementById('imageInput').click();"
                    <?php endif; ?>
                >
                <?php else: ?>
                    <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto())?>" style="width: 100px; height: 100px; border-radius: 30%;" alt="Profil">
                <?php endif; ?>

                <h2><?= htmlspecialchars($utilisateur->getPseudo())?></h2>
                <p><?= nl2br(htmlspecialchars($utilisateur->getBio()))?></p>
                <small class="margin2"><?="Compte créé le ". (new DateTime($utilisateur->getDateInscription()))->format('d/m/Y')?></small>
                <div class="flex justify-evenly test" style="width: 80%;">
                    <small id="compteur_abonne">Abonnés : <?=count($utilisateur->getSystemeAbonnement()->getAbonnes()) ?></small>
                    <small>Abonnements : <?=count($utilisateur->getSystemeAbonnement()->getAbonnements())?></small>
                </div>
            </div>

            <button onclick="partagerURL()">Partager le profil</button><br>
            
            <?php if (isset($_SESSION['Pseudo']) && $utilisateur->getPseudo() != $_SESSION['Pseudo']): ?>
                <?php if ($utilisateur->getSystemeAbonnement()->estAbonne($session_user->getId())): ?>
                    <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">Se désabonner</button>
                <?php else: ?>
                    <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">S'abonner</button>
                <?php endif; ?>
            <?php endif; ?>

            <input type="file" id="imageInput" accept="image/*" style="display: none;">
            <div id="cropperContainer">
                <img id="imagePreview" src="" alt="Preview">
                <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
                <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
            </div>

            
            
            <?php if (isset($_SESSION['Pseudo']) && (count($liste_commu_moderation) > 0 || $session_user->estAdministrateur()) && !$utilisateur->estAdministrateur()): ?>
                <button id="btnModeration">⚙️Modération</button>
                <div id="profilmodContainer" class="modal">
                    <div class="modal-content">
                        <div class="flex items-center justify-between">
                            <h2>Modération</h2>
                            <svg id="closeModContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                        </div>
                        <div id="profilmodCommu" style="display: block; border: 1px solid black;">
                            <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnwarn">
                                <h2><?= "Avertir " . htmlspecialchars($utilisateur->getPseudo()) ?></h2>
                            </div>
                            <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnban">
                                <h2><?= "Bannir " . htmlspecialchars($utilisateur->getPseudo()) ?></h2>
                            </div>
                            <?php if($est_banni_global !== null):?>
                                <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btncancel">
                                    <h2>Annuler le bannissement global</h2>
                                </div>
                            <?php endif;?>
                            <?php if($session_user->estAdministrateur()): ?>
                                <div style="cursor: pointer;" id="btndelete">
                                    <h2><?= "Supprimer " . htmlspecialchars($utilisateur->getPseudo()) ?></h2>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div id="modalWarn" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Avertir</h2>
                        <svg id="closeWarn" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalWarn" method="POST" novalidate>
                        <div style="display: inline;">
                            <p>Communauté : </p>
                            <select name="liste_commu">
                                <?php foreach ($liste_commu_moderation as $id_commu => $info_commu): ?>
                                    <option value="<?= htmlspecialchars($id_commu) ?>"><?= htmlspecialchars($info_commu['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p>Motif de l'avertissement :</p>
                        <textarea name="raisonWarn" style="resize: none;" rows="5" cols="80"></textarea><br><br>
                        <?php if (isset($_SESSION['erreurs']['raisonWarn'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['raisonWarn'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['raisonWarn']); ?>
                        <?php endif; ?>
                        <button type="submit" name="envoyer" value="Avertir">Avertir</button>
                    </form>
                </div>
            </div>

            <div id="modalBan" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Bannir</h2>
                        <svg id="closeBan" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalBan" method="POST" novalidate>
                        <div style="display: inline;">
                            <p>Communauté : </p>
                            <select name="liste_commu">
                                <?php if($session_user->estAdministrateur()): ?>
                                    <option value="global">🌐Global</option>
                                <?php endif; ?>
                                <?php foreach ($liste_commu_moderation as $id_commu => $info_commu):
                                    if (!$info_commu['estbanni']): ?>
                                        <option value="<?= htmlspecialchars($id_commu) ?>"><?= htmlspecialchars($info_commu['nom']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <p>Durée du bannissement :</p>
                        <input type="radio" name="dureeban" value="1m">
                        <label for="1month">1 mois</label><br>
                        <input type="radio" name="dureeban" value="P">
                        <label for="perm">Permanent</label><br>
                        <p>Motif du bannissement :</p>
                        <textarea name="raisonBan" style="resize: none;" rows="5" cols="80"></textarea><br><br>
                        <?php if (isset($_SESSION['erreurs']['raisonBan'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['raisonBan'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['raisonBan']); ?>
                        <?php endif; ?>
                        <button type="submit" name="envoyer" value="Bannir">Bannir</button>
                    </form>
                </div>
            </div>
            
            <div id="modalCancel" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Annuler le bannissement</h2>
                        <svg id="closeCancel" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalCancel" method="POST" novalidate>
                        <p>Êtes-vous sûr de vouloir annuler le bannissement global ?</p>
                        <input type="submit" name="AnnulerBanGlobal" value="Annuler">
                    </form>
                </div>
            </div>

            <div id="modalSuppr" class="modal">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Supprimer</h2>
                        <svg id="closeSuppr" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalSuppr" method="POST" novalidate>
                        <p>Êtes-vous sûr de vouloir supprimer ce compte ?</p>
                        <input type="submit" name="Suppr" value="Supprimer">
                    </form>
                </div>
            </div>

            <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                <button id="btnParametres">⚙️Paramètres</button>
                <div id="paramContainer" class="modal">
                    <div class="modal-content">
                        <div class="flex items-center justify-between">
                            <h2>Paramètres</h2>
                            <svg id="closeContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                        </div>
                        <div id="parametres" style="display: block; border: 1px solid black;">
                            <div class="card" style="cursor: pointer;" id="btnPseudo">
                                <h2>Pseudo</h2>
                                <p><?=htmlspecialchars($utilisateur->getPseudo())?></p>
                            </div>
                            <br>
                            <div class="card" style="cursor: pointer;" id="btnEmail">
                                <h2>Adresse email</h2>
                                <p><?=htmlspecialchars($utilisateur->getEmail())?></p>
                            </div>
                            <br>
                            <div class="card" style="cursor: pointer;" id="btnBio">
                                <h2>Bio</h2>
                                <p><?=htmlspecialchars($utilisateur->getBio())?></p>
                            </div>
                            <br>
                            <div class="card" style="cursor: pointer;" id="btnMdp">
                                <h2>Mot de passe</h2>
                                <p>Changer son mot de passe</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="modal" id="modalPseudo">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Pseudo</h2>
                        <svg id="closePseudo" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalPseudo" method="POST" novalidate>
                        <p>Veuillez entrer votre nouveau pseudo.</p>
                        <input type="text" name="modalPseudo" placeholder="<?=htmlspecialchars($utilisateur->getPseudo())?>"><br><br>
                        <?php if (isset($_SESSION['erreurs']['pseudo'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['pseudo'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['pseudo']); ?>
                        <?php endif; ?>
                        <input type="submit" name="envoyer" value="Modifier">
                    </form>
                </div>
            </div>
            <div class="modal" id="modalEmail">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Email</h2>
                        <svg id="closeEmail" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalEmail" method="POST" novalidate>
                        <p>Veuillez entrer votre nouvelle adresse email.</p>
                        <input type="email" name="modalEmail" placeholder="<?=htmlspecialchars($utilisateur->getEmail())?>"><br><br>
                        <?php if (isset($_SESSION['erreurs']['email'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['email'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['email']); ?>
                        <?php endif; ?>
                        <input type="submit" name="envoyer" value="Modifier">
                    </form>
                </div>
            </div>
            <div class="modal" id="modalBio">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Bio</h2>
                        <svg id="closeBio" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalBio" method="POST" novalidate>
                        <p>Veuillez entrer votre nouvelle bio.</p>
                        <textarea name="modalBio" style="resize: none;" rows="5" cols="50" placeholder="<?=htmlspecialchars($utilisateur->getBio())?>"></textarea><br><br>
                        <?php if (isset($_SESSION['erreurs']['bio'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['bio'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['bio']); ?>
                        <?php endif; ?>
                        <input type="submit" name="envoyer" value="Modifier">
                    </form>
                </div>
            </div>
            <div class="modal" id="modalMdp">
                <div class="modal-content">
                    <div class="flex items-center justify-between">
                        <h2>Changer son mot de passe</h2>
                        <svg id="closeMdp" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                    </div>
                    <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalMdp" method="POST" novalidate>
                        <p>Veuillez entrer votre ancien mot de passe.</p>
                        <input type="password" name="ancienMdp" placeholder="Ancien mot de passe"><br><br>
                        <?php if (isset($_SESSION['erreurs']['ancienMdp'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['ancienMdp'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['ancienMdp']); ?>
                        <?php endif; ?>
                        <p>Veuillez entrer votre nouveau mot de passe.</p>
                        <input type="password" name="nouveauMdp" placeholder="Nouveau mot de passe"><br><br>
                        <?php if (isset($_SESSION['erreurs']['nouveauMdp'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['nouveauMdp'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['nouveauMdp']); ?>
                        <?php endif; ?>
                        <p>Veuillez entrer à nouveau votre nouveau mot de passe.</p>
                        <input type="password" name="confirmationMdp" placeholder="Confirmation mot de passe"><br><br>
                        <?php if (isset($_SESSION['erreurs']['confirmationMdp'])): ?>
                            <span class="error margin-left3"><?= $_SESSION['erreurs']['confirmationMdp'] ?></span><br>
                            <?php unset($_SESSION['erreurs']['confirmationMdp']); ?>
                        <?php endif; ?>
                        <input type="submit" name="envoyer" value="Modifier">
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../../public/scripts/creer_commu.js"></script>
    <script src="../../public/scripts/profil_settings.js"></script>
    <script src="../../public/scripts/profil_moderation_settings.js"></script>
    <script src="../../public/scripts/gestion_abonnement.js"></script>
    <script src="../../public/scripts/image_cropper.js"></script>
</body>
</html>