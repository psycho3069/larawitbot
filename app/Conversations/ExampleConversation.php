<?php

namespace App\Conversations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ExampleConversation extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Hey, what do yo need?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Ask me about a service')->value('service'),
                Button::create('Recommend some services')->value('recommend'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'recommend') {
                    $this->say($this->recommend());
                } else {
                    $this->say(Inspiring::quote());
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }



    public static function recommend()
    {
        return static::recommends()
            ->map(fn($recommend) => $recommend)
            ->random();
    }

    public static function recommends()
    {
        return Collection::make([
            'recommend 1',
            'recommend 2',
        ]);
    }
}