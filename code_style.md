# PHP
### variable (camelCase 🐫)
    
    $user  = User::first(); // one
    $users = User::get(); // many

    $activeUser  = ''; // one
    $activeUsers = []; // many

### loop

    foreach ($users as $user) {
        //
    }

### function (camelCase 🐫)

    function myFunction() {
        //
    }


### relation (snake_case 🐍)

    public function phone()
    {
        return $this->hasOne(Phone::class); // one
    }

    public function comments()
    {
        return $this->hasMany(Comment::class); // many
    }

    public function kaiin_profile()
    {
        return $this->hasOne(KaiinProfile::class); // one
    }

    public function kaiin_profiles()
    {
        return $this->hasMany(KaiinProfile::class); // many
    }

# Javascript
### variable (camelCase 🐫)
    
    $user  = User::first(); // one
    $users = User::get(); // many

    $activeUser  = ''; // one
    $activeUsers = []; // many

### function (camelCase 🐫)

    function myFunction() {
        //
    }

# HTML
### id, class (kebab-case)

    <input type="text" id="my-input" class="btn-primary">

### function (camelCase 🐫)

    function myFunction() {
        //
    }

# Laravel
### Config (snake_case 🐍)
    google_calendar.php

### View (kebab-case)
    show-filtered.blade.php

# Vue (https://v2.vuejs.org/v2/style-guide/)
