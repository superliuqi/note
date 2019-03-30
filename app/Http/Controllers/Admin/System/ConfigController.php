<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/5
 * Time: 上午10:17
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 系统设置
 * Class ConfigController
 * @package App\Http\Controllers\Admin\System
 */
class ConfigController extends Controller
{
    /**
     * 站点设置
     * @param Request $request
     */
    public function lists(Request $request) {
        if ($request->isMethod('post')) {
            $config = $request->config;
            if ($config) {
                foreach ($config as $id => $value) {
                    Config::where('id', $id)->update(['value' => $value]);
                }
            }
            $this->refreshCache();
            return res_success();
        } else {
            $res_config = Config::where([])->orderBy('position', 'asc')->orderBy('id', 'asc')->get()->toArray();
            $config = array();
            $tab_name = array();
            if ($res_config) {
                foreach ($res_config as $val) {
                    $tab_name[] = $val['tab_name'];
                    $config[$val['tab_name']][] = $val;
                }
            }
            return view('admin.system.config.lists', ['tab_name' => array_unique($tab_name), 'config' => array_values($config)]);
        }
    }

    /**
     * 添加配置
     * @param Request $request
     */
    public function edit(Request $request) {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'key_name' => [
                    'required',
                    Rule::unique('config')->ignore($request->id)
                ],
                'value' => 'required',
                'input_type' => 'required',
                'tab_name' => 'required',
                'position' => 'numeric'
            ], [
                'title.required' => '名称不能为空',
                'key_name.required' => '参数key名称不能为空',
                'key_name.unique' => '参数key名称已经存在',
                'value.required' => '参数值不能为空',
                'input_type.required' => '类型不能为空',
                'tab_name.required' => 'tab名称不能为空',
                'position.numeric' => '排序必须是数字'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'key_name', 'value', 'input_type', 'tab_name', 'position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->select_value) {
                $select_value = explode(chr(10), $request->select_value);
                $select_values = array();
                foreach ($select_value as $val) {
                    $_item = str_replace(chr(13), '', $val);
                    if ($_item && !in_array($_item, $select_values)) {
                        $select_values[] = $_item;
                    }
                }
                $save_data['select_value'] = join(',', $select_values);
            }

            $result = Config::create($save_data);
            $res = $result->id;

            if ($res) {
                $this->refreshCache();
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            return view('admin.system.config.edit');
        }
    }

    /**
     * 更新缓存信息
     * @param Request $request
     */
    public function refreshCache() {
        $res_config = Config::all();
        $config = array();
        if ($res_config) {
            foreach ($res_config as $val) {
                $config[$val['key_name']] = $val['value'];
            }
            set_redis_array('app_config:' . config('app.key'), $config);
        }
    }
}