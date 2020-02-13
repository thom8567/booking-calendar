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
            'date' => new DateTime($tr->filter('.rich-results__result__content__date__datetime')->text()),
        ];
//        $tr->filter('.rich-results__result__content__synopsis')->each(function ($item) use (&$response) {
//            $itemText = $item->text();
//            if (strpos($itemText, 'Region:') !== false) {
//                $response["region"] = str_replace('Region: ', '', $itemText);
//            } elseif (strpos($itemText, 'Category:') !== false) {
//                $response["category"] = str_replace('Category: ', '', $itemText);
//            } elseif (strpos($itemText, 'Status:') !== false) {
//                $response["status"] = str_replace('Status: ', '', $itemText);
//            } elseif (strpos($itemText, 'Booking Deadline:') !== false) {
//                $response["booking_deadline"] = str_replace('Booking Deadline: ', '', $itemText);
//            } elseif (strpos($itemText, 'Planned Closing Date:') !== false) {
//                $response["planned_closing_date"] = str_replace('Planned Closing Date: ', '', $itemText);
//            } else {
//                $response = $itemText;
//            }
//        });
        $tr->filter('.rich-results__result__content__synopsis')->each(function ($item) use (&$response) {
            $itemText = $item->text();
            if (!preg_match('/[^:]*:/', $itemText)) {
                echo 'Nothing happened';
                return $itemText;
            }
            $matches = preg_match('/[^:]*:/', $itemText, $output_array);
            var_dump(preg_match('/[^:]*:/', $itemText, $output_array));
            var_dump($matches);
        });
        return $response;
    });
}

$client = createClient();

$crawler = createCrawler($client);

//var_dump(fetchEvents($crawler));


