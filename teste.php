<?php
require_once __DIR__ . '/vendor/autoload.php';
use FedaPay\FedaPay;
use FedaPay\Models\Transaction;

FedaPay::setApiKey('VOTRE_SECRET_KEY_TEST');

echo "FedaPay chargé avec succès !";
