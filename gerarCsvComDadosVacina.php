<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

// URL da página
$url = 'https://integracao.esusab.ufsc.br/ledi/documentacao/regras/validar_regras_vacinacao.html';

// Inicializa o cliente HTTP
$client = new Client();
$response = $client->request('GET', $url);
$html = (string) $response->getBody();

// Carrega o HTML na biblioteca Crawler
$crawler = new Crawler($html);

// Encontra a primeira tabela na página
$tbody = $crawler->filter('table tbody')->first();

// Abre um arquivo CSV para escrita
$csvFile = fopen('regras_vacina.csv', 'w');

// Escreve o cabeçalho do CSV
fputcsv($csvFile, [
    'CODIGO_VACINA', 
    'NOME_VACINA', 
    'CODIGO_ESTRATEGIA', 
    'NOME_ESTRATEGIA', 
    'CODIGO_DOSE', 
    'NOME_DOSE'
]);

// Itera sobre as linhas da tabela
$tbody->filter('tr')->each(function (Crawler $row) use ($csvFile) {
    $cols = $row->filter('td');        
    if ($cols->count() >= 6) {
        // Extrai o texto de cada coluna e remove espaços extras
        $data = [
            trim($cols->eq(0)->text()),
            trim($cols->eq(1)->text()),
            trim($cols->eq(2)->text()),
            trim($cols->eq(3)->text()),
            trim($cols->eq(4)->text()),
            trim($cols->eq(5)->text()),
        ];

        // Escreve os dados no CSV
        fputcsv($csvFile, $data);
    }
});

fclose($csvFile);

echo "CSV gerado com sucesso!";
?>

