<?php

namespace App\Extensions;

class MenuBuilder
{
    public static $menus;

    public static $data = [
        'name' => null,
        'url' => null,
        'level' => null,
        'icon' => null,
        'position' => null,
        'permitted' => null,
        'active' => false,
        'submenu' => [],
    ];

    public static function build()
    {
        self::$menus = app('config')->get("app.common.menu");

        foreach (self::$menus as $key => $menu) {

            app('config')->set("app.common.menus.{$key}", self::recursiveMenu($menu));
        }
    }

    /**
     * Рекурсивное формирование блоков меню
     * @param $items
     * @param int $level
     * @return array
     */
    public static function recursiveMenu($items, $level = 0, $data= [])
    {
        $result = [];

        foreach ($items as $item) {

            foreach (self::$data as $key => $datum) {

                $data[$key] = optional($item)[$key];
            }

            $data['level'] = $level;
            $data['isPermitted'] = self::isPermitted($item['permission']);
            $data['isActive'] = self::isActive($item);
            $data['isOpen'] = self::isOpen($item['submenu']);
            $data['isSubmenu'] = (bool) count($item['submenu']);

            if (empty($item['url'])) {

                $data['url'] = 'javascript:void(0);';
            } else {

                $data['url'] = "/{$item['url']}";
            }

            $data['submenu'] = self::recursiveMenu($item['submenu'], $level + 1);

            $result[] = $data;
        }

        return $result;
    }

    public static function isOpen($submenu)
    {
        return self::isSubmenuActive($submenu);
    }

    public static function isActive($item)
    {
        if (count($item['submenu'])) {

            return self::isSubmenuActive($item['submenu']);
        } else {

            $pattern = $item['active'];

            if (preg_match('/\/\*/', $pattern)) {

                return  request()->is(str_replace('/*', '', $pattern)) || request()->is($pattern);
            } else {

                return  request()->is($pattern);
            }
        }
    }

    public static function isPermitted($permission)
    {
        if (!empty($permission)) {

            $hasPermission = false;

            if (is_array($permission)) {

                foreach ($permission as $item) {

                    $check = auth()->user()->hasPermission($item);

                    if ($check) {

                        $hasPermission = $check;
                    }
                }
            } else {

                $hasPermission = auth()->user()->hasPermission($permission);
            }

            return auth()->check() && $hasPermission;
        } else {

            return true;
        }
    }

    public static function isSubmenuActive($submenu)
    {
        $result = false;

        foreach ($submenu as $item) {

            if (count($item['submenu'])) {

                if (self::isSubmenuActive($item['submenu'])) {

                    $result = true;
                }
            } else {

                $pattern = $item['active'];

                if (preg_match('/\/\*/', $pattern)) {

                    if (request()->is(str_replace('/*', '', $pattern)) || request()->is($pattern)) {

                        $result = true;
                    }
                } else {

                    if (request()->is($pattern)) {

                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
}
