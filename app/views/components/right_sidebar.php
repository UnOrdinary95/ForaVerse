<aside id="communaute_container1-2">
    <div class="auto_w">
        <div class="flex flex-col items-center justify-center margin3 card bg-background gap2" style="width: 80%;">
            <img id="communauteImage" src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="border-radius: 50%;" class="logo2 pointer" 
                <?php if (isset($_SESSION['Pseudo'])): ?>
                    onclick="document.getElementById('imageInput').click();"
                <?php endif; ?>
            >

            <h5 class="text-bold"><?= htmlspecialchars($communaute->getNom()) ?></h5>
            
            <p id="description" style="width: 100%;"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            
            <div class="flex flex-row justify-evenly test" style="width: 100%;">
                <small><?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></small>
                <small id="compteurMembres"><?= htmlspecialchars($nbr_membres) . " Membres"?></small>
            </div>
        </div>
        

    </div>
    
    <input type="file" id="imageInput" accept="image/*" style="display: none;">
    <div id="cropperContainer" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 0; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 1000; width: 40vw; max-width: 100vw; display: none;">
        <img id="imagePreview" src="" alt="Preview">
        <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
        <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
    </div>
    
    <h2>Propriétaire</h2>
    <a href="./?action=profil&utilisateur=<?= htmlspecialchars($proprio['pseudo']) ?>" class= "card flex items-center gap2 margin1">
        <img src="../../public/<?= htmlspecialchars($proprio['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
        <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $proprio['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($proprio['pseudo']) ?></span>
    </a>

    <h2>Modérateurs</h2>
    <div>
        <?php if(isset($mods) && count($mods) > 0): ?>
            <?php foreach($mods as $mod): ?>
                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($mod['pseudo']) ?>" class= "card flex items-center gap2 margin1">
                    <img src="../../public/<?= htmlspecialchars($mod['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                    <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $mod['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($mod['pseudo']) ?></span>
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
                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($membre['pseudo']) ?>" class= "card flex items-center gap2 margin1">
                    <img src="../../public/<?= htmlspecialchars($membre['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                    <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $membre['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($membre['pseudo']) ?></span>
                    <?php if($membre['admin']): ?>
                        <span style="color: red; font-weight: bold;">{Admin}</span>
                    <?php endif; ?>
                    <?php if($membre['banglobal']): ?>
                        <span style="color: green; font-weight: bold;">{Utilisateur banni}</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun membre pour le moment.</p>
        <?php endif; ?>
    </div>
</aside>