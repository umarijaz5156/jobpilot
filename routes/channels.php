<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    info('-------------user model channel---------------');
    info($user);
    info($id);
    info('-----------user model channel-----------------');

    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat', function ($user) {
    info('-------------chat channel---------------');
    info(auth()->check());
    info('-----------chat channel-----------------');

    return auth()->check();
});
