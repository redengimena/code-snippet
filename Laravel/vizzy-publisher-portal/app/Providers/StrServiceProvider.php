<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class StrServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Date formatting macro
        Str::macro('duration', function ($value) {
          if (Str::contains($value, ':')) {
            sscanf($value, "%d:%d:%d", $hours, $minutes, $seconds);
            $value = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;            
          }
          
          if (is_numeric($value)) {
            if ($value > 3600) {
              $output = gmdate('G\h', $value);
              if (gmdate('i\m', $value) != '00m') {
                $output .= ' ' .ltrim(gmdate('i\m', $value),0);
              }
              return $output;
            } else {
              return ltrim(gmdate('i\m', $value),0);
            }
          } 
            
          return $value;            
        });
    }
}