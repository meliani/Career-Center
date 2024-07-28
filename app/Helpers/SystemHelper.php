<?php

// app/Helpers/SystemHelper.php

if (! function_exists('systemUser')) {
    function systemUser()
    {
        // Replace this with the actual logic to get the system user
        return \App\Models\User::where('role', 'System')->first();
    }
}
