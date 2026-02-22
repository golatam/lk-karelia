<?php

if (!function_exists('image_path')) {

    function image_path($path, $miniatureName = null)
    {
        if ($miniatureName) {

            if (file_exists(public_path(dirname($path) . "/{$miniatureName}_" . basename($path)))) {

                return dirname($path) . "/{$miniatureName}_" . basename($path);
            } else {

                return $path;
            }
        } else {

            return $path;
        }
    }
}

if (!function_exists('has_cookie')) {

    function has_cookie($name)
    {
        return isset($_COOKIE[$name]) ? true : false;
    }
}

if (!function_exists('get_cookie')) {

    function get_cookie($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? json_decode($_COOKIE[$name], true) : $default;
    }
}

if (!function_exists('set_cookie')) {

    function set_cookie($name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}

if (!function_exists('delete_cookie')) {

    function delete_cookie($name, $value = "", $expire = -1, $path = "", $domain = "", $secure = false, $httponly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        return !has_cookie($name);
    }
}

if (!function_exists('url_frontend')) {

    function url_frontend($path = null)
    {
        $urlFrontendConfig = config('common.url_frontend', '');

        $url = implode('/', [
            trim($urlFrontendConfig, '/'),
            trim($path, '/'),
        ]);

        return $url;
    }
}

if (!function_exists('set_permissions')) {

    function set_permissions($model, $output, $actions, $counter = 0)
    {
        $tableName = $model->getTable();

        foreach ($actions as $action) {

            $type = 'core';
            $group = \Illuminate\Support\Str::singular($tableName);

            $name = __("{$tableName}.permissions.name.{$action}", [], app()->getLocale());
            $description = __("{$tableName}.permissions.descriptions.{$action}");

            $permission = \App\Models\Permission::firstOrNew([
                'type' => $type,
                'group' => $group,
                'action' => $action,
            ]);

            if (!$permission->exists) {

                $permission->name = $name;
                $permission->description = $description;
                $permission->save();
                $counter++;
            }
        }

        if ($counter) {

            $output->writeln("<info>Установлены новые разрешения:</info>  {$counter}");
        } else {

            $output->writeln("<info>Новые разрешения отсутствуют.</info>");
        }
    }
}

//
//function add_year() {
//    $data = [
//        'get_callback' => 'rest_get_year',
//        'update_callback' => 'rest_update_year',
//        'schema' => [
//            'description' => 'The name of the book author.',
//            'type' => 'string',
//            'context' => ['view', 'edit'],
//        ],
//    ];
//    register_api_field('post', 'year', $data);
//}
//
//add_action('rest_api_init', 'add_year');
//
//function rest_get_year($post, $field_name, $request) {
//    // Make modifications to field name if required.
//    return get_post_meta($post->id, $field_name);
//}
//
//function rest_update_year($value, $post, $field_name) {
//    // Perform Validation of input
//    if (!$value || !is_string($value)) {
//        return;
//    }
//    // Update the field
//    return update_post_meta($post->ID, $field_name, $value);
//}
//
//$args = array(
//    'type'=>'string',
//    'single'=>true,
//    'show_in_rest'=>true
//);
//
//register_post_meta('post', 'year', $args);
