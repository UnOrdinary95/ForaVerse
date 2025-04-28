<aside id="left_sidebar">
    <form method="POST" action="?action=accueil">
        <select class="no-border-radius" name="reseau" id="reseau" onchange="this.form.submit()">
            <option value="select">🌐Réseau</option>
            <option value="communautes">Communautés (Par défaut)</option>
            <option value="abonnements">Abonnements</option>
        </select>
    </form>

    <div class="flex flex-col gap2">
        <?php if (isset($_SESSION['Pseudo'], $communautes_joined)): ?>
            <?php foreach ($communautes_joined as $uneCommunaute): ?>
                <a href="./?action=communaute&nomCommu=<?= htmlspecialchars($uneCommunaute->getNom()) ?>">
                    <div class="card flex flex-col gap2">
                        <img src="../../public/<?= htmlspecialchars($uneCommunaute->getCheminPhoto()) ?>" alt="Logo de la communauté" style="width: 50px; height: 50px; border-radius: 30%;">
                        <p class="text-gradient text-bold"><?= htmlspecialchars($uneCommunaute->getNom()) ?><p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php elseif (isset($abonnements)): ?>
            <?php foreach ($abonnements as $abonnement): ?>
                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($abonnement->getPseudo()) ?>">
                    <div class="card flex flex-row gap3">
                        <div class="flex flex-col">
                            <img src="../../public/<?= htmlspecialchars($abonnement->getCheminPhoto()) ?>" alt="Logo de l'abonnement" style="width: 50px; height: 50px; border-radius: 30%;">
                            <p class="text-gradient text-bold"><?= htmlspecialchars($abonnement->getPseudo()) ?><p>
                        </div>
                        <div>
                            <p>
                                <?= nl2br(htmlspecialchars($abonnement->getBio())) ?>
                            </p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</aside>