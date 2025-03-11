<?php

// app/Helpers/SystemHelper.php

if (! function_exists('systemUser')) {
    function systemUser()
    {
        return \App\Models\User::where('role', 'System')->first();
    }
}
