<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryTreeNode extends Model
{

    protected $fillable = [
        'user_id','parent_id','parent_node_id','great_parent_id','active_in_sale_log','level','activation_number','sale_log_id','position','status'
    ];

    /**
     * status and active_in_sale_log columns define the user in the tree is
     * active (status = 1, active_in_sale_log = 1),
     * pending to be inactive (status = 0, active_in_sale_log = 1)
     * & inactive (status = 0, active_in_sale_log = 0)
     *  */ 

    const ACTIVE_IN_TREE = 1;
    const INACTIVE_IN_TREE = 0;
    const INACTIVE_PENDING_IN_TREE = 2;

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function saleLog() {
        return $this->belongsTo(SaleLog::class,'sale_log_id');
    }

    public function parent() {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(BinaryTreeNode::class, 'parent_id');
    }

    public function parentNode(){
        return $this->belongsTo(BinaryTreeNode::class, 'parent_id');
    }

    public static function getAncestorChain(BinaryTreeNode $node, int $saleLogId, int $depth = 3): array
    {
        $ancestors = [];

        $current = $node;

        for ($i = 1; $i <= $depth; $i++) {
            if (!$current->parent_id) {
                break;
            }

            $parent = BinaryTreeNode::where('user_id', $current->parent_id)
                ->where('sale_log_id', $saleLogId)
                ->where('level', $current->level - 1)
                ->first();

            if (!$parent) {
                break;
            }

            $ancestors["{$i}"] = $parent;
            $current = $parent;
        }

        return $ancestors;
    }

    public function getParentNodeId(){
        if (!$this->parent_id || !$this->sale_log_id) {
            return null;
        }
        
        $parent = self::where('user_id', $this->parent_id)
                    ->where('sale_log_id', $this->sale_log_id)
                    ->first();
        
        return $parent ? $parent->id : null;
    }

    public function getGreatParentNodeId()
    {
        if (!$this->parent_node_id || !$this->sale_log_id) {
            return null;
        }

        $parent = self::where('id', $this->parent_node_id)
            ->where('sale_log_id', $this->sale_log_id)
            ->latest()
            ->first();

        $grandParent = $parent?->parent_node_id ? self::find($parent->parent_node_id) : null;
        $greatParent = $grandParent?->parent_node_id ? self::find($grandParent->parent_node_id) : null;

        if (!$greatParent) {
            return null;
        }

        $user = User::find($greatParent->user_id);

        return $user && $user->is_super_prime ? null : $greatParent;
    }



}
