<?php

use App\Jobs\Users\DeleteOldUsersJob;

/*
|--------------------------------------------------------------------------
| Cron commands.
|--------------------------------------------------------------------------
*/

Schedule::job(new DeleteOldUsersJob(1))->everyTenSeconds();
