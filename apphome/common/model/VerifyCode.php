<?php
namespace app\common\model;

use think\Db;

class VerifyCode extends Base
{
    // 模型会自动对应数据表，模型类的命名规则是除去表前缀的数据表名称，采用驼峰法命名，并且首字母大写，例如：模型名UserType，约定对应数据表think_user_type(假设数据库的前缀定义是 think_)
    // 设置当前模型对应的完整数据表名称
    //protected $table = 'fl_page';
    
    // 默认主键为自动识别，如果需要指定，可以设置属性
    protected $pk = 'id';
    
    public function getDb()
    {
        return db('verify_code');
    }
    
    /**
     * 列表
     * @param array $where 查询条件
     * @param string $order 排序
     * @param string $field 字段
     * @param int $offset 偏移量
     * @param int $limit 取多少条
     * @return array
     */
    public function getList($where = array(), $order = '', $field = '*', $offset = 0, $limit = 15)
    {
        $res['count'] = $this->getDb()->where($where)->count();
        $res['list'] = array();
        
        if($res['count'] > 0)
        {
            $res['list'] = $this->getDb()->where($where);
            
            if(is_array($field))
            {
                $res['list'] = $res['list']->field($field[0],true);
            }
            else
            {
                $res['list'] = $res['list']->field($field);
            }
            
            $res['list'] = $res['list']->order($order)->limit($offset.','.$limit)->select();
        }
        
        return $res;
    }
    
    /**
     * 分页，用于前端html输出
     * @param array $where 查询条件
     * @param string $order 排序
     * @param string $field 字段
     * @param int $limit 每页几条
     * @param int $page 当前第几页
     * @return array
     */
    public function getPaginate($where = array(), $order = '', $field = '*', $limit = 15)
    {
        $res = $this->getDb()->where($where);
        
        if(is_array($field))
        {
            $res = $res->field($field[0],true);
        }
        else
        {
            $res = $res->field($field);
        }
        
        return $res->order($order)->paginate($limit, false, array('query' => request()->param()));
    }
    
    /**
     * 获取一条
     * @param array $where 条件
     * @param string $field 字段
     * @return array
     */
    public function getOne($where, $field = '*')
    {
        return $this->getDb()->where($where)->field($field)->find();
    }
    
    /**
     * 添加
     * @param array $data 数据
     * @return int
     */
    public function add($data,$type=0)
    {
        // 过滤数组中的非数据表字段数据
        // return $this->allowField(true)->isUpdate(false)->save($data);
        
        if($type==0)
        {
            // 新增单条数据并返回主键值
            return $this->getDb()->insertGetId($data);
        }
        elseif($type==1)
        {
            // 添加单条数据
            return $this->getDb()->insert($data);
        }
        elseif($type==2)
        {
            /**
             * 添加多条数据
             * $data = [
             *     ['foo' => 'bar', 'bar' => 'foo'],
             *     ['foo' => 'bar1', 'bar' => 'foo1'],
             *     ['foo' => 'bar2', 'bar' => 'foo2']
             * ];
             */
            
            return $this->getDb()->insertAll($data);
        }
    }
    
    /**
     * 修改
     * @param array $data 数据
     * @param array $where 条件
     * @return bool
     */
    public function edit($data, $where = array())
    {
        return $this->allowField(true)->isUpdate(true)->save($data, $where);
    }
    
    /**
     * 删除
     * @param array $where 条件
     * @return bool
     */
    public function del($where)
    {
        return $this->where($where)->delete();
    }
    
    //类型，0通用，注册，1:手机绑定业务验证码，2:密码修改业务验证码
    public function getTypeAttr($data)
    {
        $arr = array(0 => '通用', 1 => '手机绑定业务验证码', 2 => '密码修改业务验证码');
        return $arr[$data['type']];
    }
    
    //状态
    public function getStatusAttr($data)
    {
        $arr = array(0 => '未使用', 1 => '已使用');
        return $arr[$data['status']];
    }
}