<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Permission::get()->map(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch (\Exception $e) {
            report($e);
            return false;
        }

        //Blade directives
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})) { ?>"; //return this if statement inside php tag
        });

        Blade::directive('endrole', function ($role) {
                return "<?php } ?>"; //return this endif statement inside php tag
        });

        //Blade directives
        Blade::directive('perms', function ($perm) {
            return "<?php if(auth()->check() && auth()->user()->hasPermissionTo({$perm})) { ?>"; //return this if statement inside php tag
        });

        Blade::directive('endperms', function ($perm) {
                return "<?php } ?>"; //return this endif statement inside php tag
        });

        // Impersonation
        Blade::directive('notImpersonating', function ($guard = null) {
            return "<?php if (!is_impersonating({$guard})) : ?>";
        });

        Blade::directive('endNotImpersonating', function () {
            return '<?php endif; ?>';
        });
    }
}
