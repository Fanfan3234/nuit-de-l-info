<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['decathlon_sport_level'] = $_POST['sport_level'] ?? 'débutant';
    $_SESSION['decathlon_primary_goal'] = $_POST['primary_goal'] ?? 'cardio';
    $_SESSION['decathlon_time_available'] = $_POST['time_available'] ?? 15;
    $_SESSION['decathlon_injury'] = $_POST['injury'] ?? 'none';
    $_SESSION['decathlon_determination'] = $_POST['determination'] ?? 3;
    $_SESSION['qcm_completed'] = true;
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check if QCM is completed
$qcm_completed = $_SESSION['qcm_completed'] ?? false;

// Handle reset
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// If QCM not completed, show the quiz
if (!$qcm_completed):
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quiz Fitness - Découvrez vos produits Decathlon</title>
  <style>
    :root {
      --primary: #ffcc00;
      --secondary: #00aaff;
      --accent: #ff6600;
      --dark: #000;
      --darker: #111;
      --light: #ffffe0;
      --yellow-light: #fffacd;
    }
    
    body {
      background: var(--dark);
      color: var(--light);
      font-family: "Comic Sans MS", "Segoe UI", cursive, sans-serif;
      padding: 20px;
      min-height: 100vh;
      background: 
        radial-gradient(circle at 10% 20%, rgba(255, 204, 0, 0.1) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(0, 170, 255, 0.1) 0%, transparent 20%),
        linear-gradient(to bottom, #000 0%, #111 100%);
    }
    
    .container {
      max-width: 800px;
      margin: 0 auto;
    }
    
    header {
      text-align: center;
      margin-bottom: 40px;
      padding: 30px;
      background: rgba(255, 204, 0, 0.1);
      border-radius: 30px;
      border: 3px dashed var(--primary);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }
    
    .confetti {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 0;
      opacity: 0.3;
    }
    
    .confetti-item {
      position: absolute;
      width: 10px;
      height: 10px;
      background: var(--primary);
      border-radius: 50%;
    }
    
    .logo {
      font-size: 4rem;
      margin-bottom: 15px;
      display: block;
      animation: dance 2s infinite alternate;
      filter: drop-shadow(0 0 10px var(--primary));
    }
    
    h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
      color: var(--primary);
      text-shadow: 0 0 20px rgba(255, 204, 0, 0.5);
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-family: "Comic Sans MS", cursive;
    }
    
    .subtitle {
      font-size: 1.2rem;
      opacity: 0.9;
      margin-bottom: 20px;
      color: var(--yellow-light);
    }
    
    /* QCM Form */
    .qcm-card {
      background: rgba(30, 30, 40, 0.9);
      border-radius: 25px;
      padding: 40px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
      border: 2px solid var(--primary);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }
    
    .qcm-card::before {
      content: '🏋️🤸‍♀️💪🏃‍♂️🧘‍♀️';
      position: absolute;
      top: -20px;
      left: 0;
      right: 0;
      font-size: 3rem;
      text-align: center;
      opacity: 0.1;
      z-index: 0;
      animation: moveEmojis 20s linear infinite;
    }
    
    .question {
      margin-bottom: 40px;
      padding-bottom: 30px;
      border-bottom: 2px dashed rgba(255, 204, 0, 0.3);
      position: relative;
      z-index: 1;
    }
    
    .q-title {
      display: block;
      font-size: 1.6rem;
      font-weight: bold;
      margin-bottom: 20px;
      color: var(--primary);
      font-family: "Comic Sans MS", cursive;
    }
    
    .options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .options.compact {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .options label {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.08);
      border: 2px solid transparent;
      border-radius: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.1rem;
      position: relative;
      overflow: hidden;
    }
    
    .options label:hover {
      background: rgba(255, 204, 0, 0.2);
      border-color: var(--primary);
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 10px 20px rgba(255, 204, 0, 0.3);
    }
    
    .options input[type="radio"] {
      display: none;
    }
    
    .options input[type="radio"]:checked + span {
      color: var(--primary);
      font-weight: bold;
      text-shadow: 0 0 5px rgba(255, 204, 0, 0.5);
    }
    
    .options label span {
      flex: 1;
    }
    
    /* Range Slider */
    .range {
      width: 100%;
      height: 15px;
      -webkit-appearance: none;
      background: linear-gradient(90deg, var(--secondary), var(--primary));
      border-radius: 10px;
      outline: none;
      margin: 30px 0 15px;
      border: 2px solid rgba(255, 204, 0, 0.5);
    }
    
    .range::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary);
      cursor: pointer;
      border: 4px solid var(--dark);
      box-shadow: 0 0 20px var(--primary);
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"/></svg>');
      background-repeat: no-repeat;
      background-position: center;
      background-size: 60%;
    }
    
    .range-labels {
      display: flex;
      justify-content: space-between;
      color: var(--light);
      font-size: 2rem;
      margin-top: 10px;
    }
    
    /* Buttons */
    .actions {
      text-align: center;
      margin-top: 40px;
      padding-top: 30px;
      border-top: 2px dashed rgba(255, 204, 0, 0.3);
    }
    
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      padding: 20px 45px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.3rem;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      margin: 10px;
      font-family: "Comic Sans MS", cursive;
    }
    
    .primary {
      background: linear-gradient(90deg, var(--primary), var(--accent));
      color: var(--dark);
      box-shadow: 0 15px 35px rgba(255, 204, 0, 0.4);
      position: relative;
      overflow: hidden;
    }
    
    .primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: 0.5s;
    }
    
    .primary:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 25px 50px rgba(255, 204, 0, 0.6);
    }
    
    .primary:hover::before {
      left: 100%;
    }
    
    .ghost {
      background: transparent;
      color: var(--light);
      border: 3px solid rgba(255, 204, 0, 0.5);
    }
    
    .ghost:hover {
      background: rgba(255, 204, 0, 0.1);
      border-color: var(--primary);
      transform: translateY(-5px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }
      
      .qcm-card {
        padding: 25px;
      }
      
      h1 {
        font-size: 2.2rem;
      }
      
      .options {
        grid-template-columns: 1fr;
      }
      
      .btn {
        padding: 15px 30px;
        font-size: 1.1rem;
        width: 100%;
        margin: 10px 0;
      }
    }
    
    /* Animations */
    @keyframes dance {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      25% { transform: translateY(-20px) rotate(-5deg); }
      50% { transform: translateY(0) rotate(0deg); }
      75% { transform: translateY(-20px) rotate(5deg); }
    }
    
    @keyframes moveEmojis {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    
    @keyframes bounce {
      0%, 100% { transform: translateY(0) scale(1); }
      50% { transform: translateY(-30px) scale(1.2); }
    }
    
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    /* Emoji Animation */
    .emoji-animation {
      font-size: 4rem;
      text-align: center;
      margin: 40px 0;
      display: flex;
      justify-content: center;
      gap: 30px;
    }
    
    .emoji {
      animation: bounce 3s infinite;
      filter: drop-shadow(0 0 10px var(--primary));
    }
    
    .emoji:nth-child(1) { animation-delay: 0s; }
    .emoji:nth-child(2) { animation-delay: 0.5s; }
    .emoji:nth-child(3) { animation-delay: 1s; }
    .emoji:nth-child(4) { animation-delay: 1.5s; }
    
    /* Funny Images */
    .funny-image {
      width: 150px;
      height: 150px;
      margin: 20px auto;
      background: linear-gradient(45deg, var(--primary), var(--secondary));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 5rem;
      box-shadow: 0 20px 40px rgba(255, 204, 0, 0.4);
      animation: spin 20s linear infinite;
    }
    
    .funny-corner {
      position: fixed;
      font-size: 3rem;
      z-index: 1000;
      opacity: 0.3;
      animation: bounce 10s infinite;
    }
    
    .top-left { top: 20px; left: 20px; }
    .top-right { top: 20px; right: 20px; animation-delay: 2s; }
    .bottom-left { bottom: 20px; left: 20px; animation-delay: 4s; }
    .bottom-right { bottom: 20px; right: 20px; animation-delay: 6s; }
  </style>
</head>
<body>
  <!-- Funny corner emojis -->
  <div class="funny-corner top-left">🤪</div>
  <div class="funny-corner top-right">🤸‍♂️</div>
  <div class="funny-corner bottom-left">💥</div>
  <div class="funny-corner bottom-right">🎯</div>
  
  <div class="container">
    <header>
      <div class="confetti" id="confetti"></div>
      <div class="funny-image">😂</div>
      <h1>Quiz Fitness Extra Fun!</h1>
      <p class="subtitle">Répondez à ces questions hilarantes et découvrez vos produits Decathlon!</p>
      <div class="emoji-animation">
        <span class="emoji">🤸‍♂️</span>
        <span class="emoji">💪</span>
        <span class="emoji">🤣</span>
        <span class="emoji">🏆</span>
      </div>
    </header>
    
    <main class="qcm-card">
      <form method="POST" class="qcm">
        <!-- Question 1: Level -->
        <div class="question">
          <label class="q-title">Niveau sportif? On triche un peu? 😜</label>
          <div class="options">
            <label>
              <input type="radio" name="sport_level" value="débutant" checked>
              <span>🐌 "Je pense que le sport est une chaîne TV"</span>
            </label>
            <label>
              <input type="radio" name="sport_level" value="intermédiaire">
              <span>🚶 "Je cours... quand le bus passe"</span>
            </label>
            <label>
              <input type="radio" name="sport_level" value="avancé">
              <span>🏃‍♂️ "Mon canapé me manque déjà"</span>
            </label>
          </div>
        </div>
        
        <!-- Question 2: Goal -->
        <div class="question">
          <label class="q-title">Objectif? Soyons honnêtes... 🤔</label>
          <div class="options">
            <label>
              <input type="radio" name="primary_goal" value="muscle" checked>
              <span>💪 Impressionner ma belle-mère</span>
            </label>
            <label>
              <input type="radio" name="primary_goal" value="cardio">
              <span>🏃‍♀️ Courir après mes enfants</span>
            </label>
            <label>
              <input type="radio" name="primary_goal" value="flexibility">
              <span>🧘 Toucher mes orteils (un jour!)</span>
            </label>
            <label>
              <input type="radio" name="primary_goal" value="strength">
              <span>🏋️ Porter tous mes courses d'un coup</span>
            </label>
          </div>
        </div>
        
        <!-- Question 3: Time -->
        <div class="question">
          <label class="q-title">Temps dispo? Entre 2 épisodes Netflix 📺</label>
          <div class="options compact">
            <label>
              <input type="radio" name="time_available" value="15" checked>
              <span>⏱️ Pendant la pub</span>
            </label>
            <label>
              <input type="radio" name="time_available" value="30">
              <span>⏱️ Un épisode court</span>
            </label>
            <label>
              <input type="radio" name="time_available" value="45">
              <span>⏱️ Deux épisodes</span>
            </label>
            <label>
              <input type="radio" name="time_available" value="60">
              <span>⏱️ Un film... presque!</span>
            </label>
          </div>
        </div>
        
        <!-- Question 4: Injuries -->
        <div class="question">
          <label class="q-title">Blessures? On est tous un peu cassés 🤕</label>
          <div class="options">
            <label>
              <input type="radio" name="injury" value="none" checked>
              <span>✅ Juste l'ego</span>
            </label>
            <label>
              <input type="radio" name="injury" value="back">
              <span>🧍 Mon dos dit "non"</span>
            </label>
            <label>
              <input type="radio" name="injury" value="knee">
              <span>🦵 Genoux en cristal</span>
            </label>
            <label>
              <input type="radio" name="injury" value="wrist">
              <span>✋ Trop de scroll sur TikTok</span>
            </label>
          </div>
        </div>
        
        <!-- Question 5: Determination -->
        <div class="question">
          <label class="q-title">Détermination? De 1 à 5 cafés ☕</label>
          <input type="range" name="determination" min="1" max="5" value="3" class="range">
          <div class="range-labels">
            <span>😴</span>
            <span>🥱</span>
            <span>😊</span>
            <span>💪</span>
            <span>🤯</span>
          </div>
        </div>
        
        <div class="actions">
          <button type="submit" class="btn primary">
            🚀🎉 Voir mes recommandations FUN!
          </button>
          <a href="?reset=1" class="btn ghost">
            🔄 Je veux retenter ma chance!
          </a>
        </div>
      </form>
    </main>
  </div>
  
  <script>
    // Create confetti
    const confettiContainer = document.getElementById('confetti');
    for (let i = 0; i < 50; i++) {
      const confetti = document.createElement('div');
      confetti.className = 'confetti-item';
      confetti.style.left = Math.random() * 100 + '%';
      confetti.style.top = Math.random() * 100 + '%';
      confetti.style.width = Math.random() * 15 + 5 + 'px';
      confetti.style.height = confetti.style.width;
      confetti.style.background = Math.random() > 0.5 ? 'var(--primary)' : 'var(--secondary)';
      confetti.style.animation = `fall ${Math.random() * 3 + 2}s linear infinite`;
      confetti.style.animationDelay = Math.random() * 5 + 's';
      confettiContainer.appendChild(confetti);
    }
    
    // Add fall animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fall {
        0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
        100% { transform: translateY(500px) rotate(360deg); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
    
    // Add funny hover effects to labels
    document.querySelectorAll('.options label').forEach(label => {
      label.addEventListener('mouseenter', () => {
        const emojis = ['💥', '✨', '🌟', '🎉', '🔥', '💫'];
        const emoji = emojis[Math.floor(Math.random() * emojis.length)];
        const span = label.querySelector('span');
        const originalText = span.textContent;
        span.textContent = emoji + ' ' + originalText;
        
        setTimeout(() => {
          span.textContent = originalText;
        }, 300);
      });
    });
  </script>
</body>
</html>
<?php
else:
// ============================================================================
// QCM COMPLETED - SHOW RESULTS WITH REAL PRODUCTS
// ============================================================================

// Get user answers
$level = $_SESSION['decathlon_sport_level'] ?? 'débutant';
$goal  = $_SESSION['decathlon_primary_goal'] ?? 'cardio';
$time  = intval($_SESSION['decathlon_time_available'] ?? 15);
$inj   = $_SESSION['decathlon_injury'] ?? 'none';
$determination = $_SESSION['decathlon_determination'] ?? 3;

// Real Decathlon products with actual IDs
$products = [
  // --- MUSCULATION ---
  ["Haltère hexagonal 5 kg", "4132385"],        // Haltère Hex Dumbbell 5kg Corength
  ["Haltère hexagonal 10 kg", "4132392"],       // Haltère Hex Dumbbell 10kg Corength
  ["Kettlebell 8 kg", "4345071"],               // Kettlebell fonte base caoutchouc 8kg
  ["Kit haltères 20 kg", "411314"],            // Kit haltères musculation 20kg
  ["Banc de musculation", "2891874"],           // Banc pliable et inclinable Corength
  ["Tapis muscu 10 mm", "4792615"],             // Tapis Fitness résistant 10mm
  ["Élastique light", "2721020"],               // Training Band 5kg (Bleu)
  ["Élastique medium", "2531585"],              // Training Band 15kg (Vert)

  // --- RUNNING ---
  ["Chaussures jogging Run Cushion", "4479744"], // Jogflow 100.1 (Successeur Run Cushion)
  ["Chaussures running KS Light", "5379347"],   // Kiprun KS900 Light (Successeur KS Light)
  ["Brassard smartphone", "4810762"],           // Brassard Kiprun Multipoches
  ["Ceinture running bouteilles", "4562963"],   // Ceinture porte-flasques Kiprun
  ["Montre ONStart 110", "5094964"],            // Montre W200 S (Modèle actuel équivalent)

  // --- FITNESS / CROSS TRAINING ---
  ["Tapis fitness 7 mm", "4298568"],            // Tapis pliable 7mm Domyos
  ["Corde à sauter", "5708842"],                // Corde à sauter 100 Domyos
  ["Gym ball 65 cm", "4250010"],                // Gym Ball Résistant Taille 2
  ["Step training", "2670054"],                 // Step 100 Domyos
  ["Mini élastiques (pack)", "2933074"],        // Set de 3 Mini Bands (Ref standard Domyos)

  // --- YOGA ---
  ["Tapis yoga 4 mm", "2456757"],               // Tapis Yoga Doux 4mm Vert
  ["Brique yoga liège", "2976432"],             // Brique Liège Naturel
  ["Sangle yoga", "2528920"],                   // Sangle Coton 2.5m
  ["Roue yoga", "2687148"],                    // Bolster Yoga Coton Bio (Ref Kimjaly standard)

 // --- SPORTS COLLECTIFS ---
  ["Ballon foot F100", "4786370"],              // Ballon Foot F100 Orange
  ["Ballon basket BT100", "4325027"],           // Ballon Basket T5 BT100
  ["Ballon volley V100", "4147076"],            // Ballon Volley V100 Turquoise
  ["Ballon handball H100", "5446340"],          // Ballon Hand H100 Soft T00
  ["Mini cônes", "2747140 "],                    // Lot de 4 Cônes Essential 30cm

  

  // --- SANTÉ / RÉCUPÉRATION ---
  ["Rouleau massage", "8858864"],               // Foam Roller Soft Bleu
  ["Balle massage", "8389644"],                 // Balle Massage Small Noire
  ["Pistolet massage", "8585304"],              // Pistolet Massage Mini
               // Tapis Acupression (Ref standard)
  ["Gants training", "8595161"],                // Gants Musculation 100

  // --- NATATION ---
  ["Lunettes natation X-Base", "8669830"],      // Lunettes X-Base S Fumé
  ["Bonnet silicone", "8926575"],               // Bonnet Silicone Jaune
  ["Plaquettes nage", "8842637"],               // Plaquettes 900 L
  ["Pull buoy", "8548119"],                     // Pull Kick 900
  ["Short de bain", "8927714"],                 // Short Homme 100 Tex

  // --- VÉLO ---
  ["Gants vélo 500", "8530326"],                // Gants Vélo 500 Hiver
  ["Bidon sport 750ml", "4459437"],             // Bidon Vélo 800ml (Triban)
  ["Multi-outil vélo 900", "8767563"],          // Multitool 900 Métal
  ["Pompe vélo compacte", "8543541"],           // Pompe Main Route Compact
         // Lot 2 Chambres 700x25-32 Valve 60mm
];

// Select products based on user's goal and level
function select_recommended_products($goal, $level, $all_products) {
    $recommended = [];
    
    // Map goals to product indices
    $goal_map = [
        'muscle' => [0, 1, 2, 3, 4, 5, 6], // Strength equipment
        'cardio' => [9, 10, 12, 13, 15, 16], // Running/cardio gear
        'flexibility' => [19, 20, 21, 22, 23], // Yoga equipment
        'strength' => [0, 1, 2, 3, 4, 5, 37], // Strength training
    ];
    
    // Get base products for the goal
    $base_indices = $goal_map[$goal] ?? $goal_map['cardio'];
    
    // Select 3-4 products based on level
    $num_products = ($level === 'débutant') ? 3 : 4;
    
    // Pick random products from the goal category
    shuffle($base_indices);
    $selected_indices = array_slice($base_indices, 0, $num_products);
    
    foreach ($selected_indices as $index) {
        if (isset($all_products[$index])) {
            $recommended[] = $all_products[$index];
        }
    }
    
    // Add a lifestyle product (backpack or bottle)
    $lifestyle_products = [[29, "Sac à dos NH100 20L", "8529018"], [30, "Bouteille isotherme", "8735325"]];
    $lifestyle = $lifestyle_products[array_rand($lifestyle_products)];
    $recommended[] = [$lifestyle[1], $lifestyle[2]];
    
    return $recommended;
}

// Get recommended products
$recommended_products = select_recommended_products($goal, $level, $products);

// Create basket for Decathlon
$basket_items = [];
foreach ($recommended_products as $product) {
    // Set quantity based on product type
    $quantity = 1;
    if (strpos($product[0], 'élastique') !== false || strpos($product[0], 'pack') !== false) {
        $quantity = 2;
    }
    
    $basket_items[] = [
        "id" => $product[1],
        "quantity" => $quantity
    ];
}

// Build basket object
$basket = [
    "externalWebsite" => "NIRD",
    "items" => $basket_items
];

// Create Decathlon URL
$json = json_encode($basket);
$encoded_json = rawurlencode($json);
$decathlon_url = "https://www.decathlon.fr/externalBasket?basket=" . $encoded_json;

// Generate workout plan based on answers
function generate_workout_plan($goal, $time, $level) {
    $plans = [
        'muscle' => [
            'Échauffement: 5 min de danse ridicule',
            'Squats: 3 séries (en chantant)',
            'Pompes: 3 séries (cri autorisé)',
            'Fentes: 3 séries en imaginant éviter un zombie',
            'Gainage: Tenez le plus longtemps possible (pensez à la pizza)'
        ],
        'cardio' => [
            'Échauffement: 5 min de course sur place (comme dans les films)',
            'Jumping jacks: 30 secondes (faire le plus de bruit possible)',
            'Montées de genoux: 30 secondes (imaginer escalader une montagne de glace)',
            'Burpees: 30 secondes (le pire exercice du monde!)',
            'Repos: 30 secondes (temps de récupération des cheveux)'
        ],
        'flexibility' => [
            'Respiration profonde: 5 minutes (ou 5 respirations Instagram)',
            'Étirements jambes: 2 min chaque (en regardant une série)',
            'Posture du chat-vache: 1 minute (meow!)',
            'Torsion assise: 30 sec chaque côté (comme un essoreuse)',
            'Posture de l\'enfant: 2 minutes (faire la sieste)'
        ]
    ];
    
    $plan = $plans[$goal] ?? $plans['cardio'];
    
    // Adjust based on time
    if ($time < 20) {
        $plan = array_slice($plan, 0, 3);
    } elseif ($time > 40) {
        $plan = array_merge($plan, ['Exercice bonus: 5 min de rire thérapeutique']);
    }
    
    return $plan;
}

$workout_plan = generate_workout_plan($goal, $time, $level);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vos recommandations Fitness Extra Fun!</title>
  <style>
    :root {
      --primary: #ffcc00;
      --secondary: #00aaff;
      --accent: #ff6600;
      --dark: #000;
      --darker: #111;
      --light: #ffffe0;
      --yellow-light: #fffacd;
      --blue-light: #e6f7ff;
    }
    
    body {
      background: var(--dark);
      color: var(--light);
      font-family: "Comic Sans MS", cursive, sans-serif;
      min-height: 100vh;
      margin: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(255, 204, 0, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(0, 170, 255, 0.2) 0%, transparent 50%),
        linear-gradient(135deg, #000 0%, #222 100%);
      padding: 20px;
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
    }
    
    /* Header */
    .results-header {
      text-align: center;
      padding: 60px 40px;
      background: rgba(255, 204, 0, 0.1);
      border-radius: 40px;
      border: 4px dashed var(--primary);
      margin-bottom: 50px;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }
    
    .party-popper {
      position: absolute;
      font-size: 3rem;
      animation: partyPop 3s infinite;
    }
    
    .trophy {
      font-size: 6rem;
      margin-bottom: 30px;
      display: block;
      animation: trophyDance 3s infinite;
      filter: drop-shadow(0 0 20px var(--primary));
      text-shadow: 0 0 30px var(--primary);
    }
    
    h1 {
      font-size: 4rem;
      margin-bottom: 20px;
      background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 40px rgba(255, 204, 0, 0.3);
      font-family: "Comic Sans MS", cursive;
    }
    
    .subtitle {
      font-size: 1.8rem;
      opacity: 0.9;
      max-width: 700px;
      margin: 0 auto;
      color: var(--yellow-light);
    }
    
    /* Content Grid */
    .content-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-bottom: 60px;
    }
    
    @media (max-width: 1024px) {
      .content-grid {
        grid-template-columns: 1fr;
      }
    }
    
    /* Profile Card */
    .profile-card, .workout-card {
      background: rgba(40, 40, 60, 0.9);
      border-radius: 30px;
      padding: 40px;
      border: 3px solid var(--primary);
      backdrop-filter: blur(10px);
      box-shadow: 0 25px 70px rgba(255, 204, 0, 0.2);
      position: relative;
      overflow: hidden;
    }
    
    .profile-card::before, .workout-card::before {
      content: '💪🎯🏆✨';
      position: absolute;
      top: 10px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 2.5rem;
      opacity: 0.1;
      z-index: 0;
      animation: floatText 15s linear infinite;
    }
    
    .card-title {
      font-size: 2.2rem;
      color: var(--primary);
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
      z-index: 1;
      font-family: "Comic Sans MS", cursive;
    }
    
    /* Profile Summary */
    .profile-summary {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    
    .profile-item {
      background: rgba(255, 255, 255, 0.08);
      padding: 25px;
      border-radius: 20px;
      border-left: 5px solid var(--secondary);
      position: relative;
      z-index: 1;
      transition: transform 0.3s ease;
    }
    
    .profile-item:hover {
      transform: translateY(-10px);
      background: rgba(255, 204, 0, 0.15);
    }
    
    .profile-label {
      font-size: 0.9rem;
      color: var(--yellow-light);
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .profile-value {
      font-size: 1.6rem;
      font-weight: bold;
      color: var(--light);
    }
    
    /* Workout Plan */
    .workout-list {
      list-style: none;
      padding: 0;
      position: relative;
      z-index: 1;
    }
    
    .workout-list li {
      padding: 25px;
      margin-bottom: 15px;
      background: rgba(255, 204, 0, 0.1);
      border-radius: 15px;
      border-left: 5px solid var(--accent);
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      gap: 20px;
      transition: all 0.3s ease;
    }
    
    .workout-list li:hover {
      transform: translateX(10px);
      background: rgba(255, 204, 0, 0.2);
    }
    
    .workout-list li::before {
      content: '🎯';
      color: var(--primary);
      font-size: 1.5rem;
    }
    
    /* Products Section */
    .products-section {
      background: rgba(40, 40, 60, 0.9);
      border-radius: 30px;
      padding: 50px;
      margin-bottom: 50px;
      border: 3px solid var(--secondary);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }
    
    .products-section::before {
      content: '🛒🛍️💸🎁';
      position: absolute;
      top: 20px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 3rem;
      opacity: 0.1;
      animation: floatText 20s linear infinite reverse;
    }
    
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 30px;
      margin-top: 40px;
      position: relative;
      z-index: 1;
    }
    
    .product-card {
      background: linear-gradient(135deg, rgba(255, 204, 0, 0.1), rgba(0, 170, 255, 0.1));
      border-radius: 25px;
      padding: 35px;
      border: 2px solid transparent;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
    }
    
    .product-card:hover {
      border-color: var(--primary);
      transform: translateY(-15px) scale(1.03);
      box-shadow: 0 30px 60px rgba(255, 204, 0, 0.3);
    }
    
    .product-card::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
      transform: rotate(45deg);
      transition: 0.5s;
    }
    
    .product-card:hover::after {
      left: 100%;
    }
    
    .product-badge {
      position: absolute;
      top: 20px;
      right: 20px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
      color: var(--dark);
      padding: 10px 25px;
      border-radius: 50px;
      font-weight: bold;
      font-size: 1rem;
      z-index: 2;
      animation: badgePulse 2s infinite;
    }
    
    .product-icon {
      font-size: 4rem;
      margin-bottom: 25px;
      display: block;
      text-align: center;
      animation: bounce 3s infinite;
    }
    
    .product-name {
      font-size: 1.7rem;
      font-weight: bold;
      margin-bottom: 15px;
      color: var(--light);
      text-align: center;
      font-family: "Comic Sans MS", cursive;
    }
    
    .product-id {
      font-family: monospace;
      background: rgba(255, 204, 0, 0.2);
      color: var(--primary);
      padding: 12px 20px;
      border-radius: 15px;
      display: block;
      text-align: center;
      margin-top: 20px;
      font-size: 1.2rem;
      border: 1px dashed var(--primary);
    }
    
    /* Basket CTA */
    .basket-cta {
      background: linear-gradient(135deg, rgba(255, 204, 0, 0.2), rgba(0, 170, 255, 0.2));
      border-radius: 40px;
      padding: 80px 40px;
      text-align: center;
      margin: 80px 0;
      border: 4px solid var(--primary);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
    }
    
    .basket-cta::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="%23ffcc00" opacity="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
      opacity: 0.3;
    }
    
    .basket-icon {
      font-size: 7rem;
      margin-bottom: 40px;
      display: block;
      animation: basketJump 2s infinite;
      filter: drop-shadow(0 0 20px var(--primary));
    }
    
    .basket-title {
      font-size: 3.5rem;
      margin-bottom: 30px;
      color: var(--primary);
      font-family: "Comic Sans MS", cursive;
      text-shadow: 0 0 20px rgba(255, 204, 0, 0.5);
    }
    
    .basket-description {
      font-size: 1.5rem;
      max-width: 800px;
      margin: 0 auto 50px;
      opacity: 0.9;
      line-height: 1.6;
    }
    
    .basket-btn {
      display: inline-flex;
      align-items: center;
      gap: 25px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
      color: var(--dark);
      text-decoration: none;
      padding: 30px 70px;
      border-radius: 60px;
      font-size: 1.8rem;
      font-weight: bold;
      transition: all 0.4s ease;
      box-shadow: 0 30px 70px rgba(255, 204, 0, 0.5);
      border: none;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      font-family: "Comic Sans MS", cursive;
    }
    
    .basket-btn:hover {
      transform: translateY(-10px) scale(1.08);
      box-shadow: 0 50px 100px rgba(255, 204, 0, 0.7);
    }
    
    .basket-btn::after {
      content: '🎉';
      position: absolute;
      right: 20px;
      animation: spin 2s linear infinite;
    }
    
    /* Debug Info */
    .debug-info {
      background: rgba(0, 0, 0, 0.9);
      border-radius: 25px;
      padding: 30px;
      margin-top: 40px;
      border: 2px solid var(--primary);
      font-family: monospace;
    }
    
    details {
      color: var(--primary);
    }
    
    summary {
      cursor: pointer;
      padding: 20px;
      font-weight: bold;
      font-size: 1.3rem;
      font-family: "Comic Sans MS", cursive;
    }
    
    pre {
      background: rgba(255, 204, 0, 0.1);
      padding: 25px;
      border-radius: 15px;
      overflow-x: auto;
      color: var(--primary);
      margin-top: 20px;
      font-size: 0.9rem;
      border: 1px solid rgba(255, 204, 0, 0.3);
    }
    
    /* Restart Button */
    .restart-section {
      text-align: center;
      margin-top: 60px;
      padding-top: 40px;
      border-top: 3px dashed rgba(255, 204, 0, 0.3);
    }
    
    .restart-btn {
      display: inline-flex;
      align-items: center;
      gap: 20px;
      background: rgba(255, 204, 0, 0.2);
      color: var(--light);
      text-decoration: none;
      padding: 25px 50px;
      border-radius: 50px;
      font-size: 1.5rem;
      font-weight: bold;
      transition: all 0.3s ease;
      border: 3px solid var(--primary);
      font-family: "Comic Sans MS", cursive;
    }
    
    .restart-btn:hover {
      background: rgba(255, 204, 0, 0.4);
      transform: rotate(-5deg) scale(1.1);
    }
    
    /* Animations */
    @keyframes trophyDance {
      0%, 100% { transform: rotate(0deg) scale(1); }
      25% { transform: rotate(-15deg) scale(1.1); }
      50% { transform: rotate(0deg) scale(1); }
      75% { transform: rotate(15deg) scale(1.1); }
    }
    
    @keyframes partyPop {
      0%, 100% { transform: translateY(0) scale(1); }
      50% { transform: translateY(-50px) scale(1.2); }
    }
    
    @keyframes basketJump {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-30px) rotate(10deg); }
    }
    
    @keyframes badgePulse {
      0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.7); }
      50% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(255, 204, 0, 0); }
    }
    
    @keyframes floatText {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    /* Funny Floating Emojis */
    .floating-emoji {
      position: fixed;
      font-size: 3rem;
      z-index: 1000;
      pointer-events: none;
      animation: floatAround 20s linear infinite;
    }
    
    @keyframes floatAround {
      0% { transform: translate(0, 0) rotate(0deg); }
      25% { transform: translate(100vw, 50vh) rotate(90deg); }
      50% { transform: translate(50vw, 100vh) rotate(180deg); }
      75% { transform: translate(-50vw, 50vh) rotate(270deg); }
      100% { transform: translate(0, 0) rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }
      
      .results-header {
        padding: 40px 20px;
      }
      
      h1 {
        font-size: 2.8rem;
      }
      
      .trophy {
        font-size: 4rem;
      }
      
      .profile-card, .workout-card, .products-section {
        padding: 25px;
      }
      
      .profile-summary {
        grid-template-columns: 1fr;
      }
      
      .products-grid {
        grid-template-columns: 1fr;
      }
      
      .basket-cta {
        padding: 40px 20px;
      }
      
      .basket-title {
        font-size: 2.5rem;
      }
      
      .basket-btn {
        padding: 20px 40px;
        font-size: 1.3rem;
        width: 100%;
        justify-content: center;
      }
      
      .basket-icon {
        font-size: 5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Floating funny emojis -->
  <?php
  $funny_emojis = ['🤪', '🤸‍♂️', '💥', '🎯', '😂', '✨', '🌟', '🔥'];
  for ($i = 0; $i < 8; $i++) {
    echo '<div class="floating-emoji" style="top:' . rand(0, 100) . 'vh; left:' . rand(0, 100) . 'vw; animation-delay:' . ($i * 2) . 's;">' . $funny_emojis[$i] . '</div>';
  }
  ?>
  
  <div class="container">
    <!-- Results Header -->
    <div class="results-header">
      <!-- Party poppers -->
      <div class="party-popper" style="top:20px; left:20px;">🎉</div>
      <div class="party-popper" style="top:20px; right:20px; animation-delay:0.5s;">🎊</div>
      <div class="party-popper" style="bottom:20px; left:20px; animation-delay:1s;">✨</div>
      <div class="party-popper" style="bottom:20px; right:20px; animation-delay:1.5s;">🌟</div>
      
      <span class="trophy">🏆🤣</span>
      <h1>Bravo Champion! 🎉</h1>
      <p class="subtitle">Voici votre programme FUN et vos produits Decathlon spécialement sélectionnés avec amour (et humour)!</p>
    </div>
    
    <!-- Content Grid: Profile & Workout -->
    <div class="content-grid">
      <!-- Profile Summary -->
      <div class="profile-card">
        <h2 class="card-title">🤪 Votre profil sportif FUN</h2>
        <div class="profile-summary">
          <div class="profile-item">
            <div class="profile-label">Niveau</div>
            <div class="profile-value"><?= htmlspecialchars(ucfirst($level)) ?> 😎</div>
          </div>
          <div class="profile-item">
            <div class="profile-label">Objectif secret</div>
            <div class="profile-value"><?= htmlspecialchars(ucfirst($goal)) ?> 🎯</div>
          </div>
          <div class="profile-item">
            <div class="profile-label">Temps entre 2 snacks</div>
            <div class="profile-value"><?= $time ?> minutes ⏱️</div>
          </div>
          <div class="profile-item">
            <div class="profile-label">Blessures comiques</div>
            <div class="profile-value"><?= $inj === 'none' ? 'Juste l\'ego' : htmlspecialchars(ucfirst($inj)) ?> 🤕</div>
          </div>
        </div>
      </div>
      
      <!-- Workout Plan -->
      <div class="workout-card">
        <h2 class="card-title">😂 Programme d'entraînement FUN</h2>
        <ul class="workout-list">
          <?php foreach($workout_plan as $exercise): ?>
            <li><?= htmlspecialchars($exercise) ?></li>
          <?php endforeach; ?>
        </ul>
        <p style="color: var(--yellow-light); margin-top: 30px; font-style: italic; font-size: 1.1rem;">
          ⚠️ Important : N'oubliez pas de prendre des selfies pendant l'effort pour Instagram! 📸
        </p>
      </div>
    </div>
    
    <!-- Recommended Products -->
    <div class="products-section">
      <h2 class="card-title">🛍️ Produits recommandés (version FUN!)</h2>
      <p style="font-size: 1.3rem; opacity: 0.9; margin-bottom: 30px; text-align: center;">
        Ces produits Decathlon vont révolutionner votre vie... ou au moins votre prochain entraînement! 😂
      </p>
      
      <div class="products-grid">
        <?php 
        $product_emojis = ['💪', '🏃‍♂️', '🧘‍♀️', '🏋️', '🎽', '👟'];
        foreach($recommended_products as $index => $product): 
          $emoji = $product_emojis[$index % count($product_emojis)];
        ?>
          <div class="product-card">
            <div class="product-badge">TOP FUN! <?= $emoji ?></div>
            <div class="product-icon"><?= $emoji ?></div>
            <h3 class="product-name"><?= htmlspecialchars($product[0]) ?></h3>
            <div class="product-id">ID: <?= htmlspecialchars($product[1]) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Basket CTA -->
    <div class="basket-cta">
      <span class="basket-icon">🛒😂</span>
      <h2 class="basket-title">Ajouter tout au panier Decathlon!</h2>
      <p class="basket-description">
        Un seul clic pour ajouter tous ces produits FUN directement dans votre panier sur Decathlon.fr
        <br>La redirection est sécurisée, instantanée et 100% amusante! 🎉
      </p>
      
      <a href="<?= htmlspecialchars($decathlon_url) ?>" 
         target="_blank" 
         class="basket-btn"
         onclick="trackConversion(event)">
        <i class="fas fa-external-link-alt"></i>
        Ouvrir mon panier FUN sur Decathlon.fr
      </a>
      
      <p style="margin-top: 40px; opacity: 0.9; font-size: 1rem; font-style: italic;">
        <i class="fas fa-shield-alt"></i> Connexion sécurisée • Nouvel onglet • Produits ajoutés automatiquement • Smileys inclus! 😊
      </p>
    </div>
    
    <!-- Debug Info -->
    <div class="debug-info">
      <details>
        <summary>🤓 Voir les données techniques (pour les geeks)</summary>
        <h3>Structure JSON envoyée à Decathlon :</h3>
        <pre><?= htmlspecialchars(json_encode($basket, JSON_PRETTY_PRINT)) ?></pre>
        
        <h3>URL générée :</h3>
        <p style="word-break: break-all; color: var(--primary); font-family: monospace;">
          <?= htmlspecialchars($decathlon_url) ?>
        </p>
        
        <h3>Longueur URL :</h3>
        <p><?= strlen($decathlon_url) ?> caractères (si c'est trop long, on réduit le FUN!)</p>
      </details>
    </div>
    
    <!-- Restart Button -->
    <div class="restart-section">
      <a href="?reset=1" class="restart-btn">
        <i class="fas fa-redo"></i>
        Retenter ma chance! 🤞
      </a>
    </div>
  </div>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <script>
    // Track conversion
    function trackConversion(event) {
      // Show funny loading state
      const btn = event.target.closest('.basket-btn');
      const originalHTML = btn.innerHTML;
      
      const funnyMessages = [
        "Chargement du FUN... 🎉",
        "Préparation des smileys... 😊",
        "Ajout de l'humour... 🤣",
        "Presque prêt... 💪",
        "Redirection vers le bonheur! 🛒"
      ];
      
      let i = 0;
      btn.innerHTML = funnyMessages[i] + ' <i class="fas fa-spinner fa-spin"></i>';
      btn.style.pointerEvents = 'none';
      
      const interval = setInterval(() => {
        i++;
        if (i < funnyMessages.length) {
          btn.innerHTML = funnyMessages[i] + ' <i class="fas fa-spinner fa-spin"></i>';
        } else {
          clearInterval(interval);
          // Let the link proceed naturally
        }
      }, 800);
      
      // Re-enable button after 5 seconds
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.pointerEvents = 'auto';
      }, 5000);
    }
    
    // Add animations to product cards
    document.addEventListener('DOMContentLoaded', function() {
      const productCards = document.querySelectorAll('.product-card');
      productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
        card.style.animation = 'fadeIn 0.8s ease forwards';
      });
      
      // Make profile items bounce on hover
      document.querySelectorAll('.profile-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
          const emoji = ['😎', '🎯', '💪', '🤕', '😜', '🤸‍♂️'];
          const randomEmoji = emoji[Math.floor(Math.random() * emoji.length)];
          const valueSpan = this.querySelector('.profile-value');
          const originalText = valueSpan.textContent;
          valueSpan.textContent = randomEmoji + ' ' + originalText;
          
          setTimeout(() => {
            valueSpan.textContent = originalText;
          }, 1000);
        });
      });
      
      // Add a funny alert if URL is too long
      if (<?= strlen($decathlon_url) ?> > 1800) {
        const debug = document.querySelector('details');
        if (debug) debug.open = true;
        
        // Add funny warning
        const warning = document.createElement('div');
        warning.style.cssText = `
          background: linear-gradient(90deg, #ffcc00, #ff6600);
          color: #000;
          padding: 20px;
          border-radius: 15px;
          margin: 20px 0;
          text-align: center;
          font-weight: bold;
          font-size: 1.2rem;
          animation: pulse 2s infinite;
        `;
        warning.innerHTML = '⚠️ Oups! L\'URL est un peu trop longue... On a peut-être mis trop de FUN! 😂';
        document.querySelector('.debug-info').prepend(warning);
      }
    });
  </script>
</body>
</html>
<?php
endif;
?>