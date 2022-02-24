<?php

use App\Models\System\User;
use App\Models\System\Role;
use App\Models\System\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //清空表
        $this->truncateTable();
        $username = 'root';$password = 'root';
        //用户
        $user = User::query()->create([
            'username' => $username,
            'display_name' => '超级管理员',
            'password' => bcrypt($password),
            'uuid' => \Faker\Provider\Uuid::uuid()
        ]);
        //角色
        $role = Role::query()->create([
            'name' => 'root',
            'display_name' => '超级管理组'
        ]);
        //为用户添加角色
        $user->assignRole($role);
        //生成权限
        $permissions = config('custom.permission_data');
        if (!empty($permissions)) {
            foreach ($permissions as $pem1) {
                //生成一级菜单
                $p1 = $this->CreatePermissions($pem1, $role);
                if (!empty($pem1['child'])) {
                    foreach ($pem1['child'] as $pem2) {
                        //生成二级菜单
                        $p2 = $this->CreatePermissions($pem2, $role, $p1['id']);
                        if (!empty($pem2['child'])) {
                            foreach ($pem2['child'] as $pem3) {
                                //生成三级菜单
                                $this->CreatePermissions($pem3, $role, $p2['id']);
                            }
                        }
                    }
                }
            }
        }
        $this->clear_cache();
        echo '----------------------------------'."\n";
        echo "url: ".config('app.url')." \n";
        echo 'username: '.$username."\n";
        echo 'password: '.$password."\n";
        echo '----------------------------------'."\n";
    }

    private function truncateTable()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function clear_cache()
    {
        Cache::tags('system')->flush();
    }

    private function CreatePermissions($data, $role, $parent_id = 0)
    {
        $item = Permission::query()->create([
            "name"          => trim($data['name']),
            "display_name"  => trim($data['display_name']),
            "parent_id"     => intval($parent_id),
        ]);
        //为角色添加权限
        $role->givePermissionTo($item);
        return $item;
    }
}
