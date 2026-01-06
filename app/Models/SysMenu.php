<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'title', 'icon', 'path', 'component',
        'perm_code', 'type', 'sort', 'status','is_hidden'
    ];

    // 定义关联：子菜单
    public function children()
    {
        return $this->hasMany(SysMenu::class, 'parent_id', 'id')->orderBy('sort', 'asc');
    }
}
