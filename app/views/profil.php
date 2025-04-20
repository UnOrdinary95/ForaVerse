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
    <link rel="stylesheet" href="../../public/styles/style.css">
</head>

<body>
    <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
    <div>
        <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
        <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto())?>" style="width: 100px; height: 100px; border-radius: 30%; cursor: pointer;"
            id="profileImage" alt="Profil"
            <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                onclick="document.getElementById('imageInput').click();"
            <?php endif; ?>
        >
        <?php else: ?>
            <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto())?>" style="width: 100px; height: 100px; border-radius: 30%;" alt="Profil">
        <?php endif; ?>

        <input type="file" id="imageInput" accept="image/*" style="display: none;">
        <div id="cropperContainer">
            <img id="imagePreview" src="" alt="Preview">
            <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
            <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
        </div>

        <h1><?= htmlspecialchars($utilisateur->getPseudo())?></h1>
        <p><?= "\n". htmlspecialchars($utilisateur->getBio())?></p>
        <p id="compteur_abonne">Abonnés : <?=count($utilisateur->getSystemeAbonnement()->getAbonnes()) ?></p>
        <p>Abonnements : <?=count($utilisateur->getSystemeAbonnement()->getAbonnements())?></p>
        <p><?="Compte créé le ". (new DateTime($utilisateur->getDateInscription()))->format('d/m/Y')?></p>
        <button onclick="partagerURL()">Partager le profil</button><br>
        
        <?php if ($utilisateur->getPseudo() != $_SESSION['Pseudo']): ?>
            <?php if ($utilisateur->getSystemeAbonnement()->estAbonne($session_user->getId())): ?>
                <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">Se désabonner</button>
            <?php else: ?>
                <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">S'abonner</button>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (count($liste_commu_moderation) > 0 || $session_user->estAdministrateur() && $utilisateur->getPseudo() != $_SESSION['Pseudo']): ?>
            <button id="btnModeration">⚙️Modération</button>
            <div id="profilmodContainer" class="modal">
                <div class="modal-content">
                    <h1>Modération</h1><h1 id="closeModContainer" style="cursor: pointer;">❌</h1>
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
                <h1>Avertir</h1><h1 id="closeWarn" style="cursor: pointer;">❌</h1>
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
                        <span style="color: red"><?= $_SESSION['erreurs']['raisonWarn'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['raisonWarn']); ?>
                    <?php endif; ?>
                    <button type="submit" name="envoyer" value="Avertir">Avertir</button>
                </form>
            </div>
        </div>

        <div id="modalBan" class="modal">
            <div class="modal-content">
                <h1>Bannir</h1><h1 id="closeBan" style="cursor: pointer;">❌</h1>
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
                        <span style="color: red"><?= $_SESSION['erreurs']['raisonBan'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['raisonBan']); ?>
                    <?php endif; ?>
                    <button type="submit" name="envoyer" value="Bannir">Bannir</button>
                </form>
            </div>
        </div>
        
        <div id="modalCancel" class="modal">
            <div class="modal-content">
                <h1>Annuler le bannissement</h1><h1 id="closeCancel" style="cursor: pointer;">❌</h1>
                <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalCancel" method="POST" novalidate>
                    <p>Êtes-vous sûr de vouloir annuler le bannissement global ?</p>
                    <input type="submit" name="AnnulerBanGlobal" value="Annuler">
                </form>
            </div>
        </div>

        <div id="modalSuppr" class="modal">
            <div class="modal-content">
                <h1>Supprimer</h1><h1 id="closeSuppr" style="cursor: pointer;">❌</h1>
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
                    <h1>Paramètres</h1><h1 id="closeContainer" style="cursor: pointer;">❌</h1>
                    <div id="parametres" style="display: block; border: 1px solid black;">
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnPseudo">
                            <h2>Pseudo</h2>
                            <p><?=htmlspecialchars($utilisateur->getPseudo())?></p>
                        </div>
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnEmail">
                            <h2>Adresse email</h2>
                            <p><?=htmlspecialchars($utilisateur->getEmail())?></p>
                        </div>
                        <div style="border-bottom: 1px solid silver; cursor: pointer;" id="btnBio">
                            <h2>Bio</h2>
                            <p><?=htmlspecialchars($utilisateur->getBio())?></p>
                        </div>
                        <div style="cursor: pointer;" id="btnMdp">
                            <h2>Mot de passe</h2>
                            <p>Changer son mot de passe</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="modal" id="modalPseudo">
            <div class="modal-content">
                <h1>Pseudo</h1><h1 id="closePseudo" style="cursor: pointer;">❌</h1>
                <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalPseudo" method="POST" novalidate>
                    <p>Veuillez entrer votre nouveau pseudo.</p>
                    <input type="text" name="modalPseudo" placeholder="<?=htmlspecialchars($utilisateur->getPseudo())?>"><br><br>
                    <?php if (isset($_SESSION['erreurs']['pseudo'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['pseudo'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['pseudo']); ?>
                    <?php endif; ?>
                    <input type="submit" name="envoyer" value="Modifier">
                </form>
            </div>
        </div>
        <div class="modal" id="modalEmail">
            <div class="modal-content">
                <h1>Email</h1><h1 id="closeEmail" style="cursor: pointer;">❌</h1>
                <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalEmail" method="POST" novalidate>
                    <p>Veuillez entrer votre nouvelle adresse email.</p>
                    <input type="email" name="modalEmail" placeholder="<?=htmlspecialchars($utilisateur->getEmail())?>"><br><br>
                    <?php if (isset($_SESSION['erreurs']['email'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['email'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['email']); ?>
                    <?php endif; ?>
                    <input type="submit" name="envoyer" value="Modifier">
                </form>
            </div>
        </div>
        <div class="modal" id="modalBio">
            <div class="modal-content">
                <h1>Bio</h1><h1 id="closeBio" style="cursor: pointer;">❌</h1>
                <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalBio" method="POST" novalidate>
                    <p>Veuillez entrer votre nouvelle bio.</p>
                    <textarea name="modalBio" style="resize: none;" rows="5" cols="50" placeholder="<?=htmlspecialchars($utilisateur->getBio())?>"></textarea><br><br>
                    <?php if (isset($_SESSION['erreurs']['bio'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['bio'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['bio']); ?>
                    <?php endif; ?>
                    <input type="submit" name="envoyer" value="Modifier">
                </form>
            </div>
        </div>
        <div class="modal" id="modalMdp">
            <div class="modal-content">
                <h1>Changer son mot de passe</h1><h1 id="closeMdp" style="cursor: pointer;">❌</h1>
                <form action="?action=profil&utilisateur=<?=htmlspecialchars($utilisateur->getPseudo())?>#modalMdp" method="POST" novalidate>
                    <p>Veuillez entrer votre ancien mot de passe.</p>
                    <input type="password" name="ancienMdp" placeholder="Ancien mot de passe"><br><br>
                    <?php if (isset($_SESSION['erreurs']['ancienMdp'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['ancienMdp'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['ancienMdp']); ?>
                    <?php endif; ?>
                    <p>Veuillez entrer votre nouveau mot de passe.</p>
                    <input type="password" name="nouveauMdp" placeholder="Nouveau mot de passe"><br><br>
                    <?php if (isset($_SESSION['erreurs']['nouveauMdp'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['nouveauMdp'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['nouveauMdp']); ?>
                    <?php endif; ?>
                    <p>Veuillez entrer à nouveau votre nouveau mot de passe.</p>
                    <input type="password" name="confirmationMdp" placeholder="Confirmation mot de passe"><br><br>
                    <?php if (isset($_SESSION['erreurs']['confirmationMdp'])): ?>
                        <span style="color: red"><?= $_SESSION['erreurs']['confirmationMdp'] ?></span><br>
                        <?php unset($_SESSION['erreurs']['confirmationMdp']); ?>
                    <?php endif; ?>
                    <input type="submit" name="envoyer" value="Modifier">
                </form>
            </div>
        </div>
    </div>
    <script src="../../public/scripts/profil_settings.js"></script>
    <script src="../../public/scripts/profil_moderation_settings.js"></script>
    <script src="../../public/scripts/gestion_abonnement.js"></script>
    <script src="../../public/scripts/image_cropper.js"></script>
</body>
</html>