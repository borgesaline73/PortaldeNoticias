<?php 

require './vendor/autoload.php';
require 'Classes/OpenWeather.php';

//banco 
$dsn = "pgsql:host=localhost;port=5432;dbname=PortalNoticias;";
$pdo = new PDO($dsn, "postgres", "1234");

// Recuperar todas as notícias
$sql = "SELECT manchete, datapubli, imagem, textonoticia FROM noticia";
$statement = $pdo->prepare($sql);
$statement->execute();
$noticias = $statement->fetchAll(PDO::FETCH_ASSOC);

$o = new OpenWeather();

$clima = $o->getTempoAtual();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Notícias</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2a2a2a;
            color: #f4f4f4;
        }
        .navbar {
            background-color: #B5B1A7;
            border-bottom: 2px solid #f4b400;
            color: #f4b400;
        }
        .navbar-brand {
            color: #000000;
            font-weight: bold;
        }
        .navbar-brand:hover {
            color: #FFFFFF;
        }
        .weather-card {
            border-radius: 15px;
            background: linear-gradient(135deg, #4e4e4e, #1f1f1f);
            color: #f4b400;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .news-list img {
            object-fit: cover;
            height: 200px;
            border-radius: 10px;
        }
        .card {
            background-color: #3b3b3b;
            border: none;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .card-title {
            color: #f4b400;
            font-weight: bold;
        }
        .card-text {
            color: #f4f4f4;
            display: none;
        }
        .card:hover .card-text {
            display: block;
        }
        .add-news {
            text-align: center;
            margin: 30px 0;
        }
        .add-news a {
            color: #f4b400;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            background: #4e4e4e;
            padding: 10px 20px;
            border-radius: 15px;
            transition: 0.3s;
        }
        .add-news a:hover {
            background: #f4b400;
            color: #3b3b3b;
        }
        footer {
            background-color: #3b3b3b;
            color: #f4b400;
        }
        footer a {
            color: #f4f4f4;
        }
        footer a:hover {
            color: #f4b400;
        }
        .banner {
            max-width: 800px;
            border-radius: 25px;
        }
        .clima-banner-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .clima-banner-section .card,
        .clima-banner-section img {
            flex: 1;
            margin: 10px;
        }
    </style>
    <script>
        function toggleCardText(id) {
            const text = document.getElementById('text-' + id);
            text.style.display = text.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Univali News - O seu Portal de Notícias</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav" >
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Weather and Banner Section -->
    <div class="container mb-5">
        <div class="clima-banner-section">
            <div class="card weather-card shadow-sm p-3">
                <div class="card-body text-center">
                    <p>Cidade: <?php echo $clima->cidade; ?> <i class="fas fa-map-marker-alt"></i></p>
                    <h1 class="display-4"> <?php echo $clima->temperatura; ?> °C</h1>
                    <p>Sensação Térmica: <?php echo $clima->sensacaoTermica; ?> °C</p>
                    <p><?php echo $clima->descricao; ?></p>
                    <div class="d-flex justify-content-around mt-3">
                        <p><i class="fas fa-tint"></i> Umidade: <?php echo $clima->umidade; ?>%</p>
                        <p><i class="fas fa-wind"></i> Vento: <?php echo $clima->velocidadeDoVento; ?> m/s</p>
                    </div>
                    <div class="d-flex justify-content-around">
                        <p><i class="fas fa-temperature-low"></i> Mín: <?php echo $clima->temperaturaMinima; ?> °C</p>
                        <p><i class="fas fa-temperature-high"></i> Máx: <?php echo $clima->temperaturaMaxima; ?> °C</p>
                    </div>
                </div>
            </div>
            <img src="icones/portal.jpg" alt="Portal de Notícias" class="banner">
        </div>
    </div>

    <!-- News Section -->
    <div class="container">
        <h3 class="mb-4">Notícias Recentes</h3>
        <div class="row news-list">
            <?php foreach ($noticias as $index => $noticia): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100" onclick="toggleCardText(<?php echo $index; ?>)">
                        <?php if (!empty($noticia['imagem'])): ?>
                            <img src="fotos/<?php echo htmlspecialchars($noticia['imagem']); ?>" class="card-img-top" alt="Imagem da notícia">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x150" class="card-img-top" alt="Imagem padrão">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title" ><?php echo htmlspecialchars($noticia['manchete']); ?></h5>
                            <p class="card-text" id="text-<?php echo $index; ?>"> <?php echo htmlspecialchars($noticia['textonoticia']); ?> </p>
                            <small class="text-muted">
                                <?php if (isset($noticia['datapubli'])): ?>
                                    <?php $data = new DateTime($noticia['datapubli']); echo $data->format('d/m/Y'); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Footer -->
    <footer class="text-center py-3 mt-4">
        <p>&copy; 2024 News Manager. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
