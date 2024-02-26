<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;

class ScrapePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $crawler = GoutteFacade::request('GET', 'https://laravel.com/docs/10.x/installation');
        $title = $crawler->filter('h1.dt-news__title')->each(function ($node) {
            return $node->text();
        });
        print($title);
    }
}
