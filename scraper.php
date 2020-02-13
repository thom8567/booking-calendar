<?php declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";
use Symfony\Component\Panther\Client;

function createClient()
{
    return Client::createChromeClient();
}

function createCrawler($client)
{
    return $client->request('GET', 'https://www.britishrowing.org/rowing-activity-finder/calendar/');
}

$client = createClient();
$crawler = createCrawler($client);
$fullPageHtml = $crawler->html();

$table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
    return $tr->filter('td')->each(function ($td, $i) {
        return str_replace('VIEW EVENT', '', trim($td->text()));
    });
});

echo json_encode($table);
