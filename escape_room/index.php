<?php
session_start();

/* ---------- INICIALIZACIÓN DE ESTADO ---------- */
if (!isset($_SESSION['locks'])) {
    $_SESSION['locks'] = [
        'C1' => false,
        'C2' => false,
        'C3' => false,
        'C4' => false,
        'C5' => false
    ];
    $_SESSION['attempts_c5'] = 0;
    $_SESSION['message'] = '';
}

$message = $_SESSION['message'] ?? '';
$_SESSION['message'] = '';

/* ---------- PROCESAR FORMULARIOS ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candado = $_POST['candado'] ?? '';
    $codigo  = $_POST['codigo'] ?? '';

    $codigos_correctos = [
        'C1' => '3321',
        'C2' => '147',
        'C3' => '2',
        'C4' => '101',
        'C5' => '15'
    ];

    if ($candado === 'C5') $_SESSION['attempts_c5']++;

    if (isset($codigos_correctos[$candado]) && $codigo === $codigos_correctos[$candado]) {
        $_SESSION['locks'][$candado] = true;
        $_SESSION['message'] = '¡Candado desbloqueado!';
        header("Location: index.php?room=" . (array_search($candado, array_keys($_SESSION['locks'])) + 2));
        exit;
    } else {
        if ($candado === 'C5' && $_SESSION['attempts_c5'] >= 3) {
            $_SESSION['locks'] = ['C1'=>false,'C2'=>false,'C3'=>false,'C4'=>false,'C5'=>false];
            $_SESSION['attempts_c5'] = 0;
            $_SESSION['message'] = 'Has fallado 3 veces. Vuelves al inicio.';
            header("Location: index.php?room=0");
            exit;
        }
        $_SESSION['message'] = "Código incorrecto.";
        header("Location: index.php?room=" . ($_GET['room'] ?? 0));
        exit;
    }
}

/* ---------- CONTROL DE HABITACIONES ---------- */
$room = isset($_GET['room']) ? (int)$_GET['room'] : 0;
$locks = $_SESSION['locks'];

