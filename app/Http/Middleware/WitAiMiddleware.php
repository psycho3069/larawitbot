<?php

namespace App\Http\Middleware;

use App\Conversations\QuestionConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class WitAiMiddleware implements Received
{
    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://api.wit.ai/message', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('WIT_ACCESS_TOKEN'),
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'v' => '20230215',
                'q' => $message->getText(),
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $intents = $data['intents'];
        $entities = $data['entities'];

        if (!empty($intents)) {
            switch ($intents[0]['name']) {
                case 'greetings':
                    $bot->reply('Hello, how may I help you?!');
                    break;
                case 'nameOfService':
                    $bot->reply('We provide Hajj Package for the Muslims in Bangladesh!');
                    break;
                case 'priceOfService':
                    $bot->reply('Our Hajj Package costs $2000 from Dhaka and $2100 from anywhere else in Bangladesh!');
                    break;
                case 'serviceAtLocation':
                    // dd($entities['wit$location:location']);
                    $bot->reply("Yes, it is available in {$entities['wit$location:location'][0]['body']}!");
                    break;
                case 'query':
                    $bot->reply("Sure, You can try our Hajj Package which is cost efficient and very much comfortable even for olders");
                    break;
                default:
                    $bot->reply('I actually don\'t get what you want!');
                    break;
            }
        } else {
            $bot->reply('Sorry, Can\'t understand!');
        }

        return $next($message);
    }

    public function startQuestion(BotMan $bot)
    {
        $bot->startConversation(new QuestionConversation());
    }
}