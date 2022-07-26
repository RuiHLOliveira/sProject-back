<?php

require './vendor/autoload.php';

use PhpMiniRouter\Core\Rota;
use PhpMiniRouter\Core\Kernel;

use PhpDailyManager\Controller\ContasController;
use PhpDailyManager\Controller\MovimentosController;
use PhpDailyManager\Controller\TiposMovimentosController;

$kernel = new Kernel();

$kernel->addRoute(Rota::get()->path('/contas')->controller(ContasController::class)->action('index'));
$kernel->addRoute(Rota::post()->path('/contas')->controller(ContasController::class)->action('create'));
$kernel->addRoute(Rota::get()->path('/tiposmovimentos')->controller(TiposMovimentosController::class)->action('index'));
$kernel->addRoute(Rota::post()->path('/tiposmovimentos')->controller(TiposMovimentosController::class)->action('create'));
$kernel->addRoute(Rota::get()->path('/movimentos')->controller(MovimentosController::class)->action('index'));
$kernel->addRoute(Rota::post()->path('/movimentos')->controller(MovimentosController::class)->action('create'));


$options = new stdClass();
$options->host = 'localhost';
$options->user = 'postgres';
$options->password = '123456';
$options->name = 'fluxodecaixa';
$options->port = '5432';
$kernel->start($options);