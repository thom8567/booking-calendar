<?php declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";
use Goutte\Client as GoutteClient;

function createClient()
{
    return new GoutteClient();
}

function createCrawler($client)
{
    return $client->request('GET', 'https://www.britishrowing.org/rowing-activity-finder/calendar/');
}

function fetchEvents($crawler)
{
    return $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
        if ($tr->filter('.rich-results__result__content__title')->count() === 0) {
            return [];
        }
        $response = [
            'title' => $tr->filter('.rich-results__result__content__title')->text(),
            'date' => (new DateTime($tr->filter('.rich-results__result__content__date__datetime')->text()))->format('Y-m-d'),
        ];
        $tr->filter('.rich-results__result__content__synopsis')->each(function ($item) use (&$response) {
            $itemText = $item->text();
            if (!strpos($item->text(), ':')) {
                $response[] = $itemText;
            }
            $itemText = explode(':', $item->text());
            $response[str_replace(' ', '_', mb_strtolower($itemText[0]))] = trim($itemText[1]);
        });
        return $response;
    });
}

$client = createClient();

$crawler = createCrawler($client);

$events = array_filter(fetchEvents($crawler));

echo json_encode($events);