if ($room > 1 && !$locks['C1']) $room = 1;
if ($room > 2 && !$locks['C2']) $room = 2;
if ($room > 3 && !$locks['C3']) $room = 3;
if ($room > 4 && !$locks['C4']) $room = 4;
if ($room > 5 && !$locks['C5']) $room = 5;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Escape Room - Real Madrid</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: url("https://wallpapers.com/images/featured/real-madrid-4k-t84oqp4s6x7zj90k.jpg") no-repeat center center fixed;
    background-size: cover;
    color: white;
}
.overlay {
    background: rgba(0,0,0,0.7);
    min-height: 100vh;
    padding: 30px 0;
}
.container {
    max-width: 900px;
    margin: auto;
    background: rgba(0,0,0,0.85);
    padding: 25px 30px;
    border-radius: 12px;
    border: 2px solid gold;
    box-shadow: 0 0 15px gold;
}
h1, h2 {
    text-align: center;
    color: gold;
}
.message {
    background: rgba(255,255,255,0.1);
    border-left: 4px solid gold;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 6px;
}
.pistas {
    background: rgba(255,255,255,0.05);
    border: 1px solid gold;
    padding: 15px;
    border-radius: 10px;
}
.candado-form {
    background: rgba(255,255,255,0.08);
    border: 1px solid gold;
    padding: 15px;
    border-radius: 10px;
}
input[type="text"] {
    padding: 8px;
    width: 120px;
    border-radius: 5px;
    border: none;
}
.boton, input[type="submit"] {
    padding: 10px 20px;
    margin-top: 10px;
    border-radius: 6px;
    border: none;
    background: gold;
    color: black;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
}
.boton:hover, input[type="submit"]:hover {
    background: white;
}
img {
    width: 100%;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 2px solid gold;

    .galeria {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.galeria img {
    height: 180px;       
    width: auto;         
    object-fit: cover;   
    border: 2px solid gold;
    border-radius: 10px;
    box-shadow: 0 0 8px gold;
}

}
</style>
</head>

<body>
<div class="overlay">
<div class="container">

<h1>Escape Room del Real Madrid</h1>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($room === 0): ?>

<h2>Bienvenido Madridista</h2>
<img src="imagenes /escudo-real-madrid.png">

<div class="pistas">
<h3>Tu misión</h3>
<ul>
<li>La Copa de Europa ha sido robada del Bernabéu.</li>
<li>Debes superar 5 pruebas madridistas para recuperarla.</li>
<li>El último candado solo permite 3 intentos.</li>
</ul>
</div>

<a class="boton" href="index.php?room=1">Entrar al Bernabéu</a>

<?php elseif ($room === 1): ?>

<h2>Sala de la Champions</h2>

<div class="galeria">
    <img src="imagenes /0-1.png" alt="Marcador 0-1">
    <img src="imagenes /3-1.png" alt="Marcador 3-1">
    <img src="imagenes /1-4.png" alt="Marcador 2-1">
    <img src="imagenes /4-1.png" alt="Marcador 4-1">
</div>

<div class="pistas">
<h3>Acertijo del Candado 1</h3>
<ul>
    <li>Ante ti aparecen cuatro pantallas con las finales de Champions ganadas en 2014, 2017, 2018 y 2022.</li>
    <li>En cada una, el marcador final está oculta una pista.</li>
    <li>Forma el código en el mismo orden en el que aparecen los años.</li>
</ul>
</div>

<?php if (!$locks['C1']): ?>
<div class="candado-form">
<form method="post">
<input type="hidden" name="candado" value="C1">
<label>Código:</label>
<input type="text" name="codigo" maxlength="4" required>
<input type="submit" value="Abrir C1">
</form>
</div>
<?php else: ?>
<a class="boton" href="index.php?room=2">Ir a la Sala de Leyendas</a>
<?php endif; ?>



<?php elseif ($room === 2): ?>

<!-- ⭐ HABITACIÓN 2 -->
<h2>Sala de las Leyendas</h2>
<img src="imagenes /plantilla-2014.png">

<div class="pistas">
<h3>Acertijo del Candado 2</h3>
<ul>
<li>En esta imagen estan 3 de los jugadores capitales, sin ellos 3 este equipo no seria nada, el primero tiene algo distinto al resto,
    el segundo es un gladiador en la defensa, y el tercero es un goleador nato.
    Para resolver el candado se necesitan los dorsales de los jugadores clave.
</li>
</ul>
</div>

<?php if (!$locks['C2']): ?>
<div class="candado-form">
<form method="post">
<input type="hidden" name="candado" value="C2">
<label>Código:</label>
<input type="text" name="codigo" maxlength="3" required>
<input type="submit" value="Abrir C2">
</form>
</div>
<?php else: ?>
<a class="boton" href="index.php?room=3">Ir al Escudo Fragmentado</a>
<?php endif; ?>

<?php elseif ($room === 3): ?>

<!-- ⭐ HABITACIÓN 3 -->
<h2>Escudo Fragmentado</h2>
<img src="imagenes /chilena.png">

<div class="pistas">
<h3>Acertijo del Candado 3</h3>
<ul>
<li>Para recuperar la champions robada, se se necesita saber sobre cuantas chilenas marco el Real Madrid en la champions de 2018</li>
</ul>
</div>

<?php if (!$locks['C3']): ?>
<div class="candado-form">
<form method="post">
<input type="hidden" name="candado" value="C3">
<label>Código:</label>
<input type="text" name="codigo" maxlength="3" required>
<input type="submit" value="Abrir C3">
</form>
</div>
<?php else: ?>
<a class="boton" href="index.php?room=4">Ir al Vestuario</a>
<?php endif; ?>

<?php elseif ($room === 4): ?>

<!-- ⭐ HABITACIÓN 4 -->
<h2>Orden cronologico</h2>
<div class="galeria">
    <img src="imagenes /camiseta-1999.jpeg">
    <img src="imagenes /camiseta-2003.jpeg" >
    <img src="imagenes /camiseta-2016.jpg" >
</div>

<div class="pistas">
<h3>Acertijo del Candado 4</h3>
<ul>
<li>Para pasar al ultimo candado, tienes que hacer un orden cronologico.
    Cada camiseta representa un numero.
</li>
</ul>
</div>

<?php if (!$locks['C4']): ?>
<div class="candado-form">
<form method="post">
<input type="hidden" name="candado" value="C4">
<label>Código:</label>
<input type="text" name="codigo" maxlength="3" required>
<input type="submit" value="Abrir C4">
</form>
</div>
<?php else: ?>
<a class="boton" href="index.php?room=5">Ir a la Sala del Último Minuto</a>
<?php endif; ?>

<?php elseif ($room === 5): ?>

<!-- ⭐ HABITACIÓN 5 -->
<h2>La Vitrina</h2>
<img src="imagenes /2024-champions.png">

<div class="pistas">
<h3>Acertijo del Candado 5</h3>
<ul>
<li>Para recuperar la champions robada, el señor dice que esta es la ultima pregunta, 
    pero la mas importante, y la pregunta es.
    ¿Cuantas champions tiene el Real Madrid?
</li>
</ul>
</div>

<?php if (!$locks['C5']): ?>
<div class="candado-form">
<p>Intentos: <?= $_SESSION['attempts_c5'] ?> / 3</p>
<form method="post">
<input type="hidden" name="candado" value="C5">
<label>Código:</label>
<input type="text" name="codigo" maxlength="3" required>
<input type="submit" value="Abrir C5">
</form>
</div>
<?php else: ?>
<a class="boton" href="index.php?room=6">Ver final</a>
<?php endif; ?>

<?php elseif ($room === 6): ?>

<!-- ⭐ FINAL -->
<h2>¡HAS RECUPERADO LA COPA DE EUROPA!</h2>
<img src="imagenes /vitrina.jpg">

<p style="text-align:center; font-size:20px;">
El Bernabéu estalla de alegría.  
Has superado todas las pruebas madridistas.
</p>

<a class="boton" href="index.php?room=0">Jugar otra vez</a>

<?php endif; ?>

</div>
</div>
</body>
</html>
