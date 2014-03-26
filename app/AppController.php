<?php

class AppController extends Controller
{

    public function beforeFilter()
    {
        $settings = Cache::get('all_settings_cache');
        if (!$settings) {
            $settings = Setting::all();
            $settings = Model::result_array($settings);
            Cache::set('all_settings_cache', $settings);
        }
        foreach ($settings as $setting) {
            Configure::set('setting.' . $setting['name'], $setting['value']);
        }
    }

}