<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ForaVerse</title>
  <link rel="stylesheet" href="public/styles/styles.css?<?php echo time(); ?>"> <!-- ton fichier CSS -->
</head>
<body>

  <!-- Header -->
  <header>
    <nav>
      <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="#">Forums</a></li>
        <li><a href="#">Communauté</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <main>
    <section class="hero">
      <h1 class="text-gradient">Bienvenue sur ForaVerse</h1>
      <p>Le portail ultime pour explorer l'univers du futur.</p>
      <button>Rejoindre la communauté</button>
    </section>

    <!-- Example Cards -->
    <section>
      <div class="card">
        <h2>Dernières discussions</h2>
        <p>Découvrez les sujets chauds de la semaine.</p>
      </div>

      <div class="card">
        <h2>Évènements à venir</h2>
        <p>Participez aux prochains tournois et meetups.</p>
      </div>
    </section>

    <!-- Formulaire simple -->
    <section>
      <h2 class="text-gradient">Nous contacter</h2>
      <form action="#">
        <label for="name">Nom</label>
        <input type="text" id="name" placeholder="Votre nom">

        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Votre email">
        <input type="radio" name="dureeban" value="1m">

        <label for="message">Message</label>
        <textarea id="message" rows="5" placeholder="Votre message"></textarea>

        <label for="name">Nom</label>
        <input type="text" id="name" placeholder="Votre nom">
        <small class="error-message">Erreur : Le nom est obligatoire</small>

        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Votre email">
        <small class="error-message">Erreur : Email invalide</small>


        <button type="submit">Envoyer</button>
      </form>
    </section>

  </main>

  <!-- Footer -->
  <footer>
    <p>© 2025 ForaVerse. Tous droits réservés.</p>
  </footer>

</body>
</html>
