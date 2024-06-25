<?php

namespace Modules\Testimonial\Actions;

use Modules\Testimonial\Entities\Testimonial;

class CreateTestimonial
{
    // public static function create($request)
    // {
    //     $image = $request->image;
    //     if ($image != null) {
    //         $url = uploadImage($image, 'testimonial');
    //     }

    //     Testimonial::create([
    //         'name' => $request->name,
    //         'position' => $request->position,
    //         'description' => $request->description,
    //         'image' => $url ?? null,
    //         'stars' => $request->stars,
    //         'code' => $request->code,
    //     ]);

    //     return true;
    // }
    public static function create($request)
    {
        $image = $request->image;
        if ($image != null) {

            $path = 'uploads/images/testimonial';
            $url = uploadImage($image, $path, [100, 100]);

        }
        Testimonial::create([
            'name' => $request->name,
            'position' => $request->position,
            'description' => $request->description,
            'image' => $url ?? null,
            'stars' => $request->stars,
            'code' => $request->code,
        ]);

        return true;
    }
}
