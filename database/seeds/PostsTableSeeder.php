<?php

use Illuminate\Database\Seeder;
use App\Post;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();

        $faker = \Faker\Factory::create();
        for($i=0; $i<10; $i++)
        {            
            Post::create([
                'post_title' => $faker->sentence,
                'post_content' => $faker->paragraph,
                'user_id' => 1
            ]);
        }
    }
}
