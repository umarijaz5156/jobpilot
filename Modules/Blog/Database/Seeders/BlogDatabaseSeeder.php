<?php

namespace Modules\Blog\Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Entities\PostComment;
use Modules\Language\Entities\Language;

class BlogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Post category
        $categories = [
            [
                'category_name' => 'Career Development',
                'post_title' => "Blog posts focusing on advancing one's career, mastering interviews, and navigating transitions.",
            ],
            [
                'category_name' => 'Workplace Productivity',
                'post_title' => 'Articles about boosting productivity, time management, and effective work techniques.',
            ],
            [
                'category_name' => 'Remote Work & Future Trends',
                'post_title' => 'Content centered around remote work trends, virtual collaboration, and the future of work.',
            ],
            [
                'category_name' => 'Leadership & Management',
                'post_title' => 'Insights on effective leadership, team motivation, and cultivating a positive work culture.',
            ],
            [
                'category_name' => 'Job Search Strategies',
                'post_title' => 'Blogs offering guidance on job hunting, resume building, and successful job applications.',
            ],
        ];

        foreach ($categories as $category) {
            $created_category = PostCategory::create([
                'name' => $category['category_name'],
                'slug' => Str::slug($category['category_name']),
            ]);
        }

        // PostCategory::factory(5)->create();
        Post::factory(100)->create();

        foreach ($categories as $category) {
            $id = rand(30, 600);
            $image = 'https://picsum.photos/id/'.$id.'/700/600';

            Post::create([
                'category_id' => $created_category->id,
                'author_id' => Admin::inRandomOrder()->value('id'),
                'title' => $category['post_title'],
                'slug' => Str::slug($category['post_title']),
                'image' => $image,
                'short_description' => fake()->sentence(10),
                'description' => fake()->paragraph(50),
                'status' => Arr::random(['published', 'draft']),
                'locale' => Language::inRandomOrder()->value('code'),
            ]);
        }

        $posts = Post::all();
        foreach ($posts as $post) {
            PostComment::factory(5)->create([
                'post_id' => $post->id,
            ]);
        }
    }
}
