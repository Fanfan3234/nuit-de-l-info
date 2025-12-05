<?php
session_start();

// --- Fictitious product catalog (50 products) ---
$product_catalog = [
    'cardio' => [
        ['id'=>'DK410001','name'=>'Tapis de sol 10mm AirSoft'],
        ['id'=>'DK410002','name'=>'Corde à sauter SpeedX'],
        ['id'=>'DK410003','name'=>'Brassard smartphone RunFree'],
        ['id'=>'DK410004','name'=>'Short respirant Ventilo'],
        ['id'=>'DK410005','name'=>'T-shirt RunFit Dry+'],
        ['id'=>'DK410006','name'=>'Chaussures jogging LightStep'],
        ['id'=>'DK410007','name'=>'Gourde Running Flow 600ml'],
        ['id'=>'DK410008','name'=>'Ceinture cardio SmartBeat'],
        ['id'=>'DK410009','name'=>'Sac banane sport AirBelt'],
        ['id'=>'DK410010','name'=>'Bandeau anti-transpiration AeroCool']
    ],
    'muscle' => [
        ['id'=>'DK420001','name'=>'Haltères hexagonaux 5kg'],
        ['id'=>'DK420002','name'=>'Kettlebell 8kg BlackSteel'],
        ['id'=>'DK420003','name'=>'Élastique résistance Medium'],
        ['id'=>'DK420004','name'=>'AB Wheel Pro'],
        ['id'=>'DK420005','name'=>'Banc de musculation Compact'],
        ['id'=>'DK420006','name'=>'Barre de traction DoorFit'],
        ['id'=>'DK420007','name'=>'Gants muscu GripMax'],
        ['id'=>'DK420008','name'=>'Ceinture lombaire StrongLift'],
        ['id'=>'DK420009','name'=>'Kit Pump 20kg'],
        ['id'=>'DK420010','name'=>'Disques fonte 2×5kg']
    ],
    'flexibility' => [
        ['id'=>'DK430001','name'=>'Tapis yoga Confort 8mm'],
        ['id'=>'DK430002','name'=>'Brique yoga mousse SoftBlock'],
        ['id'=>'DK430003','name'=>'Sangle yoga 2.5m Light'],
        ['id'=>'DK430004','name'=>'Coussin méditation ZenPillow'],
        ['id'=>'DK430005','name'=>'Tapis liège Premium'],
        ['id'=>'DK430006','name'=>'Roue de yoga WheelFlex'],
        ['id'=>'DK430007','name'=>'Huile essentielle RelaxFit'],
        ['id'=>'DK430008','name'=>'Chaussettes antidérapantes YogaGrip'],
        ['id'=>'DK430009','name'=>'Sweat léger FlowWear'],
        ['id'=>'DK430010','name'=>'Sac yoga porté dos']
    ],
    'balance' => [
        ['id'=>'DK440001','name'=>'Balance Board Pro'],
        ['id'=>'DK440002','name'=>'Coussin d’équilibre Gonflable'],
        ['id'=>'DK440003','name'=>'Mini-bande élastique Set 3 niveaux'],
        ['id'=>'DK440004','name'=>'Rouleau de massage FoamRoller'],
        ['id'=>'DK440005','name'=>'Balle massage TriggerBall'],
        ['id'=>'DK440006','name'=>'Planche proprioception WoodBalance'],
        ['id'=>'DK440007','name'=>'Bande proprioception StrongMob'],
        ['id'=>'DK440008','name'=>'Tapis antidérapant Flex+'],
        ['id'=>'DK440009','name'=>'Kit mobilité MovePack'],
        ['id'=>'DK440010','name'=>'Semelles sport Anti-Shock']
    ],
    'lifestyle' => [
        ['id'=>'DK450001','name'=>'Sac à dos Sport 25L'],
        ['id'=>'DK450002','name'=>'Sweat Capuche UrbanFit'],
        ['id'=>'DK450003','name'=>'T-shirt coton SportLife'],
        ['id'=>'DK450004','name'=>'Casquette Training Fresh'],
        ['id'=>'DK450005','name'=>'Montre sport BasicTrack'],
        ['id'=>'DK450006','name'=>'Serviette microfibre QuickDry'],
        ['id'=>'DK450007','name'=>'Gourde inox TermoSport'],
        ['id'=>'DK450008','name'=>'Chaussures polyvalentes CityFit'],
        ['id'=>'DK450009','name'=>'Lunettes anti-UV Running'],
        ['id'=>'DK450010','name'=>'Porte-clés mousqueton UltraClip']
    ]
];

// Determine user answers (fallbacks)
$goal = $_SESSION['decathlon_primary_goal'] ?? 'flexibility';
$time = intval($_SESSION['decathlon_time_available'] ?? 15);
$level= $_SESSION['decathlon_sport_level'] ?? 'none';
$injury = $_SESSION['decathlon_injury'] ?? 'none';

// Choose recommended products (simple rules)
// We'll pick 3 products: primary from goal category, plus 2 supportive items (one lifestyle)
function pick_products($goal, $catalog) {
    $result = [];

    // clamp goal
    $primary_list = $catalog[$goal] ?? $catalog['flexibility'];
    // pick first primary
    $result[] = $primary_list[0];

    // pick supportive second (index 1)
    $result[] = $primary_list[1];

    // supportive lifestyle item
    global $product_catalog;
    $result[] = $product_catalog['lifestyle'][0]; // backpack
    return $result;
}

$recommended = pick_products($goal, $product_catalog);

// Build the basket array (Decathlon external basket structure imitation)
$basket = [
    'externalWebsite' => 'NIRD',
    'itemsProducts' => []
];

foreach ($recommended as $p) {
    $basket['itemsProducts'][] = [
        'id' => $p['id'],
        'quantity' => 1
    ];
}

// Create Decathlon URL (encode JSON)
$json = json_encode($basket);
$decathlonUrl = 'https://www.decathlon.fr/externalBasket?basket=' . rawurlencode($json);

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ton kit recommandé — NIRD</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="decathlon-style.css">
</head>
<body>
  <header class="site-header">
    <div class="brand">
      <div class="logo">🌀</div>
      <div class="title">NIRD — Ton Kit</div>
    </div>
  </header>

  <main class="main">
    <section class="card results">
      <h1>Produits recommandés pour toi</h1>
      <p class="muted">Ces produits sont choisis par un script très sérieux et légèrement cocasse.</p>

      <div class="products">
        <?php foreach ($recommended as $p): ?>
          <div class="product-card">
            <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
            <div class="product-id">ID: <?php echo htmlspecialchars($p['id']); ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="actions">
        <a class="btn primary big" href="<?php echo htmlspecialchars($decathlonUrl); ?>" target="_blank" rel="noreferrer noopener">
          🛒 Ouvrir le panier Decathlon (ouvre une nouvelle fenêtre)
        </a>
        <p class="note">Note : IDs fictifs (mode test). Si tu veux que ça ouvre un vrai panier, remplace les IDs par de vrais IDs Decathlon.</p>

        <a class="btn ghost" href="index.php">🔁 Refaire le QCM</a>
      </div>

      <div class="debug">
        <details>
          <summary>Voir JSON du panier (debug)</summary>
          <pre><?php echo htmlspecialchars($json); ?></pre>
          <p>URL encodée : <code><?php echo htmlspecialchars($decathlonUrl); ?></code></p>
        </details>
      </div>
    </section>
  </main>
</body>
</html>
