<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlayHangman extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:hangman';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a simple hangman game in the terminal';

    protected $maxAttempts = 6;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        do {
            $word = $this->secret("Please enter the secret word to be guessed");
            $wordLength = strlen($word);

            while (!preg_match('/^[a-zA-Z\s]+$/', $word)) {
                $this->error("Please enter alphabetic characters and spaces only.");
                $word = $this->secret("Please enter the secret word or phrase to be guessed");
            }

            $word = str_split(strtolower($word));
            $guessedWord = [];

            foreach ($word as $char) {
                if ($char === ' ') {
                    array_push($guessedWord, ' ');
                } else {
                    array_push($guessedWord, '_');
                }
            }
            $guessedLetters = [];
            $remainingAttempts = $this->maxAttempts;

            while ($word != $guessedWord) {

                if ($remainingAttempts == 0) {
                    $this->newLine();

                    $this->error('You have lost! :(');
                    break;
                }

                $this->displayWordAndGuessed($guessedWord, $guessedLetters, $remainingAttempts);
                $letter = strtolower($this->ask('Guess a letter'));

                if (strlen($letter) !== 1 || !ctype_alpha($letter)) {
                    $this->error('Please enter a valid single letter.');
                    continue;
                }

                if (in_array($letter, $guessedLetters)) {
                    $this->error('You have already guessed this letter!');
                } else if (in_array($letter, $word)) {
                    array_push($guessedLetters, $letter);
                    foreach ($word as $key => $char) {
                        if ($char === $letter) {
                            $guessedWord[$key] = $char;
                        }
                    }
                    $this->line('You have guessed a letter');
                } else {
                    array_push($guessedLetters, $letter);
                    $remainingAttempts--;
                    $this->error('This letter is not in the word');
                }

                if ($word == $guessedWord) {

                    $this->newLine();
                    $this->info("Success! You have guessed the word!");
                    break;
                }
            }

            $playAgain = strtolower($this->ask('Would you like to play again? (yes/no)'));

        } while ($playAgain === 'yes');

        $this->info('Thank you for playing!');
    }

    public function displayWordAndGuessed($guessedWord, $guessedLetters, $attempts){

        $this->newLine();

        foreach ($guessedWord as $key => $char) {
            echo($char . ' ');
        }

        $this->newLine(2);

        $this->line("Guessed Letters: " .  implode(", ", $guessedLetters));

        $this->newLine();

        $this->line('Attempts left: ' . $attempts);

        $this->newLine();
    }
}

