<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;

class BookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'title' => $this->params['title'],
            'author' => $this->params['author'],
            'category' => $this->params['category'],
        ];

        $count = Book::count();

        Book::create($data);

        if($count == 0){
            Cache::forget("books-page-1");
        }
        
        for($i = 1; $i <= $count; $i++){
            $key = 'books-page-' . $i;
            if(Cache::has($key)){
                Cache::forget($key);
            } else {
                break;
            }
        }
    }
}
