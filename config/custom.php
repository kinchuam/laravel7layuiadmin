<?php

return [
    'article_tier' => env('CUSTOM_ARTICLE_TIER',2),
    'permission_tier' => env('CUSTOM_PERMISSION_TIER',3),
    'config_cache_time' => env('CUSTOM_CONFIG_CACHE_TIME',120), //分钟

    'upload' => [
        'storage' => env('CUSTOM_UPLOAD_STORAGE','local'),
        'https' => env('CUSTOM_UPLOAD_HTTPS',false),
        'cache' => env('CUSTOM_UPLOAD_CACHE',false),
    ],

    'operation_log' => [
        'enable' => true,
        'except' => [],
    ],

    'permission_data' => [
        [
            'name' => 'content.manage',
            'display_name' => '内容管理',
            'icon' => 'layui-icon-read',
            'child' => [
                [
                    'name' => 'content.kv',
                    'display_name' => '广告管理',
                    'route' => 'admin.content.kv',
                    'icon' => 'layui-icon-util',
                    'child' => [
                        ['name' => 'content.kv.create', 'display_name' => '添加广告'],
                        ['name' => 'content.kv.edit', 'display_name' => '编辑广告'],
                        ['name' => 'content.kv.destroy', 'display_name' => '删除广告'],
                    ]
                ],
                [
                    'name' => 'content.articles',
                    'display_name' => '文章管理',
                    'route' => 'admin.content.articles',
                    'icon' => 'layui-icon-form',
                    'child' => [
                        ['name' => 'content.articles.create', 'display_name' => '添加文章'],
                        ['name' => 'content.articles.edit', 'display_name' => '编辑文章'],
                        ['name' => 'content.articles.destroy', 'display_name' => '删除文章'],
                    ]
                ],
                [
                    'name' => 'content.article_category',
                    'display_name' => '栏目管理',
                    'route' => 'admin.content.article_category',
                    'icon' => 'layui-icon-util',
                    'child' => [
                        ['name' => 'content.article_category.create', 'display_name' => '添加栏目'],
                        ['name' => 'content.article_category.edit', 'display_name' => '编辑栏目'],
                        ['name' => 'content.article_category.destroy', 'display_name' => '删除栏目'],
                    ]
                ],
                [
                    'name' => 'content.files',
                    'display_name' => '附件管理',
                    'route' => 'admin.content.files',
                    'icon' => 'layui-icon-file-b',
                    'child' => [
                        ['name' => 'content.files.create', 'display_name' => '添加附件'],
                        ['name' => 'content.files.destroy', 'display_name' => '删除附件'],
                        ['name' => 'content.files.recycle', 'display_name' => '回收站'],
                        ['name' => 'content.files.recover', 'display_name' => '回收站恢复'],
                        ['name' => 'content.files.expurgate', 'display_name' => '回收站删除'],
                    ]
                ],
                [
                    'name' => 'content.files_group',
                    'display_name' => '附件分组',
                    'route' => 'admin.content.files_group',
                    'icon' => 'layui-icon-file',
                    'child' => [
                        ['name' => 'content.files_group.create', 'display_name' => '添加分组'],
                        ['name' => 'content.files_group.edit', 'display_name' => '编辑分组'],
                        ['name' => 'content.files_group.destroy', 'display_name' => '删除分组'],
                    ]
                ],
            ]
        ],
        [
            'name' => 'system.manage',
            'display_name' => '系统管理',
            'icon' => 'layui-icon-util',
            'child' => [
                [
                    'name' => 'system.user',
                    'display_name' => '账号管理',
                    'route' => 'admin.system.user',
                    'icon' => 'layui-icon-friends',
                    'child' => [
                        ['name' => 'system.user.create', 'display_name' => '添加账号'],
                        ['name' => 'system.user.edit', 'display_name' => '编辑账号'],
                        ['name' => 'system.user.destroy', 'display_name' => '删除账号'],
                        ['name' => 'system.user.role', 'display_name' => '分配角色'],
                        ['name' => 'system.user.permission', 'display_name' => '分配权限'],
                    ]
                ],
                [
                    'name' => 'system.role',
                    'display_name' => '角色管理',
                    'route' => 'admin.system.role',
                    'icon' => 'layui-icon-set',
                    'child' => [
                        ['name' => 'system.role.create', 'display_name' => '添加角色'],
                        ['name' => 'system.role.edit', 'display_name' => '编辑角色'],
                        ['name' => 'system.role.destroy', 'display_name' => '删除角色'],
                        ['name' => 'system.role.permission', 'display_name' => '分配权限'],
                    ]
                ],
                [
                    'name' => 'system.permission',
                    'display_name' => '权限管理',
                    'route' => 'admin.system.permission',
                    'icon' => 'layui-icon-auz',
                    'child' => [
                        ['name' => 'system.permission.create', 'display_name' => '添加权限'],
                        ['name' => 'system.permission.edit', 'display_name' => '编辑权限'],
                        ['name' => 'system.permission.destroy', 'display_name' => '删除权限'],
                    ]
                ],
                [
                    'name' => 'system.config',
                    'display_name' => '系统设置',
                    'route' => 'admin.system.config',
                    'icon' => 'layui-icon-website',
                    'child' => [
                        ['name' => 'system.config.update', 'display_name' => '更新设置'],
                    ]
                ],
                [
                    'name' => 'system.cache',
                    'display_name' => '系统缓存',
                    'route' => 'admin.system.cache',
                    'icon' => 'layui-icon-set',
                    'child' => [
                        ['name' => 'system.cache.clear', 'display_name' => '更新缓存'],
                    ]
                ],
            ]
        ],
        [
            'name' => 'logs.manage',
            'display_name' => '日志管理',
            'icon' => 'layui-icon-log',
            'child' => [
                [
                    'name' => 'logs.operation',
                    'display_name' => '操作日志',
                    'route' => 'admin.logs.operation',
                    'icon' => 'layui-icon-log',
                    'child' => [
                        ['name' => 'logs.operation.show', 'display_name' => '查看日志'],
                    ]
                ],
                [
                    'name' => 'logs.access',
                    'display_name' => '访问日志',
                    'route' => 'admin.logs.access',
                    'icon' => 'layui-icon-log',
                    'child' => [
                        ['name' => 'logs.access.show', 'display_name' => '查看日志'],
                    ]
                ],
                [
                    'name' => 'logs.login',
                    'display_name' => '登录日志',
                    'route' => 'admin.logs.login',
                    'icon' => 'layui-icon-log',
                ],
                [
                    'name' => 'logs.system',
                    'display_name' => '系统日志',
                    'route' => 'admin.logs.system',
                    'icon' => 'layui-icon-log',
                ],
            ]
        ],
        [
            'name' => 'maintain.manage',
            'display_name' => '运维管理',
            'icon' => 'layui-icon-app',
            'child' => [
                [
                    'name' => 'maintain.database',
                    'display_name' => '数据库管理',
                    'route' => 'admin.maintain.database',
                    'icon' => 'layui-icon-set',
                    'child' => [
                        ['name' => 'maintain.database.optimize', 'display_name' => '优化表'],
                        ['name' => 'maintain.database.repair', 'display_name' => '修复表'],
                        ['name' => 'maintain.database.clear', 'display_name' => '清空表'],
                        ['name' => 'maintain.database.destroy', 'display_name' => '删除表'],
                    ]
                ],
                [
                    'name' => 'maintain.optimize',
                    'display_name' => '配置信息',
                    'route' => 'admin.maintain.optimize',
                    'icon' => 'layui-icon-set',
                ],
            ]
        ],
    ]

];
