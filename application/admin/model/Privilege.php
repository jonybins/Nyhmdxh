<?php
namespace app\admin\model;

use think\Model;

class Privilege extends Model
{
    protected $table = 'ny_privilege';
    // 设置数据表主键
    protected $pk    = 'id';


    public function getTree()
    {
        $data = $this->select();
        return $this->_reSort($data);
    }
    private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE)
    {
        static $ret = array();
        if($isClear)
            $ret = array();
        foreach ($data as $k => $v)
        {
            if($v['parent_id'] == $parent_id)
            {
                $v['level'] = $level;
                $ret[] = $v;
                $this->_reSort($data, $v['id'], $level+1, FALSE);
            }
        }
        return $ret;
    }
    public function getChildren($id)
    {
        $data = $this->select();
        return $this->_children($data, $id);
    }
    private function _children($data, $parent_id=0, $isClear=TRUE)
    {
        static $ret = array();
        if($isClear)
            $ret = array();
        foreach ($data as $k => $v)
        {
            if($v['parent_id'] == $parent_id)
            {
                $ret[] = $v['id'];
                $this->_children($data, $v['id'], FALSE);
            }
        }
        return $ret;
    }
    /************************************ 其他方法 ********************************************/
    public function _before_delete($option)
    {
        // 先找出所有的子分类
        $children = $this->getChildren($option['where']['id']);
        // 如果有子分类都删除掉
        if($children)
        {
            $children = implode(',', $children);
            $this->execute("DELETE FROM php34_privilege WHERE id IN($children)");
        }
    }
}